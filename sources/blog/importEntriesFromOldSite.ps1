cd 'C:\Users\wiblum\Documents\perso\newwebsite\WebsiteBlog\oldblog'

$urls = gc 'permalinkgs_to_redirect.txt'

$urls |% { $entryId = [System.IO.Path]::GetFileName($_).Split('=')[1] 
    $tmp = [System.IO.Path]::GetTempFileName()
    wget $_ -OutFile $tmp
    #gc $tmp | Set-Content -Encoding UTF8 -Path $tmp
    $tmpconv = "$tmp-conv"
    & "C:\Program Files\Git\usr\bin\iconv.exe" -f CP1252 -t utf-8 $tmp | Set-Content -Encoding UTF8 $tmpconv
    rm $tmp
    write-host "$_ -> $tmpconv"
    & pandoc -f html -t markdown_github -o "C:\Users\wiblum\Documents\perso\newwebsite\WebsiteBlog\oldblog\$entryId.md" $tmpconv
}

