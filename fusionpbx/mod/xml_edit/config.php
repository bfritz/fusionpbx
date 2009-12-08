<?php
/* $Id$ */
/*
	config.php
	Copyright (C) 2008, 2009 Mark J Crane
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/

error_reporting (E_ALL ^ E_NOTICE);
ob_start("ob_gzhandler");

$applicationname = 'Edit';
$bodyoptions = "";


$dbfilename = "clip.db";
$dbfilepath = PROJECT_PATH."/xml_edit/";

//$temp = $_ENV["TEMP"]."\\";
if (is_writable($dbfilepath.$dbfilename)) { //is writable
	//use database in current location
	echo "yes";
}
else { //not writable
    /*
    //running from a non writable location so copy to temp directory
    if (file_exists($temp.$dbfilename)) {
       $dbfilepath = $temp; //file already exists use existing file
    }
    else { //file doese not exist
        //copy the file to the temp dir
        if (copy($dbfilepath.$dbfilename, $temp.$dbfilename)) {
           //echo "copy succeeded.\n";
           $dbfilepath = $temp;
        }
        else {
            echo "Copy Failed ";
            exit;
        }
    }
    */
}

function get_string_between($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}

//$fullstring = "this is my [tag]dog[/tag]";
//$parsed = get_string_between($fullstring, "[tag]", "[/tag]");


//database connection
try {
    //$db = new PDO('sqlite2:example.db'); //sqlite 2
    //$db = new PDO('sqlite::memory:'); //sqlite 3
    if (!function_exists('phpmd5')) {
      function phpmd5($string) {
          return md5($string);
      }
    }
    if (!function_exists('phpmd5')) {
      function phpunix_timestamp($string) {
          return strtotime($string);
      }
    }
    if (!function_exists('phpnow')) {
      function phpnow() {
          return date('r');
      }
    }

    if (!function_exists('phpleft')) {
      function phpleft($string, $num) {
          return substr($string, 0, $num);
      }
    }

    if (!function_exists('phpright')) {
      function phpright($string, $num) {
          return substr($string, (strlen($string)-$num), strlen($string));
      }
    }

    if (!function_exists('phpsqlitedatatype')) {
      function phpsqlitedatatype($string, $field) {

          //--- Begin: Get String Between start and end characters -----
          $start = '(';
          $end = ')';
          $ini = stripos($string,$start);
          if ($ini == 0) return "";
          $ini += strlen($start);
          $len = stripos($string,$end,$ini) - $ini;
          $string = substr($string,$ini,$len);
          //--- End: Get String Between start and end characters -----

          $strdatatype = '';
          $stringarray = split (',', $string);
          foreach($stringarray as $lnvalue) {

              //$strdatatype .= "-- ".$lnvalue ." ".strlen($lnvalue)." delim ".strrchr($lnvalue, " ")."---<br>";
              //$delimpos = stripos($lnvalue, " ");
              //$strdatatype .= substr($value,$delimpos,strlen($value))." --<br>";

              $fieldlistarray = split (" ", $value);
              //$strdatatype .= $value ."<br>";
              //$strdatatype .= $fieldlistarray[0] ."<br>";
              //echo $fieldarray[0]."<br>\n";
              if ($fieldarray[0] == $field) {
                  //$strdatatype = $fieldarray[1]." ".$fieldarray[2]." ".$fieldarray[3]." ".$fieldarray[4]; //strdatatype
              }
              unset($fieldarray, $string, $field);
          }

          //$strdatatype = $string;
          return $strdatatype;
      }
    } //end function

/*
    $db = new PDO('sqlite:'.$dbfilepath.$dbfilename); //sqlite 3
    //bool PDO::sqliteCreateFunction ( string function_name, callback callback [, int num_args] )
    $db->sqliteCreateFunction('md5', 'phpmd5', 1);
    //$db->sqliteCreateFunction('unix_timestamp', 'phpunix_timestamp', 1);
    $db->sqliteCreateFunction('now', 'phpnow', 0);
    $db->sqliteCreateFunction('sqlitedatatype', 'phpsqlitedatatype', 2);
    $db->sqliteCreateFunction('strleft', 'phpleft', 2);
    $db->sqliteCreateFunction('strright', 'phpright', 2);
*/
}
catch (PDOException $error) {
   print "error: " . $error->getMessage() . "<br/>";
   die();
}

if(!function_exists('escapejs')){
  function escapejs($strtemp) {
      $strtemp = str_replace ("\"", "\\\"", $strtemp); //escape the single quote
      //$strtemp = str_replace ("'", "''", $strtemp); //escape the single quote
  	return $strtemp;
  }
}


if(!function_exists('checkstr')){
  function checkstr($strtemp) {
      //$strtemp = str_replace ("\$", "\\\$", $strtemp); //escape the single quote
      //$strtemp = str_replace ("\'", "''", $strtemp); //escape the single quote
      $strtemp = str_replace ("'", "''", $strtemp); //escape the single quote
      //echo "strtemp $strtemp";
  	return $strtemp;
  }
}

?>
