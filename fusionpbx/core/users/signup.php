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

if (ifgroup("admin") || ifgroup("superadmin")) {
	//access allowed
}
else {
	echo "access denied";
	return;
}

$username = check_str($_POST["username"]);
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
//$user_phone_emergency_mobile = check_str($_POST["user_phone_emergency_mobile"]);
$user_phone_fax = check_str($_POST["user_phone_fax"]);
$user_email = check_str($_POST["user_email"]);
$user_email_emergency = check_str($_POST["user_email_emergency"]);


if (count($_POST)>0 && check_str($_POST["persistform"]) != "1") {

	$msgerror = '';

	//--- begin captcha verification ---------------------
		//session_start(); //make sure sessions are started
		if (strtolower($_SESSION["captcha"]) != strtolower($_REQUEST["captcha"]) || strlen($_SESSION["captcha"]) == 0) {
			//$msgerror .= "Captcha Verification Failed<br>\n";
		}
		else {
			//echo "verified";
		}
	//--- end captcha verification -----------------------

	//username is already used.
	if (strlen($username) == 0) {
		$msgerror .= "Please provide a Username.<br>\n";
	}
	else {
		$sql = "SELECT * FROM v_users ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and username = '$username' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		if (count($prep_statement->fetchAll()) > 0) {
			$msgerror .= "Please choose a different Username.<br>\n";
		}
	}

	if (strlen($password) == 0) { $msgerror .= "Password cannot be blank.<br>\n"; }
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
	if (strlen($user_email) == 0) { $msgerror .= "Please provide an email.<br>\n"; }
	//if (strlen($user_email_emergency) == 0) { $msgerror .= "Please provide an emergency email.<br>\n"; }

	if (strlen($msgerror) > 0) {
		require_once "includes/header.php";
		echo "<div align='center'>";
		echo "<table><tr><td>";
		echo $msgerror;
		echo "</td></tr></table>";
		require_once "includes/persistform.php";
		echo persistform($_POST);
		echo "</div>";
		require_once "includes/footer.php";
		return;
	}

	$user_type = 'Individual';
	$user_category = 'user';

	//salt used with the password to create a one way hash
	$salt = generate_password('20', '4');

	$sql = "insert into v_users ";
	$sql .= "(";
	$sql .= "domain_uuid, ";
	$sql .= "username, ";
	$sql .= "password, ";
	$sql .= "salt, ";
	$sql .= "user_type, ";
	$sql .= "user_category, ";
	$sql .= "user_first_name, ";
	$sql .= "user_last_name, ";
	$sql .= "user_company_name, ";
	$sql .= "user_physical_address_1, ";
	$sql .= "user_physical_address_2, ";
	$sql .= "user_physical_city, ";
	$sql .= "user_physical_state_province, ";
	$sql .= "user_physical_country, ";
	$sql .= "user_physical_postal_code, ";
	$sql .= "user_mailing_address_1, ";
	$sql .= "user_mailing_address_2, ";
	$sql .= "user_mailing_city, ";
	$sql .= "user_mailing_state_province, ";
	$sql .= "user_mailing_country, ";
	$sql .= "user_mailing_postal_code, ";
	$sql .= "user_billing_address_1, ";
	$sql .= "user_billing_address_2, ";
	$sql .= "user_billing_city, ";
	$sql .= "user_billing_state_province, ";
	$sql .= "user_billing_country, ";
	$sql .= "user_billing_postal_code, ";
	$sql .= "user_shipping_address_1, ";
	$sql .= "user_shipping_address_2, ";
	$sql .= "user_shipping_city, ";
	$sql .= "user_shipping_state_province, ";
	$sql .= "user_shipping_country, ";
	$sql .= "user_shipping_postal_code, ";
	$sql .= "user_url, ";
	$sql .= "user_phone_1, ";
	$sql .= "user_phone_1_ext, ";
	$sql .= "user_phone_2, ";
	$sql .= "user_phone_2_ext, ";
	$sql .= "user_phone_mobile, ";
	$sql .= "user_phone_fax, ";
	$sql .= "user_email, ";
	$sql .= "user_email_emergency, ";
	$sql .= "user_add_date, ";
	$sql .= "user_add_user ";
	$sql .= ")";
	$sql .= "values ";
	$sql .= "(";
	$sql .= "'$domain_uuid', ";
	$sql .= "'$username', ";
	$sql .= "'".md5($salt.$password)."', ";
	$sql .= "'".$salt."', ";
	$sql .= "'$user_type', ";
	$sql .= "'$user_category', ";
	$sql .= "'$user_first_name', ";
	$sql .= "'$user_last_name', ";
	$sql .= "'$user_company_name', ";
	$sql .= "'$user_physical_address_1', ";
	$sql .= "'$user_physical_address_2', ";
	$sql .= "'$user_physical_city', ";
	$sql .= "'$user_physical_state_province', ";
	$sql .= "'$user_physical_country', ";
	$sql .= "'$user_physical_postal_code', ";
	$sql .= "'$user_mailing_address_1', ";
	$sql .= "'$user_mailing_address_2', ";
	$sql .= "'$user_mailing_city', ";
	$sql .= "'$user_mailing_state_province', ";
	$sql .= "'$user_mailing_country', ";
	$sql .= "'$user_mailing_postal_code', ";
	$sql .= "'$user_billing_address_1', ";
	$sql .= "'$user_billing_address_2', ";
	$sql .= "'$user_billing_city', ";
	$sql .= "'$user_billing_state_province', ";
	$sql .= "'$user_billing_country', ";
	$sql .= "'$user_billing_postal_code', ";
	$sql .= "'$user_shipping_address_1', ";
	$sql .= "'$user_shipping_address_2', ";
	$sql .= "'$user_shipping_city', ";
	$sql .= "'$user_shipping_state_province', ";
	$sql .= "'$user_shipping_country', ";
	$sql .= "'$user_shipping_postal_code', ";
	$sql .= "'$user_url', ";
	$sql .= "'$user_phone_1', ";
	$sql .= "'$user_phone_1_ext', ";
	$sql .= "'$user_phone_2', ";
	$sql .= "'$user_phone_2_ext', ";
	$sql .= "'$user_phone_mobile', ";
	$sql .= "'$user_phone_fax', ";
	$sql .= "'$user_email', ";
	$sql .= "'$user_email_emergency', ";
	$sql .= "now(), ";
	$sql .= "'".$_SESSION["username"]."' ";
	$sql .= ")";
	$db->exec(check_sql($sql));
	unset($sql);

	//log the success
	//$logtype = 'user'; $logstatus='add'; $logadduser=$_SESSION["username"]; $logdesc= "username: ".$username." user added.";
	//logadd($db, $logtype, $logstatus, $logdesc, $logadduser, $_SERVER["REMOTE_ADDR"]);

	$group_id = 'user';
	$sql = "insert into v_group_members ";
	$sql .= "(";
	$sql .= "domain_uuid, ";
	$sql .= "group_id, ";
	$sql .= "username ";
	$sql .= ")";
	$sql .= "values ";
	$sql .= "(";
	$sql .= "'$domain_uuid', ";
	$sql .= "'$group_id', ";
	$sql .= "'$username' ";
	$sql .= ")";
	$db->exec(check_sql($sql));
	unset($sql);

	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"3;url=index.php\">\n";
	echo "<div align='center'>Add Complete</div>";
	require_once "includes/footer.php";
	return;
}

//show the header
	require_once "includes/header.php";

//show the content
	echo "<div align='center'>";
	echo "<table width='90%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";

	$tablewidth ='width="100%"';
	echo "<form method='post' action=''>";
	echo "<div class='borderlight' style='padding:10px;'>\n";

	echo "<table border='0' $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>\n";
	echo "		<td width='80%'>\n";
	echo "			<b>To add a user, please fill out this form completely. All fields are required. </b><br>";
	echo "		</td>\n";
	echo "		<td width='20%' align='right'>\n";
	echo "			<input type='button' class='btn' name='back' alt='back' onclick=\"window.history.back()\" value='Back'>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";

	echo "<table border='0' $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>";
	echo "		<td class='vncellreq' width='40%'>Username:</td>";
	echo "		<td class='vtable' width='60%'><input type='text' class='formfld' autocomplete='off' name='username' value='$username'></td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='vncellreq'>Password:</td>";
	echo "		<td class='vtable'><input type='password' class='formfld' autocomplete='off' name='password' value='$password'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>Confirm Password:</td>";
	echo "		<td class='vtable'><input type='password' class='formfld' autocomplete='off' name='confirmpassword' value='$confirmpassword'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>First Name:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_first_name' value='$user_first_name'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>Last Name:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_last_name' value='$user_last_name'></td>";
	echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Company Name:</td>";
	//echo "		<td><input type='text' class='formfld' name='user_company_name' value='$user_company_name'></td>";
	//echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>Email:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='user_email' value='$user_email'></td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth>";
	echo "	<tr>";
	echo "		<td colspan='2' align='right'>";
	echo "       <input type='submit' name='submit' class='btn' value='Create Account'>";
	echo "		</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

//show the footer
	require_once "includes/footer.php";
?>
