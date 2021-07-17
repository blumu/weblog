﻿module FsBlog.Document

open System
open System.IO
open System.Collections.Generic
open System.Text.RegularExpressions

open FSharp.Formatting.Literate
open FSharp.Formatting.Markdown
open FSharp.Formatting.Markdown.HtmlFormatting

open FsBlog.Helpers

// --------------------------------------------------------------------------------------
// Document transformations
// --------------------------------------------------------------------------------------

let private (</>) a b = Path.Combine(a, b)
let private ensureDirectory d = 
  if not (Directory.Exists(d)) then Directory.CreateDirectory(d) |> ignore

let private (|CharSeparatedSpans|_|) (sep:char) spans =
  let rec loop before spans =
    match spans with
    | Literal(text=s)::rest when s.Contains(sep.ToString()) ->
        let s1, s2 = s.Substring(0, s.IndexOf(sep)).Trim(), s.Substring(s.IndexOf(sep)+1)
        let before = List.rev before
        let before = if String.IsNullOrWhiteSpace(s1) then before else Literal(s1, None)::before
        let rest = if String.IsNullOrWhiteSpace(s2) then rest else Literal(s2, None)::rest
        Some(before, rest)
    | [] -> None
    | x::xs -> loop (x::before) xs
  loop [] spans

let private (|ColonSeparatedSpans|_|) spans = (|CharSeparatedSpans|_|) ':' spans
let private (|QMarkSeparatedSpans|_|) spans = (|CharSeparatedSpans|_|) '?' spans

// let private createFormattingContext writer =
//   { Writer = writer
//     Links = dict []
//     Newline = "\n"
//     LineBreak = ignore
//     WrapCodeSnippets = false
//     GenerateHeaderAnchors = true
//     UniqueNameGenerator = new UniqueNameGenerator()
//     ParagraphIndent = ignore }

// let private formatSpans spans =
//   let sb = Text.StringBuilder()
//   ( use wr = new StringWriter(sb)
//     let fc = createFormattingContext wr
//     HtmlFormatting.formatSpans fc spans )
//   sb.ToString()

let private formatPlainSpans spans =
  let sb = Text.StringBuilder()
  let rec loop spans =
    for span in spans do
      match span with
      | DirectLink(body=body) -> loop body
      | Literal(text=t) -> sb.Append(t) |> ignore
      | _ -> failwithf "Unsupported span: %A" span
  loop spans
  sb.ToString()

let private generateSubheadings = function
  | Heading(size=1; body=QMarkSeparatedSpans(before, after)) ->
        InlineHtmlBlock
          (sprintf "<h1><span class=\"hmq\">%s</span><span class=\"hs\">%s</span></h1>"
            //(formatSpans before) (formatSpans after), None, None)
            (formatPlainSpans before) (formatPlainSpans after), None, None)
  | Heading(size=1; body=ColonSeparatedSpans(before, after)) ->
        InlineHtmlBlock
          (sprintf "<h1><span class=\"hm\">%s</span><span class=\"hs\">%s</span></h1>"
            //(formatSpans before) (formatSpans after), None, None)
            (formatPlainSpans before) (formatPlainSpans after), None, None)
  | p -> p

// --------------------------------------------------------------------------------------
// Document parsing
// --------------------------------------------------------------------------------------

let private readProperty = function
  // | [Span(body=[Literal(l, _)])] ->
  //   failwithf "!!Failed to read property: %A" l

  | [Span(body=ColonSeparatedSpans(before, after))] ->
      match formatPlainSpans before with
      | "description" -> "description", formatPlainSpans after // wiblum formatSpans after
      | s -> s, (formatPlainSpans after).Trim()
  | p -> failwithf "Failed to read property: %A" p

let private (|Properties|) = function
  | ListBlock(kind=MarkdownListKind.Unordered; items=props)::rest ->
      props |> List.map readProperty |> dict, rest
  | rest -> dict [], rest

let private (|Abstract|) = function
  | HorizontalRule(_)::ListBlock(kind=MarkdownListKind.Unordered; items=props)::rest
  | HorizontalRule(_)::Let [] (props, rest) ->
      let rec split acc = function
        | HorizontalRule _ :: rest -> List.rev acc, rest
        | p :: rest -> split (p::acc) rest
        | _ -> failwith "Parsing abstract failed"
      let standalone = props |> Seq.exists(function [Span(body=[Literal(text="standalone")])] -> true | _ -> false)
      let abs, rest = split [] rest
      Some(standalone, abs), rest
  | rest -> None, rest

let private readMetadata (pars:MarkdownParagraphs) =
  match pars with
  | Heading(size=1; body=title)::Properties(props, Abstract(abs, rest)) -> title, props, abs, rest
  | f -> failwithf "No metadata: %A" f

let private tryFind k (props:IDictionary<string, string>) =
  if props.ContainsKey k then Some(props.[k]) else None

let private parseMetadata (cfg:SiteConfig) (file:string) (title, props, abstractOpt, body) =
  let abs, body =
    match abstractOpt with
    | Some(true, abs) -> abs, Heading(1, title, None)::body
    | Some(false, abs) -> abs, Heading(1, title, None)::(abs @ body)
    | None -> [], Heading(1, title, None)::body
  let date = tryFind "date" props |> Option.map DateTime.Parse
  let references = tryFind "references" props = Some "true"

  { Title = formatPlainSpans title // wiblum
    Subtitle = defaultArg (tryFind "subtitle" props) ""
    Icon = defaultArg (tryFind "icon" props) ""
    Description = defaultArg (tryFind "description" props) ""
    Image = match tryFind "image" props, tryFind "image-large" props with Some i, _ | _, Some i -> i | _ -> ""
    LargeImage = (tryFind "image-large" props).IsSome
    References = references
    Tags =
      (defaultArg (tryFind "tags" props) "").Split([| ',' |], StringSplitOptions.RemoveEmptyEntries)
      |> Seq.map (fun s -> s.Trim()) |> List.ofSeq
    Date = defaultArg date DateTime.MinValue
    HasDate = date.IsSome
    //Url = cfg.Root + (Path.ChangeExtension(file.Substring(cfg.Source.Length), "").TrimEnd('.')).Replace('\\', '/')  + "/"
    Url = (Path.ChangeExtension(file.Substring(cfg.Source.Length), "").TrimEnd('.')).Replace('\\', '/')  + "/"
    Layout = tryFind "layout" props
    Abstract = abs; Body = body }

let private generateReferences (refs:System.Collections.Generic.IDictionary<_, _>) =
  [ Heading(2, [Literal("References", None)], None)
    ListBlock(MarkdownListKind.Ordered,
      [ for url, titleOpt in refs.Values do
          match titleOpt with
          | None -> ()
          | Some title ->
              let ref = sprintf "<a href='%s'>%s</a>" url title
              yield [ InlineHtmlBlock(ref, None, None) ] ], None) ]

let private transformMarkdownOrScript (cfg:SiteConfig) plain (inf:string) =
  let cached = Path.ChangeExtension(cfg.Cache </> inf.Substring(cfg.Source.Length+1), ".json")
  if not (sourceChanged inf cached) then
    Json.fromJson (File.ReadAllText cached)
  else
    printfn "Parsing F#/MD file: %s" (inf.Replace(cfg.Source, ""))
    let document =
      if plain then Literate.ParseMarkdownFile(inf)
      else Literate.ParseAndCheckScriptFile(inf)

    let article = parseMetadata cfg inf (readMetadata document.Paragraphs)
    let body = if article.References then article.Body @ generateReferences document.DefinedLinks else article.Body
    let body = document.With(List.map generateSubheadings body)

    let abs = document.With(article.Abstract)
    let da = Literate.ToHtml(abs, "document")
    let db = Literate.ToHtml(body, "document")
    //let fetch (o:GeneratorOutput) = (dict o.Parameters).[o.ContentTag] + (dict o.Parameters).["tooltips"]
    let res = article.With(da, db)
    ensureDirectory (Path.GetDirectoryName cached)
    File.WriteAllText(cached, Json.toJson res)
    res

/// Read Markdown document, parse metadata and format it as HTML
let transformMarkdown cfg file =
  transformMarkdownOrScript cfg true file

/// Read F# script with inline Markdown, parse metadata and format it as HTML
let transformFsScript cfg file =
  transformMarkdownOrScript cfg false file
