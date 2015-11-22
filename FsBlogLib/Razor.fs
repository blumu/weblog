namespace FsBlogLib

open System.IO
open RazorEngine
open RazorEngine.Text
open RazorEngine.Templating
open RazorEngine.Configuration

type Razor(layoutsRoot) =
    do
        Global.LayoutsRoot <- Some layoutsRoot
        let config = new TemplateServiceConfiguration()
        config.Namespaces.Add("FsBlogLib") |> ignore
        config.EncodedStringFactory <- new RawStringFactory()
        config.Resolver <- 
          { new ITemplateResolver with
              member x.Resolve name =
                let layoutFile = Path.Combine(layoutsRoot, name + ".cshtml")
                if File.Exists(layoutFile) then File.ReadAllText(layoutFile)
                else failwithf "Could not find template file: %s\nSearching in: %s" name layoutsRoot }
        config.BaseTemplateType <- typedefof<FsBlogLib.TemplateBaseExtensions<_>>
        
        // Cleanup and debug are mutually exclusive
        // See https://antaris.github.io/RazorEngine/#Temporary-files
        config.Debug <- false
        config.DisableTempFileLocking <- true
        config.CachingProvider <- new DefaultCachingProvider((fun _ -> ()))
        
        let templateservice = new TemplateService(config)
        Razor.SetTemplateService(templateservice)
           
    member val Model = obj() with get, set
    member val ViewBag = new DynamicViewBag() with get,set

    member x.ProcessString(sourceContent, originalFileName) = 
      try
        x.ViewBag <- new DynamicViewBag()
        let html = Razor.Parse(sourceContent, x.Model, x.ViewBag, null)
        html
      with e ->
        printfn "Something went wrong: %A" e
        match e with
        | :? TemplateCompilationException as ex -> 
          let csharp = Path.GetTempFileName() + ".cs"
          File.WriteAllText(csharp, ex.SourceCode)
          let msg = sprintf "Processing the file '%s' failed with exception:\n%O\nSource written to: '%s'." originalFileName ex csharp
          failwith msg
        | _ -> reraise()
    
    member x.ProcessFile(source) = 
        x.ProcessString(File.ReadAllText(source), source)

    static member SwitchAppDomain () =
      if System.AppDomain.CurrentDomain.IsDefaultAppDomain() then
        // RazorEngine cannot clean up from the default appdomain...
        printfn "Switching to secound AppDomain, for RazorEngine..."
        let adSetup = new System.AppDomainSetup()
        adSetup.ApplicationBase <- System.AppDomain.CurrentDomain.SetupInformation.ApplicationBase
        let current = System.AppDomain.CurrentDomain
        // You only need to add strongnames when your appdomain is not a full trust environment.
        //let strongNames = new System.StrongName.[0]

        let domain = System.AppDomain.CreateDomain(
                            "MyMainDomain", null,
                            current.SetupInformation
                            //, new System.Security.PermissionSet(PermissionState.Unrestricted)
                            //,strongNames
                            )
        let exitCode = domain.ExecuteAssembly(System.Reflection.Assembly.GetExecutingAssembly().Location)
        // RazorEngine will cleanup.
        System.AppDomain.Unload(domain)
        exitCode
      else
        0