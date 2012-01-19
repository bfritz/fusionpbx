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

//check permissions
	if (permission_exists('hunt_group_add')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//set the http get/post variable(s) to a php variable
	if (isset($_REQUEST["id"])) {
		$hunt_group_uuid = check_str($_REQUEST["id"]);
	}

//get the v_hunt_group data 
	$sql = "";
	$sql .= "select * from v_hunt_group ";
	$sql .= "where hunt_group_uuid = '$hunt_group_uuid' ";
	$sql .= "and domain_uuid = '$domain_uuid' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$hunt_group_extension = $row["hunt_group_extension"];
		$hunt_group_name = $row["hunt_group_name"];
		$hunt_group_type = $row["hunt_group_type"];
		$hunt_group_context = $row["hunt_group_context"];
		$hunt_group_timeout = $row["hunt_group_timeout"];
		$hunt_group_timeout_destination = $row["hunt_group_timeout_destination"];
		$hunt_group_timeout_type = $row["hunt_group_timeout_type"];
		$hunt_group_ringback = $row["hunt_group_ringback"];
		$hunt_group_cid_name_prefix = $row["hunt_group_cid_name_prefix"];
		$hunt_group_pin = $row["hunt_group_pin"];
		$hunt_group_caller_announce = $row["hunt_group_caller_announce"];
		$hunt_group_enabled = $row["hunt_group_enabled"];
		$hunt_group_descr = "copy: ".$row["hunt_group_descr"];
		break; //limit to 1 row
	}
	unset ($prepstatement);

	//copy the hunt group
		$sql = "insert into v_hunt_group ";
		$sql .= "(";
		$sql .= "domain_uuid, ";
		$sql .= "hunt_group_extension, ";
		$sql .= "hunt_group_name, ";
		$sql .= "hunt_group_type, ";
		$sql .= "hunt_group_context, ";
		$sql .= "hunt_group_timeout, ";
		$sql .= "hunt_group_timeout_destination, ";
		$sql .= "hunt_group_timeout_type, ";
		$sql .= "hunt_group_ringback, ";
		$sql .= "hunt_group_cid_name_prefix, ";
		$sql .= "hunt_group_pin, ";
		$sql .= "hunt_group_caller_announce, ";
		$sql .= "hunt_group_enabled, ";
		$sql .= "hunt_group_descr ";
		$sql .= ")";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$domain_uuid', ";
		$sql .= "'$hunt_group_extension', ";
		$sql .= "'$hunt_group_name', ";
		$sql .= "'$hunt_group_type', ";
		$sql .= "'$hunt_group_context', ";
		$sql .= "'$hunt_group_timeout', ";
		$sql .= "'$hunt_group_timeout_destination', ";
		$sql .= "'$hunt_group_timeout_type', ";
		$sql .= "'$hunt_group_ringback', ";
		$sql .= "'$hunt_group_cid_name_prefix', ";
		$sql .= "'$hunt_group_pin', ";
		$sql .= "'$hunt_group_caller_announce', ";
		$sql .= "'$hunt_group_enabled', ";
		$sql .= "'$hunt_group_descr' ";
		$sql .= ")";
		if ($db_type == "sqlite" || $db_type == "mysql" ) {
			$db->exec(check_sql($sql));
			$db_hunt_group_uuid = $db->lastInsertId($id);
		}
		if ($db_type == "pgsql") {
			$sql .= " RETURNING hunt_group_uuid ";
			$prepstatement = $db->prepare(check_sql($sql));
			$prepstatement->execute();
			$result = $prepstatement->fetchAll();
			foreach ($result as &$row) {
				$db_hunt_group_uuid = $row["hunt_group_uuid"];
			}
			unset($prepstatement, $result);
		}
		unset($sql);

	//get the the hunt group destinations
		$sql = "";
		$sql .= "select * from v_hunt_group_destinations ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and hunt_group_uuid = '$hunt_group_uuid' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		foreach ($result as &$row) {
			//$domain_uuid = $row["domain_uuid"];
			$hunt_group_uuid = $row["hunt_group_uuid"];
			$destination_data = $row["destination_data"];
			$destination_type = $row["destination_type"];
			$destination_profile = $row["destination_profile"];
			$destination_order = $row["destination_order"];
			$destination_descr = $row["destination_descr"];

			//copy the hunt group destinations
				$sql = "insert into v_hunt_group_destinations ";
				$sql .= "(";
				$sql .= "domain_uuid, ";
				$sql .= "hunt_group_uuid, ";
				$sql .= "destination_data, ";
				$sql .= "destination_type, ";
				$sql .= "destination_profile, ";
				$sql .= "destination_order, ";
				$sql .= "destination_descr ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$domain_uuid', ";
				$sql .= "'$db_hunt_group_uuid', ";
				$sql .= "'$destination_data', ";
				$sql .= "'$destination_type', ";
				$sql .= "'$destination_profile', ";
				$sql .= "'$destination_order', ";
				$sql .= "'$destination_descr' ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);
		}
		unset ($prepstatement);

	//synchronize the xml config
		sync_package_v_hunt_group();

	//redirect the user
		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_hunt_group.php\">\n";
		echo "<div align='center'>\n";
		echo "Copy Complete\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;

?>