<?php
include "root.php";
require_once "includes/config.php";

ob_end_clean();
ob_start();

//$header = ''; //disable alternate header
//$strheader = str_replace ("<!--{menu}-->", $_SESSION["menu"], $strheader);

$strheadertop ='';
if (isset($_SERVER['HTTP_USER_AGENT']) && 
(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
	//return true;
	$strheadertop .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
else {
	//return false;
}
//$strheadertop .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\">\n";


?>
