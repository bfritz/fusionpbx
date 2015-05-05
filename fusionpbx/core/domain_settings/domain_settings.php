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
 Portions created by the Initial Developer are Copyright (C) 2008-2015
 the Initial Developer. All Rights Reserved.

 Contributor(s):
 Mark J Crane <markjcrane@fusionpbx.com>
*/
require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
if (permission_exists('domain_setting_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}


if (sizeof($_REQUEST) > 1) {

	$action = check_str($_REQUEST["action"]);
	$domain_uuid = check_str($_REQUEST["domain_id"]);
	$domain_setting_uuids = $_REQUEST["id"];
	$enabled = check_str($_REQUEST['enabled']);

	//change enabled value
		if ($domain_uuid != '' && sizeof($domain_setting_uuids) == 1 && $enabled != '') {
			$sql = "update v_domain_settings set ";
			$sql .= "domain_setting_enabled = '".$enabled."' ";
			$sql .= "where domain_uuid = '".$domain_uuid."' ";
			$sql .= "and domain_setting_uuid = '".$domain_setting_uuids[0]."' ";
			//echo $sql."<br><br>";
			$db->exec(check_sql($sql));
			unset($sql);

			$_SESSION["message"] = $text['message-update'];
			header("Location: domain_edit.php?id=".$domain_uuid);
			exit;
		}

	//delete domain settings
		if ($action == 'delete' && permission_exists('domain_setting_delete')) {
			//add multi-lingual support
				$language = new text;
				$text = $language->get();

			if (sizeof($domain_setting_uuids) > 0) {
				foreach ($domain_setting_uuids as $domain_setting_uuid) {
					$sql = "delete from v_domain_settings ";
					$sql .= "where domain_setting_uuid = '".$domain_setting_uuid."' ";
					$prep_statement = $db->prepare(check_sql($sql));
					$prep_statement->execute();
					unset ($prep_statement, $sql);
				}
				// set message
				$_SESSION["message"] = $text['message-delete'].": ".sizeof($domain_setting_uuids);
			}
			else {
				// set message
				$_SESSION["message"] = $text['message-delete_failed'];
				$_SESSION["message_mood"] = "negative";
			}

			header("Location: domain_edit.php?id=".check_str($_REQUEST["domain_uuid"]));
			exit;
		}

} //REQUEST

//include the paging
	require_once "resources/paging.php";

//get the variables
	$order_by = check_str($_GET["order_by"]);
	$order = check_str($_GET["order"]);

//show the content
	echo "<form name='domain_frm' id='domain_frm' method='GET' action='domain_settings.php'>";
	echo "<input type='hidden' name='action' id='action' value=''>";
	echo "<input type='hidden' name='domain_uuid' value='".$domain_uuid."'>";

//prepare to page the results
	$sql = "select count(*) as num_rows from v_domain_settings ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$prep_statement = $db->prepare($sql);
	if ($prep_statement) {
		$prep_statement->execute();
		$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
		if ($row['num_rows'] > 0) {
			$num_rows = $row['num_rows'];
		}
		else {
			$num_rows = '0';
		}
		unset ($prep_statement, $sql);
	}

//prepare to page the results
	$rows_per_page = 200;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; }
	list($paging_controls, $rows_per_page, $var3) = paging($num_rows, $param, $rows_per_page);
	$offset = $rows_per_page * $page;

//get the list
	$sql = "select * from v_domain_settings ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	if (strlen($order_by) == 0) {
		$sql .= "order by domain_setting_category, domain_setting_subcategory, domain_setting_order asc ";
	}
	else {
		$sql .= "order by $order_by $order ";
	}
	$sql .= "limit $rows_per_page offset $offset ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	$result_count = count($result);
	unset ($prep_statement, $sql);

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

//show the content
	echo "<table class='tr_hover' width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	if ($result_count > 0) {
		$previous_category = '';
		foreach($result as $row) {
			if ($previous_category != $row['domain_setting_category']) {
				$c = 0;
				echo "<tr>\n";
				echo "	<td colspan='7' align='left'>\n";
				if ($previous_category != '') {
					echo "	<br /><br />\n";
				}
				echo "		<b>\n";
				if (strtolower($row['domain_setting_category']) == "cdr") {
					echo "		CDR";
				}
				elseif (strtolower($row['domain_setting_category']) == "ldap") {
					echo "		LDAP";
				}
				else {
					echo "		".ucfirst($row['domain_setting_category']);
				}
				echo "		</b>\n";
				echo "	</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				if ((permission_exists("domain_select")
					&& permission_exists("domain_setting_add")
					&& count($_SESSION['domains']) > 1) ||
					permission_exists('domain_setting_delete')) {
						echo "<th style='width: 30px; text-align: center; padding: 0px;'><input type='checkbox' onchange=\"(this.checked) ? check('all','".strtolower($row['domain_setting_category'])."') : check('none','".strtolower($row['domain_setting_category'])."');\"></th>";
				}
				echo "<th>".$text['label-subcategory']."</th>";
				echo "<th>".$text['label-type']."</th>";
				echo "<th>".$text['label-value']."</th>";
				echo "<th style='text-align: center;'>".$text['label-enabled']."</th>";
				echo "<th>".$text['label-description']."</th>";
				echo "<td class='list_control_icons'>";
				if (permission_exists('domain_setting_add')) {
					echo "<a href='domain_setting_edit.php?domain_setting_category=".urlencode($row['domain_setting_category'])."&domain_uuid=".check_str($_GET['id'])."' alt='".$text['button-add']."'>".$v_link_label_add."</a>";
				}
				if (permission_exists('domain_setting_delete')) {
					echo "<a href='javascript:void(0);' onclick=\"if (confirm('".$text['confirm-delete']."')) { document.getElementById('action').value = 'delete'; document.forms.domain_frm.submit(); }\" alt='".$text['button-delete']."'>".$v_link_label_delete."</a>";
				}
				echo "</td>\n";
				echo "</tr>\n";
			}
			$tr_link = (permission_exists('domain_setting_edit')) ? " href='domain_setting_edit.php?domain_uuid=".$row['domain_uuid']."&id=".$row['domain_setting_uuid']."'" : null;
			echo "<tr ".$tr_link.">\n";
			if (
				(permission_exists("domain_select") && permission_exists("domain_setting_add") && count($_SESSION['domains']) > 1) ||
				permission_exists("domain_setting_delete")
				) {
				echo "	<td valign='top' class='".$row_style[$c]." tr_link_void' style='text-align: center; padding: 3px 0px 0px 0px;'><input type='checkbox' name='id[]' id='checkbox_".$row['domain_setting_uuid']."' value='".$row['domain_setting_uuid']."'></td>\n";
				$subcat_ids[strtolower($row['domain_setting_category'])][] = 'checkbox_'.$row['domain_setting_uuid'];
			}
			echo "	<td valign='top' class='".$row_style[$c]."'>";
			if (permission_exists('domain_setting_edit')) {
				echo 	"<a href='domain_setting_edit.php?domain_uuid=".$row['domain_uuid']."&id=".$row['domain_setting_uuid']."'>".$row['domain_setting_subcategory']."</a>";
			}
			else {
				echo $row['domain_setting_subcategory'];
			}
			echo "	</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['domain_setting_name']."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."' style='width: 30%; max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;'>\n";

			$category = $row['domain_setting_category'];
			$subcategory = $row['domain_setting_subcategory'];
			$name = $row['domain_setting_name'];
			if ($category == "domain" && $subcategory == "menu" && $name == "uuid" ) {
				$sql = "select * from v_menus ";
				$sql .= "where menu_uuid = '".$row['domain_setting_value']."' ";
				$sub_prep_statement = $db->prepare(check_sql($sql));
				$sub_prep_statement->execute();
				$sub_result = $sub_prep_statement->fetchAll(PDO::FETCH_NAMED);
				unset ($prep_statement, $sql);
				foreach ($sub_result as &$sub_row) {
					echo $sub_row["menu_language"]." - ".$sub_row["menu_name"]."\n";
				}
			}
			elseif ($category == "domain" && $subcategory == "template" && $name == "name" ) {
				echo "		".ucwords($row['domain_setting_value']);
			}
			else if ($subcategory == 'password' || substr_count($subcategory, '_password') > 0 || $category == "login" && $subcategory == "password_reset_key" && $name == "text") {
				echo "		".str_repeat('*', strlen($row['domain_setting_value']));
			}
			else {
				echo "		".htmlspecialchars($row['domain_setting_value']);
			}
			echo "		&nbsp;\n";
			echo "	</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]." tr_link_void' style='text-align: center;'>\n";
			echo "		<a href='?domain_id=".$row['domain_uuid']."&id[]=".$row['domain_setting_uuid']."&enabled=".(($row['domain_setting_enabled'] == 'true') ? 'false' : 'true')."'>".ucwords($row['domain_setting_enabled'])."</a>\n";
			echo "	</td>\n";
			echo "	<td valign='top' class='row_stylebg'>".$row['domain_setting_description']."&nbsp;</td>\n";
			echo "	<td class='list_control_icons'>";
			if (permission_exists('domain_setting_edit')) {
				echo "<a href='domain_setting_edit.php?domain_uuid=".$row['domain_uuid']."&id=".$row['domain_setting_uuid']."' alt='".$text['button-edit']."'>$v_link_label_edit</a>";
			}
			if (permission_exists('domain_setting_delete')) {
				echo "<a href='domain_settings.php?domain_uuid=".$row['domain_uuid']."&id[]=".$row['domain_setting_uuid']."&action=delete' alt='".$text['button-delete']."' onclick=\"return confirm('".$text['confirm-delete']."')\">$v_link_label_delete</a>";
			}
			echo "	</td>\n";
			echo "</tr>\n";
			$previous_category = $row['domain_setting_category'];
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results

	echo "<tr>\n";
	if (
		(permission_exists("domain_select") && permission_exists("domain_setting_add") && count($_SESSION['domains']) > 1) ||
		permission_exists("domain_delete")
		) {
		$colspan = 7;
	}
	else {
		$colspan = 6;
	}
	echo "<td colspan='".$colspan."' align='left'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td class='list_control_icons'>";
	if (permission_exists('domain_setting_add')) {
		echo 		"<a href='domain_setting_edit.php?domain_uuid=".check_str($_GET['id'])."' alt='".$text['button-add']."'>$v_link_label_add</a>";
	}
	if (permission_exists('domain_setting_delete') && $result_count > 0) {
		echo "<a href='javascript:void(0);' onclick=\"if (confirm('".$text['confirm-delete']."')) { document.getElementById('action').value = 'delete'; document.getElementById('domain_frm').submit(); }\" alt='".$text['button-delete']."'>".$v_link_label_delete."</a>";
	}
	echo "		</td>\n";
	echo "	</tr>\n";
 	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "</form>";

	echo "<br /><br />";

	// check or uncheck all category checkboxes
	if (sizeof($subcat_ids) > 0) {
		echo "<script>\n";
		echo "	function check(what, category) {\n";
		foreach ($subcat_ids as $domain_setting_category => $checkbox_ids) {
			echo "if (category == '".$domain_setting_category."') {\n";
			foreach ($checkbox_ids as $index => $checkbox_id) {
				echo "document.getElementById('".$checkbox_id."').checked = (what == 'all') ? true : false;\n";
			}
			echo "}\n";
		}
		echo "	}\n";
		echo "</script>\n";
	}

//include the footer
	//require_once "resources/footer.php";
?>
