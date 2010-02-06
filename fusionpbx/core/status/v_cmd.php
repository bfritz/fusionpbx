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
$prepstatement = $db->prepare(check_sql($sql));
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