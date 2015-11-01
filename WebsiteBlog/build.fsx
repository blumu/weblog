/// Given a typical setup (with 'FSharp.Formatting' referenced using NuGet),
// the following will include binaries and load the literate script
#load @"..\packages\FSharp.Formatting\FSharp.Formatting.fsx"
open System.IO
open FSharp.Literate


//#I "../../packages/FAKE/tools/"
//#r "NuGet.Core.dll"
#r "../../packages/FAKE/tools/FakeLib.dll"
open Fake
open System.IO
open Fake
open Fake.FileHelper

/// Return path relative to the current file location
let relative subdir = Path.Combine(__SOURCE_DIRECTORY__, subdir)

let websiteRoot = __SOURCE_DIRECTORY__ @@ "output"

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

let layoutRootsAll =   "templates" :: ( subDirectories (directoryInfo "templates") |> Array.map (fun d -> d.Name) |> Array.toList)

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


// let doc() =
//     let template = relative @"templates\mydocpage.cshtml"
//     Literate.ProcessDirectory (relative "posts", template, replacements = projInfo,
//                                 outputDirectory = relative @"output\", 
//                                 layoutRoots = layoutRootsAll )
// 
// doc()


let subdirs =
    [ 
        relative "posts", relative @"templates\mydocpage.cshtml" 
//        relative @"posts\research", relative @"templates\mydocpage.cshtml" 
    
    ]

// Build documentation from `fsx` and `md` files in `docs/content`
let buildDocumentation () =
  for dir, template in subdirs do
    let sub = "." // Everything goes into the same output directory here
    Literate.ProcessDirectory
      ( dir, template, output @@ sub,
        replacements = projInfo,
        layoutRoots = layoutRootsAll,
        generateAnchors = true,
        processRecursive = true,
        includeSource = false
        )

let watch () =
  printfn "Starting watching by initial building..."
  let rebuildDocs () =
    CleanDir output // Just in case the template changed (buildDocumentation is caching internally, maybe we should remove that)
    copyFiles()
    //buildReference()
    buildDocumentation()
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

  let contentDirs = subdirs |> List.map (fun (subdir,_) -> full subdir + "*/*.*" )
  let baseContent = !! (full content + "*/*.*")
  use watcher =
    (List.fold (++) baseContent contentDirs)
    ++ (full content + "/research/*.*")
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