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
	Copyright (C) 2008-2010 All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
require_once "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//action add or update
if (isset($_REQUEST["id"])) {
	$action = "update";
	$hardware_phone_id = check_str($_REQUEST["id"]);
}
else {
	$action = "add";
}

//POST to PHP variables
if (count($_POST)>0) {
	$phone_mac_address = check_str($_POST["phone_mac_address"]);
	$phone_mac_address = strtolower($phone_mac_address);
	$phone_vendor = check_str($_POST["phone_vendor"]);
	$phone_model = check_str($_POST["phone_model"]);
	$phone_provision_enable = check_str($_POST["phone_provision_enable"]);
	$phone_template = check_str($_POST["phone_template"]);
	$phone_username = check_str($_POST["phone_username"]);
	$phone_password = check_str($_POST["phone_password"]);
	$phone_description = check_str($_POST["phone_description"]);
}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	if ($action == "update") {
		$hardware_phone_id = check_str($_POST["hardware_phone_id"]);
	}

	//check for all required data
		if (strlen($phone_mac_address) == 0) { $msg .= "Please provide: MAC Address<br>\n"; }
		//if (strlen($phone_vendor) == 0) { $msg .= "Please provide: Vendor<br>\n"; }
		//if (strlen($phone_model) == 0) { $msg .= "Please provide: Model<br>\n"; }
		//if (strlen($phone_provision_enable) == 0) { $msg .= "Please provide: Enabled<br>\n"; }
		//if (strlen($phone_template) == 0) { $msg .= "Please provide: Template<br>\n"; }
		//if (strlen($phone_username) == 0) { $msg .= "Please provide: Username<br>\n"; }
		//if (strlen($phone_password) == 0) { $msg .= "Please provide: Password<br>\n"; }
		//if (strlen($phone_description) == 0) { $msg .= "Please provide: Description<br>\n"; }
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

	//add or update the database
		if ($_POST["persistformvar"] != "true") {
			if ($action == "add") {
				$sql = "insert into v_hardware_phones ";
				$sql .= "(";
				$sql .= "v_id, ";
				$sql .= "phone_mac_address, ";
				$sql .= "phone_vendor, ";
				$sql .= "phone_model, ";
				$sql .= "phone_provision_enable, ";
				$sql .= "phone_template, ";
				$sql .= "phone_username, ";
				$sql .= "phone_password, ";
				$sql .= "phone_description ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$v_id', ";
				$sql .= "'$phone_mac_address', ";
				$sql .= "'$phone_vendor', ";
				$sql .= "'$phone_model', ";
				$sql .= "'$phone_provision_enable', ";
				$sql .= "'$phone_template', ";
				$sql .= "'$phone_username', ";
				$sql .= "'$phone_password', ";
				$sql .= "'$phone_description' ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);

				//write the provision files
					require_once "mod/provision/v_provision_write.php";

				//redirect the user
					require_once "includes/header.php";
					echo "<meta http-equiv=\"refresh\" content=\"2;url=v_hardware_phones.php\">\n";
					echo "<div align='center'>\n";
					echo "Add Complete\n";
					echo "</div>\n";
					require_once "includes/footer.php";
					return;
			} //if ($action == "add")

			if ($action == "update") {
				$sql = "update v_hardware_phones set ";
				$sql .= "phone_mac_address = '$phone_mac_address', ";
				$sql .= "phone_vendor = '$phone_vendor', ";
				$sql .= "phone_model = '$phone_model', ";
				$sql .= "phone_provision_enable = '$phone_provision_enable', ";
				$sql .= "phone_template = '$phone_template', ";
				$sql .= "phone_username = '$phone_username', ";
				$sql .= "phone_password = '$phone_password', ";
				$sql .= "phone_description = '$phone_description' ";
				$sql .= "where v_id = '$v_id' ";
				$sql .= "and hardware_phone_id = '$hardware_phone_id'";
				$db->exec(check_sql($sql));
				unset($sql);

				//write the provision files
					require_once "mod/provision/v_provision_write.php";

				//redirect the user
					require_once "includes/header.php";
					echo "<meta http-equiv=\"refresh\" content=\"2;url=v_hardware_phones.php\">\n";
					echo "<div align='center'>\n";
					echo "Update Complete\n";
					echo "</div>\n";
					require_once "includes/footer.php";
					return;
			}

		} //if ($_POST["persistformvar"] != "true")

} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
		$hardware_phone_id = $_GET["id"];
		$sql = "";
		$sql .= "select * from v_hardware_phones ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and hardware_phone_id = '$hardware_phone_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		foreach ($result as &$row) {
			$phone_mac_address = $row["phone_mac_address"];
			$phone_mac_address = strtolower($phone_mac_address);
			$phone_vendor = $row["phone_vendor"];
			$phone_model = $row["phone_model"];
			$phone_provision_enable = $row["phone_provision_enable"];
			$phone_template = $row["phone_template"];
			$phone_username = $row["phone_username"];
			$phone_password = $row["phone_password"];
			$phone_description = $row["phone_description"];
			break; //limit to 1 row
		}
		unset ($prepstatement);
	}

//begin the content
	require_once "includes/header.php";

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing=''>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "	  <br>";


	echo "<form method='post' name='frm' action=''>\n";

	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

	echo "<tr>\n";
	if ($action == "add") {
		echo "<td align='left' width='30%' nowrap='nowrap' align='left'><b>Hardware Phone Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap='nowrap' align='left'><b>Hardware Phone Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_hardware_phones.php'\" value='Back'></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan='2' align='left'>\n";
	echo "The following information is used to provision phones.<br /><br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "	MAC Address:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='phone_mac_address' maxlength='255' value=\"$phone_mac_address\">\n";
	echo "<br />\n";
	echo "Enter the MAC address.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Template:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";

	echo "<select id='phone_template' name='phone_template' class='formfld'>\n";
	echo "<option value=''></option>\n";
	$temp_dir = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/templates/provision";
	if($dh = opendir($temp_dir)) {
		while($dir = readdir($dh)) {
			if($file != "." && $dir != ".." && $dir[0] != '.') {
				if(is_dir($temp_dir . "/" . $dir)) {
					echo "<optgroup label='$dir'>";
					if($dh_sub = opendir($temp_dir.'/'.$dir)) {
						while($dir_sub = readdir($dh_sub)) {
							if($file_sub != '.' && $dir_sub != '..' && $dir_sub[0] != '.') {
								if(is_dir($temp_dir . '/' . $dir .'/'. $dir_sub)) {
									if ($phone_template == $dir."/".$dir_sub) {
										echo "<option value='".$dir."/".$dir_sub."' selected='selected'>".$dir."/".$dir_sub."</option>\n";
									}
									else {
										echo "<option value='".$dir."/".$dir_sub."'>".$dir."/".$dir_sub."</option>\n";
									}
								}
							}
						}
						closedir($dh_sub);
					}
					echo "</optgroup>";
				}
			}
		}
		closedir($dh);
	}
	echo "</select>\n";
	echo "<br />\n";
	echo "Select a template.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Vendor:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='phone_vendor' maxlength='255' value=\"$phone_vendor\">\n";
	echo "<br />\n";
	echo "Enter the vendor name.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Model:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='phone_model' maxlength='255' value=\"$phone_model\">\n";
	echo "<br />\n";
	echo "Enter the model number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	/*
	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Username:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='phone_username' maxlength='255' value=\"$phone_username\">\n";
	echo "<br />\n";
	echo "Enter the username.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Password:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='phone_password' maxlength='255' value=\"$phone_password\">\n";
	echo "<br />\n";
	echo "Enter the password.\n";
	echo "</td>\n";
	echo "</tr>\n";
	*/

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Enabled:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='phone_provision_enable'>\n";
	echo "    <option value=''></option>\n";
	if ($phone_provision_enable == "true" || strlen($phone_provision_enable) == 0) { 
		echo "    <option value='true' selected >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($phone_provision_enable == "false") { 
		echo "    <option value='false' selected >false</option>\n";
	}
	else {
		echo "    <option value='false'>false</option>\n";
	}
	echo "    </select>\n";
	echo "<br />\n";
	echo "Enable or disable provisioning for this phone.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='phone_description' maxlength='255' value=\"$phone_description\">\n";
	echo "<br />\n";
	echo "Enter the description.\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='hardware_phone_id' value='$hardware_phone_id'>\n";
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


require_once "includes/footer.php";
?>
