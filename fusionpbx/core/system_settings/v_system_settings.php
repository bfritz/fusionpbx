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
include "root.php";
require_once "includes/config.php";
require_once "includes/paging.php";
require_once "includes/checkauth.php";
if (permission_exists('system_settings_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//domain list
	unset ($_SESSION["domains"]);
	$sql = "select * from v_system_settings ";
	$prep_statement = $db->prepare($sql);
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	foreach($result as $row) {
		$_SESSION['domains'][$row['domain_uuid']]['domain_uuid'] = $row['domain_uuid'];
		$_SESSION['domains'][$row['domain_uuid']]['domain'] = $row['v_domain'];
		$_SESSION['domains'][$row['domain_uuid']]['template_name'] = $row['v_template_name'];
	}
	$num_rows = count($result);
	unset($result, $prep_statement);

//get http values and set them as variables
	$order_by = $_GET["order_by"];
	$order = $_GET["order"];

//change the tenant
	if (strlen($_GET["domain_uuid"]) > 0 && $_GET["domain_change"] == "true") {
		//update the domain_uuid and session variables
			$domain_uuid = $_GET["domain_uuid"];
			$_SESSION['domain_uuid'] = $_SESSION['domains'][$domain_uuid]['domain_uuid'];
			$_SESSION["v_domain"] = $_SESSION['domains'][$domain_uuid]['domain'];
			$_SESSION["v_template_name"] = $_SESSION['domains'][$domain_uuid]['template_name'];
		//clear the menu session so that it is regenerated for the current tenant
			$_SESSION["menu"] = '';
		//set the context
			if (count($_SESSION["domains"]) > 1) {
				$_SESSION["context"] = $_SESSION["v_domain"];
			}
			else {
				$_SESSION["context"] = 'default';
			}
	}

//include the header
	require_once "includes/header.php";

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "      <br>";

	echo "<table width='100%' border='0'>\n";
	echo "</tr></table>\n";

	$rows_per_page = 150;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
	list($paging_controls, $rows_per_page, $tmp) = paging($num_rows, $param, $rows_per_page); 
	$offset = $rows_per_page * $page; 

	$sql = "";
	$sql .= " select * from v_system_settings ";
	if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; }
	$sql .= " limit $rows_per_page offset $offset ";
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
	echo "<td align='left' nowrap><strong>System Settings</strong></td>\n";
	echo "<td>&nbsp;</td>\n";
	echo "<td align='right' align='right'>&nbsp;</td>\n";
	echo "<td align='right' width='42'>&nbsp;</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo thorder_by('v_domain', 'Domain', $order_by, $order);
	//echo thorder_by('v_package_version', 'Package Version', $order_by, $order);
	echo thorder_by('v_label', 'Label', $order_by, $order);
	//echo thorder_by('v_name', 'Name', $order_by, $order);
	//echo thorder_by('v_dir', 'Directory', $order_by, $order);
	echo "<th width='40%'>Description</th>\n";
	echo "<td align='right' width='42'>\n";
	if (permission_exists('system_settings_add')) {
		echo "	<a href='v_system_settings_add.php' alt='add'>$v_link_label_add</a>\n";
	}
	echo "</td>\n";
	echo "<tr>\n";

	if ($result_count == 0) {
		//no results found
	}
	else {
		//get the list of installed apps from the core and mod directories
			if (!is_array($apps)) {
				$config_list = glob($_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . "/*/*/v_config.php");
				$x=0;
				foreach ($config_list as &$config_path) {
					include($config_path);
					$x++;
				}
			}
		foreach($result as $row) {
			//if there are no permissions listed in v_group_permissions then set the default permissions
				$sql = "";
				$sql .= "select count(*) as count from v_group_permissions ";
				$sql .= "where domain_uuid = ".$row['domain_uuid']." ";
				$prep_statement = $db->prepare($sql);
				$prep_statement->execute();
				$result = $prep_statement->fetch();
				unset ($prep_statement);
				if ($result['count'] > 0) {
					if ($display_type == "text") {
						echo "Goup Permissions: 	no change\n";
					}
				}
				else {
					if ($display_type == "text") {
						echo "Goup Permissions: 	added\n";
					}
					//no permissions found add the defaults
						foreach($apps as $app) {
							foreach ($app['permissions'] as $sub_row) {
								foreach ($sub_row['groups'] as $group) {
									//add the record
									$sql = "insert into v_group_permissions ";
									$sql .= "(";
									$sql .= "domain_uuid, ";
									$sql .= "permission_id, ";
									$sql .= "group_id ";
									$sql .= ")";
									$sql .= "values ";
									$sql .= "(";
									$sql .= "'".$row['domain_uuid']."', ";
									$sql .= "'".$sub_row['name']."', ";
									$sql .= "'".$group."' ";
									$sql .= ")";
									$db->exec($sql);
									unset($sql);
								}
							}
						}
				}

			if (strlen($row['v_server_port']) == 0) { $row['v_server_port'] = '80'; }
			switch ($row['v_server_port']) {
				case "80":
					$url = strtolower($row['v_server_protocol']).'://'.$row['v_domain'];
					break;
				case "443":
					$url = strtolower($row['v_server_protocol']).'://'.$row['v_domain'];
					break;
				default:
					$url = strtolower($row['v_server_protocol']).'://'.$row['v_domain'].':'.$row['v_server_port'];
					break;
			}
			echo "<tr >\n";
			//echo "	<td valign='top' class='".$row_style[$c]."'><a href='".$url."'>".$row['v_domain']."</a></td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'><a href='v_system_settings.php?id=".$row['domain_uuid']."&domain=".$row['v_domain']."'>".$row['v_domain']."</a></td>\n";
			//echo "	<td valign='top' class='".$row_style[$c]."'>".$row['v_package_version']."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['v_label']."&nbsp;</td>\n";
			//echo "	<td valign='top' class='".$row_style[$c]."'>".$row['v_name']."</td>\n";
			//echo "	<td valign='top' class='".$row_style[$c]."'>".$row['v_dir']."</td>\n";
			echo "	<td valign='top' class='row_stylebg'>".$row['v_description']."&nbsp;</td>\n";
			echo "	<td valign='top' align='right'>\n";
			if (permission_exists('system_settings_edit')) {
				echo "		<a href='v_system_settings_edit.php?id=".$row['domain_uuid']."' alt='edit'>$v_link_label_edit</a>\n";
			}
			if (permission_exists('system_settings_delete')) {
				echo "		<a href='v_system_settings_delete.php?id=".$row['domain_uuid']."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
			}
			echo "	</td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='7'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	if (permission_exists('system_settings_add')) {
		echo "			<a href='v_system_settings_add.php' alt='add'>$v_link_label_add</a>\n";
	}
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "</div>";
	echo "<br><br>";
	echo "<br><br>";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br><br>";

//include the footer
	require_once "includes/footer.php";
?>
