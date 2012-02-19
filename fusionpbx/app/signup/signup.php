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
require_once "includes/recaptchalib.php";
//require_once "includes/email_address_validator.php";
include "config.php";
include "v_fields.php";

# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;

if (count($_POST)>0 && $_POST["persistform"] != "1") {

	$msgerror = '';

	$required[] = array('username', "Please provid a Username.<br>\n");
	$required[] = array('user_first_name', "Please provide a first name.<br>\n");
	$required[] = array('user_last_name', "Please provide a last name.<br>\n");
	$required[] = array('user_billing_address_1', "Please provide a street address.<br>\n");
	$required[] = array('user_billing_city', "Please provide a city.<br>\n");
	$required[] = array('user_billing_state_province', "Please provide a state.<br>\n");
	$required[] = array('user_billing_country', "Please provide a country.<br>\n");
	$required[] = array('user_billing_postal_code',"Please provide a postal code.<br>\n");
	$required[] = array('user_phone_1', "Please provide a phone number.<br>\n");
	$required[] = array('user_email', "Please provide an email address.<br>\n");

	foreach($required as $x) {
		if (strlen($_REQUEST[$x[0]]) < 1) {
			$msgerror .= $x[1];
			$error_fields[] = $x[0];
		}
	}

	//sanitize the http request array
	foreach ($_REQUEST as $field => $data){
		$request[$field] = check_str($data);
	}

	//username is already used.
	if (strlen($request['username']) != 0) {
		$sql = "SELECT * FROM v_users ";
		$sql .= " where domain_uuid = '$domain_uuid' ";
		$sql .= " and username = '" . $request['username'] . "' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		if (count($prep_statement->fetchAll()) > 0) {
			$msgerror .= "Please choose a different Username.<br>\n";
		}
	}

	// make sure password fields match
	if ($request['password'] != $request['confirmpassword']) {
		$msgerror .= "Passwords did not match.<br>\n";
	}

	// email address atleast looks valid
	//if (!in_array('user_email', $error_fields)) {
	//	$validator = new EmailAddressValidator;
	//	if (!$validator->check_email_address($request['user_email'])) {
	//		$msgerror .= "Please provide a VALID email address.<br>\n";
	//	}
	//}

	if ($_POST["recaptcha_response_field"]) {
		$resp = recaptcha_check_answer ($privatekey,
						$_SERVER["REMOTE_ADDR"],
						$_POST["recaptcha_challenge_field"],
						$_POST["recaptcha_response_field"]);

		if (!$resp->is_valid) {
			# set the error code so that we can display it
			$msgerror .= "Captcha Verification Failed<br>\n";
			$error = $resp->error;
		}
	} else {
			$msgerror .= "Captcha Verification Failed<br>\n";
	}

	if (strlen($msgerror) > 0) {
		goto showform;
	}

	//salt used with the password to create a one way hash
	$salt = generate_password('20', '4');
	$user_uuid = uuid();
	$sql = "insert into v_users ";
	$sql .= "(";
	$sql .= "domain_uuid, ";
	$sql .= "user_uuid, ";
	$sql .= "username, ";
	$sql .= "password, ";
	$sql .= "salt, ";
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
	$sql .= "user_add_user, ";
	$sql .= "user_category, ";
	$sql .= "user_type ";
	$sql .= ")";
	$sql .= "values ";
	$sql .= "(";
	$sql .= "'$domain_uuid', ";
	$sql .= "'$user_uuid', ";
	$sql .= "'" . $request['username'] . "', ";
	$sql .= "'".md5($salt.$request['password'])."', ";
	$sql .= "'" . $salt . "', ";
	$sql .= "'" . $request['user_first_name'] . "', ";
	$sql .= "'" . $request['user_last_name'] . "', ";
	$sql .= "'" . $request['user_company_name'] . "', ";
	$sql .= "'" . $request['user_physical_address_1'] . "', ";
	$sql .= "'" . $request['user_physical_address_2'] . "', ";
	$sql .= "'" . $request['user_physical_city'] . "', ";
	$sql .= "'" . $request['user_physical_state_province'] . "', ";
	$sql .= "'" . $request['user_physical_country'] . "', ";
	$sql .= "'" . $request['user_physical_postal_code'] . "', ";
	$sql .= "'" . $request['user_mailing_address_1'] . "', ";
	$sql .= "'" . $request['user_mailing_address_2'] . "', ";
	$sql .= "'" . $request['user_mailing_city'] . "', ";
	$sql .= "'" . $request['user_mailing_state_province'] . "', ";
	$sql .= "'" . $request['user_mailing_country'] . "', ";
	$sql .= "'" . $request['user_mailing_postal_code'] . "', ";
	$sql .= "'" . $request['user_billing_address_1'] . "', ";
	$sql .= "'" . $request['user_billing_address_2'] . "', ";
	$sql .= "'" . $request['user_billing_city'] . "', ";
	$sql .= "'" . $request['user_billing_state_province'] . "', ";
	$sql .= "'" . $request['user_billing_country'] . "', ";
	$sql .= "'" . $request['user_billing_postal_code'] . "', ";
	$sql .= "'" . $request['user_shipping_address_1'] . "', ";
	$sql .= "'" . $request['user_shipping_address_2'] . "', ";
	$sql .= "'" . $request['user_shipping_city'] . "', ";
	$sql .= "'" . $request['user_shipping_state_province'] . "', ";
	$sql .= "'" . $request['user_shipping_country'] . "', ";
	$sql .= "'" . $request['user_shipping_postal_code'] . "', ";
	$sql .= "'" . $request['user_url'] . "', ";
	$sql .= "'" . $request['user_phone_1'] . "', ";
	$sql .= "'" . $request['user_phone_1_ext'] . "', ";
	$sql .= "'" . $request['user_phone_2'] . "', ";
	$sql .= "'" . $request['user_phone_2_ext'] . "', ";
	$sql .= "'" . $request['user_phone_mobile'] . "', ";
	$sql .= "'" . $request['user_phone_fax'] . "', ";
	$sql .= "'" . $request['user_email'] . "', ";
	$sql .= "'" . $request['user_email_emergency'] . "', ";
	$sql .= "now(), ";
	$sql .= "'".$_SESSION["username"]."', ";
	$sql .= "'user', ";
	$sql .= "'Individual' ";
	$sql .= ") ";
	if ($db_type == "pgsql") {

	}
	$db->exec(check_sql($sql));
	unset($sql);

	//log the success
	//$log_type = 'user'; $log_status='add'; $log_add_user=$_SESSION["username"]; $log_desc= "username: ".$username." user added.";
	//log_add($db, $log_type, $log_status, $log_desc, $log_add_user, $_SERVER["REMOTE_ADDR"]);

	$group_name = 'user';
	$sql = "insert into v_group_users ";
	$sql .= "(";
	$sql .= "domain_uuid, ";
	$sql .= "group_name, ";
	$sql .= "user_uuid, ";
	$sql .= "username ";
	$sql .= ")";
	$sql .= "values ";
	$sql .= "(";
	$sql .= "'" . $domain_uuid . "', ";
	$sql .= "'" . $group_name . "', ";
	$sql .= "'" . $user_uuid . "', ";
	$sql .= "'" . $request['username']. "' ";
	$sql .= ")";
	$db->exec(check_sql($sql));
	unset($sql);

	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"3;url=".PROJECT_PATH."/index.php\">\n";
	echo "<div align='center'>Add Complete</div>";
	require_once "includes/footer.php";
	// This should probably be an exit or die() call;
	return;
}

showform:

require_once "includes/header.php";

include "user_template.php";

require_once "includes/footer.php";
?>
