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
if (permission_exists("user_account_settings_view")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//get data from the db
	if (strlen($_GET["id"])> 0) {
		$id = $_GET["id"];
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
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$username = $row["username"];
		break; //limit to 1 row
	}
	unset ($prepstatement);

//required to be a superadmin to update an account that is a member of the superadmin group
	$superadminlist = superadminlist($db);
	if (ifsuperadmin($superadminlist, $username)) {
		if (!ifgroup("superadmin")) { 
			echo "access denied";
			return;
		}
	}

if (count($_POST)>0 && $_POST["persistform"] != "1") {
	$id = $_POST["id"];
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
	$user_template_name = check_str($_POST["user_template_name"]);
	$user_email = check_str($_POST["user_email"]);
	$groupmember = check_str($_POST["groupmember"]);

	//if (strlen($password) == 0) { $msgerror .= "Password cannot be blank.<br>\n"; }
	if ($password != $confirmpassword) { $msgerror .= "Passwords did not match.<br>\n"; }
	//if (strlen($user_first_name) == 0) { $msgerror .= "Please provide a first name.<br>\n"; }
	//if (strlen($user_last_name) == 0) { $msgerror .= "Please provide a last name $user_last_name.<br>\n"; }
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

	//if the template has not been assigned by the superadmin
		if (strlen($_SESSION["v_template_name"]) == 0) {
			//set the session theme for the user
				$_SESSION["template_name"] = $user_template_name;
			//clear the template so it will rebuild in case the template was changed
				$_SESSION["template_content"] = '';
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
		$sql .= "user_template_name = '$user_template_name', ";
		$sql .= "user_email = '$user_email' ";
		if (strlen($id)> 0) {
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and id = $id ";
		}
		else {
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and username = '$username' ";
		}
		if (permission_exists("user_account_settings_edit")) {
			$count = $db->exec(check_sql($sql));
		}

	//redirect the browser
		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=".PROJECT_PATH."/\">\n";
		echo "<div align='center'>Update Complete</div>";
		require_once "includes/footer.php";
		return;
}
else {

	$sql = "";
	$sql .= "select * from v_users ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and username = '$username' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
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
		$user_template_name = $row["user_template_name"];
		break; //limit to 1 row
	}

	//get the groups the user is a member of
	//groupmemberlist function defined in config.php
	$groupmemberlist = groupmemberlist($db, $username);
	//echo "groupmemberlist $groupmemberlist";

}

//include the header
	require_once "includes/header.php";

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";

	$tablewidth ='width="100%"';
	echo "<form method='post' action=''>";

	echo "<br>";
	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
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
	echo "    </table>";
	echo "    </div>";
	echo "<br>";

	echo "<div class='' style='padding:10px;'>\n";
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
	echo "    </div>";
	echo "<br>";

	/*
	echo "<b>Mailing Address</b><br>";
	echo "<div class='' style='padding:10px;'>\n";
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
	echo "    </div>";
	echo "<br>";

	echo "<b>Billing Address</b><br>";
	echo "<div class='' style='padding:10px;'>\n";
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
	echo "    </div>";
	echo "<br>";

	echo "<b>Shipping Address</b><br>";
	echo "<div class='' style='padding:10px;'>\n";
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
	echo "    </div>";
	echo "<br>";
	*/

	echo "<div class='' style='padding:10px;'>\n";
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

	//if the template has not been assigned by the superadmin
	if (strlen($_SESSION["v_template_name"]) == 0) {
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
		echo "		</select>\n";
		echo "		<br />\n";
		echo "		Select a template to set as the default and then press save.<br />\n";
		echo "	</td>\n";
		echo "	</tr>\n";
	}

	echo "    </table>";
	echo "    </div>";
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
	echo "</form>";

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

//include the footer
	require_once "includes/footer.php";
?>
