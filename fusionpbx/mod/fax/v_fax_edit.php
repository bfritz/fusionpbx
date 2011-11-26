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
if (permission_exists('fax_extension_add') || permission_exists('fax_extension_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//get the fax_extension and save it as a variable
	if (strlen($_REQUEST["fax_extension"]) > 0) {
		$fax_extension = check_str($_REQUEST["fax_extension"]);
	}

//set the fax directory
	if (count($_SESSION["domains"]) > 1) {
		$v_fax_dir = $v_storage_dir.'/fax/'.$v_domain;
	}
	else {
		$v_fax_dir = $v_storage_dir.'/fax';
	}

//get the fax extension
	if (strlen($fax_extension) > 0) {
		//set the fax directories. example /usr/local/freeswitch/storage/fax/329/inbox
			$dir_fax_inbox = $v_fax_dir.'/'.$fax_extension.'/inbox';
			$dir_fax_sent = $v_fax_dir.'/'.$fax_extension.'/sent';
			$dir_fax_temp = $v_fax_dir.'/'.$fax_extension.'/temp';

		//make sure the directories exist
			if (!is_dir($v_storage_dir)) {
				mkdir($v_storage_dir);
				chmod($dir_fax_sent,0774);
			}
			if (!is_dir($v_fax_dir.'/'.$fax_extension)) {
				mkdir($v_fax_dir.'/'.$fax_extension,0774,true);
				chmod($v_fax_dir.'/'.$fax_extension,0774);
			}
			if (!is_dir($dir_fax_inbox)) { 
				mkdir($dir_fax_inbox,0774,true); 
				chmod($dir_fax_inbox,0774);
			}
			if (!is_dir($dir_fax_sent)) { 
				mkdir($dir_fax_sent,0774,true); 
				chmod($dir_fax_sent,0774);
			}
			if (!is_dir($dir_fax_temp)) { 
				mkdir($dir_fax_temp,0774,true); 
				chmod($dir_fax_temp,0774);
			}
	}

//set the action as an add or an update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$fax_id = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//get the http post values and set them as php variables
	if (count($_POST)>0) {
		$fax_name = check_str($_POST["fax_name"]);
		$fax_email = check_str($_POST["fax_email"]);
		$fax_pin_number = check_str($_POST["fax_pin_number"]);
		$fax_caller_id_name = check_str($_POST["fax_caller_id_name"]);
		$fax_caller_id_number = check_str($_POST["fax_caller_id_number"]);
		$fax_forward_number = check_str($_POST["fax_forward_number"]);
		if (strlen($fax_forward_number) > 0) {
			$fax_forward_number = preg_replace("~[^0-9]~", "",$fax_forward_number);
		}

		//prepare the user list for the database
		$fax_user_list = check_str(trim($_POST["fax_user_list"]));
		if (strlen($fax_user_list) > 0) {
			$fax_user_list_array = explode("\n", $fax_user_list);
			if (count($fax_user_list_array) == 0) {
				$fax_user_list = '';
			}
			else {
				$fax_user_list = '|';
				foreach($fax_user_list_array as $user){
					if(strlen(trim($user)) > 0) {
						$fax_user_list .= check_str(trim($user))."|";
					}
				}
			}
		}

		$fax_description = check_str($_POST["fax_description"]);
	}

//clear file status cache
	clearstatcache(); 

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update" && permission_exists('fax_extension_edit')) {
		$fax_id = check_str($_POST["fax_id"]);
	}

	//check for all required data
		if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		if (strlen($fax_extension) == 0) { $msg .= "Please provide: Extension<br>\n"; }
		if (strlen($fax_name) == 0) { $msg .= "Please provide: A file to Fax<br>\n"; }
		//if (strlen($fax_email) == 0) { $msg .= "Please provide: Email<br>\n"; }
		//if (strlen($fax_pin_number) == 0) { $msg .= "Please provide: Pin Number<br>\n"; }
		//if (strlen($fax_caller_id_name) == 0) { $msg .= "Please provide: Caller ID Name<br>\n"; }
		//if (strlen($fax_caller_id_number) == 0) { $msg .= "Please provide: Caller ID Number<br>\n"; }
		//if (strlen($fax_forward_number) == 0) { $msg .= "Please provide: Forward Number<br>\n"; }
		//if (strlen($fax_user_list) == 0) { $msg .= "Please provide: Assigned Users<br>\n"; }
		//if (strlen($fax_description) == 0) { $msg .= "Please provide: Description<br>\n"; }
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
			if ($action == "add" && permission_exists('fax_extension_add')) {
				$sql = "insert into v_fax ";
				$sql .= "(";
				$sql .= "v_id, ";
				$sql .= "faxextension, ";
				$sql .= "faxname, ";
				$sql .= "faxemail, ";
				$sql .= "fax_pin_number, ";
				$sql .= "fax_caller_id_name, ";
				$sql .= "fax_caller_id_number, ";
				if (strlen($fax_forward_number) > 0) {
					$sql .= "fax_forward_number, ";
				}
				$sql .= "fax_user_list, ";
				$sql .= "faxdescription ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$v_id', ";
				$sql .= "'$fax_extension', ";
				$sql .= "'$fax_name', ";
				$sql .= "'$fax_email', ";
				$sql .= "'$fax_pin_number', ";
				$sql .= "'$fax_caller_id_name', ";
				$sql .= "'$fax_caller_id_number', ";
				if (strlen($fax_forward_number) > 0) {
					$sql .= "'$fax_forward_number', ";
				}
				$sql .= "'$fax_user_list', ";
				$sql .= "'$fax_description' ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);

				sync_package_v_fax();

				require_once "includes/header.php";
				echo "<meta http-equiv=\"refresh\" content=\"2;url=v_fax.php\">\n";
				echo "<div align='center'>\n";
				echo "Add Complete\n";
				echo "</div>\n";
				require_once "includes/footer.php";
				return;
			} //if ($action == "add")

			if ($action == "update" && permission_exists('fax_extension_edit')) {
				$sql = "update v_fax set ";
				$sql .= "faxextension = '$fax_extension', ";
				$sql .= "faxname = '$fax_name', ";
				$sql .= "faxemail = '$fax_email', ";
				$sql .= "fax_pin_number = '$fax_pin_number', ";
				$sql .= "fax_caller_id_name = '$fax_caller_id_name', ";
				$sql .= "fax_caller_id_number = '$fax_caller_id_number', ";
				if (strlen($fax_forward_number) > 0) {
					$sql .= "fax_forward_number = '$fax_forward_number', ";
				}
				else {
					$sql .= "fax_forward_number = null, ";
				}
				if (ifgroup("admin") || ifgroup("superadmin")) {
					$sql .= "fax_user_list = '$fax_user_list', ";
				}
				$sql .= "faxdescription = '$fax_description' ";
				$sql .= "where v_id = '$v_id' ";
				$sql .= "and fax_id = '$fax_id' ";
				if (!(ifgroup("admin") || ifgroup("superadmin"))) {
					$sql .= "and fax_user_list like '%|".$_SESSION["username"]."|%' ";
				}
				$db->exec(check_sql($sql));
				unset($sql);

				sync_package_v_fax();

				require_once "includes/header.php";
				echo "<meta http-equiv=\"refresh\" content=\"2;url=v_fax.php\">\n";
				echo "<div align='center'>\n";
				echo "Update Complete\n";
				echo "</div>\n";
				require_once "includes/footer.php";
				return;
			} //if ($action == "update")
		} //if ($_POST["persistformvar"] != "true")
} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (strlen($_GET['id']) > 0 && $_POST["persistformvar"] != "true") {
		$fax_id = check_str($_GET["id"]);
		$sql = "";
		$sql .= "select * from v_fax ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and fax_id = '$fax_id' ";
		if (ifgroup("superadmin")) {
			//show all fax extensions
		}
		else if (ifgroup("admin")) {
			//show all fax extensions
		}
		else {
			//show only assigned fax extensions
			$sql .= "and fax_user_list like '%|".$_SESSION["username"]."|%' ";
		}
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		if (count($result) == 0) {
			echo "access denied";
			exit;
		}
		foreach ($result as &$row) {
			//set database fields as variables
				$fax_extension = $row["faxextension"];
				$fax_name = $row["faxname"];
				$fax_email = $row["faxemail"];
				$fax_pin_number = $row["fax_pin_number"];
				$fax_caller_id_name = $row["fax_caller_id_name"];
				$fax_caller_id_number = $row["fax_caller_id_number"];
				$fax_forward_number = $row["fax_forward_number"];
				$fax_user_list = $row["fax_user_list"];
				$fax_description = $row["faxdescription"];
			//limit to one row
				break;
		}
		unset ($prepstatement);
	}

//show the header
	require_once "includes/header.php";

//fax extension form
	echo "<div align='center'>";
	echo "<form method='post' name='frm' action=''>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
	echo "<tr>\n";
	if ($action == "add") {
		echo "<td align='left' width='30%' nowrap><b>Fax Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap><b>Fax Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_fax.php'\" value='Back'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "	Extension:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='fax_extension' maxlength='255' value=\"$fax_extension\">\n";
	echo "<br />\n";
	echo "Enter the fax extension here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "	Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='fax_name' maxlength='255' value=\"$fax_name\">\n";
	echo "<br />\n";
	echo "Enter the name here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Email:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='fax_email' maxlength='255' value=\"$fax_email\">\n";
	echo "<br />\n";
	echo "	Enter the email address to send the FAX to.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	PIN Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='fax_pin_number' maxlength='255' value=\"$fax_pin_number\">\n";
	echo "<br />\n";
	echo "Enter the PIN number here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Caller ID Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='fax_caller_id_name' maxlength='255' value=\"$fax_caller_id_name\">\n";
	echo "<br />\n";
	echo "Enter the Caller ID name here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Caller ID Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='fax_caller_id_number' maxlength='255' value=\"$fax_caller_id_number\">\n";
	echo "<br />\n";
	echo "Enter the Caller ID number here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Forward Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='fax_forward_number' maxlength='255' value=\"".format_phone($fax_forward_number)."\">\n";
	echo "<br />\n";
	echo "Enter the forward number here. Used to forward the fax to a registered extension or external number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	if (ifgroup("admin") || ifgroup("superadmin")) {
		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "		User List:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		$onchange = "document.getElementById('fax_user_list').value += document.getElementById('username').value + '\\n';";
		$tablename = 'v_users'; $fieldname = 'username'; $fieldcurrentvalue = ''; $sqlwhereoptional = "where v_id = '$v_id'"; 
		echo htmlselectonchange($db, $tablename, $fieldname, $sqlwhereoptional, $fieldcurrentvalue, $onchange);
		echo "<br />\n";
		echo "Use the select list to add users to the user list. This will assign users to this extension.\n";
		echo "<br />\n";
		echo "<br />\n";
		//replace the vertical bar with a line feed to display in the textarea
		$fax_user_list = trim($fax_user_list, "|");
		$fax_user_list_array = explode("|", $fax_user_list);
		$fax_user_list = '';
		foreach($fax_user_list_array as $user){
			$fax_user_list .= trim($user)."\n";
		}
		echo "		<textarea name=\"fax_user_list\" id=\"fax_user_list\" class=\"formfld\" cols=\"30\" rows=\"3\" wrap=\"off\">$fax_user_list</textarea>\n";
		echo "		<br>\n";
		echo "Assign the users that are can manage this fax extension.\n";
		echo "<br />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='fax_description' maxlength='255' value=\"$fax_description\">\n";
	echo "<br />\n";
	echo "Enter the description here.\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "			<input type='hidden' name='fax_id' value='$fax_id'>\n";
	}
	echo "			<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";
	echo "</div>";

	echo "<br />\n";
	echo "<br />\n";

//show the footer
	require_once "includes/footer.php";
?>