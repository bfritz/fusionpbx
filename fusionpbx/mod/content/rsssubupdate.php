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

$rss_uuid = $_GET["rss_uuid"];

if (count($_POST)>0 && $_POST["persistform"] == "0") {
	$rss_sub_uuid = check_str($_POST["rss_sub_uuid"]);
	$rss_uuid = check_str($_POST["rss_uuid"]);
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

	$msg = '';
	if (strlen($rss_uuid) == 0) { $msg .= "Error missing rss_uuid.<br>\n"; }
	if (strlen($rss_sub_uuid) == 0) { $msg .= "Error missing rss_sub_uuid.<br>\n"; }
	//if (strlen($rss_sub_title) == 0) { $msg .= "Please provide a title.<br>\n"; }
	if (strlen($rss_sub_desc) == 0) { $msg .= "Please provide a description.<br>\n"; }

	if (strlen($msg) > 0) {
		require_once "includes/persistform.php";
		require_once "includes/header.php";
		echo "<div align='center' style='' >";
		echo "<table>";
		echo "<tr>";
		echo "<td>";
		echo "  <div class='borderlight' align='left' style='padding:10px;'>";
		echo "      $msg";
		echo "      <br>";
		echo "      <div align='center'>".persistform($_POST)."</div>";
		echo "  </div>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";

		require_once "includes/footer.php";
		return;
	}

	//sql update
	$sql  = "update v_rss_sub set ";
	//$sql .= "rss_uuid = '$rss_uuid', ";
	$sql .= "rss_sub_title = '$rss_sub_title', ";
	$sql .= "rss_sub_link = '$rss_sub_link', ";
	$sql .= "rss_sub_desc = '$rss_sub_desc', ";
	$sql .= "rss_sub_optional_1 = '$rss_sub_optional_1', ";
	$sql .= "rss_sub_optional_2 = '$rss_sub_optional_2', ";
	$sql .= "rss_sub_optional_3 = '$rss_sub_optional_3', ";
	$sql .= "rss_sub_optional_4 = '$rss_sub_optional_4', ";
	$sql .= "rss_sub_optional_5 = '$rss_sub_optional_5' ";
	//$sql .= "rss_sub_add_date = now(), ";
	//$sql .= "rss_sub_add_user = '".$_SESSION["username"]."' ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and rss_sub_uuid = '$rss_sub_uuid' ";
	//$sql .= "and rss_uuid = '$rss_uuid' ";
	$count = $db->exec(check_sql($sql));
	//echo "Affected Rows: ".$count;

	//edit: make sure the meta redirect url is correct 
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=rsssublist.php?rss_uuid=$rss_uuid&rss_sub_uuid=$rss_sub_uuid\">\n";
	echo "<div align='center'>";
	echo "Update Complete";
	echo "</div>";
	require_once "includes/footer.php";
	return;
}
else {
	//get data from the db
	$rss_sub_uuid = $_GET["rss_sub_uuid"];

	$sql = "";
	$sql .= "select * from v_rss_sub ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and rss_sub_uuid = '$rss_sub_uuid' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		//$rss_uuid = $row["rss_uuid"];
		$rss_sub_title = $row["rss_sub_title"];
		$rss_sub_link = $row["rss_sub_link"];
		$rss_sub_desc = $row["rss_sub_desc"];
		$rss_sub_optional_1 = $row["rss_sub_optional_1"];
		$rss_sub_optional_2 = $row["rss_sub_optional_2"];
		$rss_sub_optional_3 = $row["rss_sub_optional_3"];
		$rss_sub_optional_4 = $row["rss_sub_optional_4"];
		$rss_sub_optional_5 = $row["rss_sub_optional_5"];
		$rss_sub_add_date = $row["rss_sub_add_date"];
		$rss_sub_add_user = $row["rss_sub_add_user"];
		break; //limit to 1 row
	}
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
	//echo "	<tr>";
	//echo "		<td>rss_uuid:</td>";
	//echo "		<td><input type='text' name='rss_uuid' class='txt' value='$rss_uuid'></td>";
	//echo "	</tr>";
	echo "	<tr>";
	echo "		<td nowrap>Sub Title:</td>";
	echo "		<td width='100%'><input type='text' name='rss_sub_title' class='txt' value='$rss_sub_title'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td>Sub Link:</td>";
	echo "		<td><input type='text' name='rss_sub_link' class='txt' value='$rss_sub_link'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td valign='top'>Description:</td>";
	echo "		<td>";
	echo "            <textarea name='rss_sub_desc' rows='12' class='txt'>$rss_sub_desc</textarea>";
	echo "        </td>";
	echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>rss_sub_optional_1:</td>";
	//echo "		<td><input type='text' name='rss_sub_optional_1' value='$rss_sub_optional_1'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>rss_sub_optional_2:</td>";
	//echo "		<td><input type='text' name='rss_sub_optional_2' value='$rss_sub_optional_2'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>rss_sub_optional_3:</td>";
	//echo "		<td><input type='text' name='rss_sub_optional_3' value='$rss_sub_optional_3'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>rss_sub_optional_4:</td>";
	//echo "		<td><input type='text' name='rss_sub_optional_4' value='$rss_sub_optional_4'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>rss_sub_optional_5:</td>";
	//echo "		<td><input type='text' name='rss_sub_optional_5' value='$rss_sub_optional_5'></td>";
	//echo "	</tr>";

	echo "	<tr>";
	echo "		<td colspan='2' align='right'>";
	echo "		    <input type='hidden' name='rss_uuid' value='$rss_uuid'>";
	echo "		    <input type='hidden' name='persistform' value='0'>";
	echo "          <input type='hidden' name='rss_sub_uuid' value='$rss_sub_uuid'>";
	echo "          <input type='submit' name='submit' class='btn' value='Update'>";
	echo "		</td>";
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
