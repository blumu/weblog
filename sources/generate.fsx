/// Build script for my weblog site
/// Adapted by William Blum from F# Formatting example by Tomas Petricek.

open System.IO

#load @"../packages/FSharp.Formatting/FSharp.Formatting.fsx"
open FSharp.Literate

#r "../packages/FAKE/tools/FakeLib.dll"
open Fake
open Fake.FileHelper

#load "FsBlogLib/Scripts/load-project.fsx"
#r "RazorEngine.dll"
open FsBlogLib.FileHelpers
open FsBlogLib.BlogPosts
open FsBlogLib.Blog
open FsBlogLib.Calendar

/// Return path relative to the current file location
let relative subdir = Path.Combine(__SOURCE_DIRECTORY__, subdir)

let subdirsRecurse dir =
    [
        yield dir 
        yield! System.IO.Directory.EnumerateDirectories(dir,"*", SearchOption.AllDirectories)
    ]

let staticRoot  = relative "static"
let templates   = relative "templates" 

let sourceRoot = __SOURCE_DIRECTORY__.Replace("\\", "/")

// Get the domain name from command-line (for Kudu deployment)
let AzureDomainNameVar = "WEBSITE_HOSTNAME"
let domainName =
    let d = System.Environment.GetEnvironmentVariable(AzureDomainNameVar) 
    if isNull d then
         "localhost:8080"
    elif d = "luweiblog.azurewebsites.net" then // remap Azure domain to custom domain name.
        "william.famille-blum.org"
    else
        d

printfn "Domain is %s" domainName


// --------------------------------------------------------------------------------------
// Test using local HTTP server
// --------------------------------------------------------------------------------------
open FSharp.Http

let server : ref<option<HttpServer>> = ref None
let stop () =
  server.Value |> Option.iter (fun v -> v.Stop())
let run output =
  let url = "http://localhost:8080/" 
  stop ()
  server := Some(HttpServer.Start(url, output, Replacements = ["http://william.famille-blum.org/", url]))
  printfn "Starting web server at %s" url
  System.Diagnostics.Process.Start(url) |> ignore

//////////////

let websiteRoot = sprintf "http://%s" domainName

let projInfo =
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


let layoutRootsAll =  subdirsRecurse templates |> Seq.toList


// Copy static files and CSS + JS from F# Formatting
let copyFiles output =
  CopyRecursive staticRoot output true |> Log "Copying file: "
  ensureDirectory (output @@ "content")


/// Describe a directory containing content to be compiled
type ContentDirectory =
    {
        sourceDirectory : string
        allDirectories : bool
        outputDirectory : string
        template : string
    }

let contentDirectories =
    [ 
        {
            sourceDirectory = @"blog\legacy"
            allDirectories = true
            outputDirectory = @"blog\"
            template = @"templates\blogpost.cshtml"
        }
        {
            sourceDirectory = @"blog\new"
            allDirectories = false
            outputDirectory = @"blog\"
            template = @"templates\blogpost.cshtml"
        }
        {
            sourceDirectory = @"pages"
            allDirectories = true
            outputDirectory = @""
            template = @"templates\pages.cshtml"
        }
        {
            sourceDirectory = @"specialpages\software\cracklock"
            allDirectories = true
            outputDirectory = @"software\cracklock"
            template = @"templates\cracklock.cshtml"
        }
        {
            sourceDirectory = @"specialpages\research\"
            allDirectories = true
            outputDirectory = @"research"
            template = @"templates\researchref.cshtml"
        }
    ]

// Build website from `md` and `fsx` files
let buildSite output updateTagArchive =
    copyFiles output
    for contentDir in contentDirectories do
      Literate.ProcessDirectory
        ( __SOURCE_DIRECTORY__ @@ contentDir.sourceDirectory,
          relative contentDir.template, 
          output @@ contentDir.outputDirectory,
          replacements = projInfo,
          layoutRoots = layoutRootsAll,
          generateAnchors = true,
          processRecursive = true,
          includeSource = false,
          lineNumbers = false
        )

///////////////


// --------------------------------------------------------------------------------------
// Configuration
// --------------------------------------------------------------------------------------

// Root URL for the generated HTML & other basic information
let title = "William Blum's site"
let description = 
   "Posts about software I wrote and other things."

// Information about source directory, blog subdirectory, layouts & content
let source = relative "../source"
let blog = relative "../source/blog"
let blogIndex = relative "../source/blog/index.cshtml"
let layouts = relative "../layouts"
let parts = relative "../layouts/parts"
let content = relative "../content"
let template = relative "empty-template.html"
let calendar = relative "../calendar"
let calendarMonth = relative "../source/calendar/month.cshtml"
let calendarIndex = relative "../source/calendar/index.cshtml"

// F# code generation - skip 'exclude' directory & add 'references'
let exclude = 
  [ yield relative "../source/blog/packages"
    yield relative "../source/blog/abstracts"
    yield relative "../source/calendar"
  ]

let references = []

let special =
  [ source ++ "index.cshtml"
    source ++ "blog" ++ "index.cshtml" ]

// Dependencies - if any of these files change, then we must regenerate all
let dependencies = 
  [ yield! Directory.GetFiles(layouts) 
    yield! Directory.GetFiles(parts) 
    yield calendarMonth 
    yield calendarIndex ]


let tagRenames = 
  [ ("F# language", "f#"); ("Functional Programming in .NET", "functional");
    ("Materials & Links", "links"); ("C# language", "c#"); (".NET General", ".net") ] |> dict

let buildSiteWithFsBlog output updateTagArchive =
  let noModel = { Model.Root = websiteRoot; MonthlyPosts = [||]; Posts = [||]; TaglyPosts = [||]; GenerateAll = true }
  let razor = FsBlogLib.Razor(layouts, Model = noModel)
  let model = LoadModel(tagRenames, TransformAsTemp (template, source) razor, websiteRoot, blog)

  // Generate RSS feed
  GenerateRss websiteRoot title description model (output ++ "rss.xml")
  GenerateCalendar websiteRoot layouts output dependencies calendar calendarMonth calendarIndex model

  let uk = System.Globalization.CultureInfo.GetCultureInfo("en-US")
  GeneratePostListing 
    layouts template blogIndex model model.MonthlyPosts 
    (fun (y, m, _) -> output ++ "blog" ++ "archive" ++ (m.ToLower() + "-" + (string y)) ++ "index.html")
    (fun (y, m, _) -> y = System.DateTime.Now.Year && m = uk.DateTimeFormat.GetMonthName(System.DateTime.Now.Month))
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

  CopyFiles content output 
  CopyFiles calendar (output ++ "calendar")


/////////////////


let rebuildSite output updateTagArchive =
    printfn "Building site..."
    CleanDir output // Just in case the template changed (buildDocumentation is caching internally, maybe we should remove that)
    buildSite output updateTagArchive

let watch output runServer =
  printfn "Starting watching by initial building..."

  rebuildSite output true
  printfn "Watching for changes..."

  let queue = new System.Collections.Concurrent.ConcurrentQueue<_>()
  let processTask () =
    async {
      let! tok = Async.CancellationToken
      while not tok.IsCancellationRequested do
        try
          if queue.IsEmpty then
            do! Async.Sleep 1000
          else
            let data = ref []
            let hasData = ref true
            while !hasData do
              match queue.TryDequeue() with
              | true, d ->
                data := d :: !data
              | _ ->
                hasData := false

            printfn "Detected changes (%A). Invalidate cache and rebuild." !data
            FSharp.MetadataFormat.RazorEngineCache.InvalidateCache (!data |> Seq.map (fun change -> change.FullPath))
            FSharp.Literate.RazorEngineCache.InvalidateCache (!data |> Seq.map (fun change -> change.FullPath))
            rebuildSite output true
            printfn "Documentation generation finished."
        with e ->
          printfn "Documentation generation failed: %O" e
    }

  // Given a list of directories return a list of filters matching all files in each directory
  let allFilesInDirectory =
    Seq.map (fun d -> relative d + "/*.*" )
    >> Seq.toList

  let allFilesInAllSubdirectories =
    Seq.collect subdirsRecurse
    >> allFilesInDirectory

  let (+++) x dirs =  List.fold (++) x dirs

  let contentDirs = 
    contentDirectories
    |> Seq.map (fun d -> __SOURCE_DIRECTORY__ @@ d.sourceDirectory)

  use filter =
    !! (templates + "/*.*"
          +++ allFilesInAllSubdirectories contentDirs
          +++ allFilesInAllSubdirectories [ staticRoot ]
      ) |> WatchChanges (fun changes -> changes |> Seq.iter queue.Enqueue)
  use source = new System.Threading.CancellationTokenSource()
  Async.Start(processTask (), source.Token)
  if runServer then
    run output
  printfn "Press enter to exit watching..."
  System.Console.ReadLine() |> ignore
  source.Cancel()

