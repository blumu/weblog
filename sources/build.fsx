/// Build script for my weblog site
/// Adapted by William Blum from F# Formatting example by Tomas Petricek.

#load @"packages\FSharp.Formatting\FSharp.Formatting.fsx"
open System.IO
open FSharp.Literate

#r "packages/FAKE/tools/FakeLib.dll"
open Fake
open System.IO
open Fake
open Fake.FileHelper

/// Return path relative to the current file location
let relative subdir = Path.Combine(__SOURCE_DIRECTORY__, subdir)

let subdirsRecurse dir =
    [
        yield dir 
        yield! System.IO.Directory.EnumerateDirectories(dir,"*", SearchOption.AllDirectories)
    ]

let staticRoot  = relative "static"
let templates   = relative "templates" 

#if PUBLISH
let output      = relative "..\..\wwwroot"
#else
let output      = relative "..\output"
#endif

let sourceRoot = (__SOURCE_DIRECTORY__ @@ output).Replace("\\", "/")
argList


// Get the domain name from command-line (for Kudu deployment)
let domainName = 
    if fsi.CommandLineArgs.Length > 1 then
        fsi.CommandLineArgs.[1]
    else
        "william.famille-blum.org"

printfn "Domain is %s" domainName

#if PUBLISH
let websiteRoot = sprintf "http://%s" domainName
#else
let websiteRoot = sourceRoot
#endif

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
let copyFiles () =
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
let buildSite() =
  for contentDir in contentDirectories do
    Literate.ProcessDirectory
      ( contentDir.sourceDirectory,
        relative contentDir.template, 
        output @@ contentDir.outputDirectory,
        replacements = projInfo,
        layoutRoots = layoutRootsAll,
        generateAnchors = true,
        processRecursive = true,
        includeSource = false,
        lineNumbers = false
      )

let rebuildSite () =
    CleanDir output // Just in case the template changed (buildDocumentation is caching internally, maybe we should remove that)
    copyFiles()
    buildSite()

let watch () =
  printfn "Starting watching by initial building..."

  rebuildSite()
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
            rebuildSite()
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
    |> Seq.map (fun d -> d.sourceDirectory)

  use watcher =
    (!! (templates + "/*.*")
      +++ allFilesInAllSubdirectories contentDirs
      +++ allFilesInAllSubdirectories [ staticRoot ]
    )
    |> WatchChanges (fun changes -> changes |> Seq.iter queue.Enqueue)
  use source = new System.Threading.CancellationTokenSource()
  Async.Start(processTask (), source.Token)
  printfn "Press enter to exit watching..."
  System.Console.ReadLine() |> ignore
  watcher.Dispose()
  source.Cancel()

#if PUBLISH
printfn "Publishing site"
rebuildSite ()
#else
watch () 
#endif