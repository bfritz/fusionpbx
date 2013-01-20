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
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
require_once "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists('ivr_menu_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//additional includes
	require_once "includes/paging.php";

//get the http values and set them as variables
	if (isset($_GET["order_by"])) {
		$order_by = check_str($_GET["order_by"]);
		$order = check_str($_GET["order"]);
	}

//begin content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "		<br>";

	echo "<table width='100%' border='0'>\n";
	echo "<tr>\n";
	echo "<td width='50%' nowrap='nowrap' align='left'><b>IVR Menu Option List</b></td>\n";
	echo "<td width='50%' align='right'>&nbsp;</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan='2' align='left'>\n";
	echo "The recording presents options to the caller. Options match key presses (DTMF digits) from the caller which directs the call to the destinations. <br /><br />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</tr>\n";
	echo "</table>\n";

//get the number of rows in ivr_menu_options
	$sql = "select count(*) as num_rows from v_ivr_menu_options ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and ivr_menu_uuid = '$ivr_menu_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	if ($prep_statement) {
		$prep_statement->execute();
		$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
			if ($row['num_rows'] > 0) {
				$num_rows = $row['num_rows'];
			}
			else {
				$num_rows = '0';
			}
		}
		unset($prep_statement, $result);

//prepare to page the results
	$rows_per_page = 150;
	$param = $_SERVER["QUERY_STRING"];
	if (!isset($_GET['page'])) { $_GET['page'] = 0; }
	$_GET['page'] = check_str($_GET['page']);
	list($paging_controls, $rows_per_page, $var_3) = paging($num_rows, $param, $rows_per_page);
	$offset = $rows_per_page * $_GET['page'];

//get the menu options
	$sql = "select * from v_ivr_menu_options ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and ivr_menu_uuid = '$ivr_menu_uuid' ";
	$sql .= "order by ivr_menu_option_digits, ivr_menu_option_order asc "; 
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	$result_count = count($result);
	unset ($prep_statement, $sql);

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<th>Option</th>\n";
	echo "<th>Destination</th>\n";
	echo "<th>Order</th>\n";
	echo "<th>Description</th>\n";
	echo "<td align='right' width='42'>\n";
	if (permission_exists('ivr_menu_add')) {
		echo "	<a href='ivr_menu_option_edit.php?ivr_menu_uuid=".$ivr_menu_uuid."' alt='add'>$v_link_label_add</a>\n";
	}
	echo "</td>\n";
	echo "<tr>\n";

	if ($result_count == 0) {
		//no results
	}
	else { //received results
		foreach($result as $row) {
			$ivr_menu_option_param = $row['ivr_menu_option_param'];
			if (strlen(trim($ivr_menu_option_param)) == 0) {
				$ivr_menu_option_param = $row['ivr_menu_option_action'];
			}
			$ivr_menu_option_param = str_replace("menu-", "", $ivr_menu_option_param);
			$ivr_menu_option_param = str_replace("XML", "", $ivr_menu_option_param);
			$ivr_menu_option_param = str_replace("\${domain_name}", "", $ivr_menu_option_param);
			$ivr_menu_option_param = str_replace("\${domain}", "", $ivr_menu_option_param);
			$ivr_menu_option_param = ucfirst(trim($ivr_menu_option_param));

			echo "<tr >\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['ivr_menu_option_digits']."</td>\n";
			//echo "	<td valign='top' class='".$row_style[$c]."'>".$row['ivr_menu_option_action']."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$ivr_menu_option_param."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['ivr_menu_option_order']."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['ivr_menu_option_description']."&nbsp;</td>\n";
			echo "	<td valign='top' align='right'>\n";
			if (permission_exists('ivr_menu_edit')) {
				echo "		<a href='ivr_menu_option_edit.php?ivr_menu_uuid=".$row['ivr_menu_uuid']."&id=".$row['ivr_menu_option_uuid']."' alt='edit'>$v_link_label_edit</a>\n";
			}
			if (permission_exists('ivr_menu_delete')) {
				echo "		<a href='ivr_menu_option_delete.php?ivr_menu_uuid=".$row['ivr_menu_uuid']."&id=".$row['ivr_menu_option_uuid']."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
			}
			echo "	</td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='6' align='left'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	if (permission_exists('ivr_menu_add')) {
		echo "			<a href='ivr_menu_option_edit.php?ivr_menu_uuid=".$ivr_menu_uuid."' alt='add'>$v_link_label_add</a>\n";
	}
	echo "		</td>\n";
	echo "	</tr>\n";
 	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>";

	echo "<br><br>";
	echo "<br><br>";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br><br>";

?>
