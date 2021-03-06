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
	Copyright (C) 2010
	All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";


//http get and set variables
	if (strlen($_GET['url']) > 0) {
		$url = $_GET['url'];
	}
	if (strlen($_GET['rows']) > 0) {
		$rows = $_GET['rows'];
	}
	else {
		$rows = 0;
	}


//set http compression
	if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
		ob_start("ob_gzhandler");
	}
	else{
		ob_start();
	}

//define variables
	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

//get the event socket information
	if (strlen($_SESSION['event_socket_ip_address']) == 0) {
		$sql = "";
		$sql .= "select * from v_settings ";
		$sql .= "where v_id = '$v_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		foreach ($result as &$row) {
			$_SESSION['event_socket_ip_address'] = $row["event_socket_ip_address"];
			$_SESSION['event_socket_port'] = $row["event_socket_port"];
			$_SESSION['event_socket_password'] = $row["event_socket_password"];
			break; //limit to 1 row
		}
	}

//get information over event socket
	$switch_cmd = 'show channels as xml';
	$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
	$xml_str = trim(event_socket_request($fp, 'api '.$switch_cmd));
	//echo $xml_str;

//parse the xml
	try {
		$xml = new SimpleXMLElement($xml_str);
	}
	catch(Exception $e) {
		//echo $e->getMessage();
	}
	//print_r($xml);

//active channels array ----------------------
	$channel_array = "";
	foreach ($xml as $row) {
		//print_r($row);
		$name = $row->name;
		//echo $name;
		$name_array = explode("/", $name);
		$sip_profile = $name_array[1];
		$sip_uri = $name_array[2];
		//echo $sip_uri;
		$temp_array = explode("@", $sip_uri);
		$number = $temp_array[0];
		$number = str_replace("sip:", "", $number);
		$row->addChild('number', $number);
		$row->addChild('sip_profile', $sip_profile);
		//$row->addAttribute('number', $number);
	}
	//echo "<pre>\n";
	//print_r($xml);
	//echo "</pre>\n";


//active extensions -----------------------------
	//get the extension information
		if (count($_SESSION['extension_array']) == 0) {
			$sql = "";
			$sql .= "select * from v_extensions ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "order by extension asc ";
			$prepstatement = $db->prepare(check_sql($sql));
			$prepstatement->execute();
			$result = $prepstatement->fetchAll();
			foreach ($result as &$row) {
				if ($row["enabled"] == "true") {
					$extension = $row["extension"];
					//echo $extension;
					$extension_array[$extension]['v_id'] = $row["v_id"];
					$extension_array[$extension]['extension'] = $row["extension"];

					//$extension_array[$extension]['password'] = $row["password"];
					$extension_array[$extension]['user_list'] = $row["user_list"];
					$extension_array[$extension]['mailbox'] = $row["mailbox"];
					//$vm_password = $row["vm_password"];
					//$vm_password = str_replace("#", "", $vm_password); //preserves leading zeros
					//$_SESSION['extension_array'][$extension]['vm_password'] = $vm_password;
					$extension_array[$extension]['accountcode'] = $row["accountcode"];
					$extension_array[$extension]['effective_caller_id_name'] = $row["effective_caller_id_name"];
					$extension_array[$extension]['effective_caller_id_number'] = $row["effective_caller_id_number"];
					$extension_array[$extension]['outbound_caller_id_name'] = $row["outbound_caller_id_name"];
					$extension_array[$extension]['outbound_caller_id_number'] = $row["outbound_caller_id_number"];
					$extension_array[$extension]['vm_mailto'] = $row["vm_mailto"];
					$extension_array[$extension]['vm_attach_file'] = $row["vm_attach_file"];
					$extension_array[$extension]['vm_keep_local_after_email'] = $row["vm_keep_local_after_email"];
					$extension_array[$extension]['user_context'] = $row["user_context"];
					$extension_array[$extension]['callgroup'] = $row["callgroup"];
					$extension_array[$extension]['auth_acl'] = $row["auth_acl"];
					$extension_array[$extension]['cidr'] = $row["cidr"];
					$extension_array[$extension]['sip_force_contact'] = $row["sip_force_contact"];
					//$extension_array[$extension]['enabled'] = $row["enabled"];
					$extension_array[$extension]['description'] = $row["description"];
					//break; //limit to 1 row
				}
			}
			$_SESSION['extension_array'] = $extension_array;
		}
		//echo "<pre>\n";
		//print_r($_SESSION['extension_array']);
		//echo "</pre>\n";


	//get a list of assigned extensions for this user
		include "v_calls_active_assigned_extensions_inc.php";

	//list all extensions
		echo "<table width='100%' border='0' cellpadding='5' cellspacing='0'>\n";
		echo "<tr>\n";
		echo "<td valign='top'>\n";

		echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
		//echo "<tr>\n";
		//echo "<td >\n";
		//echo "	<strong>Count: $row_count</strong>\n";
		//echo "</td>\n";
		//echo "<td colspan='2'>\n";
		//echo "	&nbsp;\n";
		//echo "</td>\n";
		//echo "<td colspan='1' align='right'>\n";
		//echo "</tr>\n";

		echo "<tr>\n";
		echo "<th>Ext</th>\n";
		echo "<th>Time</th>\n";
		if (ifgroup("admin") || ifgroup("superadmin")) {
			if (strlen(($_GET['rows'])) == 0) {
				echo "<th>Direction</th>\n";
				echo "<th>Profile</th>\n";
				echo "<th>CID Name</th>\n";
				echo "<th>CID Number</th>\n";
				echo "<th>Dest</th>\n";
				echo "<th>App</th>\n";
				echo "<th>Secure</th>\n";
			}
		}
		echo "<th>Description</th>\n";
		if (ifgroup("admin") || ifgroup("superadmin")) {
			if (strlen(($_GET['rows'])) == 0) {
				echo "<th>Options</th>\n";
			}
		}
		echo "</tr>\n";
		$x = 1;
		foreach ($_SESSION['extension_array'] as $row) {
			//print_r($row);
			$v_id = $row['v_id'];
			$extension = $row['extension'];
			$enabled = $row['enabled'];
			$description = $row['description'];

			$found_extension = false;
			foreach ($xml as $tmp_row) {
				if ($tmp_row->number == $extension) {
					$found_extension = true;
					$uuid = $tmp_row->uuid;
					$direction = $tmp_row->direction;
					$sip_profile = $tmp_row->sip_profile;
					$created = $tmp_row->created;
					$created_epoch = $tmp_row->created_epoch;
					$name = $tmp_row->name;
					$state = $tmp_row->state;
					$cid_name = $tmp_row->cid_name;
					$cid_num = $tmp_row->cid_num;
					$ip_addr = $tmp_row->ip_addr;
					$dest = $tmp_row->dest;
					$application = $tmp_row->application;
					$application_data = $tmp_row->application_data;
					$dialplan = $tmp_row->dialplan;
					$context = $tmp_row->context;
					$read_codec = $tmp_row->read_codec;
					$read_rate = $tmp_row->read_rate;
					$write_codec = $tmp_row->write_codec;
					$write_rate = $tmp_row->write_rate;
					$secure = $tmp_row->secure;

					//remove the '+' because it breaks the call recording
					$cid_num = str_replace("+", "", $cid_num);

					$call_length_seconds = time() - $created_epoch;
					$call_length_hour = floor($call_length_seconds/3600);
					$call_length_min = floor($call_length_seconds/60 - ($call_length_hour * 60));
					$call_length_sec = $call_length_seconds - (($call_length_hour * 3600) + ($call_length_min * 60));
					$call_length_min = sprintf("%02d", $call_length_min);
					$call_length_sec = sprintf("%02d", $call_length_sec);
					$call_length = $call_length_hour.':'.$call_length_min.':'.$call_length_sec;
				}
			}

			if ($found_extension) {
				if ($application == "conference") { 
					$alt_color = "background-image: url('/images/background_cell_active.gif";
				}
				switch ($application) {
				case "conference":
					$style_alternate = "style=\"color: #444444; background-image: url('/images/background_cell_conference.gif');\"";
					break;
				case "fifo":
					$style_alternate = "style=\"color: #444444; background-image: url('/images/background_cell_fifo.gif');\"";
					break;
				default:
					$style_alternate = "style=\"color: #444444; background-image: url('/images/background_cell_active.gif');\"";
				}
				echo "<tr>\n";
				echo "<td class='".$rowstyle[$c]."' $style_alternate>$extension</td>\n";
				echo "<td class='".$rowstyle[$c]."' $style_alternate width='20px;'>".$call_length."</td>\n";
				if (ifgroup("admin") || ifgroup("superadmin")) {
					if (strlen(($_GET['rows'])) == 0) {
						echo "<td class='".$rowstyle[$c]."' $style_alternate>$direction</td>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>$sip_profile</td>\n";
						if (strlen($url) == 0) {
							echo "<td class='".$rowstyle[$c]."' $style_alternate>".$cid_name."</td>\n";
							echo "<td class='".$rowstyle[$c]."' $style_alternate>".$cid_num."</td>\n";
						}
						else {
							echo "<td class='".$rowstyle[$c]."' $style_alternate><a href='".$url."cid_name=".$cid_name."&cid_num=".$cid_num."' style='color: #444444;' target='_blank'>".$cid_name."</a></td>\n";
							echo "<td class='".$rowstyle[$c]."' $style_alternate><a href='".$url."cid_name=".$cid_name."&cid_num=".$cid_num."' style='color: #444444;' target='_blank'>".$cid_num."</a></td>\n";
						}
					}
				}
			}
			else {
				$style_alternate = "style=\"color: #444444; background-image: url('/images/background_cell_light.gif');\"";
				echo "<tr>\n";
				echo "<td class='".$rowstyle[$c]."' $style_alternate>$extension</td>\n";
				echo "<td class='".$rowstyle[$c]."' $style_alternate>&nbsp;</td>\n";
				if (ifgroup("admin") || ifgroup("superadmin")) {
					if (strlen(($_GET['rows'])) == 0) {
						echo "<td class='".$rowstyle[$c]."' $style_alternate>&nbsp;</td>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>&nbsp;</td>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>&nbsp;</td>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>&nbsp;</td>\n";
					}
				}
			}
			if (ifgroup("admin") || ifgroup("superadmin")) {
				if (strlen(($_GET['rows'])) == 0) {
					if ($found_extension) {
						echo "<td class='".$rowstyle[$c]."' $style_alternate>\n";
					}
					else {
						echo "<td valign='top' class='".$rowstyle[$c]."' $style_alternate>\n";
					}
					echo "".$dest."<br />\n";
					echo "</td>\n";

					if ($found_extension) {
						echo "<td class='".$rowstyle[$c]."' $style_alternate>\n";
					}
					else {
						echo "<td valign='top' class='".$rowstyle[$c]."' $style_alternate>\n";
					}
					if ($application == "fifo") {
						echo "queue &nbsp;\n";
					}
					else {
						echo $application." &nbsp;\n";
					}
					echo "</td>\n";

					if ($found_extension) {
						echo "<td class='".$rowstyle[$c]."' $style_alternate>\n";
					}
					else {
						echo "<td valign='top' class='".$rowstyle[$c]."' $style_alternate>\n";
					}
					echo "".$secure."<br />\n";
					echo "</td>\n";
				}
			}
			if ($found_extension) {
				echo "<td class='".$rowstyle[$c]."' $style_alternate>\n";
			}
			else {
				echo "<td valign='top' class='".$rowstyle[$c]."' $style_alternate>\n";
			}
			echo "".$description."<br />\n";
			echo "</td>\n";
			if (ifgroup("admin") || ifgroup("superadmin")) {
				if (strlen(($_GET['rows'])) == 0) {
					if ($found_extension) {
						echo "<td valign='top' class='".$rowstyle[$c]."' $style_alternate>\n";
							//transfer
								//uuid_transfer c985c31b-7e5d-3844-8b3b-aa0835ff6db9 -bleg *9999 xml default
								//document.getElementById('url').innerHTML='v_calls_exec.php?action=energy&direction=down&cmd='+prepare_cmd(escape('$uuid'));
								echo "	<a href='javascript:void(0);' style='color: #444444;' onMouseover=\"document.getElementById('form_label').innerHTML='<strong>Transfer To</strong>';\" onclick=\"send_cmd('v_calls_exec.php?cmd='+get_transfer_cmd(escape('$uuid')));\">transfer</a>&nbsp;\n";

							//park
								echo "	<a href='javascript:void(0);' style='color: #444444;' onclick=\"send_cmd('v_calls_exec.php?cmd='+get_park_cmd(escape('$uuid')));\">park</a>&nbsp;\n";

							//hangup
								echo "	<a href='javascript:void(0);' style='color: #444444;' onclick=\"confirm_response = confirm('Do you really want to hangup this call?');if (confirm_response){send_cmd('v_calls_exec.php?cmd=uuid_kill%20'+(escape('$uuid')));}\">hangup</a>&nbsp;\n";

							//record start/stop
								$tmp_file = $v_recordings_dir."/active_extensions_".$cid_num."_recording.wav";
								if (file_exists($tmp_file)) {
									//stop
									echo "	<a href='javascript:void(0);' style='color: #444444;' onclick=\"send_cmd('v_calls_exec.php?cmd='+get_record_cmd(escape('$uuid'), 'active_extensions_', escape('$cid_num'))+'&uuid='+escape('$uuid')+'&action=record&action2=stop&prefix=active_extensions_&name='+escape('$cid_num'));\">stop record</a>&nbsp;\n";
								}
								else {
									//start
									echo "	<a href='javascript:void(0);' style='color: #444444;' onclick=\"send_cmd('v_calls_exec.php?cmd='+get_record_cmd(escape('$uuid'), 'active_extensions_', escape('$cid_num'))+'&uuid='+escape('$uuid')+'&action=record&action2=start&prefix=active_extensions_');\">start record</a>&nbsp;\n";
								}

							echo "	&nbsp;";
						echo "</td>\n";
					}
					else {
						echo "<td valign='top' class='".$rowstyle[$c]."' $style_alternate>\n";
						echo "	2&nbsp;";
						echo "</td>\n";
					}
				}
			}

			echo "</tr>\n";

			unset($found_extension);
			unset($uuid);
			unset($direction);
			unset($sip_profile);
			unset($created);
			unset($created_epoch);
			unset($name);
			unset($state);
			unset($cid_name);
			unset($cid_num);
			unset($ip_addr);
			unset($dest);
			unset($application);
			unset($application_data);
			unset($dialplan);
			unset($context);
			unset($read_codec);
			unset($read_rate);
			unset($write_codec);
			unset($write_rate);
			unset($secure);

			if ($x == $rows) {
				$x = 0;
				echo "</table>\n";

				echo "</td>\n";
				echo "<td valign='top'>\n";

				echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
				echo "<tr>\n";
				echo "<th>Ext</th>\n";
				echo "<th>Time</th>\n";
				if (ifgroup("admin") || ifgroup("superadmin")) {
					if (strlen(($_GET['rows'])) == 0) {
						echo "<th>Direction</th>\n";
						echo "<th>Profile</th>\n";
						echo "<th>CID Name</th>\n";
						echo "<th>CID Number</th>\n";
						echo "<th>Dest</th>\n";
						echo "<th>App</th>\n";
						echo "<th>Secure</th>\n";
					}
				}
				echo "<th>Description</th>\n";
				if (ifgroup("admin") || ifgroup("superadmin")) {
					if (strlen(($_GET['rows'])) == 0) {
						echo "<th>Options</th>\n";
					}
				}
				echo "</tr>\n";
			}
			$x++;
			if ($c==0) { $c=1; } else { $c=0; }
		}

	echo "</table>\n";



echo "<br /><br />\n";
echo "<div id='cmd_reponse'>\n";
echo "</div>\n";

?>