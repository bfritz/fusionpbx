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
if (ifgroup("agent") || ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//get the queue_name and set it as a variable
	$queue_name = $_GET[queue_name].'@'. $_SESSION['domains'][$v_id]['domain'];

//convert the string to a named array
	function str_to_named_array($tmp_str, $tmp_delimiter) {
		$tmp_array = explode ("\n", $tmp_str);
		$result = '';
		if (trim(strtoupper($tmp_array[0])) != "+OK") {
			$tmp_field_name_array = explode ($tmp_delimiter, $tmp_array[0]);
			$x = 0;
			foreach ($tmp_array as $row) {
				if ($x > 0) {
					$tmp_field_value_array = explode ($tmp_delimiter, $tmp_array[$x]);
					$y = 0;
					foreach ($tmp_field_value_array as $tmp_value) {
						$tmp_name = $tmp_field_name_array[$y];
						if (trim(strtoupper($tmp_value)) != "+OK") {
							$result[$x][$tmp_name] = $tmp_value;
						}
						$y++;
					}
				}
				$x++;
			}
			unset($row);
		}
		return $result;
	}

//alternate the color of the row
	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

//create an event socket connection
	$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);

//get the call center queue, agent and tiers list
	if (!$fp) {
		$msg = "<div align='center'>Connection to Event Socket failed.<br /></div>"; 
		echo "<div align='center'>\n";
		echo "<table width='40%'>\n";
		echo "<tr>\n";
		echo "<th align='left'>Message</th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td class='rowstyle1'><strong>$msg</strong></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";
	}
	else {
		//get the queue list
			//send the event socket command and get the response
				//callcenter_config queue list members [queue_name]
				$switch_cmd = 'callcenter_config queue list members '.$queue_name;
				$event_socket_str = trim(event_socket_request($fp, 'api '.$switch_cmd));
				$result = str_to_named_array($event_socket_str, '|');

			//show the title
				echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
				echo "  <tr>\n";
				echo "	<td align='left'><b>".ucfirst($_GET[queue_name])." Queue</b><br />\n";
				echo "		Shows a list of callers in the queue.<br />\n";
				echo "	</td>\n";
				echo "  </tr>\n";
				echo "</table>\n";
				echo "<br />\n";

			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "<tr>\n";
			echo "<th>Time</th>\n";
			//echo "<th>System</th>\n";
			echo "<th>Name</th>\n";
			echo "<th>Number</th>\n";
			echo "<th>Status</th>\n";
			if (ifgroup("admin") || ifgroup("superadmin")) {
				echo "<th>Options</th>\n";
			}
			echo "</tr>\n";

			foreach ($result as $row) {
				$switch_cmd = 'uuid_exists '.$row['session_uuid'];
				if (trim(event_socket_request($fp, 'api '.$switch_cmd)) == "true") {
					$queue = $row['queue'];
					$system = $row['system'];
					$uuid = $row['uuid'];
					$session_uuid = $row['session_uuid'];
					$caller_number = $row['caller_number'];
					$caller_name = $row['caller_name'];
					$system_epoch = $row['system_epoch'];
					$joined_epoch = $row['joined_epoch'];
					$rejoined_epoch = $row['rejoined_epoch'];
					$bridge_epoch = $row['bridge_epoch'];
					$abandoned_epoch = $row['abandoned_epoch'];
					$base_score = $row['base_score'];
					$skill_score = $row['skill_score'];
					$serving_agent = $row['serving_agent'];
					$serving_system = $row['serving_system'];
					$state = $row['state'];
	
					$joined_seconds = time() - $joined_epoch;
					$joined_length_hour = floor($joined_seconds/3600);
					$joined_length_min = floor($joined_seconds/60 - ($joined_length_hour * 60));
					$joined_length_sec = $joined_seconds - (($joined_length_hour * 3600) + ($joined_length_min * 60));
					$joined_length_min = sprintf("%02d", $joined_length_min);
					$joined_length_sec = sprintf("%02d", $joined_length_sec);
					$joined_length = $joined_length_hour.':'.$joined_length_min.':'.$joined_length_sec;
	
					//$system_seconds = time() - $system_epoch;
					//$system_length_hour = floor($system_seconds/3600);
					//$system_length_min = floor($system_seconds/60 - ($system_length_hour * 60));
					//$system_length_sec = $system_seconds - (($system_length_hour * 3600) + ($system_length_min * 60));
					//$system_length_min = sprintf("%02d", $system_length_min);
					//$system_length_sec = sprintf("%02d", $system_length_sec);
					//$system_length = $system_length_hour.':'.$system_length_min.':'.$system_length_sec;
	
					//get the extensions that are assigned to this user 
					$user_extension_array = explode("|", $_SESSION['user_extension_list']);
					echo "<tr>\n";
					echo "<td valign='top' class='".$rowstyle[$c]."'>".$joined_length."</td>\n";
					//echo "<td valign='top' class='".$rowstyle[$c]."'>".$system_length."</td>\n";
					echo "<td valign='top' class='".$rowstyle[$c]."'>".$caller_name."&nbsp;</td>\n";
					echo "<td valign='top' class='".$rowstyle[$c]."'>".$caller_number."&nbsp;</td>\n";
					echo "<td valign='top' class='".$rowstyle[$c]."'>".$state."</td>\n";
					if (ifgroup("admin") || ifgroup("superadmin")) {
						echo "<td valign='top' class='".$rowstyle[$c]."'>";
						echo "	<a href='javascript:void(0);' style='color: #444444;' onclick=\"confirm_response = confirm('Do you really want to do this?');if (confirm_response){send_cmd('v_call_center_exec.php?cmd=originate+user/".$user_extension_array[0]."+%26eavesdrop(".$uuid.")');}\">eavesdrop</a>&nbsp;\n";
						echo "</td>";
					}
					echo "</tr>\n";
					if ($c==0) { $c=1; } else { $c=0; }
				} //end if uuid_exists
			}
			echo "</table>\n";

		//add vertical spacing
			echo "<br />\n";
			echo "<br />\n";
			echo "<br />\n";

		//get the agent list

			//show the title
				echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
				echo "  <tr>\n";
				echo "	<td align='left'><b>Agents</b><br />\n";
				echo "		List all the agents.<br />\n";
				echo "	</td>\n";
				echo "  </tr>\n";
				echo "</table>\n";
				echo "<br />\n";

			//send the event socket command and get the response
				//callcenter_config queue list tiers [queue_name] | 
				$switch_cmd = 'callcenter_config queue list tiers '.$queue_name;
				$event_socket_str = trim(event_socket_request($fp, 'api '.$switch_cmd));
				$result = str_to_named_array($event_socket_str, '|');

			//prepare the result for array_multisort
				$x = 0;
				foreach ($result as $row) {
					$tier_result[$x]['level'] = $row['level'];
					$tier_result[$x]['position'] = $row['position'];
					$tier_result[$x]['agent'] = $row['agent'];
					$tier_result[$x]['state'] = trim($row['state']);
					$tier_result[$x]['queue'] = $row['queue'];
					$x++;
				}

			//sort the array //SORT_ASC, SORT_DESC, SORT_REGULAR, SORT_NUMERIC, SORT_STRING
				array_multisort($tier_result, SORT_ASC);

			//send the event socket command and get the response
				//callcenter_config queue list agents [queue_name] [status] | 
				$switch_cmd = 'callcenter_config queue list agents '.$queue_name;
				$event_socket_str = trim(event_socket_request($fp, 'api '.$switch_cmd));
				$agent_result = str_to_named_array($event_socket_str, '|');

			//list the agents
				echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
				echo "<tr>\n";
				echo "<th>Name</th>\n";
				echo "<th>Contact</th>\n";
				echo "<th>Status</th>\n";
				echo "<th>State</th>\n";
				//echo "<th>Offered Call</th>\n";
				echo "<th>Status Change</th>\n";
				echo "<th>Missed</th>\n";
				echo "<th>Answered</th>\n";
				echo "<th>Tier State</th>\n";
				echo "<th>Tier Level</th>\n";
				echo "<th>Tier Position</th>\n";
				echo "</tr>\n";
				foreach ($tier_result as $tier_row) {
					//$queue = $tier_row['queue'];
					//$queue = str_replace('@'.$v_domain, '', $queue);
					$agent = $tier_row['agent'];
					//$agent = str_replace('@'.$v_domain, '', $agent);
					$tier_state = $tier_row['state'];
					$tier_level = $tier_row['level'];
					$tier_position = $tier_row['position'];

					foreach ($agent_result as $agent_row) {
						if ($tier_row['agent'] == $agent_row['name']) {
							$name = $agent_row['name'];
							$name = str_replace('@'.$v_domain, '', $name);
							//$system = $agent_row['system'];
							//$uuid = $agent_row['uuid'];
							//$type = $agent_row['type'];
							$contact = $agent_row['contact'];
							$status = $agent_row['status'];
							$state = $agent_row['state'];
							$max_no_answer = $agent_row['max_no_answer'];
							$wrap_up_time = $agent_row['wrap_up_time'];
							$reject_delay_time = $agent_row['reject_delay_time'];
							$busy_delay_time = $agent_row['busy_delay_time'];
							$last_bridge_start = $agent_row['last_bridge_start'];
							$last_bridge_end = $agent_row['last_bridge_end'];
							//$last_offered_call = $agent_row['last_offered_call'];
							$last_status_change = $agent_row['last_status_change'];
							$no_answer_count = $agent_row['no_answer_count'];
							$calls_answered = $agent_row['calls_answered'];
							$talk_time = $agent_row['talk_time'];
							$ready_time = $agent_row['ready_time'];

							$last_offered_call_seconds = time() - $last_offered_call;
							$last_offered_call_length_hour = floor($last_offered_call_seconds/3600);
							$last_offered_call_length_min = floor($last_offered_call_seconds/60 - ($last_offered_call_length_hour * 60));
							$last_offered_call_length_sec = $last_offered_call_seconds - (($last_offered_call_length_hour * 3600) + ($last_offered_call_length_min * 60));
							$last_offered_call_length_min = sprintf("%02d", $last_offered_call_length_min);
							$last_offered_call_length_sec = sprintf("%02d", $last_offered_call_length_sec);
							$last_offered_call_length = $last_offered_call_length_hour.':'.$last_offered_call_length_min.':'.$last_offered_call_length_sec;

							$last_status_change_seconds = time() - $last_status_change;
							$last_status_change_length_hour = floor($last_status_change_seconds/3600);
							$last_status_change_length_min = floor($last_status_change_seconds/60 - ($last_status_change_length_hour * 60));
							$last_status_change_length_sec = $last_status_change_seconds - (($last_status_change_length_hour * 3600) + ($last_status_change_length_min * 60));
							$last_status_change_length_min = sprintf("%02d", $last_status_change_length_min);
							$last_status_change_length_sec = sprintf("%02d", $last_status_change_length_sec);
							$last_status_change_length = $last_status_change_length_hour.':'.$last_status_change_length_min.':'.$last_status_change_length_sec;

							echo "<tr>\n";
							echo "<td valign='top' class='".$rowstyle[$c]."'>".$name."</td>\n";
							echo "<td valign='top' class='".$rowstyle[$c]."'>".$contact."</td>\n";
							echo "<td valign='top' class='".$rowstyle[$c]."'>".$status."</td>\n";
							echo "<td valign='top' class='".$rowstyle[$c]."'>".$state."</td>\n";
							//echo "<td valign='top' class='".$rowstyle[$c]."'>".$last_offered_call_length."</td>\n";
							echo "<td valign='top' class='".$rowstyle[$c]."'>".$last_status_change_length."</td>\n";
							echo "<td valign='top' class='".$rowstyle[$c]."'>".$no_answer_count."</td>\n";
							echo "<td valign='top' class='".$rowstyle[$c]."'>".$calls_answered."</td>\n";
							echo "<td valign='top' class='".$rowstyle[$c]."'>".$tier_state."</td>\n";
							echo "<td valign='top' class='".$rowstyle[$c]."'>".$tier_level."</td>\n";
							echo "<td valign='top' class='".$rowstyle[$c]."'>".$tier_position."</td>\n";
						}
					}
					echo "</tr>\n";

					if ($c==0) { $c=1; } else { $c=0; }
				}
				echo "</table>\n";

		//add vertical spacing
			echo "<br />\n";
			echo "<br />\n";
			echo "<br />\n";
	}
?>