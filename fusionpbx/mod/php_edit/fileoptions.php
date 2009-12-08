<?php
/* $Id$ */
/*
	fileoptions.php
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
require_once "config.php";
require_once "header.php";


$file = $_GET["file"];
$file = str_replace ("\\", "/", $file);
$folder = $_GET["folder"];
$folder = str_replace ($file, "", $folder);

//$file = end($folder);
//echo "folder $folder<br>";
//echo "file $file<br>";
//echo "DOCUMENT_ROOT "."<br>";

$urlpath = str_replace ($_SERVER["DOCUMENT_ROOT"], "", $folder);
//echo "urlpath $urlpath<br>";

echo "<div align='left'>";
echo "<table border='0' style=\"height: 100%; width: 100%;\">\n";
echo "<tr>";
echo "<td colspan='2'>";

echo "<table border='0'>";
echo "<form method='post' name='frm' action=''>";
echo "<tr><td>Path:</td><td width='95%'><input type='text' name='folder' id='folder' style=\"width: 100%;\" value=''></td><tr>\n";
echo "<tr><td>File:</td><td width='95%' style=\"width: 60%;\"><input type='text' name='filename' id='filename' style=\"width: 100%;\" value=''></div></td></tr>\n";
echo "</form>";
echo "</table>";

echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td valign='top' width='200' nowrap>";

echo "  <IFRAME SRC='fileoptionslist.php' style='border: solid 1px #CCCCCC; height: 100%; width: 100%;' TITLE=''>\n";
echo "  <!-- Clip List: Requires IFRAME support -->\n";
echo "  </IFRAME>";

echo "</td>\n";
echo "<td valign='top' style=\"height: 100%;\">";



echo "<div align='left'>";
echo "<table width='100%' class='border'>";


//echo "  <tr><td><input type='button' class='btn' name='' onclick=\"window.location='$urlpath'+document.getElementById('folder').value;\" value=' www          '></td></tr>\n";

echo "  <tr><td><input type='button' class='btn' name='' onclick=\"window.location='filenew.php?folder='+document.getElementById('folder').value;\" value='Add File'></td></tr>\n";
echo "  <tr><td><input type='button' class='btn' name='' onclick=\"window.location='foldernew.php?folder='+document.getElementById('folder').value;\" value='Add Dir'></td></tr>\n";
echo "  <tr><td><input type='button' class='btn' name='' onclick=\"window.location='filerename.php?folder='+document.getElementById('folder').value+'&filename='+document.getElementById('filename').value;\" value='Rename File'></td></tr>\n";
echo "  <tr><td><input type='button' class='btn' name='' onclick=\"if (confirm('Are you sure you want to delete the selected file?')){ window.location='filedelete.php?folder='+document.getElementById('folder').value+'&file='+document.getElementById('filename').value; }\" value='Delete File'></td></tr>\n";
echo "  <tr><td><input type='button' class='btn' name='' onclick=\"if (confirm('Are you sure you want to delete the selected folder?')){ window.location='folderdelete.php?folder='+document.getElementById('folder').value; }\" value='Delete Dir'></td></tr>\n";
echo "  <tr><td><br><br><br><br><br></td></tr>\n";
echo "  <tr><td><input type='button' class='btn' name='' onclick='javascript:self.close();' value='Close'></td></tr>\n";

echo "</table>";
echo "</div>";




echo "</td>\n";
echo "</tr>\n";
echo "</table>";
echo "</div>";

require_once "footer.php";
?>
