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

//check permissions
	if (ifgroup("admin") || ifgroup("superadmin")) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//request form values and set them as variables
	$sip_profile_name = trim($_REQUEST["profile"]);

//define variables
	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

//get the event socket connection information
	$sql = "";
	$sql .= "select * from v_settings ";
	$sql .= "where v_id = '$v_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		//$v_id = $row["v_id"];
		$event_socket_ip_address = $row["event_socket_ip_address"];
		$event_socket_port = $row["event_socket_port"];
		$event_socket_password = $row["event_socket_password"];
		break; //limit to 1 row
	}

//get sofia status profile information including registrations
	$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
	$cmd = "api sofia xmlstatus profile ".$sip_profile_name."";
	$xml_response = trim(event_socket_request($fp, $cmd));
	if ($xml_response == "Invalid Profile!") { $xml_response = "<error_msg>Invalid Profile!</error_msg>"; }
	$xml_response = str_replace("<profile-info>", "<profile_info>", $xml_response);
	$xml_response = str_replace("</profile-info>", "</profile_info>", $xml_response);
	try {
		$xml = new SimpleXMLElement($xml_response);
	}
	catch(Exception $e) {
		echo $e->getMessage();
		exit;
	}

//show the header
	require_once "includes/header.php";

//show the registrations
	//echo "<br />\n\n";
	echo "<table width='100%' border='0' cellspacing='0' cellpadding='5'>\n";
	echo "<tr>\n";
	echo "<td colspan='4'>\n";
	echo "	<b>Profile: ". $sip_profile_name."</b>\n";
	echo "</td>\n";
	echo "<td colspan='1' align='right'>\n";
	echo "  <input type='button' class='btn' value='back' onclick=\"document.location.href='v_status.php';\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<table width='100%' border='0' cellspacing='0' cellpadding='5'>\n";
	echo "<tr>\n";
	//echo "	<th class='vncell'>Caller ID</th>\n";
	echo "	<th>User</th>\n";
	//echo "	<th class='vncell'>Contact</th>\n";
	//echo "	<th class='vncell'>sip-auth-user</th>\n";
	echo "	<th>Agent</th>\n";
	//echo "	<th class='vncell'>Host</th>\n";
	echo "	<th>IP</th>\n";
	echo "	<th>Port</th>\n";
	//echo "	<th class='vncell'>sip-auth-realm</th>\n";
	//echo "	<th class='vncell'>mwi-account</th>\n";
	echo "	<th>Status</th>\n";
	echo "</tr>\n";

	if (count($xml->registrations->registration) > 0) {
		foreach ($xml->registrations->registration as $row) {
			//print_r($row);
			echo "<tr>\n";
			//<td class='".$rowstyle[$c]."'>&nbsp;".$row->{'call-id'}."&nbsp;</td>\n";
			//echo "	<td class='".$rowstyle[$c]."'>&nbsp;".$row->{'user'}."&nbsp;</td>\n";
			//echo "	<td class='".$rowstyle[$c]."'>&nbsp;".$row->{'contact'}."&nbsp;</td>\n";
			echo "	<td class='".$rowstyle[$c]."'>&nbsp;".$row->{'sip-auth-user'}."&nbsp;</td>\n";
			echo "	<td class='".$rowstyle[$c]."'>&nbsp;".$row->{'agent'}."&nbsp;</td>\n";
			//echo "	<td class='".$rowstyle[$c]."'>&nbsp;".$row->{'host'}."&nbsp;</td>\n";
			echo "	<td class='".$rowstyle[$c]."'>&nbsp;<a href='http://".$row->{'network-ip'}."' target='_blank'>".$row->{'network-ip'}."</a>&nbsp;</td>\n";
			echo "	<td class='".$rowstyle[$c]."'>&nbsp;".$row->{'network-port'}."&nbsp;</td>\n";
			//echo "	<td class='".$rowstyle[$c]."'>&nbsp;".$row->{'sip-auth-realm'}."&nbsp;</td>\n";
			//echo "	<td class='".$rowstyle[$c]."'>&nbsp;".$row->{'mwi-account'}."&nbsp;</td>\n";
			echo "	<td class='".$rowstyle[$c]."'>&nbsp;".$row->{'status'}."&nbsp;</td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		}

		echo "<tr>\n";
		echo "<td colspan='5' align='right'>\n";
		echo "	<b>".count($xml->registrations->registration)." registrations</b>\n";
		echo "</td>\n";
		echo "</tr>\n";

	}
	echo "</table>\n";
	fclose($fp);
	unset($xml);

//add some space at the bottom of the page
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";

//get the footer
	require_once "includes/footer.php";

?>