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
if (permission_exists('dialplan_add') 
	|| permission_exists('inbound_route_add') 
	|| permission_exists('outbound_route_add') 
	|| permission_exists('time_conditions_add')) {
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

//get the dialplan data 
	$dialplan_uuid = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_dialplans ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	foreach ($result as &$row) {
		$database_dialplan_uuid = $row["dialplan_uuid"];
		$app_uuid = $row["app_uuid"];
		$dialplan_name = $row["dialplan_name"];
		$dialplan_order = $row["dialplan_order"];
		$dialplan_continue = $row["dialplan_continue"];
		$dialplan_context = $row["dialplan_context"];
		$dialplan_enabled = $row["dialplan_enabled"];
		$dialplan_description = "copy: ".$row["dialplan_description"];
		break; //limit to 1 row
	}
	unset ($prep_statement);

	//copy the dialplan
		$dialplan_context = $_SESSION['context'];
		$dialplan_uuid = uuid();
		$sql = "insert into v_dialplans ";
		$sql .= "(";
		$sql .= "domain_uuid, ";
		$sql .= "dialplan_uuid, ";
		$sql .= "app_uuid, ";
		$sql .= "dialplan_name, ";
		$sql .= "dialplan_order, ";
		$sql .= "dialplan_continue, ";
		$sql .= "dialplan_context, ";
		$sql .= "dialplan_enabled, ";
		$sql .= "dialplan_description ";
		$sql .= ")";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$domain_uuid', ";
		$sql .= "'$dialplan_uuid', ";
		$sql .= "'$app_uuid', ";
		$sql .= "'$dialplan_name', ";
		$sql .= "'$dialplan_order', ";
		$sql .= "'$dialplan_continue', ";
		$sql .= "'$dialplan_context', ";
		$sql .= "'$dialplan_enabled', ";
		$sql .= "'$dialplan_description' ";
		$sql .= ")";
		$db->exec(check_sql($sql));
		unset($sql);

	//get the the dialplan details
		$sql = "";
		$sql .= "select * from v_dialplan_details ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and dialplan_uuid = '$database_dialplan_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			$dialplan_detail_tag = $row["dialplan_detail_tag"];
			$dialplan_detail_order = $row["dialplan_detail_order"];
			$dialplan_detail_type = $row["dialplan_detail_type"];
			$dialplan_detail_data = $row["dialplan_detail_data"];

			//copy the dialplan details
				$dialplan_detail_uuid = uuid();
				$sql = "insert into v_dialplan_details ";
				$sql .= "(";
				$sql .= "domain_uuid, ";
				$sql .= "dialplan_uuid, ";
				$sql .= "dialplan_detail_uuid, ";
				$sql .= "dialplan_detail_tag, ";
				$sql .= "dialplan_detail_order, ";
				$sql .= "dialplan_detail_type, ";
				$sql .= "dialplan_detail_data ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$domain_uuid', ";
				$sql .= "'".check_str($dialplan_uuid)."', ";
				$sql .= "'".check_str($dialplan_detail_uuid)."', ";
				$sql .= "'".check_str($dialplan_detail_tag)."', ";
				$sql .= "'".check_str($dialplan_detail_order)."', ";
				$sql .= "'".check_str($dialplan_detail_type)."', ";
				$sql .= "'".check_str($dialplan_detail_data)."' ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);
		}
		unset ($prep_statement);

	//synchronize the xml config
		save_dialplan_xml();

	//redirect the user
		require_once "includes/header.php";
		if ($dialplan_context == "public") {
			echo "<meta http-equiv=\"refresh\" content=\"2;url=dialplans.php?dialplan_context=public\">\n";
		}
		else {
			echo "<meta http-equiv=\"refresh\" content=\"2;url=dialplans.php\">\n";
		}
		echo "<div align='center'>\n";
		echo "Copy Complete\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;

?>