<?php
/* $Id$ */
/*
	v_cmd.php
	Copyright (C) 2008 Mark J Crane
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
	//access granted
}
else {
	echo "access denied";
	exit;
}


$cmd = $_GET['cmd'];
$rdr = $_GET['rdr'];

$sql = "";
$sql .= "select * from v_settings ";
$sql .= "where v_id = '$v_id' ";
$prepstatement = $db->prepare($sql);
$prepstatement->execute();
$result = $prepstatement->fetchAll();
foreach ($result as &$row) {
	$event_socket_ip_address = $row["event_socket_ip_address"];
	$event_socket_port = $row["event_socket_port"];
	$event_socket_password = $row["event_socket_password"];

	break; //limit to 1 row
}
unset ($prepstatement);


$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
$response = event_socket_request($fp, $cmd);
fclose($fp);

if ($rdr == "false") {
	//redirect false
	echo $response;
}
else {
	header("Location: v_status.php?savemsg=".urlencode($response));
}
?>