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
if (permission_exists('content_add')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

$rss_id = $_GET["rss_id"];

if (count($_POST)>0) {
	$rss_id = check_str($_POST["rss_id"]);
	$rss_sub_title = check_str($_POST["rss_sub_title"]);
	$rss_sub_link = check_str($_POST["rss_sub_link"]);
	$rss_sub_desc = check_str($_POST["rss_sub_desc"]);
	$rss_sub_optional_1 = check_str($_POST["rss_sub_optional_1"]);
	$rss_sub_optional_2 = check_str($_POST["rss_sub_optional_2"]);
	$rss_sub_optional_3 = check_str($_POST["rss_sub_optional_3"]);
	$rss_sub_optional_4 = check_str($_POST["rss_sub_optional_4"]);
	$rss_sub_optional_5 = check_str($_POST["rss_sub_optional_5"]);
	$rss_sub_add_date = check_str($_POST["rss_sub_add_date"]);
	$rss_sub_add_user = check_str($_POST["rss_sub_add_user"]);

	$sql = "insert into v_rss_sub ";
	$sql .= "(";
	$sql .= "v_id, ";
	$sql .= "rss_id, ";
	$sql .= "rss_sub_title, ";
	$sql .= "rss_sub_link, ";
	$sql .= "rss_sub_desc, ";
	$sql .= "rss_sub_optional_1, ";
	$sql .= "rss_sub_optional_2, ";
	$sql .= "rss_sub_optional_3, ";
	$sql .= "rss_sub_optional_4, ";
	$sql .= "rss_sub_optional_5, ";
	$sql .= "rss_sub_add_date, ";
	$sql .= "rss_sub_add_user ";
	$sql .= ")";
	$sql .= "values ";
	$sql .= "(";
	$sql .= "'$v_id', ";
	$sql .= "'$rss_id', ";
	$sql .= "'$rss_sub_title', ";
	$sql .= "'$rss_sub_link', ";
	$sql .= "'$rss_sub_desc', ";
	$sql .= "'$rss_sub_optional_1', ";
	$sql .= "'$rss_sub_optional_2', ";
	$sql .= "'$rss_sub_optional_3', ";
	$sql .= "'$rss_sub_optional_4', ";
	$sql .= "'$rss_sub_optional_5', ";
	$sql .= "now(), ";
	$sql .= "'".$_SESSION["username"]."' ";
	$sql .= ")";
	$db->exec(check_sql($sql));
	unset($sql);

	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=rsssublist.php?rss_id=$rss_id\">\n";
	echo "<div align='center'>";
	echo "Add Complete";
	echo "</div>";
	require_once "includes/footer.php";
	return;
}

//show the header
	require_once "includes/header.php";
	require_once "includes/wysiwyg.php";

//show the content
	echo "<div align='center'>";
	echo "<table border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";

	echo "<form method='post' action=''>";
	echo "<table width='100%'>";
	echo "	<tr>";
	echo "		<td nowrap>Title:</td>";
	echo "		<td width='100%'><input type='text' class='txt' name='rss_sub_title'></td>";
	echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Link:</td>";
	//echo "		<td><input type='text' class='txt' name='rss_sub_link'></td>";
	//echo "	</tr>";
	echo "	<tr>";
	echo "		<td valign='top'>Description:</td>";
	echo "        <td>";
	echo "		    <textarea class='txt' rows='12' name='rss_sub_desc'></textarea>";
	echo "        </td>";
	echo "	</tr>";
	/*
	echo "	<tr>";
	echo "		<td>rss_sub_optional_1:</td>";
	echo "		<td><input type='text' name='rss_sub_optional_1'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td>rss_sub_optional_2:</td>";
	echo "		<td><input type='text' name='rss_sub_optional_2'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td>rss_sub_optional_3:</td>";
	echo "		<td><input type='text' name='rss_sub_optional_3'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td>rss_sub_optional_4:</td>";
	echo "		<td><input type='text' name='rss_sub_optional_4'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td>rss_sub_optional_5:</td>";
	echo "		<td><input type='text' name='rss_sub_optional_5'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td>rss_sub_add_date:</td>";
	echo "		<td><input type='text' name='rss_sub_add_date'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td>rss_sub_add_user:</td>";
	echo "		<td><input type='text' name='rss_sub_add_user'></td>";
	echo "	</tr>";
	*/
	//echo "	<tr>";
	//echo "	<td>example:</td>";
	//echo "	<td><textarea name='example'></textarea></td>";
	//echo "	</tr>";    echo "	<tr>";
	echo "		<td colspan='2' align='right'>";
	echo "		    <input type='hidden' name='rss_id' value='$rss_id'>";
	echo "          <input type='submit' name='submit' class='btn' value='Add'>";
	echo "      </td>";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

//show the footer
	require_once "includes/footer.php";
?>