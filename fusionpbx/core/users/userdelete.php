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
if (permission_exists('user_delete')) {
	//access allowed
}
else {
	echo "access denied";
	return;
}

//get the id
	$id = check_str($_GET["id"]);

//get the username from v_users
	$sql = "";
	$sql .= "select * from v_users ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and user_uuid = '$id' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	foreach ($result as &$row) {
		$username = $row["username"];
		break; //limit to 1 row
	}
	unset ($prep_statement);

//get the username from v_users
	$sql = "";
	$sql .= "select * from v_users ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and user_uuid = '$id' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	foreach ($result as &$row) {
		$username = $row["username"];
		break; //limit to 1 row
	}
	unset ($prep_statement);

//required to be a superadmin to delete a member of the superadmin group
	$superadmin_list = superadmin_list($db);
	if (if_superadmin($superadmin_list, $username)) {
		if (!if_group("superadmin")) { 
			echo "access denied";
			return;
		}
	}

//delete the user
	$sqldelete = "delete from v_users ";
	$sqldelete .= "where domain_uuid = '$domain_uuid' ";
	$sqldelete .= "and user_uuid = '$id' ";
	if (!$db->exec($sqldelete)) {
		//echo $db->errorCode() . "<br>";
		$info = $db->errorInfo();
		print_r($info);
		// $info[0] == $db->errorCode() unified error code
		// $info[1] is the driver specific error code
		// $info[2] is the driver specific error string
	}

//delete the groups the user is assigned to
	$sqldelete = "delete from v_group_users ";
	$sqldelete .= "where domain_uuid = '$domain_uuid' ";
	$sqldelete .= "and username = '$username' ";
	if (!$db->exec($sqldelete)) {
		$info = $db->errorInfo();
		print_r($info);
	}

//redirect the user
	header("Location: index.php");

?>
