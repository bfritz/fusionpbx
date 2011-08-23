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
if (permission_exists('extensions_active_assigned_view')) {

	//http get and set variables
		if (strlen($_GET['url']) > 0) {
			$url = $_GET['url'];
		}

	//get a list of assigned extensions for this user
		if (count($_SESSION['user_extension_array']) == 0) {
			$sql = "";
			$sql .= "select * from v_extensions ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and user_list like '%|".$_SESSION["username"]."|%' ";
			$prepstatement = $db->prepare(check_sql($sql));
			$prepstatement->execute();
			$x = 0;
			$result = $prepstatement->fetchAll();
			foreach ($result as &$row) {
				$user_extension_array[$x]['extension_id'] = $row["extension_id"];
				$user_extension_array[$x]['extension'] = $row["extension"];
				$x++;
			}
			unset ($prepstatement, $x);
			$_SESSION['user_extension_array'] = $user_extension_array;
		}

		echo "<table width='100%' border='0' cellpadding='5' cellspacing='0'>\n";
		echo "<tr>\n";
		echo "<td valign='top'>\n";

		echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
		echo "<tr>\n";
		echo "<th width='50px;'>Ext</th>\n";
		if ($_SESSION['user_status_display'] == "false") {
			//hide the user_status when it is set to false
		}
		else {
			echo "<th>Status</th>\n";
		}
		echo "<th>Time</th>\n";
		//echo "<th>Direction</th>\n";
		//echo "<th>Profile</th>\n";
		echo "<th>CID Name</th>\n";
		echo "<th>CID Number</th>\n";
		echo "<th>Dest</th>\n";
		echo "<th>Application</th>\n";
		echo "<th>Secure</th>\n";
		echo "<th>Name</th>\n";
		echo "<th>Options</th>\n";
		echo "</tr>\n";
		foreach ($_SESSION['extension_array'] as $row) {
			$v_id = $row['v_id'];
			$extension = $row['extension'];
			$enabled = $row['enabled'];
			$effective_caller_id_name = $row['effective_caller_id_name'];

			foreach ($_SESSION['user_extension_array'] as &$user_row) {
				if ($extension == $user_row['extension']) {
					$found_extension = false;
					$x = 1;

					foreach ($channels_array as $row) {
						//set the php variables
							foreach ($row as $key => $value) {
								$$key = $value;
							}
						//find the matching extensions
							if ($number == $extension) {
								//set the found extension to true
									$found_extension = true;
									break;
							}
					} //end foreach

					if ($number == $extension) {
						if ($application == "conference") { 
							$alt_color = "background-image: url('".PROJECT_PATH."/images/background_cell_active.gif";
						}
						switch ($application) {
						case "conference":
							$style_alternate = "style=\"color: #444444; background-image: url('".PROJECT_PATH."/images/background_cell_conference.gif');\"";
							break;
						case "fifo":
							$style_alternate = "style=\"color: #444444; background-image: url('".PROJECT_PATH."/images/background_cell_fifo.gif');\"";
							break;
						case "valet_park":
							$style_alternate = "style=\"color: #444444; background-image: url('".PROJECT_PATH."/images/background_cell_fifo.gif');\"";
							break;
						default:
							$style_alternate = "style=\"color: #444444; background-image: url('".PROJECT_PATH."/images/background_cell_active.gif');\"";
						}
						echo "<tr>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>$extension</td>\n";
						if ($_SESSION['user_status_display'] == "false") {
							//hide the user_status when it is set to false
						}
						else {
							echo "<td class='".$rowstyle[$c]."' $style_alternate>".$user_array[$extension]['user_status']."&nbsp;</td>\n";
						}
						echo "<td class='".$rowstyle[$c]."' $style_alternate width='20px;'>".$call_length."</td>\n";

						if (strlen($url) == 0) {
							$url = "/mod/contacts/v_contacts.php?search_all={cid_num}";
						}
						$url = str_replace ("{cid_num}", $cid_num, $url);
						$url = str_replace ("{cid_name}", $cid_name, $url);
						echo "<td class='".$rowstyle[$c]."' $style_alternate><a href='".$url."' style='color: #444444;' target='_blank'>".$cid_name."</a></td>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate><a href='".$url."' style='color: #444444;' target='_blank'>".$cid_num."</a></td>\n";

						//get the active uuid list
							if (strlen($uuid) > 1) {
								if (strlen($uuid_1) == 0) {
									$uuid_1 = $uuid;
									//$direction_1 = $direction;
									$cid_name_1 = $cid_name;
									$cid_num_1 = $cid_num;
								}
								if (strlen($uuid_1) > 0 && $uuid != $uuid_1) {
									$uuid_2 = $uuid;
									//$direction_2 = $direction;
									$cid_name_2 = $cid_name;
									$cid_num_2 = $cid_num;
								}
							}

						echo "<td class='".$rowstyle[$c]."' $style_alternate>\n";
						if ($application == "valet_park") {
							echo $valet_array[trim($uuid)]['extension']."\n";
						}
						else {
							echo $dest."&nbsp;\n";
						}
						echo "</td>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>\n";
						if ($application == "fifo") {
							echo "queue &nbsp;\n";
						}
						else {
							echo $application."&nbsp;\n";
						}
						echo "</td>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>\n";
						echo "".$secure."&nbsp;\n";
						echo "</td>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>\n";
						echo "".$effective_caller_id_name."&nbsp;\n";
						echo "</td>\n";
						echo "<td valign='top' class='".$rowstyle[$c]."' $style_alternate>\n";
							//transfer
								echo "	<a href='javascript:void(0);' style='color: #444444;' onMouseover=\"document.getElementById('form_label').innerHTML='<strong>Transfer To</strong>';\" onclick=\"send_cmd('v_calls_exec.php?cmd='+get_transfer_cmd(escape('$uuid')));\">transfer</a>&nbsp;\n";
							//park
								echo "	<a href='javascript:void(0);' style='color: #444444;' onclick=\"send_cmd('v_calls_exec.php?cmd='+get_park_cmd(escape('$uuid')));\">park</a>&nbsp;\n";
							//hangup
								echo "	<a href='javascript:void(0);' style='color: #444444;' onclick=\"confirm_response = confirm('Do you really want to hangup this call?');if (confirm_response){send_cmd('v_calls_exec.php?cmd=uuid_kill%20'+(escape('$uuid')));}\">hangup</a>&nbsp;\n";
							//record start/stop
								$tmp_file = $v_recordings_dir."/archive/".date("Y")."/".date("M")."/".date("d")."/".$uuid.".wav";
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
						echo "</tr>\n";
					}
					else {
						$style_alternate = "style=\"color: #444444; background-image: url('".PROJECT_PATH."/images/background_cell_light.gif');\"";
						echo "<tr>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>$extension</td>\n";
						if ($_SESSION['user_status_display'] == "false") {
							//hide the user_status when it is set to false
						}
						else {
							echo "<td class='".$rowstyle[$c]."' $style_alternate>".$user_array[$extension]['user_status']."&nbsp;</td>\n";
						}
						echo "<td class='".$rowstyle[$c]."' $style_alternate>&nbsp;</td>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>&nbsp;</td>\n";
						echo "<td class='".$rowstyle[$c]."' $style_alternate>&nbsp;</td>\n";
						echo "<td valign='top' class='".$rowstyle[$c]."' $style_alternate>\n";
						echo "	&nbsp;";
						echo "</td>\n";
						echo "<td valign='top' class='".$rowstyle[$c]."' $style_alternate>\n";
						echo "	&nbsp;";
						echo "</td>\n";
						echo "<td valign='top' class='".$rowstyle[$c]."' $style_alternate>\n";
						echo "	&nbsp;";
						echo "</td>\n";
						echo "<td valign='top' class='".$rowstyle[$c]."' $style_alternate>\n";
						echo "	&nbsp;";
						echo "</td>\n";
						echo "<td valign='top' class='".$rowstyle[$c]."' $style_alternate>\n";
						echo "	&nbsp;";
						echo "</td>\n";
						echo "</tr>\n";
					}
					if ($c==0) { $c=1; } else { $c=0; }
				} //end if
			} //end foreach
		}
		echo "</table>\n";

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<span id='uuid_1' style='visibility:hidden;'>$uuid_1</span>\n";
		//echo "<span id='direction_1' style='visibility:hidden;'>$direction_1</span>\n";
		echo "<span id='cid_name_1' style='visibility:hidden;'>$cid_name_1</span>\n";
		echo "<span id='cid_num_1' style='visibility:hidden;'>$cid_num_1</span>\n";

		echo "<span id='uuid_2' style='visibility:hidden;'>$uuid_2</span>\n";
		//echo "<span id='direction_2' style='visibility:hidden;'>$direction_2</span>\n";
		echo "<span id='cid_name_2' style='visibility:hidden;'>$cid_name_2</span>\n";
		echo "<span id='cid_num_2' style='visibility:hidden;'>$cid_num_2</span>\n";

		echo "<br />\n";
}

?>