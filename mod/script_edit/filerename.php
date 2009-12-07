<?php
/* $Id$ */
/*
	filename.php
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
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (ifgroup("admin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

$folder = $_GET["folder"];
//$folder = str_replace ("\\", "/", $folder);
//if (substr($folder, -1) != "/") { $folder = $folder.'/'; }
$newfilename = $_GET["newfilename"];
$filename = $_GET["filename"];
//echo $folder.$file;


if (strlen($folder) > 0 && strlen($newfilename) > 0) {
    //echo "new file: ".$newfilename."<br>";
    //echo "folder: ".$folder."<br>";
    //echo "orig filename: ".$filename."<br>";;
    rename($folder.$filename, $folder.$newfilename);
    header("Location: fileoptions.php");
}
else { //display form

    require_once "header.php";
    echo "<br>";
    echo "<div align='left'>";
    echo "<form method='get' action=''>";
    echo "<table>";
    echo "	<tr>";
    echo "		<td>Path:</td>";
    echo "	</tr>";
    echo "	<tr>";
    echo "		<td>".$folder.$filename."</td>";
    echo "	</tr>";
    echo "	<tr>";
    echo "		<td><br></td>";
    echo "	</tr>";
    echo "	<tr>";
    echo "		<td>Original file name:</td>";
    echo "	</tr>";
    echo "	<tr>";
    echo "		<td>".$filename."</td>";
    echo "	</tr>";
    echo "</table>";

    echo "<br />";

    echo "<table>";
    echo "	<tr>";
    echo "	  <td>Rename file to:</td>";
    echo "	</tr>";

    echo "	<tr>";
    echo "		<td><input type='text' name='newfilename' value=''></td>";
    echo "	</tr>";

    echo "	<tr>";
    echo "	  <td colspan='1' align='right'>";
    echo "          <input type='hidden' name='folder' value='$folder'>";
    echo "          <input type='hidden' name='filename' value='$filename'>";
    echo "		    <input type='submit' value='Rename File'>";
    echo "    </td>";
    echo "	</tr>";
    echo "</table>";
    echo "</form>";
    echo "</div>";

    require_once "footer.php";

}


?>
