// --------------------------------------------------------------------------------------
// FAKE build script
// --------------------------------------------------------------------------------------

#r @"packages/FAKE/tools/FakeLib.dll"
open Fake


Target "FsBlogLib" (fun _ ->
    !! "**/FsBlogLib.fsproj"
        |> MSBuildDebug ".build/FsBlogLib" "Build"
        |> Log "Build-output: ")

RunTargetOrDefault "FsBlogLib"