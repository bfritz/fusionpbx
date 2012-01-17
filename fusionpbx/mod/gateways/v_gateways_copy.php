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
require_once "includes/paging.php";
if (permission_exists('gateways_add')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//set the http get/post variable(s) to a php variable
	if (isset($_REQUEST["id"])) {
		$gateway_id = check_str($_REQUEST["id"]);
	}

//get the data 
	$sql = "";
	$sql .= "select * from v_gateways ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and gateway_id = '$gateway_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$gateway = $row["gateway"];
		$username = $row["username"];
		$password = $row["password"];
		$auth_username = $row["auth_username"];
		$realm = $row["realm"];
		$from_user = $row["from_user"];
		$from_domain = $row["from_domain"];
		$proxy = $row["proxy"];
		$register_proxy = $row["register_proxy"];
		$outbound_proxy = $row["outbound_proxy"];
		$expire_seconds = $row["expire_seconds"];
		$register = $row["register"];
		$register_transport = $row["register_transport"];
		$retry_seconds = $row["retry_seconds"];
		$extension = $row["extension"];
		$ping = $row["ping"];
		$caller_id_in_from = $row["caller_id_in_from"];
		$supress_cng = $row["supress_cng"];
		$extension_in_contact = $row["extension_in_contact"];
		$effective_caller_id_name = $row["effective_caller_id_name"];
		$effective_caller_id_number = $row["effective_caller_id_number"];
		$outbound_caller_id_name = $row["outbound_caller_id_name"];
		$outbound_caller_id_number = $row["outbound_caller_id_number"];
		$context = $row["context"];
		$enabled = $row["enabled"];
		$description = 'copy: '.$row["description"];
		break; //limit to 1 row
	}
	unset ($prepstatement);

//copy the gateways
	$sql = "insert into v_gateways ";
	$sql .= "(";
	$sql .= "domain_uuid, ";
	$sql .= "gateway, ";
	$sql .= "username, ";
	$sql .= "password, ";
	$sql .= "auth_username, ";
	$sql .= "realm, ";
	$sql .= "from_user, ";
	$sql .= "from_domain, ";
	$sql .= "proxy, ";
	$sql .= "register_proxy, ";
	$sql .= "outbound_proxy, ";
	$sql .= "expire_seconds, ";
	$sql .= "register, ";
	$sql .= "register_transport, ";
	$sql .= "retry_seconds, ";
	$sql .= "extension, ";
	$sql .= "ping, ";
	$sql .= "caller_id_in_from, ";
	$sql .= "supress_cng, ";
	$sql .= "extension_in_contact, ";
	$sql .= "effective_caller_id_name, ";
	$sql .= "effective_caller_id_number, ";
	$sql .= "outbound_caller_id_name, ";
	$sql .= "outbound_caller_id_number, ";
	$sql .= "context, ";
	$sql .= "enabled, ";
	$sql .= "description ";
	$sql .= ")";
	$sql .= "values ";
	$sql .= "(";
	$sql .= "'$domain_uuid', ";
	$sql .= "'$gateway', ";
	$sql .= "'$username', ";
	$sql .= "'$password', ";
	$sql .= "'$auth_username', ";
	$sql .= "'$realm', ";
	$sql .= "'$from_user', ";
	$sql .= "'$from_domain', ";
	$sql .= "'$proxy', ";
	$sql .= "'$register_proxy', ";
	$sql .= "'$outbound_proxy', ";
	$sql .= "'$expire_seconds', ";
	$sql .= "'$register', ";
	$sql .= "'$register_transport', ";
	$sql .= "'$retry_seconds', ";
	$sql .= "'$extension', ";
	$sql .= "'$ping', ";
	$sql .= "'$caller_id_in_from', ";
	$sql .= "'$supress_cng', ";
	$sql .= "'$extension_in_contact', ";
	$sql .= "'$effective_caller_id_name', ";
	$sql .= "'$effective_caller_id_number', ";
	$sql .= "'$outbound_caller_id_name', ";
	$sql .= "'$outbound_caller_id_number', ";
	$sql .= "'$context', ";
	$sql .= "'$enabled', ";
	$sql .= "'$description' ";
	$sql .= ")";
	$db->exec(check_sql($sql));
	unset($sql);

//synchronize the xml config
	sync_package_v_gateways();

//redirect the user
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=v_gateways.php\">\n";
	echo "<div align='center'>\n";
	echo "Copy Complete\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

?>