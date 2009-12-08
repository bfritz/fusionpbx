<?php
/* $Id$ */
/*
	userdelete.php
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

$id = checkstr($_GET["id"]);

//get the username from v_users
	$sql = "";
	$sql .= "select * from v_users ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and id = '$id' ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	while($row = $prepstatement->fetch()) {
		$username = $row["username"];
		break; //limit to 1 row
	}
	unset ($prepstatement);

//get the username from v_users
	$sql = "";
	$sql .= "select * from v_users ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and id = '$id' ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	while($row = $prepstatement->fetch()) {
		$username = $row["username"];
		break; //limit to 1 row
	}
	unset ($prepstatement);

//required to be a superadmin to delete a member of the superadmin group
	$superadminlist = superadminlist($db);
	if (ifsuperadmin($superadminlist, $username)) {
		if (!ifgroup("superadmin")) { 
			echo "access denied";
			return;
		}
	}


//if (ifpermission("delete")) {

	$sqldelete = "delete from v_users ";
	$sqldelete .= "where v_id = '$v_id' ";
	$sqldelete .= "and id = '$id' ";
	//echo $sqldelete;
	if (!$db->exec($sqldelete)) {
		//echo $db->errorCode() . "<br>";
		$info = $db->errorInfo();
		print_r($info);
		// $info[0] == $db->errorCode() unified error code
		// $info[1] is the driver specific error code
		// $info[2] is the driver specific error string
	}
//} //end ifpermission


header("Location: index.php");
exit;

?>
