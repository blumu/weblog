<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
	</script>
    <script type="text/javascript">
	_uacct = "UA-77730-1";
    urchinTracker();
    </script>
    <title> William Blum's website </title>
    <link href="perso.css" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
    <link rel="icon" type="image/png" href="favicon.png" />
  </head>

<body id="threecolumn">
<div id="container">
	<?php
		$cursection="home";
		$cursubsection="";
		include("common/arround.html");
	?>
	<div id="main-content">
<?php
  if (in_array('page',array_keys($_GET)) )
    $page = $_GET['page'] ;
  else 
    $page = "";
  
  if (in_array('mode',array_keys($_GET)) )
    $mode = $_GET['mode'] ;
  else 
    $mode = "frame";
    

  $url="";
  $base="/"; 
  if( file_exists($page) )
    $url = $page;
  elseif( file_exists($page.".html") )
	$url = $base.$page.".html";
  elseif( file_exists($page."index.html" ) )
	$url = $base.$page."index.html";
  
  if($url=="")
    echo 'The requested page ('.$page.') does not exist!';
  else {
    if($mode=="include")
        include($url);
    else // ($mode=="frame")
        echo 
  '<center><iframe src="'.$url.'" width="90%" height="800" frameborder="0" >
<a href="'.$url.'">Hmm, you are using a very old browser.
Click here to go directly to included content.</a>
</iframe></center>';
  }
?> 
	</div>
	<?php include("common/footer.html"); ?>
</div>
</body>
</html>
