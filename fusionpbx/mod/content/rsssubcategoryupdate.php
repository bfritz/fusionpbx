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
require_once "config.php";
if (permission_exists('content_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (count($_POST)>0) {
	$rss_sub_category_id = check_str($_POST["rss_sub_category_id"]);
	$rss_category = check_str($_POST["rss_category"]);
	$rss_sub_category = check_str($_POST["rss_sub_category"]);
	$rss_sub_category_desc = check_str($_POST["rss_sub_category_desc"]);
	$rss_add_user = check_str($_POST["rss_add_user"]);
	$rss_add_date = check_str($_POST["rss_add_date"]);

	//sql update
	$sql  = "update v_rss_sub_category set ";
	$sql .= "rss_category = '$rss_category', ";
	$sql .= "rss_sub_category = '$rss_sub_category', ";
	$sql .= "rss_sub_category_desc = '$rss_sub_category_desc', ";
	$sql .= "rss_add_user = '$rss_add_user', ";
	$sql .= "rss_add_date = '$rss_add_date' ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and rss_sub_category_id = '$rss_sub_category_id' ";
	$count = $db->exec(check_sql($sql));
	//echo "Affected Rows: ".$count;

	//edit: make sure the meta redirect url is correct 
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"5;url=rss_sub_categorylist.php\">\n";
	echo "Update Complete";
	require_once "includes/footer.php";
	return;
}
else {
	//get data from the db
	$rss_sub_category_id = $_GET["rss_sub_category_id"];

	$sql = "";
	$sql .= "select * from v_rss_sub_category ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and rss_sub_category_id = '$rss_sub_category_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$rss_category = $row["rss_category"];
		$rss_sub_category = $row["rss_sub_category"];
		$rss_sub_category_desc = $row["rss_sub_category_desc"];
		$rss_add_user = $row["rss_add_user"];
		$rss_add_date = $row["rss_add_date"];
		break; //limit to 1 row
	}
}

require_once "includes/header.php";
echo "<div align='center'>";
echo "<table border='0' cellpadding='0' cellspacing='2'>\n";

echo "<tr class='border'>\n";
echo "	<td align=\"left\">\n";
echo "      <br>";


echo "<form method='post' action=''>";
echo "<table>";
echo "	<tr>";
echo "		<td>rss_category:</td>";
echo "		<td><input type='text' name='rss_category' value='$rss_category'></td>";
echo "	</tr>";
echo "	<tr>";
echo "		<td>rss_sub_category:</td>";
echo "		<td><input type='text' name='rss_sub_category' value='$rss_sub_category'></td>";
echo "	</tr>";
echo "	<tr>";
echo "		<td>rss_sub_category_desc:</td>";
echo "		<td><input type='text' name='rss_sub_category_desc' value='$rss_sub_category_desc'></td>";
echo "	</tr>";
echo "	<tr>";
echo "		<td>rss_add_user:</td>";
echo "		<td><input type='text' name='rss_add_user' value='$rss_add_user'></td>";
echo "	</tr>";
echo "	<tr>";
echo "		<td>rss_add_date:</td>";
echo "		<td><input type='text' name='rss_add_date' value='$rss_add_date'></td>";
echo "	</tr>";
echo "	<tr>";
echo "		<td colspan='2' align='right'>";
echo "     <input type='hidden' name='rss_sub_category_id' value='$rss_sub_category_id'>";
echo "     <input type='submit' name='submit' value='Update'>";
echo "		</td>";
echo "	</tr>";
echo "</table>";
echo "</form>";


echo "	</td>";
echo "	</tr>";
echo "</table>";
echo "</div>";


  require_once "includes/footer.php";
?>
