<?php
/* $Id$ */
/*
	index.php
	Copyright (C) 2008 Mark J Crane
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

if (ifgroup("superadmin")) {
  //access granted
}
else {
  echo "access denied";
  return;
}

require_once "includes/header.php";
//require_once("header.php");

echo "<table width='97%' border='0' cellpadding='0' cellspacing='2'>\n";

echo "<tr class='border'>\n";
echo "	<td align=\"left\">";
//echo "  <br>";

echo "<div align='center'>";
echo "<form method='post' target='frame' action='index2.php' >";
echo "<table border='0'>";

echo "	<tr>\n";
echo "	<td colspan='3' nowrap><b>SQLite Query</b></td>\n";
echo "	</tr>\n";

echo "	<tr>";
echo "	<td colspan='3'><textarea name='frmsql' style='width: 100%;' rows='4'></textarea></td>\n";
echo "	</tr>\n";

echo "	<tr>\n";
echo "		<td width='75%' align='right' valign='top' nowrap><b>Table / View</b> &nbsp; ";

//<input type='text' name='frmtblname' value=''>";
//display a list of all tables
echo "          <select name='frmtblname'>\n";
echo "	        <option value=''></option>\n";
$sql = "SELECT name FROM sqlite_master ";
$sql .= "WHERE type='table' ";
$sql .= "order by type;";
$prepstatement = $db->prepare($sql);
$prepstatement->execute();
while($row = $prepstatement->fetch()) {
    //print_r( $row );
    echo "	        <option value='".$row[name]."'>".$row[name]."</option>\n";
}
echo "	        </select>\n";

echo "      </td>\n";
echo "		<td width='35%' align='right' nowrap>&nbsp; <b>Result</b>&nbsp; \n";
echo "          <select name='frmresulttype'>\n";
echo "	        <option value='html'>html</option>\n";
echo "	        <option value='csv'>csv</option>\n";
echo "	        <option value='sqlinsert'>sql insert</option>\n";
echo "	        <option value='sqlcreatetbl'>sql create tbl</option>\n";
echo "	        <option value='phpcsv'>php to csv</option>\n";
echo "	        <option value='phplist'>php pdo procedural list</option>\n";
echo "	        <option value='phpadd'>php pdo procedural add</option>\n";
echo "	        <option value='phpupdate'>php pdo procedural update</option>\n";
echo "	        <option value='phpsearch'>php pdo procedural search</option>\n";
echo "	        </select>\n";
echo "      </td>\n";
echo "		<td width='15%' align='right' nowrap>&nbsp;<input type='submit' class='btn' value='Execute'></td>\n";
echo "	</tr>\n";
echo "</table>\n";
echo "</form>\n";

//require_once("footer.php");

echo "  <br><br>";
echo "  </td>\n";
echo "</tr>\n";
echo "</table>\n";
//echo "<br>";

echo "<iframe id='frame' width='100%' height='400' FRAMEBORDER='0' name='frame' style='background-color : #FFFFFF;'></iframe>\n";
echo "</div>\n";


require_once "includes/footer.php";



?>
