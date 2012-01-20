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
if (permission_exists('public_includes_copy')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//set the http get/post variable(s) to a php variable
	if (isset($_REQUEST["id"])) {
		$public_uuid = check_str($_REQUEST["id"]);
	}

//get the public includes data 
	$sql = "";
	$sql .= "select * from v_public ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and public_uuid = '$public_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	foreach ($result as &$row) {
		$extension_name = $row["extension_name"];
		$public_order = $row["public_order"];
		$extension_continue = $row["extension_continue"];
		$context = $row["context"];
		$enabled = $row["enabled"];
		$descr = 'copy: '.$row["descr"];
		break; //limit to 1 row
	}
	unset ($prep_statement);

//copy the public
	$sql = "insert into v_public ";
	$sql .= "(";
	$sql .= "domain_uuid, ";
	$sql .= "extension_name, ";
	$sql .= "public_order, ";
	$sql .= "extension_continue, ";
	$sql .= "context, ";
	$sql .= "enabled, ";
	$sql .= "descr ";
	$sql .= ")";
	$sql .= "values ";
	$sql .= "(";
	$sql .= "'$domain_uuid', ";
	$sql .= "'$extension_name', ";
	$sql .= "'$public_order', ";
	$sql .= "'$extension_continue', ";
	$sql .= "'default', ";
	$sql .= "'$enabled', ";
	$sql .= "'$descr' ";
	$sql .= ")";
	if ($db_type == "sqlite" || $db_type == "mysql" ) {
		$db->exec(check_sql($sql));
		$db_public_uuid = $db->lastInsertId($id);
	}
	if ($db_type == "pgsql") {
		$sql .= " RETURNING public_uuid ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			$db_public_uuid = $row["public_uuid"];
		}
		unset($prep_statement, $result);
	}
	unset($sql);

//get the the public details
	$sql = "";
	$sql .= "select * from v_public_details ";
	$sql .= "where public_uuid = '$public_uuid' ";
	$sql .= "and domain_uuid = '$domain_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	foreach ($result as &$row) {
		$domain_uuid = $row["domain_uuid"];
		$public_uuid = $row["public_uuid"];
		$tag = $row["tag"];
		$field_type = $row["field_type"];
		$field_data = $row["field_data"];
		$field_order = $row["field_order"];

		//copy the public details
			$sql = "insert into v_public_details ";
			$sql .= "(";
			$sql .= "domain_uuid, ";
			$sql .= "public_uuid, ";
			$sql .= "tag, ";
			$sql .= "field_type, ";
			$sql .= "field_data, ";
			$sql .= "field_order ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$domain_uuid', ";
			$sql .= "'".check_str($db_public_uuid)."', ";
			$sql .= "'".check_str($tag)."', ";
			$sql .= "'".check_str($field_type)."', ";
			$sql .= "'".check_str($field_data)."', ";
			$sql .= "'".check_str($field_order)."' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);
	}
	unset ($prep_statement);

//synchronize the xml config
	sync_package_v_dialplan();

//redirect the user
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=v_public.php\">\n";
	echo "<div align='center'>\n";
	echo "Copy Complete\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

?>
