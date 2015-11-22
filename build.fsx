// --------------------------------------------------------------------------------------
// FAKE build script
// --------------------------------------------------------------------------------------

#r @"packages/FAKE/tools/FakeLib.dll"
open Fake

#load "generate.fsx"

let defaultOutputDir = __SOURCE_DIRECTORY__ + "/wwwroot"

Target "Run" (fun _ ->
    Generate.watch defaultOutputDir true
)

Target "Generate" (fun _ ->
    Generate.rebuildSite defaultOutputDir true
)

Target "AzureDeploy" (fun _ ->
    // See https://github.com/projectkudu/kudu/wiki/Custom-Deployment-Script
    Generate.rebuildSite (__SOURCE_DIRECTORY__ + "/../wwwroot") true
)

RunTargetOrDefault "Run"