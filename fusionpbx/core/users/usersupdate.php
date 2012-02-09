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
if (permission_exists("user_add") ||
	permission_exists("user_edit") || 
	permission_exists("user_delete") ||
	ifgroup("superadmin")) {
	//access allowed
}
else {
	echo "access denied";
	return;
}

//get data from the db
	if (strlen($_REQUEST["id"])> 0) {
		$id = $_REQUEST["id"];
	}
	else {
		if (strlen($_SESSION["username"]) > 0) {
			$username = $_SESSION["username"];
		}
	}

//get the username from v_users
	$sql = "";
	$sql .= "select * from v_users ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and id = '$id' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	foreach ($result as &$row) {
		$username = $row["username"];
		break; //limit to 1 row
	}
	unset ($prep_statement);

//required to be a superadmin to update an account that is a member of the superadmin group
	$superadminlist = superadminlist($db);
	if (ifsuperadmin($superadminlist, $username)) {
		if (!ifgroup("superadmin")) { 
			echo "access denied";
			return;
		}
	}

//delete the group from the user
	if ($_GET["a"] == "delete" && permission_exists("user_delete")) {
		//set the variables
			$group_id = check_str($_GET["group_id"]);
		//delete the group from the users
			$sql = "delete from v_group_members ";
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and group_id = '$group_id' ";
			$sql .= "and username = '$username' ";
			$db->exec(check_sql($sql));
		//redirect the user
			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=usersupdate.php?id=$id\">\n";
			echo "<div align='center'>Update Complete</div>";
			require_once "includes/footer.php";
			return;
	}

if (count($_POST)>0 && $_POST["persistform"] != "1") {
	$id = $_REQUEST["id"];
	$password = check_str($_POST["password"]);
	$confirmpassword = check_str($_POST["confirmpassword"]);
	$user_first_name = check_str($_POST["user_first_name"]);
	$user_last_name = check_str($_POST["user_last_name"]);
	$user_company_name = check_str($_POST["user_company_name"]);
	$user_physical_address_1 = check_str($_POST["user_physical_address_1"]);
	$user_physical_address_2 = check_str($_POST["user_physical_address_2"]);
	$user_physical_city = check_str($_POST["user_physical_city"]);
	$user_physical_state_province = check_str($_POST["user_physical_state_province"]);
	$user_physical_country = check_str($_POST["user_physical_country"]);
	$user_physical_postal_code = check_str($_POST["user_physical_postal_code"]);
	$user_mailing_address_1 = check_str($_POST["user_mailing_address_1"]);
	$user_mailing_address_2 = check_str($_POST["user_mailing_address_2"]);
	$user_mailing_city = check_str($_POST["user_mailing_city"]);
	$user_mailing_state_province = check_str($_POST["user_mailing_state_province"]);
	$user_mailing_country = check_str($_POST["user_mailing_country"]);
	$user_mailing_postal_code = check_str($_POST["user_mailing_postal_code"]);
	$user_billing_address_1 = check_str($_POST["user_billing_address_1"]);
	$user_billing_address_2 = check_str($_POST["user_billing_address_2"]);
	$user_billing_city = check_str($_POST["user_billing_city"]);
	$user_billing_state_province = check_str($_POST["user_billing_state_province"]);
	$user_billing_country = check_str($_POST["user_billing_country"]);
	$user_billing_postal_code = check_str($_POST["user_billing_postal_code"]);
	$user_shipping_address_1 = check_str($_POST["user_shipping_address_1"]);
	$user_shipping_address_2 = check_str($_POST["user_shipping_address_2"]);
	$user_shipping_city = check_str($_POST["user_shipping_city"]);
	$user_shipping_state_province = check_str($_POST["user_shipping_state_province"]);
	$user_shipping_country = check_str($_POST["user_shipping_country"]);
	$user_shipping_postal_code = check_str($_POST["user_shipping_postal_code"]);
	$user_url = check_str($_POST["user_url"]);
	$user_phone_1 = check_str($_POST["user_phone_1"]);
	$user_phone_1_ext = check_str($_POST["user_phone_1_ext"]);
	$user_phone_2 = check_str($_POST["user_phone_2"]);
	$user_phone_2_ext = check_str($_POST["user_phone_2_ext"]);
	$user_phone_mobile = check_str($_POST["user_phone_mobile"]);
	$user_phone_fax = check_str($_POST["user_phone_fax"]);
	$user_status = check_str($_POST["user_status"]);
	$user_template_name = check_str($_POST["user_template_name"]);
	$user_time_zone = check_str($_POST["user_time_zone"]);
	$user_email = check_str($_POST["user_email"]);
	$groupmember = check_str($_POST["groupmember"]);

	//if (strlen($password) == 0) { $msgerror .= "Password cannot be blank.<br>\n"; }
	if (strlen($username) == 0) { $msgerror .= "Please provide the username.<br>\n"; }
	if ($password != $confirmpassword) { $msgerror .= "Passwords did not match.<br>\n"; }
	if (strlen($user_first_name) == 0) { $msgerror .= "Please provide a first name.<br>\n"; }
	if (strlen($user_last_name) == 0) { $msgerror .= "Please provide a last name $user_last_name.<br>\n"; }
	//if (strlen($user_company_name) == 0) { $msgerror .= "Please provide a company name.<br>\n"; }
	//if (strlen($user_physical_address_1) == 0) { $msgerror .= "Please provide a address.<br>\n"; }
	//if (strlen($user_physical_address_2) == 0) { $msgerror .= "Please provide a user_physical_address_2.<br>\n"; }
	//if (strlen($user_physical_city) == 0) { $msgerror .= "Please provide a city.<br>\n"; }
	//if (strlen($user_physical_state_province) == 0) { $msgerror .= "Please provide a state.<br>\n"; }
	//if (strlen($user_physical_country) == 0) { $msgerror .= "Please provide a country.<br>\n"; }
	//if (strlen($user_physical_postal_code) == 0) { $msgerror .= "Please provide a postal code.<br>\n"; }
	//if (strlen($user_url) == 0) { $msgerror .= "Please provide a url.<br>\n"; }
	//if (strlen($user_phone_1) == 0) { $msgerror .= "Please provide a phone number.<br>\n"; }
	//if (strlen($user_phone_2) == 0) { $msgerror .= "Please provide a user_phone_2.<br>\n"; }
	//if (strlen($user_phone_mobile) == 0) { $msgerror .= "Please provide a mobile number.<br>\n"; }
	//if (strlen($user_phone_emergency_mobile) == 0) { $msgerror .= "Please provide a emergency mobile.<br>\n"; }
	//if (strlen($user_phone_fax) == 0) { $msgerror .= "Please provide a fax number.<br>\n"; }
	//if (strlen($user_email) == 0) { $msgerror .= "Please provide an email.<br>\n"; }
	//if (strlen($user_email_emergency) == 0) { $msgerror .= "Please provide an emergency email.<br>\n"; }
	//if (strlen($user_time_zone) == 0) { $msgerror .= "Please provide an time zone.<br>\n"; }

	if (strlen($msgerror) > 0) {
		require_once "includes/header.php";
		echo "<div align='center'>";
		echo "<table><tr><td>";
		echo $msgerror;
		echo "</td></tr></table>";
		echo "<br />\n";
		require_once "includes/persistform.php";
		echo persistform($_POST);
		echo "</div>";
		require_once "includes/footer.php";
		return;
	}

	//assign the user to the group
		if (strlen($_REQUEST["group_id"]) > 0) {
			$sqlinsert = "insert into v_group_members ";
			$sqlinsert .= "(";
			$sqlinsert .= "domain_uuid, ";
			$sqlinsert .= "group_id, ";
			$sqlinsert .= "username ";
			$sqlinsert .= ")";
			$sqlinsert .= "values ";
			$sqlinsert .= "(";
			$sqlinsert .= "'$domain_uuid', ";
			$sqlinsert .= "'".$_REQUEST["group_id"]."', ";
			$sqlinsert .= "'$username' ";
			$sqlinsert .= ")";
			if ($_REQUEST["group_id"] == "superadmin") {
				//only a user in the superadmin group can add other users to that group
				if (ifgroup("superadmin")) {
					$db->exec($sqlinsert);
				}
			}
			else {
				$db->exec($sqlinsert);
			}
		}

	//if the template has not been assigned by the superadmin
		if (strlen($_SESSION['domain']['template']['name']) == 0) {
			//set the session theme for the active user
			if ($_SESSION["username"] == $username) {
				$_SESSION['domain']['template']['name'] = $user_template_name;
			}
		}

	//sql update
		$sql  = "update v_users set ";
		if (ifgroup("admin") && strlen($_POST["username"])> 0) {
			$sql .= "username = '$username', ";
		}
		if (strlen($password) > 0 && $confirmpassword == $password) {
			//salt used with the password to create a one way hash
				$salt = generate_password('20', '4');
			//set the password
				$sql .= "password = '".md5($salt.$password)."', ";
				$sql .= "salt = '".$salt."', ";
		}
		$sql .= "user_first_name = '$user_first_name', ";
		$sql .= "user_last_name = '$user_last_name', ";
		$sql .= "user_company_name = '$user_company_name', ";
		$sql .= "user_physical_address_1 = '$user_physical_address_1', ";
		$sql .= "user_physical_address_2 = '$user_physical_address_2', ";
		$sql .= "user_physical_city = '$user_physical_city', ";
		$sql .= "user_physical_state_province = '$user_physical_state_province', ";
		$sql .= "user_physical_country = '$user_physical_country', ";
		$sql .= "user_physical_postal_code = '$user_physical_postal_code', ";
		$sql .= "user_mailing_address_1 = '$user_mailing_address_1', ";
		$sql .= "user_mailing_address_2 = '$user_mailing_address_2', ";
		$sql .= "user_mailing_city = '$user_mailing_city', ";
		$sql .= "user_mailing_state_province = '$user_mailing_state_province', ";
		$sql .= "user_mailing_country = '$user_mailing_country', ";
		$sql .= "user_mailing_postal_code = '$user_mailing_postal_code', ";
		$sql .= "user_billing_address_1 = '$user_billing_address_1', ";
		$sql .= "user_billing_address_2 = '$user_billing_address_2', ";
		$sql .= "user_billing_city = '$user_billing_city', ";
		$sql .= "user_billing_state_province = '$user_billing_state_province', ";
		$sql .= "user_billing_country = '$user_billing_country', ";
		$sql .= "user_billing_postal_code = '$user_billing_postal_code', ";
		$sql .= "user_shipping_address_1 = '$user_shipping_address_1', ";
		$sql .= "user_shipping_address_2 = '$user_shipping_address_2', ";
		$sql .= "user_shipping_city = '$user_shipping_city', ";
		$sql .= "user_shipping_state_province = '$user_shipping_state_province', ";
		$sql .= "user_shipping_country = '$user_shipping_country', ";
		$sql .= "user_shipping_postal_code = '$user_shipping_postal_code', ";
		$sql .= "user_url = '$user_url', ";
		$sql .= "user_phone_1 = '$user_phone_1', ";
		$sql .= "user_phone_1_ext = '$user_phone_1_ext', ";
		$sql .= "user_phone_2 = '$user_phone_2', ";
		$sql .= "user_phone_2_ext = '$user_phone_2_ext', ";
		$sql .= "user_phone_mobile = '$user_phone_mobile', ";
		$sql .= "user_phone_fax = '$user_phone_fax', ";
		$sql .= "user_status = '$user_status', ";
		$sql .= "user_template_name = '$user_template_name', ";
		$sql .= "user_time_zone = '$user_time_zone', ";
		$sql .= "user_email = '$user_email' ";
		if (strlen($id)> 0) {
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and id = $id ";
		}
		else {
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and username = '$username' ";
		}
		$count = $db->exec(check_sql($sql));

	//update the user_status
		$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
		$switch_cmd .= "callcenter_config agent set status ".$username."@".$v_domain." '".$user_status."'";
		$switch_result = event_socket_request($fp, 'api '.$switch_cmd);

	//update the user state
		$cmd = "api callcenter_config agent set state ".$username."@".$v_domain." Waiting";
		$response = event_socket_request($fp, $cmd);

	//clear the template so it will rebuild in case the template was changed
		$_SESSION["template_content"] = '';

	//redirect the user
		require_once "includes/header.php";
		if (ifgroup("admin")) {
			echo "<meta http-equiv=\"refresh\" content=\"2;url=usersupdate.php?id=$id\">\n";
		}
		else {
			echo "<meta http-equiv=\"refresh\" content=\"2;url=usersupdate.php?id=$id\">\n";
		}
		echo "<div align='center'>Update Complete</div>";
		require_once "includes/footer.php";
		return;
}
else {
	$sql = "";
	$sql .= "select * from v_users ";
	//allow admin access
	if (ifgroup("admin") || ifgroup("superadmin")) {
		if (strlen($id)> 0) {
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and id = '$id' ";
		}
		else {
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and username = '$username' ";
		}
	}
	else {
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and username = '$username' ";
	}
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	foreach ($result as &$row) {
		if (ifgroup("admin")) {
			$username = $row["username"];
		}
		$password = $row["password"];
		$user_first_name = $row["user_first_name"];
		$user_last_name = $row["user_last_name"];
		$user_company_name = $row["user_company_name"];
		$user_physical_address_1 = $row["user_physical_address_1"];
		$user_physical_address_2 = $row["user_physical_address_2"];
		$user_physical_city = $row["user_physical_city"];
		$user_physical_state_province = $row["user_physical_state_province"];
		$user_physical_country = $row["user_physical_country"];
		$user_physical_postal_code = $row["user_physical_postal_code"];
		$user_mailing_address_1 = $row["user_mailing_address_1"];
		$user_mailing_address_2 = $row["user_mailing_address_2"];
		$user_mailing_city = $row["user_mailing_city"];
		$user_mailing_state_province = $row["user_mailing_state_province"];
		$user_mailing_country = $row["user_mailing_country"];
		$user_mailing_postal_code = $row["user_mailing_postal_code"];
		$user_billing_address_1 = $row["user_billing_address_1"];
		$user_billing_address_2 = $row["user_billing_address_2"];
		$user_billing_city = $row["user_billing_city"];
		$user_billing_state_province = $row["user_billing_state_province"];
		$user_billing_country = $row["user_billing_country"];
		$user_billing_postal_code = $row["user_billing_postal_code"];
		$user_shipping_address_1 = $row["user_shipping_address_1"];
		$user_shipping_address_2 = $row["user_shipping_address_2"];
		$user_shipping_city = $row["user_shipping_city"];
		$user_shipping_state_province = $row["user_shipping_state_province"];
		$user_shipping_country = $row["user_shipping_country"];
		$user_shipping_postal_code = $row["user_shipping_postal_code"];
		$user_url = $row["user_url"];
		$user_phone_1 = $row["user_phone_1"];
		$user_phone_1_ext = $row["user_phone_1_ext"];
		$user_phone_2 = $row["user_phone_2"];
		$user_phone_2_ext = $row["user_phone_2_ext"];
		$user_phone_mobile = $row["user_phone_mobile"];
		$user_phone_fax = $row["user_phone_fax"];
		$user_email = $row["user_email"];
		$user_status = $row["user_status"];
		$user_template_name = $row["user_template_name"];
		$user_time_zone = $row["user_time_zone"];
		break; //limit to 1 row
	}

	//get the groups the user is a member of
	//groupmemberlist function defined in config.php
	$groupmemberlist = groupmemberlist($db, $username);
}

//include the header
	require_once "includes/header.php";

//show the content
	$tablewidth ='width="100%"';
	echo "<form method='post' action=''>";
	echo "<br />\n";

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr>\n";
	echo "<td>\n";

	echo "<table $tablewidth cellpadding='3' cellspacing='0' border='0'>";
	echo "<td align='left' width='90%' nowrap><b>User Manager</b></td>\n";
	echo "<td nowrap='nowrap'>\n";
	echo "	<input type='submit' name='submit' class='btn' value='Save'>";
	echo "	<input type='button' class='btn' onclick=\"window.location='index.php'\" value='Back'>";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' colspan='2'>\n";
	echo "	Edit user information and group membership. \n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<br />\n";

	echo "<table $tablewidth cellpadding='6' cellspacing='0' border='0'>";
	echo "<tr>\n";
	echo "	<th class='th' colspan='2' align='left'>User Info</th>\n";
	echo "</tr>\n";

	echo "	<tr>";
	echo "		<td width='30%' class='vncellreq'>Username:</td>";
	echo "		<td width='70%' class='vtable'>$username</td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='vncell'>Password:</td>";
	echo "		<td class='vtable'><input type='password' autocomplete='off' class='formfld' name='password' value=\"\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Confirm Password:</td>";
	echo "		<td class='vtable'><input type='password' autocomplete='off' class='formfld' name='confirmpassword' value=\"\"></td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='vncell'>First Name:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_first_name' value=\"$user_first_name\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Last Name:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_last_name' value=\"$user_last_name\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Company Name:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_company_name' value=\"$user_company_name\"></td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='vncell' valign='top'>Groups:</td>";
	echo "		<td class='vtable'>";

	echo "<table width='52%'>\n";
	$sql = "SELECT * FROM v_group_members ";
	$sql .= "where domain_uuid=:domain_uuid ";
	$sql .= "and username=:username ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->bindParam(':domain_uuid', $domain_uuid);
	$prep_statement->bindParam(':username', $username);
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	$result_count = count($result);
	foreach($result as $field) {
		if (strlen($field['group_id']) > 0) {
			echo "<tr>\n";
			echo "	<td class='vtable'>".$field['group_id']."</td>\n";
			echo "	<td>\n";
			if (permission_exists('group_member_delete') || ifgroup("superadmin")) {
				echo "		<a href='usersupdate.php?id=".$id."&domain_uuid=".$domain_uuid."&group_id=".$field['group_id']."&a=delete' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
			}
			echo "	</td>\n";
			echo "</tr>\n";
		}
	}
	echo "</table>\n";

	echo "<br />\n";
	$sql = "SELECT * FROM v_groups ";
	$sql .= "where domain_uuid = '".$domain_uuid."' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	echo "<select name=\"group_id\" class='frm'>\n";
	echo "<option value=\"\"></option>\n";
	$result = $prep_statement->fetchAll();
	foreach($result as $field) {
		if ($field['group_id'] == "superadmin") {
			//only show the superadmin group to other users in the superadmin group
			if (ifgroup("superadmin")) {
				echo "<option value='".$field['group_id']."'>".$field['group_id']."</option>\n";
			}
		}
		else {
			echo "<option value='".$field['group_id']."'>".$field['group_id']."</option>\n";
		}
	}
	echo "</select>";
	echo "<input type=\"submit\" class='btn' value=\"Add\">\n";
	unset($sql, $result);
	echo "		</td>";
	echo "	</tr>";
	echo "</table>";

	echo "<br>";
	echo "<br>";

	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "<tr>\n";
	echo "	<th class='th' colspan='2' align='left'>Physical Address</th>\n";
	echo "</tr>\n";
	echo "	<tr>";
	echo "		<td class='vncell' width='30%'>Address 1:</td>";
	echo "		<td class='vtable' width='70%'><input type='text' class='formfld' name='user_physical_address_1' value=\"$user_physical_address_1\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Address 2:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_physical_address_2' value=\"$user_physical_address_2\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>City:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_physical_city' value=\"$user_physical_city\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>State/Province:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_physical_state_province' value=\"$user_physical_state_province\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Country:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_physical_country' value=\"$user_physical_country\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Postal Code:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_physical_postal_code' value=\"$user_physical_postal_code\"></td>";
	echo "	</tr>";
	echo "    </table>";

	echo "<br>";
	echo "<br>";

	/*
	echo "<b>Mailing Address</b><br>";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>";
	echo "		<td class='vncell' width='40%'>Address 1:</td>";
	echo "		<td class='vtable' width='60%'><input type='text' class='formfld' name='user_mailing_address_1' value='$user_mailing_address_1'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Address 2:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_mailing_address_2' value='$user_mailing_address_2'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>City:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_mailing_city' value='$user_mailing_city'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>State/Province:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_mailing_state_province' value='$user_mailing_state_province'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Country:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_mailing_country' value='$user_mailing_country'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Postal Code:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_mailing_postal_code' value='$user_mailing_postal_code'></td>";
	echo "	</tr>";
	echo "    </table>";

	echo "<br>";
	echo "<br>";

	echo "<b>Billing Address</b><br>";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>";
	echo "		<td class='vncell' width='30%'>Address 1:</td>";
	echo "		<td class='vtable' width='70%'><input type='text' class='formfld' name='user_billing_address_1' value='$user_billing_address_1'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Address 2:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_billing_address_2' value='$user_billing_address_2'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>City:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_billing_city' value='$user_billing_city'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>State/Province:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_billing_state_province' value='$user_billing_state_province'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Country:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_billing_country' value='$user_billing_country'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Postal Code:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_billing_postal_code' value='$user_billing_postal_code'></td>";
	echo "	</tr>";
	echo "    </table>";

	echo "<br>";
	echo "<br>";

	echo "<b>Shipping Address</b><br>";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>";
	echo "		<td class='vncell' width='30%'>Address 1:</td>";
	echo "		<td class='vtable' width='70%'><input type='text' class='formfld' name='user_shipping_address_1' value='$user_shipping_address_1'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Address 2:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_shipping_address_2' value='$user_shipping_address_2'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>City:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_shipping_city' value='$user_shipping_city'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>State/Province:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_shipping_state_province' value='$user_shipping_state_province'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Country:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_shipping_country' value='$user_shipping_country'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Postal Code:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_shipping_postal_code' value='$user_shipping_postal_code'></td>";
	echo "	</tr>";
	echo "    </table>";

	echo "<br>";
	echo "<br>";
	*/

	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>\n";
	echo "	<th class='th' colspan='2' align='left'>Additional Info</th>\n";
	echo "	</tr>\n";
	echo "	<tr>";
	echo "		<td class='vncell'width='30%'>Website:</td>";
	echo "		<td class='vtable' width='70%'><input type='text' class='formfld' name='user_url' value=\"$user_url\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Phone 1:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_phone_1' value=\"$user_phone_1\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Phone 1 Ext:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_phone_1_ext' value=\"$user_phone_1_ext\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Phone 2:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_phone_2' value=\"$user_phone_2\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Phone 2 Ext:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_phone_2_ext' value=\"$user_phone_2_ext\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Mobile:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_phone_mobile' value=\"$user_phone_mobile\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Fax:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_phone_fax' value=\"$user_phone_fax\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Email:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_email' value=\"$user_email\"></td>";
	echo "	</tr>";
	if ($_SESSION['user_status_display'] == "false") {
		//hide the user_status when it is set to false
	}
	else {
		echo "	<tr>\n";
		echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
		echo "		Status:\n";
		echo "	</td>\n";
		echo "	<td class=\"vtable\">\n";
		$cmd = "'/mod/calls_active/v_calls_exec.php?cmd=callcenter_config+agent+set+status+".$_SESSION['username']."@".$v_domain."+'+this.value";
		echo "		<select id='user_status' name='user_status' class='formfld' style='' onchange=\"send_cmd($cmd);\">\n";
		echo "		<option value=''></option>\n";
		if ($user_status == "Available") {
			echo "		<option value='Available' selected='selected'>Available</option>\n";
		}
		else {
			echo "		<option value='Available'>Available</option>\n";
		}
		if ($user_status == "Available (On Demand)") {
			echo "		<option value='Available (On Demand)' selected='selected'>Available (On Demand)</option>\n";
		}
		else {
			echo "		<option value='Available (On Demand)'>Available (On Demand)</option>\n";
		}
		if ($user_status == "Logged Out") {
			echo "		<option value='Logged Out' selected='selected'>Logged Out</option>\n";
		}
		else {
			echo "		<option value='Logged Out'>Logged Out</option>\n";
		}
		if ($user_status == "On Break") {
			echo "		<option value='On Break' selected='selected'>On Break</option>\n";
		}
		else {
			echo "		<option value='On Break'>On Break</option>\n";
		}
		if ($user_status == "Do Not Disturb") {
			echo "		<option value='Do Not Disturb' selected='selected'>Do Not Disturb</option>\n";
		}
		else {
			echo "		<option value='Do Not Disturb'>Do Not Disturb</option>\n";
		}
		echo "		</select>\n";
		echo "		<br />\n";
		echo "		Select a the user status.<br />\n";
		echo "	</td>\n";
		echo "	</tr>\n";
	}

	//if the template has not been assigned by the superadmin
		if (strlen($_SESSION['domain']['template']['name']) == 0) {
			echo "	<tr>\n";
			echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
			echo "		Template: \n";
			echo "	</td>\n";
			echo "	<td class=\"vtable\">\n";
			echo "		<select id='user_template_name' name='user_template_name' class='formfld' style=''>\n";
			echo "		<option value=''></option>\n";
			$theme_dir = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/themes';
			if ($handle = opendir($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/themes')) {
				while (false !== ($dir_name = readdir($handle))) {
					if ($dir_name != "." && $dir_name != ".." && $dir_name != ".svn" && is_dir($theme_dir.'/'.$dir_name)) {
						$dir_label = str_replace('_', ' ', $dir_name);
						$dir_label = str_replace('-', ' ', $dir_label);
						if ($dir_name == $user_template_name) {
							echo "		<option value='$dir_name' selected='selected'>$dir_label</option>\n";
						}
						else {
							echo "		<option value='$dir_name'>$dir_label</option>\n";
						}
					}
				}
				closedir($handle);
			}
			echo "	</select>\n";
			echo "	<br />\n";
			echo "	Select a template to set as the default and then press save.<br />\n";
			echo "	</td>\n";
			echo "	</tr>\n";
		}

	echo "	<tr>\n";
	echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
	echo "		Time Zone: \n";
	echo "	</td>\n";
	echo "	<td class=\"vtable\" align='left'>\n";
	echo "		<select id='user_time_zone' name='user_time_zone' class='formfld' style=''>\n";
	echo "		<option value=''></option>\n";
	//$list = DateTimeZone::listAbbreviations();
    $time_zone_identifiers = DateTimeZone::listIdentifiers();
	$previous_category = '';
	$x = 0;
	foreach ($time_zone_identifiers as $key => $row) {
		$tz = explode("/", $row);
		$category = $tz[0];
		if ($category != $previous_category) {
			if ($x > 0) {
				echo "		</optgroup>\n";
			}
			echo "		<optgroup label='".$category."'>\n";
		}
		if ($row == $user_time_zone) {
			echo "			<option value='".$row."' selected='selected'>".$row."</option>\n";
		}
		else {
			echo "			<option value='".$row."'>".$row."</option>\n";
		}
		$previous_category = $category;
		$x++;
	}
	echo "		</select>\n";
	echo "		<br />\n";
	echo "		Select the default time zone.<br />\n";
	echo "	</td>\n";
	echo "	</tr>\n";

	echo "	</table>";
	echo "<br>";

	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth>";
	echo "	<tr>";
	echo "		<td colspan='2' align='right'>";
	echo "			<input type='hidden' name='id' value=\"$id\">";
	echo "			<input type='hidden' name='username' value=\"$username\">";
	echo "			<input type='submit' name='submit' class='btn' value='Save'>";
	echo "		</td>";
	echo "	</tr>";
	echo "</table>";

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";
	echo "</form>";

//include the footer
	require_once "includes/footer.php";

?>
