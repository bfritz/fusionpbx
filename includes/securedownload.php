<?php
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";

//clears if file exists cache
clearstatcache();


function getDownloadFilename($strfile) {
	// Get download file name and path
	//$basedir = "c:\\products\\";
    //$basedir = "/home/wwwbeta/secure/files/";
		$basedir = "c:/www/demo.netprofx.com/secure/files/";
	// Build and return download file name
	return $basedir . $strfile;
}

function DownloadFile($filename) {
	// Check filename
	if (empty($filename) || !file_exists($filename)) {
        echo "Error: file doesn't exist or is empty. <br>\n $filename";
		return FALSE;
	}

    $file_extension = strtolower(substr(strrchr($filename,"."),1));
     switch ($file_extension) {
         case "pdf": $ctype="application/pdf"; break;
         case "exe": $ctype="application/octet-stream"; break;
         case "zip": $ctype="application/zip"; break;
         case "doc": $ctype="application/msword"; break;
         case "xls": $ctype="application/vnd.ms-excel"; break;
         case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
         case "gif": $ctype="image/gif"; break;
         case "png": $ctype="image/png"; break;
         case "jpe": case "jpeg":
         case "jpg": $ctype="image/jpg"; break;
         default: $ctype="application/force-download";
     }

     //if (!file_exists($filename)) {
     //    die("NO FILE HERE<br>$filename");
     //}

	// Create download file name to be displayed to user
	$saveasname = basename($filename);

    header("Expires: 0");
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
    header("Content-Type: $ctype");
    header("Content-Disposition: attachment; filename=\"".basename($filename)."\";");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".@filesize($filename));

    set_time_limit(0);
    @readfile($filename) or die("File not found.");

    // Done
	return TRUE;
}



?>
