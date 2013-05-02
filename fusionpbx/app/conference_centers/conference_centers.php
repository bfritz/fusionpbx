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
if (permission_exists('conference_center_view')) {
	//access granted
}
else {
	if (permission_exists('conference_room_view')) {
		//redirect to the conference rooms
		header( 'Location: conference_rooms.php') ;
	}
	else {
		echo "access denied";
		exit;
	}
}

//add multi-lingual support
	require_once "app_languages.php";
	foreach($text as $key => $value) {
		$text[$key] = $value[$_SESSION['domain']['language']['code']];
	}

//additional includes
	require_once "includes/header.php";
	require_once "includes/paging.php";

//get variables used to control the order
	$order_by = $_GET["order_by"];
	$order = $_GET["order"];

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "		<br />";

	echo "<table width='100%' border='0'>\n";
	echo "	<tr>\n";
	echo "<td align='left' width='30%' nowrap='nowrap'><b>".$text['title-conference-centers']."</b></td>\n";
	echo "<td width='70%' align='right'>\n";
	if (permission_exists('conferences_active_advanced_view')) {
		echo "	<input type='button' class='btn' name='' alt='".$text['button-view']."' onclick=\"window.location='".PROJECT_PATH."/app/conferences_active/conferences_active.php'\" value='".$text['button-view']."'>\n";
	}
	echo "	<input type='button' class='btn' name='' alt='".$text['button-rooms']."' onclick=\"window.location='conference_rooms.php'\" value='".$text['button-rooms']."'>\n";
	echo "</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align='left' colspan='2'>\n";
	echo "			".$text['description-conference-centers']."\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";

	//prepare to page the results
		if (if_group("superadmin") || if_group("admin")) {
			//show all extensions
			$sql = "select count(*) as num_rows from v_conference_centers ";
			$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
		}
		else {
			//show only assigned extensions
			$sql = "select count(*) as num_rows from v_conference_centers as c, v_conference_center_users as u ";
			$sql .= "where c.conference_center_uuid = u.conference_center_uuid ";
			$sql .= "and c.domain_uuid = '".$_SESSION['domain_uuid']."' ";
			$sql .= "and u.user_uuid = '".$_SESSION['user_uuid']."' ";
		}
		if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; }
		$prep_statement = $db->prepare($sql);
		if ($prep_statement) {
			$prep_statement->execute();
			$row = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
			if ($row['num_rows'] > 0) {
				$num_rows = $row['num_rows'];
			}
			else {
				$num_rows = '0';
			}
		}

	//prepare to page the results
		$rows_per_page = 10;
		$param = "";
		$page = $_GET['page'];
		if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
		list($paging_controls, $rows_per_page, $var3) = paging($num_rows, $param, $rows_per_page); 
		$offset = $rows_per_page * $page; 

	//get the list
		if (if_group("superadmin") || if_group("admin")) {
			//show all extensions
			$sql = "select * from v_conference_centers ";
			$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
		}
		else {
			//show only assigned extensions
			$sql = "select * from v_conference_centers as c, v_conference_center_users as u ";
			$sql .= "where c.conference_center_uuid = u.conference_center_uuid ";
			$sql .= "and c.domain_uuid = '".$_SESSION['domain_uuid']."' ";
			$sql .= "and u.user_uuid = '".$_SESSION['user_uuid']."' ";
		}
		if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; }
		$sql .= "limit $rows_per_page offset $offset ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll();
		$result_count = count($result);
		unset ($prep_statement, $sql);

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo th_order_by('conference_center_name', $text['label-name'], $order_by, $order);
	echo th_order_by('conference_center_extension', $text['label-extension'], $order_by, $order);
	//echo th_order_by('conference_center_order', 'Order', $order_by, $order);
	echo th_order_by('conference_center_enabled', $text['label-enabled'], $order_by, $order);
	echo th_order_by('conference_center_description', $text['label-description'], $order_by, $order);
	echo "<td align='right' width='42'>\n";
	if (permission_exists('conference_center_add')) {
		echo "	<a href='conference_center_edit.php' alt='add'>$v_link_label_add</a>\n";
	}
	else {
		echo "	&nbsp;\n";
	}
	echo "</td>\n";
	echo "<tr>\n";

	if ($result_count > 0) {
		foreach($result as $row) {
			$conference_center_name = $row['conference_center_name'];
			$conference_center_name = str_replace("-", " ", $conference_center_name);
			echo "<tr >\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$conference_center_name."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['conference_center_extension']."&nbsp;</td>\n";
			//echo "	<td valign='top' class='".$row_style[$c]."'>".$row['conference_center_order']."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['conference_center_enabled']."&nbsp;</td>\n";
			echo "	<td valign='top' class='row_stylebg' width='35%'>".$row['conference_center_description']."&nbsp;</td>\n";
			echo "	<td valign='top' align='right'>\n";
			if (permission_exists('conference_center_edit')) {
				echo "		<a href='conference_center_edit.php?id=".$row['conference_center_uuid']."' alt='".$text['label-edit']."'>$v_link_label_edit</a>\n";
			}
			if (permission_exists('conference_center_delete')) {
				echo "		<a href='conference_center_delete.php?id=".$row['conference_center_uuid']."' alt='".$text['label-delete']."' onclick=\"return confirm('".$text['confirm-delete']."')\">$v_link_label_delete</a>\n";
			}
			echo "	</td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='10' align='left'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	if (permission_exists('conference_center_add')) {
		echo "			<a href='conference_center_edit.php' alt='add'>$v_link_label_add</a>\n";
	}
	else {
		echo "			&nbsp;\n";
	}
	echo "		</td>\n";
	echo "	</tr>\n";
 	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "</div>";
	echo "<br /><br />";
	echo "<br /><br />";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br /><br />";

//include the footer
	require_once "includes/footer.php";
?>