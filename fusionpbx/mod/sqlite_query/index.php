<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2010
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
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
$prepstatement = $db->prepare(check_sql($sql));
$prepstatement->execute();
$result = $prepstatement->fetchAll();
foreach ($result as &$row) {
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
