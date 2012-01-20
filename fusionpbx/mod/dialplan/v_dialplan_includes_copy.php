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
if (permission_exists('dialplan_add')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//set the http get/post variable(s) to a php variable
	if (isset($_REQUEST["id"])) {
		$dialplan_uuid = check_str($_REQUEST["id"]);
	}

//get the v_dialplan data 
	$dialplan_uuid = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_dialplan ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	foreach ($result as &$row) {
		$domain_uuid = $row["domain_uuid"];
		$extension_name = $row["extension_name"];
		$dialplan_order = $row["dialplan_order"];
		$extension_continue = $row["extension_continue"];
		$context = $row["context"];
		$enabled = $row["enabled"];
		$descr = "copy: ".$row["descr"];
		break; //limit to 1 row
	}
	unset ($prep_statement);

	//copy the dialplan
		$dialplan_uuid = uuid();
		$sql = "insert into v_dialplan ";
		$sql .= "(";
		$sql .= "domain_uuid, ";
		$sql .= "dialplan_uuid, ";
		$sql .= "extension_name, ";
		$sql .= "dialplan_order, ";
		$sql .= "extension_continue, ";
		$sql .= "context, ";
		$sql .= "enabled, ";
		$sql .= "descr ";
		$sql .= ")";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$domain_uuid', ";
		$sql .= "'$dialplan_uuid', ";
		$sql .= "'$extension_name', ";
		$sql .= "'$dialplan_order', ";
		$sql .= "'$extension_continue', ";
		$sql .= "'$context', ";
		$sql .= "'$enabled', ";
		$sql .= "'$descr' ";
		$sql .= ")";
		$db->exec(check_sql($sql));
		unset($sql);

	//get the the dialplan details
		$sql = "";
		$sql .= "select * from v_dialplan_details ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			$domain_uuid = $row["domain_uuid"];
			$tag = $row["tag"];
			$field_order = $row["field_order"];
			$field_type = $row["field_type"];
			$field_data = $row["field_data"];

			//copy the dialplan details
				$sql = "insert into v_dialplan_details ";
				$sql .= "(";
				$sql .= "domain_uuid, ";
				$sql .= "dialplan_uuid, ";
				$sql .= "dialplan_detail_uuid, ";
				$sql .= "tag, ";
				$sql .= "field_order, ";
				$sql .= "field_type, ";
				$sql .= "field_data ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$domain_uuid', ";
				$sql .= "'".check_str($dialplan_uuid)."', ";
				$sql .= "'".check_str($dialplan_detail_uuid)."', ";
				$sql .= "'".check_str($tag)."', ";
				$sql .= "'".check_str($field_order)."', ";
				$sql .= "'".check_str($field_type)."', ";
				$sql .= "'".check_str($field_data)."' ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);
		}
		unset ($prep_statement);

	//synchronize the xml config
		sync_package_v_dialplan();

	//redirect the user
		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_dialplan.php\">\n";
		echo "<div align='center'>\n";
		echo "Copy Complete\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;

?>