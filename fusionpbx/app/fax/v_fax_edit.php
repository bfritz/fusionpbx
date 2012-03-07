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
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists('fax_extension_add') || permission_exists('fax_extension_edit') || permission_exists('fax_extension_delete')) {
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
		$v_fax_dir = $_SESSION['switch']['storage']['dir'].'/fax/'.$_SESSION['domain_name'];
	}
	else {
		$v_fax_dir = $_SESSION['switch']['storage']['dir'].'/fax';
	}

//get the fax extension
	if (strlen($fax_extension) > 0) {
		//set the fax directories. example /usr/local/freeswitch/storage/fax/329/inbox
			$dir_fax_inbox = $v_fax_dir.'/'.$fax_extension.'/inbox';
			$dir_fax_sent = $v_fax_dir.'/'.$fax_extension.'/sent';
			$dir_fax_temp = $v_fax_dir.'/'.$fax_extension.'/temp';

		//make sure the directories exist
			if (!is_dir($_SESSION['switch']['storage']['dir'])) {
				mkdir($_SESSION['switch']['storage']['dir']);
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
		$fax_uuid = check_str($_REQUEST["id"]);
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
		$fax_description = check_str($_POST["fax_description"]);
	}

//delete the user from the v_extension_users
	if ($_GET["a"] == "delete" && permission_exists("fax_extension_delete")) {
		//set the variables
			$user_uuid = check_str($_REQUEST["user_uuid"]);
			$fax_uuid = check_str($_REQUEST["id"]);
		//delete the group from the users
			$sql = "delete from v_fax_users ";
			$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
			$sql .= "and fax_uuid = '".$fax_uuid."' ";
			$sql .= "and user_uuid = '".$user_uuid."' ";
			$db->exec(check_sql($sql));
		//redirect the browser
			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_fax_edit.php?id=$fax_uuid\">\n";
			echo "<div align='center'>Delete Complete</div>";
			require_once "includes/footer.php";
			return;
	}

//assign the extension to the user
	if (strlen($_REQUEST["user_uuid"]) > 0 && strlen($_REQUEST["id"]) > 0 && $_GET["a"] != "delete") {
		//set the variables
			$user_uuid = check_str($_REQUEST["user_uuid"]);
			$fax_uuid = check_str($_REQUEST["id"]);
		//assign the user to the fax extension
			$sql_insert = "insert into v_fax_users ";
			$sql_insert .= "(";
			$sql_insert .= "fax_user_uuid, ";
			$sql_insert .= "domain_uuid, ";
			$sql_insert .= "fax_uuid, ";
			$sql_insert .= "user_uuid ";
			$sql_insert .= ")";
			$sql_insert .= "values ";
			$sql_insert .= "(";
			$sql_insert .= "'".uuid()."', ";
			$sql_insert .= "'".$_SESSION['domain_uuid']."', ";
			$sql_insert .= "'".$fax_uuid."', ";
			$sql_insert .= "'".$user_uuid."' ";
			$sql_insert .= ")";
			$db->exec($sql_insert);
		//redirect the browser
			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_fax_edit.php?id=$fax_uuid\">\n";
			echo "<div align='center'>Add Complete</div>";
			require_once "includes/footer.php";
			return;
	}

//clear file status cache
	clearstatcache(); 

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update" && permission_exists('fax_extension_edit')) {
		$fax_uuid = check_str($_POST["fax_uuid"]);
	}

	//check for all required data
		if (strlen($fax_extension) == 0) { $msg .= "Please provide: Extension<br>\n"; }
		if (strlen($fax_name) == 0) { $msg .= "Please provide: A file to Fax<br>\n"; }
		//if (strlen($fax_email) == 0) { $msg .= "Please provide: Email<br>\n"; }
		//if (strlen($fax_pin_number) == 0) { $msg .= "Please provide: Pin Number<br>\n"; }
		//if (strlen($fax_caller_id_name) == 0) { $msg .= "Please provide: Caller ID Name<br>\n"; }
		//if (strlen($fax_caller_id_number) == 0) { $msg .= "Please provide: Caller ID Number<br>\n"; }
		//if (strlen($fax_forward_number) == 0) { $msg .= "Please provide: Forward Number<br>\n"; }
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
				$fax_uuid = uuid();
				$sql = "insert into v_fax ";
				$sql .= "(";
				$sql .= "domain_uuid, ";
				$sql .= "fax_uuid, ";
				$sql .= "fax_extension, ";
				$sql .= "fax_name, ";
				$sql .= "fax_email, ";
				$sql .= "fax_pin_number, ";
				$sql .= "fax_caller_id_name, ";
				$sql .= "fax_caller_id_number, ";
				if (strlen($fax_forward_number) > 0) {
					$sql .= "fax_forward_number, ";
				}
				$sql .= "fax_description ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'".$_SESSION['domain_uuid']."', ";
				$sql .= "'$fax_uuid', ";
				$sql .= "'$fax_extension', ";
				$sql .= "'$fax_name', ";
				$sql .= "'$fax_email', ";
				$sql .= "'$fax_pin_number', ";
				$sql .= "'$fax_caller_id_name', ";
				$sql .= "'$fax_caller_id_number', ";
				if (strlen($fax_forward_number) > 0) {
					$sql .= "'$fax_forward_number', ";
				}
				$sql .= "'$fax_description' ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);

				save_fax_xml();

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
				$sql .= "fax_extension = '$fax_extension', ";
				$sql .= "fax_name = '$fax_name', ";
				$sql .= "fax_email = '$fax_email', ";
				$sql .= "fax_pin_number = '$fax_pin_number', ";
				$sql .= "fax_caller_id_name = '$fax_caller_id_name', ";
				$sql .= "fax_caller_id_number = '$fax_caller_id_number', ";
				if (strlen($fax_forward_number) > 0) {
					$sql .= "fax_forward_number = '$fax_forward_number', ";
				}
				else {
					$sql .= "fax_forward_number = null, ";
				}
				$sql .= "fax_description = '$fax_description' ";
				$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
				$sql .= "and fax_uuid = '$fax_uuid' ";
				$db->exec(check_sql($sql));
				unset($sql);

				save_fax_xml();

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
		$fax_uuid = check_str($_GET["id"]);
		$sql = "";
		$sql .= "select * from v_fax ";
		$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
		$sql .= "and fax_uuid = '$fax_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		if (count($result) == 0) {
			echo "access denied";
			exit;
		}
		foreach ($result as &$row) {
			//set database fields as variables
				$fax_extension = $row["fax_extension"];
				$fax_name = $row["fax_name"];
				$fax_email = $row["fax_email"];
				$fax_pin_number = $row["fax_pin_number"];
				$fax_caller_id_name = $row["fax_caller_id_name"];
				$fax_caller_id_number = $row["fax_caller_id_number"];
				$fax_forward_number = $row["fax_forward_number"];
				$fax_description = $row["fax_description"];
			//limit to one row
				break;
		}
		unset ($prep_statement);
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
	echo "	Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='fax_name' maxlength='255' value=\"$fax_name\">\n";
	echo "<br />\n";
	echo "Enter the name here.\n";
	echo "</td>\n";
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

	if (if_group("admin") || if_group("superadmin")) {
		if ($action == "update") {
			echo "	<tr>";
			echo "		<td class='vncell' valign='top'>User List:</td>";
			echo "		<td class='vtable'>";

			echo "			<table width='52%'>\n";
			$sql = "SELECT * FROM v_fax_users as e, v_users as u ";
			$sql .= "where e.user_uuid = u.user_uuid  ";
			$sql .= "and e.domain_uuid = '".$_SESSION['domain_uuid']."' ";
			$sql .= "and e.fax_uuid = '".$fax_uuid."' ";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->execute();
			$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
			$result_count = count($result);
			foreach($result as $field) {
				echo "			<tr>\n";
				echo "				<td class='vtable'>".$field['username']."</td>\n";
				echo "				<td>\n";
				echo "					<a href='v_fax_edit.php?id=".$fax_uuid."&domain_uuid=".$_SESSION['domain_uuid']."&user_uuid=".$field['user_uuid']."&a=delete' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
				echo "				</td>\n";
				echo "			</tr>\n";
			}
			echo "			</table>\n";

			echo "			<br />\n";
			$sql = "SELECT * FROM v_users ";
			$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->execute();
			echo "			<select name=\"user_uuid\" class='frm'>\n";
			echo "			<option value=\"\"></option>\n";
			$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
			foreach($result as $field) {
				echo "			<option value='".$field['user_uuid']."'>".$field['username']."</option>\n";
			}
			echo "			</select>";
			echo "			<input type=\"submit\" class='btn' value=\"Add\">\n";
			unset($sql, $result);
			echo "			<br>\n";
			echo "			Assign the users that are can manage this fax extension.\n";
			echo "			<br />\n";
			echo "		</td>";
			echo "	</tr>";
		}
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
		echo "			<input type='hidden' name='fax_uuid' value='$fax_uuid'>\n";
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