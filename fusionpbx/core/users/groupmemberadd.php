<?php
/* $Id$ */
/*
	groupmemberadd.php
	Copyright (C) 2008, 2009 Mark J Crane
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
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

//requires a superadmin to add a user to the superadmin group
	if (!ifgroup("superadmin") && $_GET["groupid"] == "superadmin") {
		echo "access denied";
		return;
	}

//HTTP GET set to a variable
	$groupid = check_str($_POST["groupid"]);
	$username = check_str($_POST["username"]);

if (strlen($username) > 0  && strlen($groupid) > 0)   {

	$sqlinsert = "insert into v_group_members ";
	$sqlinsert .= "(";
	$sqlinsert .= "v_id, ";
	$sqlinsert .= "groupid, ";
	$sqlinsert .= "username ";
	$sqlinsert .= ")";
	$sqlinsert .= "values ";
	$sqlinsert .= "(";
	$sqlinsert .= "'$v_id', ";
	$sqlinsert .= "'$groupid', ";
	$sqlinsert .= "'$username' ";
	$sqlinsert .= ")";
	//echo $sqlinsert;
	//exit;
	if (!$db->exec($sqlinsert)) {
		//echo $db->errorCode() . "<br>";
		$info = $db->errorInfo();
	  print_r($info);
		// $info[0] == $db->errorCode() unified error code
		// $info[1] is the driver specific error code
		// $info[2] is the driver specific error string
	}
	else {
		//log the success
		//$logtype = 'group'; $logstatus='add'; $logadduser=$_SESSION["username"]; $logdesc= "username: ".$username." added to group: ".$groupid;
		//logadd($db, $logtype, $logstatus, $logdesc, $logadduser, $_SERVER["REMOTE_ADDR"]);

	}

}

header("Location: groupmembers.php?groupid=$groupid");


?>
