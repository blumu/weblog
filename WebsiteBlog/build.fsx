/// Given a typical setup (with 'FSharp.Formatting' referenced using NuGet),
// the following will include binaries and load the literate script
#load @"..\packages\FSharp.Formatting\FSharp.Formatting.fsx"
open System.IO
open FSharp.Literate

#r "../../packages/FAKE/tools/FakeLib.dll"
open Fake
open System.IO
open Fake
open Fake.FileHelper

/// Return path relative to the current file location
let relative subdir = Path.Combine(__SOURCE_DIRECTORY__, subdir)

let subdirsRecurse dir =
    seq {
        yield dir 
        yield! System.IO.Directory.EnumerateDirectories(dir,"*", SearchOption.AllDirectories)
    }

let websiteRoot = (__SOURCE_DIRECTORY__ @@ "output").Replace("\\", "/")

let projInfo =
  [ "page-description", "William Blum's personal website"
    "page-author", "William Blum"
    "github-link", ""
    "project-name", "William blum site" 
    "project-author", "William Blum"
    "project-github", "todo"
    "page-title", "title"
    "project-title", "title"
    "project-summary", "William Blum's site http://william.famille-blum.org"
//    "project-github", githubLink
    "project-nuget", "https://github.com/blumu/weblog"
    "root", websiteRoot
    ]

System.IO.Directory.SetCurrentDirectory (__SOURCE_DIRECTORY__)


let layoutRootsAll =   "templates" :: ( subdirsRecurse (relative "templates") |> Seq.toList)


let output      = relative "output"
let staticFiles = relative "static"
let content     = relative "posts"
let templates   = relative "templates" 


// Copy static files and CSS + JS from F# Formatting
let copyFiles () =
  CopyRecursive staticFiles output true |> Log "Copying file: "
  ensureDirectory (output @@ "content")
  //CopyRecursive (formatting @@ "styles") (output @@ "content") true 
  //  |> Log "Copying styles and scripts: "

let contentDirectories =
    [ 
        @"posts",                    "",                    @"templates\mydocpage.cshtml" 
        @"pages\software\cracklock", @"software\cracklock", @"templates\cracklock.cshtml" 
        @"pages\research\",          @"research",           @"templates\researchref.cshtml" 

        @"oldblog",                  @"",                   @"templates\mydocpage.cshtml" 
    
    ]

// Build website from `md` files
let buildSite() =
  for sourceDir, outputDir, template in contentDirectories do
    Literate.ProcessDirectory
      ( sourceDir, 
        relative template, 
        output @@ outputDir,
        replacements = projInfo,
        layoutRoots = layoutRootsAll,
        generateAnchors = true,
        processRecursive = true,
        includeSource = false,
        lineNumbers = false
        )

let watch () =
  printfn "Starting watching by initial building..."
  let rebuildDocs () =
    CleanDir output // Just in case the template changed (buildDocumentation is caching internally, maybe we should remove that)
    copyFiles()
    buildSite()
  rebuildDocs()
  printfn "Watching for changes..."

  let full s = Path.GetFullPath s
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
            rebuildDocs()
            printfn "Documentation generation finished."
        with e ->
          printfn "Documentation generation failed: %O" e
    }

  let contentDirs = contentDirectories
                    |> Seq.map (fun (dir, _, _) -> dir)
                    |> Seq.collect subdirsRecurse
                    |> Seq.map (fun d -> relative d + "/*.*" )
                    |> Seq.toList

  let baseContent = !! (full content + "/*.*")
  use watcher =
    (List.fold (++) baseContent contentDirs)
    ++ (full templates + "/*.*")
    ++ (full staticFiles + "/*.*")
    |> WatchChanges (fun changes ->
      changes |> Seq.iter queue.Enqueue)
  use source = new System.Threading.CancellationTokenSource()
  Async.Start(processTask (), source.Token)
  printfn "Press enter to exit watching..."
  System.Console.ReadLine() |> ignore
  watcher.Dispose()
  source.Cancel()

watch () 