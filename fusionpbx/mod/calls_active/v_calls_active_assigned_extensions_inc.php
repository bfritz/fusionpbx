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

//http get and set variables
	if (strlen($_GET['url']) > 0) {
		$url = $_GET['url'];
	}

// active extensions -----------------------------

		//get a list of assigned extensions for this user
			$sql = "";
			$sql .= " select * from v_extensions ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and user_list like '%|".$_SESSION["username"]."|%' ";
			$prepstatement = $db->prepare(check_sql($sql));
			$prepstatement->execute();
			//$v_mailboxes = '';
			$x = 0;
			$result = $prepstatement->fetchAll();
			foreach ($result as &$row) {
				//$v_mailboxes = $v_mailboxes.$row["extension"].'|';
				//$extension_id = $row["extension_id"]
				//$extension = $row["extension"]
				$user_array[$x]['extension_id'] = $row["extension_id"];
				$user_array[$x]['extension'] = $row["extension"];
				$x++;
			}
			unset ($prepstatement, $x);
			//$user_list = str_replace("\n", "|", "|".$user_list);
			//echo "v_mailboxes $v_mailboxes<br />";
			//$user_array = explode ("|", $v_mailboxes);
			//echo "<pre>\n";
			//print_r($user_array);
			//echo "</pre>\n";


		echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
		//echo "<tr>\n";
		//echo "<td >\n";
		//echo "	<strong>Count: $row_count</strong>\n";
		//echo "</td>\n";
		//echo "<td colspan='2'>\n";
		//echo "	&nbsp;\n";
		//echo "</td>\n";
		//echo "<td colspan='1' align='right'>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<th>Extension</th>\n";
		echo "<th>Time</th>\n";
		echo "<th>Direction</th>\n";
		echo "<th>Profile</th>\n";
		echo "<th>CID Name</th>\n";
		echo "<th>CID Number</th>\n";
		echo "<th>Dest</th>\n";
		echo "<th>Application</th>\n";
		echo "<th>Secure</th>\n";
		echo "<th>Description</th>\n";
		echo "<th>Options</th>\n";
		echo "</tr>\n";
		foreach ($_SESSION['extension_array'] as $row) {

			//print_r($row);
			$v_id = $row['v_id'];
			$extension = $row['extension'];
			$enabled = $row['enabled'];
			$description = $row['description'];

			foreach ($user_array as &$user_row) {
				if ($extension == $user_row['extension']) {

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
					} //end foreach

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
					else {
						$style_alternate = "style=\"color: #444444; background-image: url('/images/background_cell_light.gif');\"";
						echo "<tr>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>$extension</td>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>&nbsp;</td>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>&nbsp;</td>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>&nbsp;</td>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>&nbsp;</td>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>&nbsp;</td>\n";
					}

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

					if ($found_extension) {
						echo "<td class='".$rowstyle[$c]."' $style_alternate>\n";
					}
					else {
						echo "<td valign='top' class='".$rowstyle[$c]."' $style_alternate>\n";
					}
					echo "".$description."<br />\n";
					echo "</td>\n";

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
						echo "	&nbsp;";
						echo "</td>\n";
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

					if ($c==0) { $c=1; } else { $c=0; }
				} //end if
			} //end foreach
		}

	echo "</table>\n";

echo "<br /><br />\n";

?>