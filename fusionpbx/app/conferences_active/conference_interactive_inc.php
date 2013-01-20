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
	James Rose <james.o.rose@gmail.com>
*/
include "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists('conferences_active_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//add multi-lingual support
	require_once "app_languages.php";
	foreach($text as $key => $value) {
		$text[$key] = $value[$_SESSION['domain']['language']['code']];
	}

//get the http get or post and set it as php variables
	$conference_name = check_str($_REQUEST["c"]);

//determine if the user should have access to the conference room
	if (if_group("superadmin") || if_group("admin")) {
		//access granted
	}
	else {
		//get the conference_uuid from the coference_name
			$sql = "select conference_uuid from v_conferences ";
			$sql .= "where conference_name = '".$conference_name."' ";
			$sql .= "and domain_uuid = '".$_SESSION['domain_uuid']."' ";
			$prep_statement = $db->prepare($sql);
			if ($prep_statement) {
			$prep_statement->execute();
				$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
				$conference_uuid = $row['conference_uuid'];
			}

		//show only assigned extensions
			$sql = "select count(*) as num_rows from v_conferences as c, v_conference_users as u ";
			$sql .= "where c.conference_uuid = u.conference_uuid ";
			$sql .= "and c.conference_uuid = '".$conference_uuid."' ";
			$sql .= "and c.domain_uuid = '".$_SESSION['domain_uuid']."' ";
			$sql .= "and u.user_uuid = '".$_SESSION['user_uuid']."' ";
			if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; }
			$prep_statement = $db->prepare($sql);
			if ($prep_statement) {
			$prep_statement->execute();
				$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
				if ($row['num_rows'] == 0) {
					echo $text['message-denied'];
					exit;
				}
			}
	}

//replace the space with underscore
	$conference_name = $conference_name.'-'.$_SESSION['domain_name'];

//create the conference list command
	$switch_cmd = "conference '".$conference_name."' xml_list";

//connect to event socket, send the command and process the results
	$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
	if (!$fp) {
		$msg = "<div align='center'>".$text['message-connection']."<br /></div>"; 
		echo "<div align='center'>\n";
		echo "<table width='40%'>\n";
		echo "<tr>\n";
		echo "<th align='left'>".$text['label-message']."</th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td class='row_style1'><strong>$msg</strong></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";
	}
	else {
		//show the content
		$xml_str = trim(event_socket_request($fp, 'api '.$switch_cmd));
		try {
			$xml = new SimpleXMLElement($xml_str);
		}
		catch(Exception $e) {
			//echo $e->getMessage();
		}
		//$name = $xml->conference['name'];
		$session_uuid = $xml->conference['uuid'];
		$member_count = $xml->conference['member-count'];
		$locked = $xml->conference['locked'];
		$recording = $xml->conference['recording'];

		$c = 0;
		$row_style["0"] = "row_style0";
		$row_style["1"] = "row_style1";

		echo "<div id='cmd_reponse'>\n";
		echo "</div>\n";

		echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
		echo "<tr>\n";
		echo "<td >\n";
		echo "	<strong>Count: $member_count</strong>\n";
		echo "</td>\n";
		echo "<td colspan='9'>\n";
		echo "	&nbsp;\n";
		echo "</td>\n";
		echo "<td colspan='1' align='right'>\n";
		if (permission_exists('conferences_active_record') || permission_exists('conferences_active_lock')) {
			echo "	<strong>".$text['label-tools'].":</strong> \n";
		}

		$recording_dir = $_SESSION['switch']['recordings']['dir'].'/archive/'.date("Y").'/'.date("M").'/'.date("d");
		$recording_name = '';
		if (file_exists($recording_dir.'/'.$row['uuid'].'.wav')) {
			$recording_name = $session_uuid.".wav";
		}
		elseif (file_exists($recording_dir.'/'.$row['uuid'].'.mp3')) {
			$recording_name = $session_uuid.".mp3";
		}

		if (permission_exists('conferences_active_record')) {
			if ($recording == "true") {
				echo "	<a href='javascript:void(0);' onclick=\"send_cmd('conference_exec.php?cmd=conference&name=".$conference_name."&uuid=".$session_uuid."&data=norecord');\">".$text['button-stop-rec']."</a>&nbsp;\n";
			}
			else {
				echo "	<a href='javascript:void(0);' onclick=\"send_cmd('conference_exec.php?cmd=conference&name=".$conference_name."&uuid=".$session_uuid."&data=record');\">".$text['button-start-rec']."</a>&nbsp;\n";
			}
		}
		else {
			if ($recording == "true") {
				echo "	".$text['label-recording']." &nbsp;";
			}
			else {
				echo "	".$text['label-not-recording']." &nbsp;";
			}
		}
		if (permission_exists('conferences_active_lock')) {
			if ($locked == "true") {
				echo "	<a href='javascript:void(0);' onclick=\"send_cmd('conference_exec.php?cmd=conference&name=".$conference_name."&data=unlock');\">".$text['label-unlock']."</a>&nbsp;\n";
			}
			else {
				echo "	<a href='javascript:void(0);' onclick=\"send_cmd('conference_exec.php?cmd=conference&name=".$conference_name."&data=lock');\">".$text['label-lock']."</a>&nbsp;\n";
			}
		}
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<th>".$text['label-id']."</th>\n";
		//echo "<th>UUID</th>\n";
		echo "<th>".$text['label-cid-name']."</th>\n";
		echo "<th>".$text['label-cid-num']."</th>\n";
		echo "<th>".$text['label-joined']."</th>\n";
		echo "<th>".$text['label-hear']."</th>\n";
		echo "<th>".$text['label-speak']."</th>\n";
		echo "<th>".$text['label-talking']."</th>\n";
		echo "<th>".$text['label-last-talk']."</th>\n";
		echo "<th>".$text['label-video']."</th>\n";
		echo "<th>".$text['label-floor']."</th>\n";
		echo "<th>".$text['label-tool']."</th>\n";
		echo "</tr>\n";

		foreach ($xml->conference->members->member as $row) {
			$id = $row->id;
			$record_path = $row->record_path;
			$flag_can_hear = $row->flags->can_hear;
			$flag_can_speak = $row->flags->can_speak;
			$flag_talking = $row->flags->talking;
			$last_talking = $row->last_talking;
			$join_time = $row->join_time;
			$flag_has_video = $row->flags->has_video;
			$flag_has_floor = $row->flags->has_floor;
			$uuid = $row->uuid;
			$caller_id_name = $row->caller_id_name;
			$caller_id_name = str_replace("%20", " ", $caller_id_name);
			$caller_id_number = $row->caller_id_number;

			//format the seconds
			$join_time_formatted = floor($join_time/60)."' ".($join_time - (floor($join_time/60))*60)."\"";
			$last_talking_formatted = floor($last_talking/60)."' ".($last_talking - (floor($last_talking/60))*60)."\"";

			if (strlen($record_path) == 0) {
				echo "<tr>\n";
				echo "<td valign='top' class='".$row_style[$c]."'>$id</td>\n";
				//echo "<td valign='top' class='".$row_style[$c]."'>$uuid</td>\n";
				echo "<td valign='top' class='".$row_style[$c]."'>$caller_id_name</td>\n";
				echo "<td valign='top' class='".$row_style[$c]."'>$caller_id_number</td>\n";
				echo "<td valign='top' class='".$row_style[$c]."'>".$join_time_formatted."</td>\n";
				if ($flag_can_hear == "true") {
					echo "<td valign='top' class='".$row_style[$c]."'>".$text['label-yes']."</td>\n";
				}
				else {
					echo "<td valign='top' class='".$row_style[$c]."'>".$text['label-no']."</td>\n";
				}
				if ($flag_can_speak == "true") {
					echo "<td valign='top' class='".$row_style[$c]."'>".$text['label-yes']."</td>\n";
				}
				else {
					echo "<td valign='top' class='".$row_style[$c]."'>".$text['label-no']."</td>\n";
				}
				if ($flag_talking == "true") {
					echo "<td valign='top' class='".$row_style[$c]."'>".$text['label-yes']."</td>\n";
				}
				else {
					echo "<td valign='top' class='".$row_style[$c]."'>".$text['label-no']."</td>\n";
				}
				echo "<td valign='top' class='".$row_style[$c]."'>$last_talking_formatted</td>\n";
				if ($flag_has_video == "true") {
					echo "<td valign='top' class='".$row_style[$c]."'>".$text['label-yes']."</td>\n";
				}
				else {
					echo "<td valign='top' class='".$row_style[$c]."'>".$text['label-no']."</td>\n";
				}
				if ($flag_has_floor == "true") {
					echo "<td valign='top' class='".$row_style[$c]."'>".$text['label-yes']."</td>\n";
				}
				else {
					echo "<td valign='top' class='".$row_style[$c]."'>".$text['label-no']."</td>\n";
				}
				echo "<td valign='top' class='".$row_style[$c]."' style='text-align:right;'>\n";
				//energy
					if (permission_exists('conferences_active_energy')) {
						echo "	<a href='javascript:void(0);' onclick=\"send_cmd('conference_exec.php?direction=up&cmd=conference&name=".$conference_name."&data=energy&id=".$id."');\">+".$text['label-energy']."</a>&nbsp;\n";
						echo "	<a href='javascript:void(0);' onclick=\"send_cmd('conference_exec.php?direction=down&cmd=conference&name=".$conference_name."&data=energy&id=".$id."');\">-".$text['label-energy']."</a>&nbsp;\n";
					}
				//volume
					if (permission_exists('conferences_active_volume')) {
						echo "	<a href='javascript:void(0);' onclick=\"send_cmd('conference_exec.php?direction=up&cmd=conference&name=".$conference_name."%&data=volume_in&id=".$id."');\">+".$text['label-volume']."</a>&nbsp;\n";
						echo "	<a href='javascript:void(0);' onclick=\"send_cmd('conference_exec.php?direction=down&cmd=conference&name=".$conference_name."&data=volume_in&id=".$id."');\">-".$text['label-volume']."</a>&nbsp;\n";
					}
					if (permission_exists('conferences_active_gain')) {
						echo "	<a href='javascript:void(0);' onclick=\"send_cmd('conference_exec.php?direction=up&cmd=conference&name=".$conference_name."&data=volume_out&id=".$id."');\">+".$text['label-gain']."</a>&nbsp;\n";
						echo "	<a href='javascript:void(0);' onclick=\"send_cmd('conference_exec.php?direction=down&cmd=conference&name=".$conference_name."&data=volume_out&id=".$id."');\">-".$text['label-gain']."</a>&nbsp;\n";
					}
				//mute and unmute
					if (permission_exists('conferences_active_mute')) {
						if ($flag_can_speak == "true"){
							echo "	<a href='javascript:void(0);' onclick=\"send_cmd('conference_exec.php?cmd=conference&name=".$conference_name."&data=mute&id=".$id."');\">".$text['label-mute']."</a>&nbsp;\n";
						}
						else {
							echo "	<a href='javascript:void(0);' onclick=\"send_cmd('conference_exec.php?cmd=conference&name=".$conference_name."&data=unmute&id=".$id."');\">".$text['label-unmute']."</a>&nbsp;\n";
						}
					}
				//deaf and undeaf
					if (permission_exists('conferences_active_deaf')) {
						if ($flag_can_hear == "true"){
							echo "	<a href='javascript:void(0);' onclick=\"send_cmd('conference_exec.php?cmd=conference&name=".$conference_name."&data=deaf&id=".$id."');\">".$text['label-deaf']."</a>&nbsp;\n";
						}
						else {
							echo "	<a href='javascript:void(0);' onclick=\"send_cmd('conference_exec.php?cmd=conference&name=".$conference_name."&data=undeaf&id=".$id."');\">".$text['label-undeaf']."</a>&nbsp;\n";
						}
					}
				//kick someone from the conference
					if (permission_exists('conferences_active_kick')) {
						echo "	<a href='javascript:void(0);' onclick=\"send_cmd('conference_exec.php?cmd=conference&name=".$conference_name."&data=kick&id=".$id."');\">".$text['label-kick']."</a>&nbsp;\n";
					}
				echo "	&nbsp;";
				echo "</td>\n";
				echo "</tr>\n";
			}
			if ($c==0) { $c=1; } else { $c=0; }
		}
		echo "</table>\n";
	}
?>