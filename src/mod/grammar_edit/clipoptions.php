<?php
/* $Id$ */
/*
	clipoptions.php
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


echo "<div align='left'>";
echo "<table border='0' style=\"height: 100%; width: 100%;\">\n";
echo "<form method='post' name='frm' action=''>";
echo "<tr><td colspan='2'><div id='selectedclip'>Selected Clip: <input type='text' name='clipname' id='clipname' value=''><input type='hidden' name='clipid' id='clipid' value=''></div></td></tr>\n";
echo "</form>";
echo "<tr>\n";
echo "<td valign='top' width='200' nowrap>";

echo "  <IFRAME SRC='clipoptionslist.php' style='border: solid 1px #CCCCCC; height: 100%; width: 100%;' WIDTH='100%' TITLE=''>\n";
echo "  <!-- Clip List: Requires IFRAME support -->\n";
echo "  </IFRAME>";

echo "</td>\n";
echo "<td valign='top' style=\"height: 100%;\">";

echo "  <table width='' class='border'>";
//echo "  <tr><td colspan='1'><img src='images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";
//echo "  <tr><th>Options</th></tr>\n";
//echo "  <tr><td colspan='1'><img src='images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";

echo "  <tr><td><input type='button' class='btn' name='' onclick=\"window.location='clipupdate.php?id='+document.getElementById('clipid').value;\" value='Edit Clip'></td></tr>\n";
echo "  <tr><td><input type='button' class='btn' name='' onclick=\"window.location='clipadd.php'\" value='Add Clip'></td></tr>\n";
//echo "  <tr><td><input type='button' class='btn' name='' onclick=\"window.location='clipadd.php'\" value='  Search  '></td></tr>\n";
echo "  <tr><td><input type='button' class='btn' name='' onclick=\"if (confirm('Are you sure you want to delete the selected clip?')){ window.location='clipdelete.php?id='+document.getElementById('clipid').value; }\" value='  Delete   '></td></tr>\n";
echo "  <tr><td><br><br><br><br><br><br><br><br><br><br><br></td></tr>\n";

echo "  <tr><td><input type='button' class='btn' name='' onclick='javascript:self.close();' value='   Close    '></td></tr>\n";


echo "  </table>";

echo "</td>\n";
echo "</tr>\n";
echo "</table>";
echo "</div>";

require_once "footer.php";
?>
