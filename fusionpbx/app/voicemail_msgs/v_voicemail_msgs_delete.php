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
require "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists('voicemail_delete')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//get the http get values
	if (count($_GET)>0) {
		$uuid = $_GET["uuid"];
		$id = $_GET["id"];
	}

//get the domain from the domains array
	$domain_name = $_SESSION['domains'][$domain_uuid]['domain'];

//create the event socket connection
	$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
	if (!$fp) {
		$msg = "<div align='center'>Connection to Event Socket failed.<br /></div>";
	}

//show the error message or show the content
	if (strlen($msg) > 0) {
		require_once "includes/header.php";
		echo "<div align='center'>\n";
		echo "	<table width='40%'>\n";
		echo "		<tr>\n";
		echo "			<th align='left'>Message</th>\n";
		echo "		</tr>\n";
		echo "		<tr>\n";
		echo "			<td class='row_style1'><strong>$msg</strong></td>\n";
		echo "		</tr>\n";
		echo "	</table>\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;
	}

// delete the voicemail
	$cmd = "api vm_delete " .$id."@".$domain_name." ".$uuid;
	$response = trim(event_socket_request($fp, $cmd));
	echo $xml_response;
	if (strcmp($response,"+OK")==0) {
		$msg = "Complete";
	}
	else {
		$msg = "Failed";
	}

//redirect the user
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=v_voicemail_msgs.php\">\n";
	echo "<div align='center'>\n";
	echo "Delete $msg\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

?>