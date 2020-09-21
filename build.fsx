// --------------------------------------------------------------------------------------
// FAKE build script
// --------------------------------------------------------------------------------------

#r @"packages/FAKE/tools/FakeLib.dll"
open Fake

#load "generate.fsx"

let defaultOutputDir = __SOURCE_DIRECTORY__ + "/wwwroot"

/// Run a local web server using the local copy of wwwroot/
Target "Run" (fun _ ->
    Generate.watch defaultOutputDir true
)

/// Rebuild the local copy of wwwroot/
Target "Generate" (fun _ ->
    Generate.rebuildSite defaultOutputDir true None
)

/// Update the local copy of the /wwwroot directory to be later
/// published by git pushing the git repository under wwwroot/ to
/// the Azure website at https://luweiblog.scm.azurewebsites.net:443/luweiblog.git
Target "Stage" (fun _ ->
    Generate.rebuildSite defaultOutputDir true (Some "william.famille-blum.org")

    printfn "ACTION REQUIRED: Push wwwroot/ git repository to Azure to publish the website.
    Steps:
       git clone https://luweiblog.scm.azurewebsites.net:443/luweiblog.git wwwroot
       fake build.fsx stage  # this command
       cd wwwroot; git commit; git push

    See https://github.com/projectkudu/kudu/wiki/Deploying-inplace-and-without-repository
    for instructions on how to perform inplace Git deployment to wwwroot.
       SCM_REPOSITORY_PATH=wwwroot
       SCM_TARGET_PATH=wwwroot"
)

RunTargetOrDefault "Run"