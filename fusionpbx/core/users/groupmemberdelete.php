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

if (ifgroup("admin") || ifgroup("superadmin")) {
	//access allowed
}
else {
	echo "access denied";
	return;
}

//requires a superadmin to delete superadmin group
	if (!ifgroup("superadmin") && $_GET["groupid"] == "superadmin") {
		echo "access denied";
		return;
	}

//HTTP GET set to a variable
	$groupid = check_str($_GET["groupid"]);
	$username = check_str($_GET["username"]);

//if (ifpermission("delete")) {

	$sqldelete = "delete from v_group_members ";
	$sqldelete .= "where v_id = '$v_id' ";
	$sqldelete .= "and username = '$username' ";
	$sqldelete .= "and groupid = '$groupid' ";

	//echo $sqldelete;
	if (!$db->exec($sqldelete)) {
		//echo $db->errorCode() . "<br>";
		$info = $db->errorInfo();
		print_r($info);
		// $info[0] == $db->errorCode() unified error code
		// $info[1] is the driver specific error code
		// $info[2] is the driver specific error string
	}
	else {
		$logtype = 'group'; $logstatus='remove'; $logadduser=$_SESSION["username"]; $logdesc= "username: ".$username." removed from group: ".$groupid;
		logadd($db, $logtype, $logstatus, $logdesc, $logadduser, $_SERVER["REMOTE_ADDR"]);
	}
//} //end ifpermission


header("Location: groupmembers.php?groupid=$groupid");
exit;

?>
