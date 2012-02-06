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
require_once "includes/checkauth.php";
require_once "includes/paging.php";
if (permission_exists('dialplan_add') || permission_exists('dialplan_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//set the action as an add or an update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$dialplan_uuid = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//get the http post values and set them as php variables
	if (count($_POST)>0) {
		$extension_name = check_str($_POST["extension_name"]);
		$extension_number = check_str($_POST["extension_number"]);
		$dialplan_order = check_str($_POST["dialplan_order"]);
		$extension_continue = check_str($_POST["extension_continue"]);
		if (strlen($extension_continue) == 0) { $extension_continue = "false"; }
		$context = check_str($_POST["context"]);
		$enabled = check_str($_POST["enabled"]);
		$descr = check_str($_POST["descr"]);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update") {
		$dialplan_uuid = check_str($_POST["dialplan_uuid"]);
	}

	//check for all required data
		if (strlen($domain_uuid) == 0) { $msg .= "Please provide: domain_uuid<br>\n"; }
		if (strlen($extension_name) == 0) { $msg .= "Please provide: Extension Name<br>\n"; }
		if (strlen($dialplan_order) == 0) { $msg .= "Please provide: Order<br>\n"; }
		if (strlen($extension_continue) == 0) { $msg .= "Please provide: Continue<br>\n"; }
		if (strlen($context) == 0) { $msg .= "Please provide: Context<br>\n"; }
		if (strlen($enabled) == 0) { $msg .= "Please provide: Enabled<br>\n"; }
		//if (strlen($descr) == 0) { $msg .= "Please provide: Description<br>\n"; }
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "includes/header.php";
			require_once "includes/persistformvar.php";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg."<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		}

	//remove the invalid characters from the extension name
		$extension_name = str_replace(" ", "_", $extension_name);
		$extension_name = str_replace("/", "", $extension_name);

	//add or update the database
		if ($_POST["persistformvar"] != "true") {
			if ($action == "add" && permission_exists('dialplan_add')) {
				//add the data into the database
					$dialplan_uuid = uuid();
					$sql = "insert into v_dialplans ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "dialplan_uuid, ";
					$sql .= "extension_name, ";
					$sql .= "extension_number, ";
					$sql .= "dialplan_order, ";
					$sql .= "extension_continue, ";
					$sql .= "context, ";
					$sql .= "enabled, ";
					$sql .= "descr ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$domain_uuid', ";
					$sql .= "'$dialplan_uuid', ";
					$sql .= "'$extension_name', ";
					$sql .= "'$extension_number', ";
					$sql .= "'$dialplan_order', ";
					$sql .= "'$extension_continue', ";
					$sql .= "'$context', ";
					$sql .= "'$enabled', ";
					$sql .= "'$descr' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);

				//synchronize the xml config
					sync_package_v_dialplan();

				//redirect the user
					require_once "includes/header.php";
					echo "<meta http-equiv=\"refresh\" content=\"2;url=dialplans.php\">\n";
					echo "<div align='center'>\n";
					echo "Add Complete\n";
					echo "</div>\n";
					require_once "includes/footer.php";
					return;
			} //if ($action == "add")

			if ($action == "update" && permission_exists('dialplan_edit')) {
				//update the database
					$sql = "update v_dialplans set ";
					$sql .= "domain_uuid = '$domain_uuid', ";
					$sql .= "extension_name = '$extension_name', ";
					$sql .= "extension_number = '$extension_number', ";
					$sql .= "dialplan_order = '$dialplan_order', ";
					$sql .= "extension_continue = '$extension_continue', ";
					$sql .= "context = '$context', ";
					$sql .= "enabled = '$enabled', ";
					$sql .= "descr = '$descr' ";
					$sql .= "where domain_uuid = '$domain_uuid' ";
					$sql .= "and dialplan_uuid = '$dialplan_uuid'";
					$db->exec(check_sql($sql));
					unset($sql);

				//synchronize the xml config
					sync_package_v_dialplan();

				//redirect the user
					require_once "includes/header.php";
					echo "<meta http-equiv=\"refresh\" content=\"2;url=dialplans.php\">\n";
					echo "<div align='center'>\n";
					echo "Update Complete\n";
					echo "</div>\n";
					require_once "includes/footer.php";
					return;
			} //if ($action == "update")
		} //if ($_POST["persistformvar"] != "true")
} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
		$dialplan_uuid = $_GET["id"];
		$sql = "";
		$sql .= "select * from v_dialplans ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			$extension_name = $row["extension_name"];
			$extension_number = $row["extension_number"];
			$dialplan_order = $row["dialplan_order"];
			$extension_continue = $row["extension_continue"];
			$context = $row["context"];
			$enabled = $row["enabled"];
			$descr = $row["descr"];
			break; //limit to 1 row
		}
		unset ($prep_statement);
	}

//show the header
	require_once "includes/header.php";

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";

	echo "<form method='post' name='frm' action=''>\n";
	echo "<div align='center'>\n";

	echo "<table width=\"100%\" border=\"0\" cellpadding=\"1\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<td align='left' width='30%'>\n";
	echo"			<span class=\"vexpl\"><strong>Dialplan</strong></span><br />\n";
	echo "    </td>\n";
	echo "    <td width='70%' align='right'>\n";
	echo "		<input type='button' class='btn' name='' alt='copy' onclick=\"if (confirm('Do you really want to copy this?')){window.location='dialplan_copy.php?id=".$row['dialplan_uuid']."';}\" value='Copy'>\n";
	if ($context == "public") {
		echo "		<input type='button' class='btn' name='' alt='back' onclick=\"window.location='dialplans.php?context=public'\" value='Back'>\n";
	}
	else {
		echo "		<input type='button' class='btn' name='' alt='back' onclick=\"window.location='dialplans.php'\" value='Back'>\n";
	}
	echo "	</td>\n";
	echo "  </tr>\n";
	echo "  <tr>\n";
	echo "    <td align='left' colspan='2'>\n";
	echo "        Dialplan Include general settings. \n";
	echo "        \n";
	echo "    </td>\n";
	echo "  </tr>\n";
	echo "</table>";
	echo "<br />\n";

	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "    Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='extension_name' maxlength='255' value=\"".htmlspecialchars($extension_name)."\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "    Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='extension_number' maxlength='255' value=\"".htmlspecialchars($extension_number)."\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "    Context:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='context' maxlength='255' value=\"$context\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "    Continue:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='extension_continue'>\n";
	echo "    <option value=''></option>\n";
	if ($extension_continue == "true") { 
		echo "    <option value='true' SELECTED >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($extension_continue == "false") { 
		echo "    <option value='false' SELECTED >false</option>\n";
	}
	else {
		echo "    <option value='false'>false</option>\n";
	}
	echo "    </select>\n";
	echo "<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "    Order:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "              <select name='dialplan_order' class='formfld'>\n";
	//echo "              <option></option>\n";
	if (strlen(htmlspecialchars($dialplan_order))> 0) {
		echo "              <option selected='yes' value='".htmlspecialchars($dialplan_order)."'>".htmlspecialchars($dialplan_order)."</option>\n";
	}
	$i=0;
	while($i<=999) {
	  if (strlen($i) == 1) {
		echo "              <option value='00$i'>00$i</option>\n";
	  }
	  if (strlen($i) == 2) {
		echo "              <option value='0$i'>0$i</option>\n";
	  }
	  if (strlen($i) == 3) {
		echo "              <option value='$i'>$i</option>\n";
	  }

	  $i++;
	}
	echo "              </select>\n";
	//echo "  <input class='formfld' type='text' name='dialplan_order' maxlength='255' value='$dialplan_order'>\n";
	echo "<br />\n";
	//echo "Dialplans are processed from the lowest to the highest number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "    Enabled:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='enabled'>\n";
	echo "    <option value=''></option>\n";
	if ($enabled == "true") { 
		echo "    <option value='true' SELECTED >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($enabled == "false") { 
		echo "    <option value='false' SELECTED >false</option>\n";
	}
	else {
		echo "    <option value='false'>false</option>\n";
	}
	echo "    </select>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "    Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <textarea class='formfld' name='descr' rows='4'>".htmlspecialchars($descr)."</textarea>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='dialplan_uuid' value='$dialplan_uuid'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

	//v_dialplan_details
	if ($action == "update") {
		echo "<div align='center'>";
		echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
		echo "<tr class='border'>\n";
		echo "	<td align=\"center\">\n";

		echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
		echo "  <tr>\n";
		echo "    <td align='left'><p><span class=\"vexpl\"><span class=\"red\"><strong>Conditions and Actions<br />\n";
		echo "        </strong></span>\n";
		echo "        The following conditions, actions and anti-actions are used in the dialplan to direct \n";
		echo "        call flow. Each is processed in order until you reach the action tag which tells what action to perform. \n";
		echo "        You are not limited to only one condition or action tag for a given extension.\n";
		echo "        </span></p></td>\n";
		echo "  </tr>\n";
		echo "</table>";
		echo "<br />\n";

		$sql = "";
		$sql .= " select * from v_dialplan_details ";
		$sql .= " where domain_uuid = '$domain_uuid' ";
		$sql .= " and dialplan_uuid = '$dialplan_uuid' ";
		$sql .= " order by field_group asc, field_order asc";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		$result_count = count($result);
		unset ($prep_statement, $sql);
	
		//create a new array that is sorted into groups and put the tags in order conditions, actions, anti-actions
			$x = 0;
			$details = '';
			//conditions
				foreach($result as $row) {
					if ($row['tag'] == "condition") {
						$group = $row['field_group'];
						foreach ($row as $key => $val) {
							$details[$group][$x][$key] = $val;
						}
					}
					$x++;
				}
			//regex
				foreach($result as $row) {
					if ($row['tag'] == "regex") {
						$group = $row['field_group'];
						foreach ($row as $key => $val) {
							$details[$group][$x][$key] = $val;
						}
					}
					$x++;
				}
			//actions
				foreach($result as $row) {
					if ($row['tag'] == "action") {
						$group = $row['field_group'];
						foreach ($row as $key => $val) {
							$details[$group][$x][$key] = $val;
						}
					}
					$x++;
				}
			//anti-actions
				foreach($result as $row) {
					if ($row['tag'] == "anti-action") {
						$group = $row['field_group'];
						foreach ($row as $key => $val) {
							$details[$group][$x][$key] = $val;
						}
					}
					$x++;
				}
			unset($result);
			
		//define the alternating row styles
			$c = 0;
			$row_style["0"] = "row_style0";
			$row_style["1"] = "row_style1";

		//display the results
			echo "<div align='center'>\n";
			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "<tr>\n";
			echo "<th align='center' width='90px;'>Tag</th>\n";
			echo "<th align='center' width='150px;'>Type</th>\n";
			echo "<th align='center' width='70%'>Data</th>\n";
			echo "<th align='center'>Order</th>\n";
			//echo "<th align='center'>Group</th>\n";
			echo "<td align='right' width='42'>\n";
			echo "	<a href='dialplan_details_edit.php?id2=".$dialplan_uuid."' alt='add'>$v_link_label_add</a>\n";
			echo "</td>\n";
			echo "<tr>\n";

			if ($result_count == 0) {
				//no results
			}
			else { //received results
				$x = 0;
				foreach($details as $group) {
					if ($x > 0) {
						echo "<tr>\n";
						echo "<td colspan='6'>\n";
						echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
						echo "	<tr>\n";
						echo "		<td width='33.3%' nowrap='nowrap'>&nbsp;</td>\n";
						echo "		<td width='33.3%' align='center' nowrap='nowrap'>$paging_controls</td>\n";
						echo "		<td width='33.3%' align='right'>\n";
						echo "			<a href='dialplan_details_edit.php?id2=".$dialplan_uuid."' alt='add'>$v_link_label_add</a>\n";
						echo "		</td>\n";
						echo "	</tr>\n";
						echo "	</table>\n";
						echo "</td>\n";
						echo "</tr>\n";
						echo "</table>";
						echo "</div>";
						echo "<br><br>";

						echo "<div align='center'>\n";
						echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
						echo "<tr>\n";
						echo "<th align='center' width='90px;'>Tag</th>\n";
						echo "<th align='center' width='150px;'>Type</th>\n";
						echo "<th align='center' width='70%'>Data</th>\n";
						echo "<th align='center'>Order</th>\n";
						//echo "<th align='center'>Group</th>\n";
						echo "<td align='right' width='42'>\n";
						echo "	<a href='dialplan_details_edit.php?id2=".$dialplan_uuid."' alt='add'>$v_link_label_add</a>\n";
						echo "</td>\n";
						echo "<tr>\n";
					}

					foreach($group as $row) {
						echo "<tr >\n";
						echo "	<td valign='top' class='".$row_style[$c]."'>&nbsp;&nbsp;".$row['tag']."</td>\n";
						echo "	<td valign='top' class='".$row_style[$c]."'>&nbsp;&nbsp;".$row['field_type']."</td>\n";
						echo "	<td valign='top' class='".$row_style[$c]."'>&nbsp;&nbsp;".wordwrap($row['field_data'],180,"<br>",1)."</td>\n";
						echo "	<td valign='top' class='".$row_style[$c]."'>&nbsp;&nbsp;".$row['field_order']."</td>\n";
						//echo "	<td valign='top' class='".$row_style[$c]."'>&nbsp;&nbsp;".$row['field_group']."</td>\n";
						echo "	<td valign='top' align='right' nowrap='nowrap'='nowrap='nowrap''>\n";
						echo "		<a href='dialplan_details_edit.php?id=".$row[dialplan_detail_uuid]."&id2=".$dialplan_uuid."' alt='edit'>$v_link_label_edit</a>\n";
						echo "		<a href='dialplan_details_delete.php?id=".$row[dialplan_detail_uuid]."&id2=".$dialplan_uuid."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
						echo "	</td>\n";
						echo "</tr>\n";
					}
					if ($c==0) { $c=1; } else { $c=0; }
					$x++;
				} //end foreach
				unset($sql, $result, $row_count);
				
			} //end if results

			echo "<tr>\n";
			echo "<td colspan='6'>\n";
			echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
			echo "	<tr>\n";
			echo "		<td width='33.3%' nowrap='nowrap'>&nbsp;</td>\n";
			echo "		<td width='33.3%' align='center' nowrap='nowrap'>$paging_controls</td>\n";
			echo "		<td width='33.3%' align='right'>\n";
			echo "			<a href='dialplan_details_edit.php?id2=".$dialplan_uuid."' alt='add'>$v_link_label_add</a>\n";
			echo "		</td>\n";
			echo "	</tr>\n";
			echo "	</table>\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>";
			echo "</div>";
			echo "<br><br>";

			echo "</td>";
			echo "</tr>";
			echo "</table>";
			echo "</div>";
			echo "<br><br>";
	} //end if update

//show the footer
	require_once "includes/footer.php";
?>