# Import blog posts from simplephpblog (http://sourceforge.net/projects/sphpblog/)
# to WordPress (https://wordpress.com/)
# The wordpress WXR format is documented here: http://devtidbits.com/2011/03/16/the-wordpress-extended-rss-wxr-exportimport-xml-document-format-decoded-and-explained/
#
# By William Blum, Oct 22 2015
param(
    $blogContentDir ='C:\Users\wiblum\OneDrive\dev\website\blog\content',
    $outputFile = 'C:\Users\wiblum\OneDrive\dev\simplephpblob_tools\myblog-wxr.xml',
    $baseUrl = 'http://william.famille-blum.org/blog/'
)


$7zip = Get-Command "7z.exe" -ErrorAction SilentlyContinue
if(-not $7zip) {
    $7zip = get-command "c:\Program Files (x86)\7-Zip\7z.exe"
}

function convertBody ($content) {
    # TODO: add support of other tags
    $content -replace '\[h5\]','<strong>' `
             -replace '\[/h5\]','</strong>' `
             -replace '\[h4\]','<strong>' `
             -replace '\[/h4\]','</strong>' `
             -replace '\[b\]','<strong>' `
             -replace '\[/b\]','</strong>' `
             -replace '\[code\]','<code>' `
             -replace '\[/code\]','</code>' `
             -replace '\[url=([^\]]*)\]([^[]*)\[/url\]','<a href="$1">$2</a>' `
              -replace '\[img=([^\]]*)\]','<img src="$1"/>'
}

$postId = 0;

function genWxrComment($commentId, $comment) {
    $date = ($comment.Date).ToString('yyyy-MM-dd HH:mm:ss')

    return @"
        <wp:comment>
			<wp:comment_id>$commentId</wp:comment_id>
			<wp:comment_author><![CDATA[$($comment.Author)]]></wp:comment_author>
			<wp:comment_author_email>$($comment.Email)</wp:comment_author_email>
			<wp:comment_author_url>$($comment.Url)</wp:comment_author_url>
			<wp:comment_author_IP>$($comment.Ip)</wp:comment_author_IP>
			<wp:comment_date>$date</wp:comment_date>
			<wp:comment_date_gmt>$date</wp:comment_date_gmt>
			<wp:comment_content><![CDATA[$($comment.Content)]]></wp:comment_content>
			<wp:comment_approved>1</wp:comment_approved>
			<wp:comment_type></wp:comment_type>
			<wp:comment_parent>0</wp:comment_parent>
			<wp:comment_user_id>0</wp:comment_user_id>
		</wp:comment>

"@
}

function genWxrEntry ($entry) {
    $Script:postId++;
    $date = $entry.Date.ToString('yyyy-MM-dd HH:mm:ss')
    $commentId = 0
    $comments = $entry.Comments |% { $commentId++; genWxrComment $commentId $_  }
    return @"
<item>
		<title>$($entry.Title)</title>
		<link>$($entry.Url)</link>
		<pubDate>Wed, 21 Oct 2015 17:53:16 +0000</pubDate>
		<dc:creator><![CDATA[$($entry.CreatedBy)]]></dc:creator>
		<guid isPermaLink="true">$($entry.Url)</guid>
		<description></description>
		<content:encoded><![CDATA[$($entry.Content)]]></content:encoded>
		<excerpt:encoded><![CDATA[]]></excerpt:encoded>
		<wp:post_id>$postId</wp:post_id>
		<wp:post_date>$date</wp:post_date>
		<wp:post_date_gmt>$date</wp:post_date_gmt>
		<wp:comment_status>open</wp:comment_status>
		<wp:ping_status>open</wp:ping_status>
		<wp:post_name>$($entry.Name)</wp:post_name>
		<wp:status>publish</wp:status>
		<wp:post_parent>0</wp:post_parent>
		<wp:menu_order>0</wp:menu_order>
		<wp:post_type>page</wp:post_type>
		<wp:post_password></wp:post_password>
		<wp:is_sticky>0</wp:is_sticky>
		<category domain="category" nicename="uncategorized"><![CDATA[Uncategorized]]></category>
        $comments
	</item>
"@
}

function fromEpoch ($epochTimeInSeconds) {
    return [DateTimeOffset]::FromUnixTimeseconds($epochTimeInSeconds)
}

function get-compressedContent ($file) {
   if([System.IO.Path]::GetExtension($file) -eq '.gz') {
        Write-Host "Compressed entry $file"
        pushd $_.DirectoryName | Out-Null
        & $7zip  e -y $file | Out-Null
        popd
        $uncompressedFile = "$($file.DirectoryName)\$($file.BaseName)"
        $content = gc $uncompressedFile -Raw
        rm $uncompressedFile
        return @{
            content = $content
            name = [System.IO.Path]::GetFileNameWithoutExtension($uncompressedFile)
        }
    }
    else {
        return @{
            content = gc $file -Raw
            name = [System.IO.Path]::GetFileNameWithoutExtension($file)
        }
    }
}

[System.IO.Path]::GetFileName($x)
function get-Comments ($dir) {

    $comments = gci -Recurse "$dir" -Filter "comment*.gz"
    Write-host "Total comments:" $comments.Count
    return $comments |% `
        {
            $file = $_
            $c = get-compressedContent $file
            $comment = $c.content
            $baseName = $c.name
            $month = $file.Directory.Parent.Parent.Name
            $year = $file.Directory.Parent.Parent.Parent.Name

            if($comment -match "(?s)^VERSION\|(.*)\|NAME\|(.*)\|DATE\|(.*)\|CONTENT\|(.*)\|EMAIL\|(.*)\|IP-ADDRESS\|(.*)\|MODERATIONFLAG\|(.*)") {
                @{
                    Version = $Matches[1]
                    Author = $Matches[2]
                    Date = (fromEpoch $Matches[3])
                    Content = (convertBody $Matches[4])
                    Email = $Matches[5]
                    Ip = $Matches[6]
                    Url = "$($baseUrl)comments.php?y=$year&amp;m=$month&amp;entry=$baseName"
                }
            } elseif ($comment -match "(?s)^VERSION\|(.*)\|NAME\|(.*)\|DATE\|(.*)\|CONTENT\|(.*)\|IP-ADDRESS\|(.*)\|MODERATIONFLAG\|") {
                @{
                    Version = $Matches[1]
                    Author = $Matches[2]
                    Date = (fromEpoch $Matches[3])
                    Content = (convertBody $Matches[4])
                    Email = 'noemail'
                    Ip = $Matches[5]
                    Url = "$($baseUrl)comments.php?y=$year&amp;m=$month&amp;entry=$baseName"
                }
            } else {
                Write-Warning "Cannot parse comment $_"
            }
        }
}

function convert ($entryFile)
{

    $e = get-compressedContent $entryFile
    $post = $e.content
    $name = $e.name
    $dir = Split-Path $entryFile

    if($post -match "(?s)^VERSION\|(.*)\|SUBJECT\|(.*)\|CONTENT\|(.*)\|IP-ADDRESS\|(.*)\|DATE\|(.*)\|CREATEDBY\|(.*)") {
        $entry = @{
            Version = $Matches[1]
            Title = $Matches[2]
            Content = (convertBody $Matches[3])
            Ip = $Matches[4]
            Date = (fromEpoch $Matches[5])
            CreatedBy = $Matches[6]
            Url = "$($baseUrl)index.php?entry=$name"
            Name = $name
            Comments = get-Comments $dir
        }
    } elseif($post -match "(?s)^VERSION\|(.*)\|SUBJECT\|(.*)\|CONTENT\|(.*)\|IP-ADDRESS\|(.*)\|CREATEDBY\|(.*)\|DATE\|(.*)") {
        $entry = @{
            Version = $Matches[1]
            Title = $Matches[2]
            Content = (convertBody $Matches[3])
            Ip = $Matches[4]
            CreatedBy = $Matches[5]
            Date = (fromEpoch $Matches[6])
            Url = "$($baseUrl)index.php?entry=$name"
            Name = $name
            Comments = get-Comments $dir
        }
    } else {
        Write-Warning "Cannot parse entry $entryFile. Skipping $post"
        return
    }

    return $entry
}

$posts = gci $blogContentDir\entry*.* -Recurse

$entries = $posts |% { convert $_ } |% { genWxrEntry $_ }

$output =
@"
<?xml version="1.0" encoding="UTF-8" ?>
<!-- This is a WordPress eXtended RSS file generated by WordPress as an export of your site. -->
<!-- It contains information about your site's posts, pages, comments, categories, and other content. -->
<!-- You may use this file to transfer that content from one site to another. -->
<!-- This file is not intended to serve as a complete backup of your site. -->

<!-- To import this information into a WordPress site follow these steps: -->
<!-- 1. Log in to that site as an administrator. -->
<!-- 2. Go to Tools: Import in the WordPress admin panel. -->
<!-- 3. Install the "WordPress" importer from the list. -->
<!-- 4. Activate & Run Importer. -->
<!-- 5. Upload this file using the form provided on that page. -->
<!-- 6. You will first be asked to map the authors in this export file to users -->
<!--    on the site. For each author, you may choose to map to an -->
<!--    existing user on the site or to create a new user. -->
<!-- 7. WordPress will then import each of the posts, pages, comments, categories, etc. -->
<!--    contained in this file into your site. -->

<!-- generator="simplephpblobToWxr/0.0.1" created="2015-10-22 08:16" -->
<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.2/"
>

<channel>
	<title>My blog</title>
	<link>http://wbwordpress.azurewebsites.net</link>
	<description>Just another WordPress site</description>
	<pubDate>Thu, 22 Oct 2015 08:16:49 +0000</pubDate>
	<language>en-US</language>
	<wp:wxr_version>1.2</wp:wxr_version>
	<wp:base_site_url>http://wbwordpress.azurewebsites.net</wp:base_site_url>
	<wp:base_blog_url>http://wbwordpress.azurewebsites.net</wp:base_blog_url>

	<wp:author><wp:author_id>1</wp:author_id><wp:author_login>william</wp:author_login><wp:author_email>william.blum@gmail.com</wp:author_email><wp:author_display_name><![CDATA[william]]></wp:author_display_name><wp:author_first_name><![CDATA[]]></wp:author_first_name><wp:author_last_name><![CDATA[]]></wp:author_last_name></wp:author>

	<wp:category><wp:term_id>1</wp:term_id><wp:category_nicename>uncategorized</wp:category_nicename><wp:category_parent></wp:category_parent><wp:cat_name><![CDATA[Uncategorized]]></wp:cat_name></wp:category>

	<generator>https://www.famille-blum.org/?v=0.0.1</generator>

    $entries
</channel>
</rss>
"@

$output | Set-Content $outputFile  -Encoding UTF8
