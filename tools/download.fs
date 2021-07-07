module FsBlog.DownloadTable

let readableFileSize (size:int64) : string =
    let units = [| "B"; "kB"; "MB"; "GB"; "TB"; "PB"; "EB"; "ZB"; "YB" |]
    let mutable i :int = 0
    let mutable selectedUnit = units.[0]
    let mutable size = size
    while (size > int64 1024) && (i < units.Length-1) do
        size <- size / int64 1024
        i <- i + 1
        selectedUnit <- units.[i]
    
    System.String.Format("{0} {1}", size, selectedUnit)

module Filters = 

    let downloadtableFromCsv(csvFilePath:string) : string =
        let downloadTableCsv : string[] = System.IO.File.ReadAllLines(csvFilePath)
        let dir  = System.IO.Path.GetDirectoryName(csvFilePath)
        let mutable content = "<tr><th width=\"120\" scope=\"col\">Filename</th><th width=\"160\" scope=\"col\">Date</th><th width=\"50\" scope=\"col\">Size</th><th>Description</th></tr>"
        let mutable alt = false
        for ligneText in downloadTableCsv do
            let ligne = ligneText.Split(',') |> Seq.map (fun x -> x.Replace("\"","")) |> Seq.toArray
            let name = ligne.[0]
            let filePath = dir + @"/downloads/" + name
            let fsize, link =
                if System.IO.File.Exists(filePath) then
                    let fsize = readableFileSize(System.IO.FileInfo(filePath).Length)
                    let link = "<a href=\"downloads/" + name + "\">" + name + "</a>"
                    if ligne.[1] = "" then
                        fsize, link
                    else 
                        fsize, (link + "<br/><a href=\"downloads/" + ligne.[1] + "\">" + ligne.[1] + "</a>";)
                else
                    "missing", name
            
            let className = if alt then "filenamealt" else "filename"
            content <- content +
                "<tr>" +
                "<th class=\"@className\" scope=\"row\">" + link + "</th>" +
                "<td class=\"alt\">" + ligne.[2] + "</td>" +
                "<td class=\"alt\">" + fsize + "</td>" +
                "<td class=\"alt\">" + ligne.[3] + "</td>" +
                "</tr>"
            alt <- not alt
        content
