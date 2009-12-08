<?php
include "root.php";
require_once "includes/config.php";
//require_once "includes/checkauth.php";

$file = $_GET["f"];
require_once "includes/securedownload.php";
//echo $file;
DownloadFile($file_dir.$file);

?>

