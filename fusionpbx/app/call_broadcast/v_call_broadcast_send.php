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
if (permission_exists('call_broadcast_send')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//set the max execution time to 1 hour
	ini_set(max_execution_time,3600);

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

//get the http get values and set as php variables
	$group_name = $_GET["group_name"];
	$call_broadcast_uuid = $_GET["call_broadcast_uuid"];
	$user_category = $_GET["user_category"];
	$gateway = $_GET["gateway"];
	$phonetype1 = $_GET["phonetype1"];
	$phonetype2 = $_GET["phonetype2"];

//get the call broadcast details from the database
	$sql = "";
	$sql .= "select * from v_call_broadcasts ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and call_broadcast_uuid = '$call_broadcast_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	while($row = $prep_statement->fetch()) {
		$broadcast_name = $row["broadcast_name"];
		$broadcast_description = $row["broadcast_description"];
		$broadcast_timeout = $row["broadcast_timeout"];
		$broadcast_concurrent_limit = $row["broadcast_concurrent_limit"];
		$recordingid = $row["recordingid"];
		$broadcast_caller_id_name = $row["broadcast_caller_id_name"];
		$broadcast_caller_id_number = $row["broadcast_caller_id_number"];
		$broadcast_destination_type = $row["broadcast_destination_type"];
		$broadcast_phone_numbers = $row["broadcast_phone_numbers"];
		$broadcast_destination_data = $row["broadcast_destination_data"];
		//if (strlen($row["broadcast_destination_data"]) == 0) {
		//	$broadcast_destination_application = '';
		//	$broadcast_destination_data = '';
		//}
		//else {
		//	$broadcast_destination_array = explode(":", $row["broadcast_destination_data"]);
		//	$broadcast_destination_application = $broadcast_destination_array[0];
		//	$broadcast_destination_data = $broadcast_destination_array[1];
		//}
		break; //limit to 1 row
	}
	unset ($prep_statement);

	if (strlen($broadcast_caller_id_name) == 0) {
		$broadcast_caller_id_name = "anonymous";
	}
	if (strlen($broadcast_caller_id_number) == 0) {
		$broadcast_caller_id_number = "0000000000";
	}

	//get the recording name
		//$recording_filename = get_recording_filename($recordingid);

//remove unsafe characters from the name
	$broadcast_name = str_replace(" ", "", $broadcast_name);
	$broadcast_name = str_replace("'", "", $broadcast_name);

//create the event socket connection
	$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);

//get information over event socket
	if (!$fp) {
		require_once "includes/header.php";
		$msg = "<div align='center'>Connection to Event Socket failed.<br /></div>"; 
		echo "<div align='center'>\n";
		echo "<table width='40%'>\n";
		echo "<tr>\n";
		echo "<th align='left'>Message</th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td class='row_style1'><strong>$msg</strong></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";
		require_once "includes/footer.php";
	}
	else {
		//show the header
			require_once "includes/header.php";

		//send the call broadcast
			if (strlen($broadcast_phone_numbers) > 0) {
				$broadcast_phone_number_array = explode ("\n", $broadcast_phone_numbers);
				$count = 1;
				$sched_seconds = '3';
				foreach ($broadcast_phone_number_array as $tmp_value) {
					$tmp_value = str_replace(";", "|", $tmp_value);
					$tmp_value_array = explode ("|", $tmp_value);

					//make sure the phone numbers are correct
						$phone1 = trim($tmp_value_array[0]);
						$phone1 = str_replace("-", "", $phone1);
						$phone1 = str_replace("(", "", $phone1);
						$phone1 = str_replace(")", "", $phone1);
						$phone1 = str_replace(" ", "", $phone1);
						$phone1 = str_replace(".", "", $phone1);

					//get the correct gateway
						$bridge_array = outbound_route_to_bridge ($phone1);

					//prepare the string
						$channel_variables = "ignore_early_media=true,origination_number=$phone1,origination_caller_id_name='$broadcast_caller_id_name',origination_caller_id_number=$broadcast_caller_id_number";
						$origination_url = "{".$channel_variables."}".$bridge_array[0]."";

					//get the context
						if (count($_SESSION["domains"]) > 1) {
							$context =  $_SESSION['domain_name'];
						}
						else {
							$context = "default";
						}

					//set the command
						$cmd = "bgapi sched_api +".$sched_seconds." none bgapi originate ".$origination_url." ".$broadcast_destination_data." XML $context";

					//if the event socket connection is lost then re-connect
						if (!$fp) {
							$fp = eventsocket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
						}

					//method 1
						$response = trim(event_socket_request($fp, 'api '.$cmd));
						
					//method 2
						//cmd_async($_SESSION['switch']['bin']['dir']."/fs_cli -x \"".$cmd."\";");

					//spread the calls out so that they are scheduled with different times
						if (strlen($broadcast_concurrent_limit) > 0 && strlen($broadcast_timeout) > 0) {
							if ($broadcast_concurrent_limit == $count) { 
								$sched_seconds = $sched_seconds + $broadcast_timeout;
								$count=0;
							}
						}

					$count++;
				}
				fclose($fp);

				//echo "<meta http-equiv=\"refresh\" content=\"2;url=".PROJECT_PATH."/app/calls_active/v_calls_active.php\">\n";
				echo "<div align='center'>\n";
				echo "<table width='50%'>\n";
				echo "<tr>\n";
				echo "<th align='left'>Message</th>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td class='row_style1' align='center'>\n";
				echo "	<strong>Call Broadcast $broadcast_name has been started.</strong>\n";
				echo "	<br /><br />\n";
				echo "	<table width='100%'>\n";
				echo "	<tr>\n";
				echo "	<td align='center'>\n";
				echo "		<a href='".PROJECT_PATH."/app/calls_active/v_calls_active.php'>View Calls</a>\n";
				echo "	</td>\n";
				echo "	</table>\n";
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";
				echo "</div>\n";
				
			}

		//show the footer
			require_once "includes/footer.php";
	}

/*
//reserved for future use

require_once "includes/header.php";

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "		<br>";


	echo "<table width='100%' border='0'><tr>\n";
	echo "<td width='50%' nowrap><b>Contact List</b></td>\n";
	echo "<td width='50%' align='right'>&nbsp;</td>\n";
	echo "</tr></table>\n";

		$broadcast_phone_number_array = explode ("\n", $broadcast_phone_numbers);
		foreach ($broadcast_phone_number_array as $tmp_value) {
			$tmp_value = str_replace(";", "|", $tmp_value);
			$tmp_value_array = explode ("|", $tmp_value);

			//make sure the phone numbers are correct
				$phone1 = trim($tmp_value_array[0]);
				$phone1 = str_replace("-", "", $phone1);
				$phone1 = str_replace("(", "", $phone1);
				$phone1 = str_replace(")", "", $phone1);
				$phone1 = str_replace(" ", "", $phone1);
				$phone1 = str_replace(".", "", $phone1);

			if ($gateway == "loopback") {
				$cmd = $_SESSION['switch']['bin']['dir']."/fs_cli -x \"luarun call_broadcast_originate.lua {call_timeout=".$broadcast_timeout."}loopback/".$phone1."/default/XML ".$_SESSION['switch']['recordings']['dir']."/".$recording_filename." '".$broadcast_caller_id_name."' ".$broadcast_caller_id_number." ".$broadcast_timeout." '".$broadcast_destination_application."' '".$broadcast_destination_data."'\";";
			}
			else {
				$cmd = $_SESSION['switch']['bin']['dir']."/fs_cli -x \"luarun call_broadcast_originate.lua {call_timeout=".$broadcast_timeout."}sofia/gateway/".$gateway."/".$phone1." ".$_SESSION['switch']['recordings']['dir']."/".$recording_filename." '".$broadcast_caller_id_name."' ".$broadcast_caller_id_number." ".$broadcast_timeout." '".$broadcast_destination_application."' '".$broadcast_destination_data."'\";";
			}
			echo $cmd."<br />\n";
			cmd_async($cmd);
			//sleep(60);
		}

	if (strlen($group_name) > 0) {
		$sql = " select * from v_users as u, v_group_users as m ";
		$sql .= " where u.user_uuid = m.user_uuid ";
		$sql .= " and m.group_name = '".$group_name."' ";
		$sql .= " and u.user_category = '".$user_category."' ";
		//echo $sql."<br />";
	}
	else {
		$sql = " select * from v_users as u ";
		$sql .= " where u.user_category = '".$user_category."' ";
		//echo $sql."<br />";
	}
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	$result_count = count($result);
	unset ($prep_statement, $sql);

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	//echo th_order_by('username', 'Username', $order_by, $order);
	echo th_order_by('user_type', 'Type', $order_by, $order);
	//echo th_order_by('user_category', 'Category', $order_by, $order);
	echo th_order_by('user_first_name', 'First Name', $order_by, $order);
	echo th_order_by('user_last_name', 'Last Name', $order_by, $order);
	echo th_order_by('user_company_name', 'Organization', $order_by, $order);
	echo th_order_by('user_phone_1', 'Phone1', $order_by, $order);
	echo th_order_by('user_phone_2', 'Phone2', $order_by, $order);
	echo "<tr>\n";

	if ($result_count > 0) {
		foreach($result as $row) {
			echo "<tr >\n";
			//echo "	<td valign='top' class='".$row_style[$c]."'>".$row[username]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row[user_type]."&nbsp;</td>\n";
			//echo "	<td valign='top' class='".$row_style[$c]."'>".$row[user_category]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row[user_first_name]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row[user_last_name]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row[user_company_name]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row[user_phone_1]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row[user_phone_2]."&nbsp;</td>\n";
			echo "</tr>\n";

			//if (strlen($gateway) > 0) {
				if ($phonetype1 == "phone1" && strlen($row[user_phone_1]) > 0) { $phone1 = $row[user_phone_1]; }
				if ($phonetype1 == "phone2" && strlen($row[user_phone_2]) > 0) { $phone1 = $row[user_phone_2]; }
				if ($phonetype1 == "cell" && strlen($row[user_phone_mobile]) > 0) { $phone1 = $row[user_phone_mobile]; }
				if ($phonetype2 == "phone1" && strlen($row[user_phone_2]) > 0) { $phone2 = $row[user_phone_2]; }
				if ($phonetype2 == "phone2" && strlen($row[user_phone_2]) > 0) { $phone2 = $row[user_phone_2]; }
				if ($phonetype2 == "cell" && strlen($row[user_phone_mobile]) > 0) { $phone2 = $row[user_phone_mobile]; }

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

			//make the call
				if (strlen($phone1)> 0) {
					if ($gateway == "loopback") {
						$cmd = $_SESSION['switch']['bin']['dir']."/fs_cli -x \"luarun call_broadcast_originate.lua {call_timeout=".$broadcast_timeout."}loopback/".$phone1."/default/XML ".$_SESSION['switch']['recordings']['dir']."/".$recording_filename." '".$broadcast_caller_id_name."' ".$broadcast_caller_id_number." ".$broadcast_timeout." '".$broadcast_destination_application."' '".$broadcast_destination_data."'\";";
					}
					else {
						$cmd = $_SESSION['switch']['bin']['dir']."/fs_cli -x \"luarun call_broadcast_originate.lua {call_timeout=".$broadcast_timeout."}sofia/gateway/".$gateway."/".$phone1." ".$_SESSION['switch']['recordings']['dir']."/".$recording_filename." '".$broadcast_caller_id_name."' ".$broadcast_caller_id_number." ".$broadcast_timeout." '".$broadcast_destination_application."' '".$broadcast_destination_data."'\";";
					}
					//echo $cmd."<br />\n";
					cmd_async($cmd);
				}
				if (strlen($phone2)> 0) {
					if ($gateway == "loopback") {
						$cmd = $_SESSION['switch']['bin']['dir']."/fs_cli -x \"luarun call_broadcast_originate.lua {call_timeout=".$broadcast_timeout."}loopback/".$phone2."/default/XML ".$_SESSION['switch']['recordings']['dir']."/".$recording_filename." '".$broadcast_caller_id_name."' ".$broadcast_caller_id_number." ".$broadcast_timeout." '".$broadcast_destination_application."' '".$broadcast_destination_data."'\";";
					}
					else {
						$cmd = $_SESSION['switch']['bin']['dir']."/fs_cli -x \"luarun call_broadcast_originate.lua {call_timeout=".$broadcast_timeout."}sofia/gateway/".$gateway."/".$phone2." ".$_SESSION['switch']['recordings']['dir']."/".$recording_filename." '".$broadcast_caller_id_name."' ".$broadcast_caller_id_number." ".$broadcast_timeout." '".$broadcast_destination_application."' '".$broadcast_destination_data."'\";";
					}
					//echo $cmd."<br />\n";
					cmd_async($cmd);
				}

			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results

	echo "</table>";
	echo "</div>";
	echo "<br><br>";
	echo "<br><br>";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br><br>";


require_once "includes/footer.php";
unset ($result_count);
unset ($result);
unset ($key);
unset ($val);
unset ($c);
*/
?>
