let entryid = fsi.CommandLineArgs.[1]
let date = System.DateTimeOffset.Now.ToString("O")

printfn
    """
@{
Layout = "blogpost";
Title = "";
Tags = "";
Date = "%s";
Description = "%s";
EntryId = "";
}
""" date entryid
