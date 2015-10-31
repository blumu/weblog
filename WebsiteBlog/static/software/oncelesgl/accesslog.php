<?php
if (empty($REMOTE_ADDR)) $userip="inconnue";
else $userip= $REMOTE_ADDR;

$str = date("Y/m/d h:i:s", mktime()) . "," .
	   $_SERVER['REQUEST_URI'] . "," .
	   $userip . "," .
	   $_SERVER['HTTP_USER_AGENT'] . "\n";
	
error_log($str, 3, "access.log");
?>
