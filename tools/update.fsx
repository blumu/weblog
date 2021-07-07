/// Build script for my weblog site
/// Adapted by William Blum from F# Formatting example by Tomas Petricek.

#r "System.Xml.Linq.dll"
#r "nuget: Newtonsoft.Json"
#r "nuget: DotLiquid"
#r "nuget: FSharp.Formatting"

#load "domain.fs"
#load "helpers.fs"
#load "dotliquid.fs"
#load "document.fs"
#load "download.fs"
#load "blog.fs"
open System
open System.IO
open FsBlog

printfn "%A" (Environment.GetCommandLineArgs())
// --------------------------------------------------------------------------------------
// Blog configuration 
// --------------------------------------------------------------------------------------

let (</>) a b = Path.Combine(a, b)
let fullPath p = Path.GetFullPath(__SOURCE_DIRECTORY__ </> p)

let config =
  { // Where the site is hosted (without trailing '/')
    Root = "https://william.famille-blum.org/"
    // Directory with DotLiquid templates  
    Layouts = fullPath "../layouts"

    // Cache and outptu directory (can be outside of the repo)
    Cache = fullPath "../../cache" 
    Output = fullPath "../../output"

    // Files from source are transformed/copied to the output
    // Blog & Academic are also parsed and available in DotLiquid templates
    Source = fullPath "../sources" 
    Blog = fullPath "../sources/blog"
    Academic = fullPath "../sources/academic"
    }

// --------------------------------------------------------------------------------------
// Generating and updating site
// --------------------------------------------------------------------------------------

DotLiquid.initialize config

let loadSite () =
  let posts, papers = Blog.groupArticles config
  let archives = Blog.archives posts
  { Posts = posts; Papers = papers; Archives = archives; PostsTitle = "" }

let mutable site = loadSite ()

/// Update site - generate all output files if they need to be refreshed
/// When `full = true`, also update archives & calendar pages
let updateSite full changes =
  printfn "Updating site"
  printfn "Copying static files"
  Blog.copyFiles config changes
  printfn "Processing site source"
  if Blog.processFiles config site.Archives changes then 
    site <- loadSite()
    
  printfn "Processing special files"
  let specialFiles = 
    [ "404.html", "404.html", site
      "index.html", "index.html", site
      "blog/index.html", "listing.html", 
        { site with Posts = Seq.truncate 20 site.Posts } 
        ]
  for target, layout, model in specialFiles do
    DotLiquid.transform (config.Output </> target) (config.Layouts </> layout) model

  if full then
    printfn "Generating RSS feed"
    Blog.generateRss (config.Output </> "rss.xml") config
      "William Blum"
      ( "A website where I share my interests in compter science, programming and research." )
      site.Posts 
    printfn "Generating archives"
    Blog.generateBlogArchives config site
    Blog.generateTagArchives config site

/// Regenerate site - clean the output folder & regenerate (does not clean cache)
let regenerateSite () = 
  printfn "Regenerating site from scratch"
  for dir in Directory.GetDirectories(config.Output) do
    if not (dir.EndsWith(".git")) then 
      Directory.Delete(dir, true)
  for f in Directory.GetFiles(config.Output) do
    if f <> "README.TXT" then
      File.Delete f
  updateSite true None

/// Run some operation based on command line argument
let mutable cmd = ""
while not (isNull cmd) do
  cmd <- Console.ReadLine()
  if not (isNull cmd) then
    let args = cmd.Split([|' '|], StringSplitOptions.RemoveEmptyEntries) |> List.ofSeq 
    printfn "Running command: %A" args
    try
      match args with
      | ["regenerate"] -> regenerateSite ()
      | ["updateall"] -> updateSite false None
      | "update"::changes -> updateSite false (Some(set changes))
      | _ -> printfn "Unrecognized command"
     with e ->
      eprintf "Exception occured %O" e
    printfn "DONE"