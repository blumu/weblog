/// Build script for my weblog site
/// Adapted by William Blum from F# Formatting example by Tomas Petricek.

open System.IO

#load @"packages/FSharp.Formatting/FSharp.Formatting.fsx"
open FSharp.Literate

#r "packages/FAKE/tools/FakeLib.dll"
open Fake
open Fake.FileHelper

//#load "FsBlogLib/Scripts/load-project.fsx"
#I "FsBlogLib/bin/Debug"
#r "FsBlogLib.dll"
#r "RazorEngine.dll"
open FsBlogLib.FileHelpers
open FsBlogLib.BlogPosts
open FsBlogLib.Blog


module Constants =
    let LocalhostDomain = "localhost:8080"
    let AzureDomainNameVar = "WEBSITE_HOSTNAME"

// --------------------------------------------------------------------------------------
// Test using local HTTP server
// --------------------------------------------------------------------------------------
open FSharp.Http

let server : ref<option<HttpServer>> = ref None
let stop () =
  server.Value |> Option.iter (fun v -> v.Stop())
let run output =
  let url = sprintf "http://%s/" Constants.LocalhostDomain
  stop ()
  server := Some(HttpServer.Start(url, output, Replacements = ["https://william.famille-blum.org/", url]))
  printfn "Starting web server at %s" url
  System.Diagnostics.Process.Start(url) |> ignore

// --------------------------------------------------------------------------------------
// Configuration
// --------------------------------------------------------------------------------------

let sourceRoot = __SOURCE_DIRECTORY__.Replace("\\", "/")

let projInfo websiteRoot =
  [ "page-description", "William Blum's personal website"
    "page-author", "William Blum"
    "github-link", "https://github.com/blumu/weblog"
    "project-name", "William blum site"
    "project-author", "William Blum"
    "project-github", "https://github.com/blumu/weblog"
    "page-title", "William Blum's personal website"
    "project-title", "William Blum's personal website"
    "project-summary", "William Blum's site http://william.famille-blum.org"
    "project-github", "https://github.com/blumu/weblog"
    "root", websiteRoot
    "sourceroot", sourceRoot
  ]

// Root URL for the generated HTML & other basic information
let title = "William Blum's site"
let description = "Posts about software I wrote and other things."

// Information about source directory, blog subdirectory, layouts & content
let layouts = __SOURCE_DIRECTORY__ ++ "layouts"
let parts = layouts ++ "parts"
let template = layouts ++ "empty-template.html"

let source = __SOURCE_DIRECTORY__ ++ "sources"
let staticRoot  = source ++ "static"
let homeIndex = source ++ "index.cshtml"
let blog = source ++ "blog"
let blogIndex = source ++ "blog/index.cshtml"

// let calendar = source @@ "calendar"
// let calendarMonth = source @@ "calendar/month.cshtml"
// let calendarIndex = source @@ "calendar/index.cshtml"

// F# code generation - skip 'exclude' directory & add 'references'
let exclude =
  [ yield source ++ "blog/abstracts"
    yield staticRoot
    //yield __SOURCE_DIRECTORY__ ++ "calendar"
  ]

let references = []

let special =
  [ homeIndex
    blogIndex ]

// Dependencies - if any of these files change, then we must regenerate all
let dependencies =
  [ yield! Directory.GetFiles(layouts)
    yield! Directory.GetFiles(parts)
    //yield calendarMonth
    //yield calendarIndex
    ]


let tagRenames =
  [ ("F# language", "f#"); ("Functional Programming in .NET", "functional");
    ("Materials & Links", "links"); ("C# language", "c#"); (".NET General", ".net") ] |> dict

let rec CopyCachedRecursive source target =
    let files = System.IO.Directory.GetFiles(source, "*.*")
                |> CopyCached target source
                //|> Log "Copying file: "
    System.IO.Directory.EnumerateDirectories(source)
    |> Seq.iter(fun sourceSubdir ->
        let s = sourceSubdir |> System.IO.DirectoryInfo
        let targetSubdir = target ++ s.Name |> System.IO.DirectoryInfo
        if not targetSubdir.Exists then targetSubdir.Create()
        CopyCachedRecursive sourceSubdir targetSubdir.FullName)

let buildSite output updateTagArchive domainName =
  printfn "Building site..."
  // Copy static files and CSS + JS from F# Formatting
  CopyCachedRecursive staticRoot output

  // Get the domain name from command-line (for Kudu deployment)
  let domainName = defaultArg domainName Constants.LocalhostDomain
  printfn "Domain is %s" domainName

  let websiteRoot = sprintf "https://%s" domainName

  let noModel = { Model.Root = websiteRoot; MonthlyPosts = [||]; Posts = [||]; TaglyPosts = [||]; GenerateAll = true; Properties = dict [] }
  let razor = FsBlogLib.Razor(layouts, Model = noModel)
  let model = LoadModel(tagRenames, TransformAsTemp (template, source) razor, websiteRoot, blog, projInfo websiteRoot)

  // Generate RSS feed
  // GenerateRss websiteRoot title description model (output ++ "rss.xml")
  // GenerateCalendar websiteRoot layouts output dependencies calendar calendarMonth calendarIndex model

  let postListing = true
  if postListing then
      let us = System.Globalization.CultureInfo.GetCultureInfo("en-US")
      GeneratePostListing
        layouts template blogIndex model model.MonthlyPosts
        (fun (y, m, _) -> output ++ "blog" ++ "archive" ++ (m.ToLower() + "-" + (string y)) ++ "index.html")
        (fun (y, m, _) -> y = System.DateTime.Now.Year && m = us.DateTimeFormat.GetMonthName(System.DateTime.Now.Month))
        (fun (y, m, _) -> sprintf "%d %s" y m)
        (fun (_, _, p) -> p)

      if updateTagArchive then
        GeneratePostListing
          layouts template blogIndex model model.TaglyPosts
          (fun (_, u, _) -> output ++ "blog" ++ "tag" ++ u ++ "index.html")
          (fun (_, _, _) -> true)
          (fun (t, _, _) -> t)
          (fun (_, _, p) -> p)

  let filesToProcess =
    GetSourceFiles source output
    |> SkipExcludedFiles exclude
    |> TransformOutputFiles output
    |> FilterChangedFiles dependencies special

  let razor = FsBlogLib.Razor(layouts, Model = model)
  for current, target in filesToProcess do
    EnsureDirectory(Path.GetDirectoryName(target))
    printfn "Processing file: %s" (current.Substring(source.Length + 1))
    TransformFile template true razor None current target

  //CopyFiles calendar (output ++ "calendar")

let clean output =
  for dir in Directory.GetDirectories(output) do
    if not (dir.EndsWith(".git")) then SafeDeleteDir dir true
  for file in Directory.GetFiles(output) do
    File.Delete(file)

let rebuildSite output updateTagArchive domainName =
  printfn "Rebuilding site..."
  clean output // Just in case the template changed (buildDocumentation is caching internally, maybe we should remove that)
  buildSite output updateTagArchive domainName

/////////////////

let watch output runServer =
  printfn "Starting watching by initial building..."

  buildSite output true None
  printfn "Watching for changes..."

  let queue = new System.Collections.Concurrent.ConcurrentQueue<_>()
  //let processTask () =
  //  async {
  //    let! tok = Async.CancellationToken
  //    while not tok.IsCancellationRequested do
  //      try
  //        if queue.IsEmpty then
  //          do! Async.Sleep 1000
  //        else
  //          let data = ref []
  //          let hasData = ref true
  //          while !hasData do
  //            match queue.TryDequeue() with
  //            | true, d ->
  //              data := d :: !data
  //            | _ ->
  //              hasData := false
  //
  //          printfn "Detected changes (%A). Invalidate cache and rebuild." !data
  //          FSharp.MetadataFormat.RazorEngineCache.InvalidateCache (!data |> Seq.map (fun change -> change.FullPath))
  //          FSharp.Literate.RazorEngineCache.InvalidateCache (!data |> Seq.map (fun change -> change.FullPath))
  //          buildSite output false
  //          printfn "Documentation generation finished."
  //      with e ->
  //        printfn "Documentation generation failed: %O" e
  //  }

  let filter =
      { BaseDirectory = __SOURCE_DIRECTORY__
        Includes = [ "layouts/**/*.*"; "sources/**/*.*" ]
        Excludes = [] }

  use watch = filter |> WatchChanges (fun changes -> buildSite output false None
                                        //changes |> Seq.iter queue.Enqueue
                                        )
  //use source = new System.Threading.CancellationTokenSource()
  //Async.Start(processTask (), source.Token)
  if runServer then
    run output
  printfn "Press enter to exit watching..."
  System.Console.ReadLine() |> ignore
  //source.Cancel()

