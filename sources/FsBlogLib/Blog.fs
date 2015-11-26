﻿namespace FsBlogLib

open System
open System.IO
open BlogPosts
open FileHelpers
open System.Xml.Linq
open FSharp.Literate
open FSharp.Markdown
open FSharp.Markdown.Html

// --------------------------------------------------------------------------------------
// Document transformations
// --------------------------------------------------------------------------------------

module Transforms = 
  
  let (|ColonSeparatedSpans|_|) spans =
    let rec loop before spans = 
      match spans with
      | Literal(s)::rest when s.Contains(":") ->
          let s1, s2 = s.Substring(0, s.IndexOf(':')).Trim(), s.Substring(s.IndexOf(':')+1).Trim()
          let before = List.rev before
          let before = if String.IsNullOrWhiteSpace(s1) then before else Literal(s1)::before
          let rest = if String.IsNullOrWhiteSpace(s2) then rest else Literal(s2)::rest
          Some(before, rest)
      | [] -> None
      | x::xs -> loop (x::before) xs
    loop [] spans

  let createFormattingContext writer = 
    { Writer = writer
      Links = dict []
      Newline = "\n"
      LineBreak = ignore
      WrapCodeSnippets = false
      GenerateHeaderAnchors = true
      UniqueNameGenerator = new UniqueNameGenerator()
      ParagraphIndent = ignore }

  let formatSpans spans = 
    let sb = Text.StringBuilder()
    ( use wr = new StringWriter(sb)
      let fc = createFormattingContext wr
      Html.formatSpans fc spans )
    sb.ToString()

  let generateSubheadings = function
    | Heading(1, ColonSeparatedSpans(before, after)) -> 
          InlineBlock
            (sprintf "<h1><span class=\"hm\">%s</span><span class=\"hs\">%s</span></h1>" 
              (formatSpans before) (formatSpans after))
    | p -> p

// --------------------------------------------------------------------------------------
// Blog - the main blog functionality
// --------------------------------------------------------------------------------------

module Blog = 

  /// Represents the model that is passed to all pages
  type Model = 
    { Posts : BlogHeader[] 
      MonthlyPosts : (int * string * seq<BlogHeader>)[]
      TaglyPosts : (string * string * seq<BlogHeader>)[]
      GenerateAll : bool
      Root : string }

  /// Walks over all blog post files and loads model (caches abstracts along the way)
  let LoadModel(tagRenames, transformer, (root:string), blog) = 
    let urlFriendly (s:string) = s.Replace("#", "sharp").Replace(" ", "-").Replace(".", "dot")
    let posts = LoadBlogPosts tagRenames transformer blog
    let uk = System.Globalization.CultureInfo.GetCultureInfo("en-GB")
    { Posts = posts
      GenerateAll = false
      TaglyPosts = 
        query { for p in posts do
                for t in p.Tags do
                select t into t
                distinct
                let posts = posts |> Seq.filter (fun p -> p.Tags |> Seq.exists ((=) t))
                let recent = posts |> Seq.filter (fun p -> p.Date > (DateTime.Now.AddYears(-1))) |> Seq.length
                where (recent > 0)
                sortByDescending (recent * (Seq.length posts))
                select (t, urlFriendly t, posts) } 
        |> Array.ofSeq
      MonthlyPosts = 
        query { for p in posts do
                groupBy (p.Date.Year, p.Date.Month) into g
                let year, month = g.Key
                sortByDescending (year, month)
                select (year, uk.DateTimeFormat.GetMonthName(month), g :> seq<_>) }
        |> Array.ofSeq
      Root = root.Replace('\\', '/') }

  let TransformFile template hasHeader (razor:FsBlogLib.Razor) prefix current target = 
    let html =
      match Path.GetExtension(current).ToLower() with
      | (".fsx" | ".md") as ext ->
          let header, content = 
            if not hasHeader then "", File.ReadAllText(current)
            else RemoveScriptHeader ext current
          use fsx = DisposableFile.Create(current.Replace(ext, "_" + ext))
          use html = DisposableFile.CreateTemp(".html")
          File.WriteAllText(fsx.FileName, content)
          let parsed = 
            if ext = ".fsx" then
              Literate.ParseScriptFile(fsx.FileName)
            else
              Literate.ParseMarkdownFile(fsx.FileName)
          let parsed = parsed.With(List.map Transforms.generateSubheadings parsed.Paragraphs)
          Literate.ProcessDocument(parsed, html.FileName, template, OutputKind.Html, ?prefix=prefix)
          let processed = File.ReadAllText(html.FileName)
          File.WriteAllText(html.FileName, header + processed)
          EnsureDirectory(Path.GetDirectoryName(target))
          razor.ProcessFile(html.FileName)

      | ".html" | ".cshtml" ->
          let html =
            razor.ProcessFile(current)
            |> CSharpFormat.SyntaxHighlighter.FormatHtml
          html.Replace("&amp;", "&")
           
      | _ -> failwith "Not supported file!"
    File.WriteAllText(target, html)

  let TransformAsTemp (template, source:string) razor prefix current = 
    let cached = (Path.GetDirectoryName(current) ++ "cached" ++ Path.GetFileName(current))
    if File.Exists(cached) && 
      (File.GetLastWriteTime(cached) > File.GetLastWriteTime(current)) then 
      File.ReadAllText(cached)
    else
      printfn "Processing abstract: %s" (current.Substring(source.Length + 1))
      EnsureDirectory(Path.GetDirectoryName(current) ++ "cached")
      TransformFile template false razor (Some prefix) current cached
      File.ReadAllText(cached)

  let GenerateRss root title description model target = 
    let (!) name = XName.Get(name)
    let items = 
      [| for item in model.Posts |> Seq.take 20 ->
           XElement
            ( !"item", 
              XElement(!"title", item.Title),
              XElement(!"guid", root + "/blog/" + item.Url),
              XElement(!"link", root + "/blog/" + item.Url + "/index.html"),
              XElement(!"pubDate", item.Date.ToUniversalTime().ToString("r")),
              XElement(!"description", item.Abstract) ) |]
    let channel = 
      XElement
        ( !"channel",
          XElement(!"title", (title:string)),
          XElement(!"link", (root:string)),
          XElement(!"description", (description:string)),
          items )
    let doc = XDocument(XElement(!"rss", XAttribute(!"version", "2.0"), channel))
    File.WriteAllText(target, doc.ToString())

  let GeneratePostListing layouts template blogIndex model posts urlFunc needsUpdate infoFunc getPosts =
    for item in posts do
      let model = { model with GenerateAll = true; Posts = Array.ofSeq (getPosts item) }
      let razor = FsBlogLib.Razor(layouts, Model = model)
      let target = urlFunc item
      EnsureDirectory(Path.GetDirectoryName(target))
      if not (File.Exists(target)) || needsUpdate item then
        printfn "Generating archive: %s" (infoFunc item)
        TransformFile template true razor None blogIndex target

