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

//check permissions
	if (permission_exists('hunt_group_delete')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

if (count($_GET)>0) {
	$id = $_GET["id"];
}

if (strlen($id)>0) {

	//start the atomic transaction
		$count = $db->exec("BEGIN;");

	//get the dialplan uuid
		$sql = "select * from v_hunt_groups ";
		$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
		$sql .= "and hunt_group_uuid = '$id' ";
		$prep_statement = $db->prepare($sql);
		$prep_statement->execute();
		while($row = $prep_statement->fetch(PDO::FETCH_ASSOC)) {
			$dialplan_uuid = $row['dialplan_uuid'];
		}

	//delete child data
		$sql = "";
		$sql .= "delete from v_hunt_group_destinations ";
		$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
		$sql .= "and hunt_group_uuid = '$id' ";
		$db->query($sql);
		unset($sql);

	//delete parent data
		$sql = "";
		$sql .= "delete from v_hunt_groups ";
		$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
		$sql .= "and hunt_group_uuid = '$id' ";
		$db->query($sql);
		unset($sql);

	//delete the dialplan entry
		$sql = "";
		$sql .= "delete from v_dialplans ";
		$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
		$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
		//echo $sql."<br>\n";
		$db->query($sql);
		unset($sql);

	//delete the dialplan details
		$sql = "";
		$sql .= "delete from v_dialplan_details ";
		$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
		$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
		//echo $sql."<br>\n";
		$db->query($sql);
		unset($sql);

	//commit the atomic transaction
		$count = $db->exec("COMMIT;");

	//synchronize the xml config
		save_hunt_group_xml();

	//synchronize the xml config
		save_dialplan_xml();
}

//redirect the user
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=v_hunt_group.php\">\n";
	echo "<div align='center'>\n";
	echo "Delete Complete\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

?>