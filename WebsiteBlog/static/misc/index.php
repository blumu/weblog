
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>

	<head>
		<title>Page de William Blum</title>
		<meta name="vs_showGrid" content="False">
	</head>
	<body>
	

		<P><FONT face="Verdana" size="4"><STRONG>Miscellaneous files</STRONG></FONT></P>
		<P><FONT face="Verdana"><STRONG>
<?php
$dir = ".";

if (is_dir($dir)) {
   if ($dh = opendir($dir)) {
       while( (($file = readdir($dh)) !== false)  ){
        if( ($file!=".") && ($file!="index.php") )
           print "<a href=\"$file\">$file</a><BR>"."\n";
       }
   closedir($dh);
   }
}
?> 

		</body>
</html>
