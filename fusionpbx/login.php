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

$path = check_str($_GET["path"]);
$msg = check_str($_GET["msg"]);

include "includes/header.php";
echo "<br><br>";
echo "<div align='center'>";
if (strlen($msg) > 0) {
	echo "<div align='center'>\n";
	echo "<table width='50%'>\n";
	echo "<tr>\n";
	echo "<th align='left'>Message</th>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td class='rowstyle1'><strong>$msg</strong></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>\n";
	echo "<br /><br />\n\n";
}

echo "<form name='login' METHOD=\"POST\" action=\"".PROJECT_PATH."/index.php\">\n";
echo "<table width='200' border='0'>\n";
echo "<tr>\n";
echo "<td align='left'>\n";
echo "	<strong>UserName:</strong>\n";
echo "</td>\n";
echo "<td>\n";
echo "  <input type=\"text\" style='width: 125px;' class='formfld' name=\"username\">\n";
echo "</td>\n";
echo "</tr>\n";
echo "\n";
echo "<tr>\n";
echo "<td align='left'>\n";
echo "	<strong>Password:</strong>\n";
echo "</td>\n";
echo "\n";
echo "<td align='left'>\n";
echo "	<input type=\"password\" style='width: 125px;' class='formfld' name=\"password\">\n";
echo "</td>\n";
echo "</tr>\n";
echo "\n";
echo "<tr>\n";
echo "<td>\n";
echo "</td>\n";
echo "<td align=\"right\">\n";
echo "  <input type=\"submit\" class='btn' value=\"Login\">\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</form>";

//if (strlen($msg) == 0) {
//    echo "<br><a href='loginpasswordchange.php'>Change Password</a>";
//}
//else {
//    echo "<br><a href='loginpasswordforgot.php'>Forgot Password</a>";
//}

echo "</div>";

//echo "<br><br>";


include "includes/footer.php";
?>
