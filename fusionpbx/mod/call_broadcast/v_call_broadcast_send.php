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
if (ifgroup("admin") || ifgroup("tenant")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//set the max execution time to 3600 * 4
	ini_set(max_execution_time,14400);

function cmd_async($cmd) {
	//windows
	if (stristr(PHP_OS, 'WIN')) {
		$descriptorspec = array(
			0 => array("pipe", "r"),   // stdin
			1 => array("pipe", "w"),  // stdout
			2 => array("pipe", "w")   // stderr
		);
		$process = proc_open("start ".$cmd, $descriptorspec, $pipes);
		//sleep(1);
		proc_close($process);
	}
	else { //posix
		exec ($cmd ." /dev/null 2>&1 &");
	}
}

//get the event socket connection information
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


//send the call broadcast
if (strlen($_GET["f"]) > 0) {

	$sql = "";
	$sql .= "select * from v_settings ";
	$sql .= "where v_id = '$v_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	while($row = $prepstatement->fetch()) {
		$event_socket_ip_address = $row["event_socket_ip_address"];
		$event_socket_port = $row["event_socket_port"];
		$event_socket_password = $row["event_socket_password"];
		break; //limit to 1 row
	}
	unset ($prepstatement);

	$cmd = 'api jsrun '.$_GET["f"];
	$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
	$response = event_socket_request($fp, $cmd);
	fclose($fp);

	require_once "includes/header.php";
	//echo "<meta http-equiv=\"refresh\" content=\"2;url=v_call_broadcast.php\">\n";
	echo "<div align='center'>\n";

	echo "<div align='center'>\n";
	echo "<table width='50%'>\n";
	echo "<tr>\n";
	echo "<th align='left'>Message</th>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td class='rowstyle1'><strong>Call Broadcast has been started.</strong></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>\n";

	require_once "includes/footer.php";
	return;

}


require_once "includes/header.php";


$groupid = $_GET["groupid"];
$call_broadcast_id = $_GET["call_broadcast_id"];
$usercategory = $_GET["usercategory"];
$gateway = $_GET["gateway"];
$phonetype1 = $_GET["phonetype1"];
$phonetype2 = $_GET["phonetype2"];



$sql = "";
$sql .= "select * from v_call_broadcast ";
$sql .= "where call_broadcast_id = '$call_broadcast_id' ";
$prepstatement = $db->prepare(check_sql($sql));
$prepstatement->execute();
while($row = $prepstatement->fetch()) {
	$broadcast_name = $row["broadcast_name"];
	$broadcast_desc = $row["broadcast_desc"];
	$broadcast_timeout = $row["broadcast_timeout"];
	$broadcast_concurrent_limit = $row["broadcast_concurrent_limit"];
	$recordingid = $row["recordingid"];
	$broadcast_caller_id_name = $row["broadcast_caller_id_name"];
	$broadcast_caller_id_number = $row["broadcast_caller_id_number"];
	$broadcast_destination_type = $row["broadcast_destination_type"];
	$broadcast_destination_data = $row["broadcast_destination_data"];
	break; //limit to 1 row
}
unset ($prepstatement);

if (strlen($broadcast_caller_id_name) == 0) {
	$broadcast_caller_id_name = "anonymous";
}
if (strlen($broadcast_caller_id_number) == 0) {
	$broadcast_caller_id_number = "0000000000";
}

//get the recording name
	$recording_filename = get_recording_filename($recordingid);


//remove unsafe characters from the name
	$broadcast_name = str_replace(" ", "", $broadcast_name);
	$broadcast_name = str_replace("'", "", $broadcast_name);



//$tmpjs .= "function extract_numbers(str){\n";
//$tmpjs .= "	return str.match(/\\d+/g);\n";


//start the count at 0
$response = exec($bin_dir."/fs_cli -x \"global_setvar broadcast_".$broadcast_name."_count=0\"");

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "		<br>";


	echo "<table width='100%' border='0'><tr>\n";
	echo "<td width='50%' nowrap><b>Contact List</b></td>\n";
	echo "<td width='50%' align='right'>&nbsp;</td>\n";
	echo "</tr></table>\n";

	if (strlen($groupid) > 0) {
		$sql = "";
		$sql .= " select * from v_users as u, v_group_members as m ";
		$sql .= " where u.username = m.username ";
		$sql .= " and m.groupid = '".$groupid."' ";
		$sql .= " and u.usercategory = '".$usercategory."' ";
		//echo $sql;
	}
	else {
		$sql = "";
		$sql .= " select * from v_users as u ";
		$sql .= " where u.usercategory = '".$usercategory."' ";
		//echo $sql;
	}
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	unset ($prepstatement, $sql);

	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr>\n";
	//echo thorderby('username', 'Username', $orderby, $order);
	echo thorderby('usertype', 'Type', $orderby, $order);
	//echo thorderby('usercategory', 'Category', $orderby, $order);
	echo thorderby('userfirstname', 'First Name', $orderby, $order);
	echo thorderby('userlastname', 'Last Name', $orderby, $order);
	echo thorderby('usercompanyname', 'Organization', $orderby, $order);
	echo thorderby('userphone1', 'Phone1', $orderby, $order);
	echo thorderby('userphone2', 'Phone2', $orderby, $order);
	echo "<tr>\n";

	if ($resultcount == 0) { //no results
	}
	else { //received results
		foreach($result as $row) {
			//print_r( $row );
			echo "<tr >\n";
			//echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[username]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[usertype]."&nbsp;</td>\n";
			//echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[usercategory]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[userfirstname]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[userlastname]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[usercompanyname]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[userphone1]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[userphone2]."&nbsp;</td>\n";
			echo "</tr>\n";

			$tmpjs .= "var count = getGlobalVariable('broadcast_".$broadcast_name."_count');\n";
			$tmpjs .= "setGlobalVariable(\"broadcast_".$broadcast_name."_count\", (parseInt(count)+1));\n";
			//cmd_async($cmd);

			//if (strlen($gateway) > 0) {
				if ($phonetype1 == "phone1" && strlen($row[userphone1]) > 0) { $phone1 = $row[userphone1]; }
				if ($phonetype1 == "phone2" && strlen($row[userphone2]) > 0) { $phone1 = $row[userphone2]; }
				if ($phonetype1 == "cell" && strlen($row[userphonemobile]) > 0) { $phone1 = $row[userphonemobile]; }
				if ($phonetype2 == "phone1" && strlen($row[userphone2]) > 0) { $phone2 = $row[userphone2]; }
				if ($phonetype2 == "phone2" && strlen($row[userphone2]) > 0) { $phone2 = $row[userphone2]; }
				if ($phonetype2 == "cell" && strlen($row[userphonemobile]) > 0) { $phone2 = $row[userphonemobile]; }

			//make sure the phone numbers are correct
				$phone1 = str_replace("-", "", $phone1);
				$phone1 = str_replace("(", "", $phone1);
				$phone1 = str_replace(")", "", $phone1);
				$phone1 = str_replace(" ", "", $phone1);
				$phone1 = str_replace(".", "", $phone1);
				$phone2 = str_replace("-", "", $phone2);
				$phone2 = str_replace("(", "", $phone2);
				$phone2 = str_replace(")", "", $phone2);
				$phone2 = str_replace(" ", "", $phone2);
				$phone2 = str_replace(".", "", $phone2);
				if (strlen($phone1) == 10) {
					$phone1 = "1".$phone1;
				}
				if (strlen($phone2) == 10) {
					$phone2 = "1".$phone2;
				}

			//set the global variable
			//$cmd = $bin_dir."/fs_cli -x \"global_setvar broadcast_test_count=75\"";
			//echo exec($cmd);

			//get the global variable
			//$cmd = $bin_dir."/fs_cli -x \"global_getvar broadcast_test_count\"";
			//echo exec($cmd);
			//echo "phone1: $phone1<br />\n";
			//echo "phone2: $phone2<br />\n";

			//make the call
				if (strlen($phone1)> 0) {
					if (strlen($broadcast_concurrent_limit) > 0) {
						$broadcast_count = trim(exec($bin_dir."/fs_cli -x \"global_getvar broadcast_".$broadcast_name."_count\""));
						$response = exec($bin_dir."/fs_cli -x \"global_setvar broadcast_".$broadcast_name."_count=".($broadcast_count+1)."\"");
					}
					if ($gateway == "loopback") {
						$cmd = $bin_dir."/fs_cli -x \"jsrun call_broadcast_originate.js {call_timeout=".$broadcast_timeout."}loopback/".$phone1."/default/XML ".$recording_filename." '".$broadcast_caller_id_name."' ".$broadcast_caller_id_number." ".$broadcast_timeout." broadcast_".$broadcast_name."_count\"";
					}
					else {
						$cmd = $bin_dir."/fs_cli -x \"jsrun call_broadcast_originate.js {call_timeout=".$broadcast_timeout."}sofia/gateway/".$gateway."/".$phone1." ".$recording_filename." '".$broadcast_caller_id_name."' ".$broadcast_caller_id_number." ".$broadcast_timeout." broadcast_".$broadcast_name."_count\"";
					}
					//echo $cmd."<br />\n";
					cmd_async($cmd);
				}
				if (strlen($phone2)> 0) {
					if (strlen($broadcast_concurrent_limit) > 0) {
						$broadcast_count = trim(exec($bin_dir."/fs_cli -x \"global_getvar broadcast_".$broadcast_name."_count\""));
						$response = exec($bin_dir."/fs_cli -x \"global_setvar broadcast_".$broadcast_name."_count=".($broadcast_count+1)."\"");
					}
					if ($gateway == "loopback") {
						$cmd = $bin_dir."/fs_cli -x \"jsrun call_broadcast_originate.js {call_timeout=".$broadcast_timeout."}loopback/".$phone2."/default/XML ".$recording_filename." '".$broadcast_caller_id_name."' ".$broadcast_caller_id_number." ".$broadcast_timeout." broadcast_".$broadcast_name."_count\"";
					}
					else {
						$cmd = $bin_dir."/fs_cli -x \"jsrun call_broadcast_originate.js {call_timeout=".$broadcast_timeout."}sofia/gateway/".$gateway."/".$phone2." ".$recording_filename." '".$broadcast_caller_id_name."' ".$broadcast_caller_id_number." ".$broadcast_timeout." broadcast_".$broadcast_name."_count\"";
					}					//echo $cmd."<br />\n";
					cmd_async($cmd);
				}

			//check the number of calls do not continue wait until the number is below the limit
				$x = 0;
				while (true) {
					if (strlen($broadcast_concurrent_limit) == 0) {
						break; //for testing
					}
					$broadcast_count = trim(exec($bin_dir."/fs_cli -x \"global_getvar broadcast_".$broadcast_name."_count\""));
					if ($broadcast_count < $broadcast_concurrent_limit) {
						break;
					}
					else {
						//100000 microseconds = 0.1 seconds
						usleep(100000);
					}
					if ($x > 200) {
						$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
						$cmd = "api show channels count";
						$response = event_socket_request($fp, $cmd);
						$concurrent_count = preg_replace("/[^0-9]/", '', $response);
						fclose($fp);

						$response = trim(exec($bin_dir."/fs_cli -x \"global_setvar broadcast_".$broadcast_name."_count=$concurrent_count\""));
						$x = 0;
					}
					$x++;
				}

			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results

	echo "</table>";
	echo "</div>";
	echo "<br><br>";
	echo "<br><br>";

	//echo "  <input type='button' class='btn' value='call now' onclick=\"document.location.href='v_call_broadcast_send.php?f=".$filename."';\" />\n";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br><br>";


require_once "includes/footer.php";
unset ($resultcount);
unset ($result);
unset ($key);
unset ($val);
unset ($c);
?>
