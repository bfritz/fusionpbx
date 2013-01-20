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
require_once "includes/paging.php";
if (permission_exists('fax_extension_add')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//add multi-lingual support
	require_once "app_languages.php";
	foreach($text as $key => $value) {
		$text[$key] = $value[$_SESSION['domain']['language']['code']];
	}

//set the http get/post variable(s) to a php variable
	if (isset($_REQUEST["id"])) {
		$fax_uuid = check_str($_REQUEST["id"]);
	}

//get the data 
	$sql = "select * from v_fax ";
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
		$fax_extension = $row["fax_extension"];
		$fax_name = $row["fax_name"];
		$fax_email = $row["fax_email"];
		$fax_pin_number = $row["fax_pin_number"];
		$fax_caller_id_name = $row["fax_caller_id_name"];
		$fax_caller_id_number = $row["fax_caller_id_number"];
		$fax_forward_number = $row["fax_forward_number"];
		$fax_description = 'copy: '.$row["fax_description"];
	}
	unset ($prep_statement);

//copy the fax extension
	$fax_uuid = uuid();
	$dialplan_uuid = uuid();
	$sql = "insert into v_fax ";
	$sql .= "(";
	$sql .= "domain_uuid, ";
	$sql .= "fax_uuid, ";
	$sql .= "dialplan_uuid, ";
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
	$sql .= "'$dialplan_uuid', ";
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

//redirect the user
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=fax.php\">\n";
	echo "<div align='center'>\n";
	echo "".$text['confirm-copy']."\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

?>