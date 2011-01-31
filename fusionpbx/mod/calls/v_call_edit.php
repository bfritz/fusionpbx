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
require_once "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";

function destination_select($select_name, $select_value, $select_default) {
	if (strlen($select_value) == 0) { $select_value = $select_default; }
	echo "	<select class='formfld' style='width: 45px;' name='$select_name'>\n";
	echo "	<option value=''></option>\n";

	$i=5;
	while($i<=100) {
		if ($select_value == $i) {
			echo "	<option value='$i' selected='selected'>$i</option>\n";
		}
		else {
			echo "	<option value='$i'>$i</option>\n";
		}
		$i=$i+5;
	}
	echo "</select>\n";
}

//get the extension_id
	$extension_id = $_REQUEST["id"];

//get the extension number
	$sql = "";
	$sql .= "select * from v_extensions ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and extension_id = '$extension_id' ";
	if (!ifgroup("admin") || !ifgroup("superadmin")) {
		$sql .= "and user_list like '%|".$_SESSION["username"]."|%' ";
	}
	$sql .= "and enabled = 'true' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	if (count($result)== 0) {
		echo "access denied";
		exit;
	}
	else {
		foreach ($result as &$row) {
			$extension = $row["extension"];
			$effective_caller_id_name = $row["effective_caller_id_name"];
			$effective_caller_id_number = $row["effective_caller_id_number"];
			$outbound_caller_id_name = $row["outbound_caller_id_name"];
			$outbound_caller_id_number = $row["outbound_caller_id_number"];
			$description = $row["description"];
			break; //limit to 1 row
		}
	}
	unset ($prepstatement);

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	//get http post variables and set them to php variables
		if (count($_POST)>0) {
			$call_forward_enabled = check_str($_POST["call_forward_enabled"]);
			$call_forward_number = check_str($_POST["call_forward_number"]);
			$follow_me_enabled = check_str($_POST["follow_me_enabled"]);
			$follow_me_type = check_str($_POST["follow_me_type"]);
			$destination_data_1 = check_str($_POST["destination_data_1"]);
			$destination_timeout_1 = check_str($_POST["destination_timeout_1"]);
			$destination_data_2 = check_str($_POST["destination_data_2"]);
			$destination_timeout_2 = check_str($_POST["destination_timeout_2"]);
			$destination_data_3 = check_str($_POST["destination_data_3"]);
			$destination_timeout_3 = check_str($_POST["destination_timeout_3"]);
			$destination_data_4 = check_str($_POST["destination_data_4"]);
			$destination_timeout_4 = check_str($_POST["destination_timeout_4"]);
			$destination_data_5 = check_str($_POST["destination_data_5"]);
			$destination_timeout_5 = check_str($_POST["destination_timeout_5"]);
			$dnd_enabled = check_str($_POST["dnd_enabled"]);
			$hunt_group_call_prompt = check_str($_POST["hunt_group_call_prompt"]);

			if (strlen($follow_me_type) == 0) { $follow_me_type = "follow_me_sequence"; }

			if (strlen($call_forward_number) > 0) {
				$call_forward_number = preg_replace("~[^0-9]~", "",$call_forward_number);
			}
			if (strlen($destination_data_1) > 0) {
				$destination_data_1 = preg_replace("~[^0-9]~", "",$destination_data_1);
			}
			if (strlen($destination_data_2) > 0) {
				$destination_data_2 = preg_replace("~[^0-9]~", "",$destination_data_2);
			}
			if (strlen($destination_data_3) > 0) {
				$destination_data_3 = preg_replace("~[^0-9]~", "",$destination_data_3);
			}
			if (strlen($destination_data_4) > 0) {
				$destination_data_4 = preg_replace("~[^0-9]~", "",$destination_data_4);
			}
			if (strlen($destination_data_5) > 0) {
				$destination_data_5 = preg_replace("~[^0-9]~", "",$destination_data_5);
			}

			//get the event socket information
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

			//set the default
				if (strlen($hunt_group_call_prompt) == 0) {
					$hunt_group_call_prompt = 'false';
				}

			//destination_1
				if (strlen($destination_data_1) > 0) {
					if (extension_exists($destination_data_1)) {
						$destination_type_1 = 'extension';
					}
					else {
						$destination_type_1 = 'sip uri';
					}
				}
			//destination_2
				if (extension_exists($destination_data_2)) {
					$destination_type_2 = 'extension';
				}
				else {
					$destination_type_2 = 'sip uri';
				}
			//destination_3
				if (extension_exists($destination_data_3)) {
					$destination_type_3 = 'extension';
				}
				else {
					$destination_type_3 = 'sip uri';
				}
			//destination_4
				if (extension_exists($destination_data_4)) {
					$destination_type_4 = 'extension';
				}
				else {
					$destination_type_4 = 'sip uri';
				}
			//destination_5
				if (extension_exists($destination_data_5)) {
					$destination_type_5 = 'extension';
				}
				else {
					$destination_type_5 = 'sip uri';
				}
		}

		//check for all required data
			//if (strlen($call_forward_enabled) == 0) { $msg .= "Please provide: Call Forward<br>\n"; }
			//if (strlen($call_forward_number) == 0) { $msg .= "Please provide: Number<br>\n"; }
			//if (strlen($follow_me_enabled) == 0) { $msg .= "Please provide: Follow Me<br>\n"; }
			//if (strlen($destination_data_1) == 0) { $msg .= "Please provide: 1st Number<br>\n"; }
			//if (strlen($destination_timeout_1) == 0) { $msg .= "Please provide: sec<br>\n"; }
			//if (strlen($destination_data_2) == 0) { $msg .= "Please provide: 2nd Number<br>\n"; }
			//if (strlen($destination_timeout_2) == 0) { $msg .= "Please provide: sec<br>\n"; }
			//if (strlen($destination_data_3) == 0) { $msg .= "Please provide: 3rd Number<br>\n"; }
			//if (strlen($destination_timeout_3) == 0) { $msg .= "Please provide: sec<br>\n"; }
			//if (strlen($destination_data_4) == 0) { $msg .= "Please provide: 4th Number<br>\n"; }
			//if (strlen($destination_timeout_4) == 0) { $msg .= "Please provide: sec<br>\n"; }
			//if (strlen($destination_data_5) == 0) { $msg .= "Please provide: 5th Number<br>\n"; }
			//if (strlen($destination_timeout_5) == 0) { $msg .= "Please provide: sec<br>\n"; }
			//if (strlen($destination_data_6) == 0) { $msg .= "Please provide: 6th Number<br>\n"; }
			//if (strlen($destination_timeout_6) == 0) { $msg .= "Please provide: sec<br>\n"; }
			//if (strlen($destination_data_7) == 0) { $msg .= "Please provide: 7th Number<br>\n"; }
			//if (strlen($destination_timeout_7) == 0) { $msg .= "Please provide: sec<br>\n"; }
			//if (strlen($hunt_group_call_prompt) == 0) { $msg .= "Please provide: call prompt<br>\n"; }
			if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
				require_once "includes/header.php";
				require_once "includes/persistformvar.php";
				echo "<div align='center'>\n";
				echo "<table><tr><td>\n";
				echo $msg."<br />";
				echo "</td></tr></table>\n";
				persistformvar($_POST);
				echo "</div>\n";
				require_once "includes/footer.php";
				return;
			}

	//set the default action to add
		$call_forward_action = "add";
		$dnd_action = "add";
		$follow_me_action = "add";

	//get the hunt group timeout
		//add the destination timeouts together to create the hunt group timeout
			if ($follow_me_type == "follow_me_sequence") {
				if (strlen($destination_data_1) > 0) {
					$hunt_group_timeout = $destination_timeout_1;
				}
				if (strlen($destination_data_2) > 0) {
					$hunt_group_timeout = $hunt_group_timeout + $destination_timeout_2;
				}
				if (strlen($destination_data_3) > 0) {
					$hunt_group_timeout = $hunt_group_timeout + $destination_timeout_3;
				}
				if (strlen($destination_data_4) > 0) {
					$hunt_group_timeout = $hunt_group_timeout + $destination_timeout_4;
				}
				if (strlen($destination_data_5) > 0) {
					$hunt_group_timeout = $hunt_group_timeout + $destination_timeout_5;
				}
			}
		//find the highest timeout and set that as the hunt_group_timeout
			if ($follow_me_type == "follow_me_simultaneous") {
				if (strlen($destination_data_1) > 0) {
					$hunt_group_timeout = $destination_timeout_1;
				}
				if (strlen($destination_data_2) > 0 && $hunt_group_timeout < $destination_timeout_2) {
					$hunt_group_timeout = $destination_timeout_2;
				}
				if (strlen($destination_data_3) > 0 && $hunt_group_timeout < $destination_timeout_3) {
					$hunt_group_timeout = $destination_timeout_3;
				}
				if (strlen($destination_data_4) > 0 && $hunt_group_timeout < $destination_timeout_4) {
					$hunt_group_timeout = $destination_timeout_4;
				}
				if (strlen($destination_data_5) > 0 && $hunt_group_timeout < $destination_timeout_5) {
					$hunt_group_timeout = $destination_timeout_5;
				}
			}

	//hunt_group information used to determine if this is an add or an update
		$sql = "";
		$sql .= "select * from v_hunt_group ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and huntgroupextension = '$extension' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		foreach ($result as &$row) {
			if ($row["huntgrouptype"] == 'call_forward') {
				$call_forward_action = "update";
				$call_forward_id = $row["hunt_group_id"];
			}
			if ($row["huntgrouptype"] == 'follow_me_sequence') {
				$follow_me_action = "update";
				$follow_me_id = $row["hunt_group_id"];
			}
			if ($row["huntgrouptype"] == 'follow_me_simultaneous') {
				$follow_me_action = "update";
				$follow_me_id = $row["hunt_group_id"];
			}
			if ($row["huntgrouptype"] == 'dnd') {
				$dnd_action = "update";
				$dnd_id = $row["hunt_group_id"];
			}
		}
		unset ($prepstatement);

	//call forward config
		$hunt_group_extension = $extension;
		$huntgroup_name = 'call_forward_'.$extension;
		$hunt_group_type = 'call_forward';
		$hunt_group_context = 'default';
		$hunt_group_timeout_destination = $extension;
		$hunt_group_timeout_type = 'voicemail';
		$hunt_group_ring_back = 'us-ring';
		$hunt_group_cid_name_prefix = '';
		$hunt_group_pin = '';
		$huntgroup_caller_announce = 'false';
		$hunt_group_user_list = '';
		$hunt_group_enabled = $call_forward_enabled;
		$hunt_group_descr = 'call forward '.$extension;

		if ($call_forward_action == "add") {
			$sql = "insert into v_hunt_group ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "huntgroupextension, ";
			$sql .= "huntgroupname, ";
			$sql .= "huntgrouptype, ";
			$sql .= "huntgroupcontext, ";
			$sql .= "huntgrouptimeout, ";
			$sql .= "huntgrouptimeoutdestination, ";
			$sql .= "huntgrouptimeouttype, ";
			$sql .= "huntgroupringback, ";
			$sql .= "huntgroupcidnameprefix, ";
			$sql .= "huntgrouppin, ";
			$sql .= "hunt_group_call_prompt, ";
			$sql .= "huntgroupcallerannounce, ";
			$sql .= "hunt_group_user_list, ";
			$sql .= "hunt_group_enabled, ";
			$sql .= "huntgroupdescr ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$hunt_group_extension', ";
			$sql .= "'$huntgroup_name', ";
			$sql .= "'$hunt_group_type', ";
			$sql .= "'$hunt_group_context', ";
			$sql .= "'$hunt_group_timeout', ";
			$sql .= "'$hunt_group_timeout_destination', ";
			$sql .= "'$hunt_group_timeout_type', ";
			$sql .= "'$hunt_group_ring_back', ";
			$sql .= "'$hunt_group_cid_name_prefix', ";
			$sql .= "'$hunt_group_pin', ";
			$sql .= "'$hunt_group_call_prompt', ";
			$sql .= "'$huntgroup_caller_announce', ";
			$sql .= "'$hunt_group_user_list', ";
			$sql .= "'$hunt_group_enabled', ";
			$sql .= "'$hunt_group_descr' ";
			$sql .= ")";
			if ($v_debug) {
				echo $sql."<br />";
			}
			if ($db_type == "sqlite" || $db_type == "mysql" ) {
				$db->exec(check_sql($sql));
				$call_forward_id = $db->lastInsertId($id);
			}
			if ($db_type == "pgsql") {
				$sql .= " RETURNING hunt_group_id ";
				$prepstatement = $db->prepare(check_sql($sql));
				$prepstatement->execute();
				$result = $prepstatement->fetchAll();
				foreach ($result as &$row) {
					$call_forward_id = $row["hunt_group_id"];
				}
				unset($prepstatement, $result);
			}
			unset($sql);

			//delete related v_hunt_group_destinations
				$sql = "delete from v_hunt_group_destinations where hunt_group_id = '$call_forward_id' ";
				$db->exec(check_sql($sql));

			if (extension_exists($call_forward_number)) {
				$destination_data = $call_forward_number;
				$destination_type = 'extension';
			}
			$destination_profile = 'internal';
			$destination_timeout = '';
			$destination_order = '1';
			$destination_enabled = 'true';
			$destination_descr = 'call forward';

			$sql = "insert into v_hunt_group_destinations ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "hunt_group_id, ";
			$sql .= "destinationdata, ";
			$sql .= "destinationtype, ";
			$sql .= "destinationprofile, ";
			$sql .= "destination_timeout, ";
			$sql .= "destinationorder, ";
			$sql .= "destination_enabled, ";
			$sql .= "destinationdescr ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$call_forward_id', ";
			$sql .= "'$destination_data', ";
			$sql .= "'$destination_type', ";
			$sql .= "'$destination_profile', ";
			$sql .= "'$destination_timeout', ";
			$sql .= "'$destination_order', ";
			$sql .= "'$destination_enabled', ";
			$sql .= "'$destination_descr' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);

		} //if ($call_forward_action == "add")

		if ($call_forward_action == "update") {
			$sql = "update v_hunt_group set ";
			$sql .= "huntgroupextension = '$hunt_group_extension', ";
			$sql .= "huntgroupname = '$huntgroup_name', ";
			$sql .= "huntgrouptype = '$hunt_group_type', ";
			$sql .= "huntgroupcontext = '$hunt_group_context', ";
			$sql .= "huntgrouptimeout = '$hunt_group_timeout', ";
			$sql .= "huntgrouptimeoutdestination = '$hunt_group_timeout_destination', ";
			$sql .= "huntgrouptimeouttype = '$hunt_group_timeout_type', ";
			$sql .= "huntgroupringback = '$hunt_group_ring_back', ";
			$sql .= "huntgroupcidnameprefix = '$hunt_group_cid_name_prefix', ";
			$sql .= "huntgrouppin = '$hunt_group_pin', ";
			$sql .= "hunt_group_call_prompt = '$hunt_group_call_prompt', ";
			$sql .= "huntgroupcallerannounce = '$huntgroup_caller_announce', ";
			$sql .= "hunt_group_user_list = '$hunt_group_user_list', ";
			$sql .= "hunt_group_enabled = '$hunt_group_enabled', ";
			$sql .= "huntgroupdescr = '$hunt_group_descr' ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and hunt_group_id = '$call_forward_id'";
			$db->exec(check_sql($sql));
			unset($sql);

			//set the variables
				$destination_data = $call_forward_number;
				if (extension_exists($call_forward_number)) {
					$destination_type = 'extension';
				}
				else {
					$destination_type = 'sip uri';
				}
				$destination_profile = 'internal';
				$destination_timeout = '';
				$destination_order = '1';
				$destination_enabled = 'true';
				$destination_descr = 'call forward';

			//delete related v_hunt_group_destinations
				$sql = "delete from v_hunt_group_destinations where hunt_group_id = '$call_forward_id' ";
				$db->exec(check_sql($sql));

			//insert the v_hunt_group_destinations
				$sql = "insert into v_hunt_group_destinations ";
				$sql .= "(";
				$sql .= "v_id, ";
				$sql .= "hunt_group_id, ";
				$sql .= "destinationdata, ";
				$sql .= "destinationtype, ";
				$sql .= "destinationprofile, ";
				$sql .= "destination_timeout, ";
				$sql .= "destinationorder, ";
				$sql .= "destination_enabled, ";
				$sql .= "destinationdescr ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$v_id', ";
				$sql .= "'$call_forward_id', ";
				$sql .= "'$destination_data', ";
				$sql .= "'$destination_type', ";
				$sql .= "'$destination_profile', ";
				$sql .= "'$destination_timeout', ";
				$sql .= "'$destination_order', ";
				$sql .= "'$destination_enabled', ";
				$sql .= "'$destination_descr' ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);
		} //if ($call_forward_action == "update")

	//follow me config
		$hunt_group_extension = $extension;
		$huntgroup_name = 'follow_me_'.$extension;
		$hunt_group_type = $follow_me_type;
		$hunt_group_context = 'default';
		//$hunt_group_timeout = '';
		$hunt_group_timeout_destination = $extension;
		$hunt_group_timeout_type = 'voicemail';
		$hunt_group_ring_back = 'us-ring';
		//$hunt_group_cid_name_prefix = '';
		//$hunt_group_pin = '';
		$huntgroup_caller_announce = 'false';
		//$hunt_group_user_list = '';
		$hunt_group_enabled = $follow_me_enabled;
		$hunt_group_descr = 'follow me '.$extension;

		if ($follow_me_action == "add") {
			$sql = "insert into v_hunt_group ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "huntgroupextension, ";
			$sql .= "huntgroupname, ";
			$sql .= "huntgrouptype, ";
			$sql .= "huntgroupcontext, ";
			$sql .= "huntgrouptimeout, ";
			$sql .= "huntgrouptimeoutdestination, ";
			$sql .= "huntgrouptimeouttype, ";
			$sql .= "huntgroupringback, ";
			$sql .= "huntgroupcidnameprefix, ";
			$sql .= "huntgrouppin, ";
			$sql .= "hunt_group_call_prompt, ";
			$sql .= "huntgroupcallerannounce, ";
			$sql .= "hunt_group_user_list, ";
			$sql .= "hunt_group_enabled, ";
			$sql .= "huntgroupdescr ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$hunt_group_extension', ";
			$sql .= "'$huntgroup_name', ";
			$sql .= "'$hunt_group_type', ";
			$sql .= "'$hunt_group_context', ";
			$sql .= "'$hunt_group_timeout', ";
			$sql .= "'$hunt_group_timeout_destination', ";
			$sql .= "'$hunt_group_timeout_type', ";
			$sql .= "'$hunt_group_ring_back', ";
			$sql .= "'$hunt_group_cid_name_prefix', ";
			$sql .= "'$hunt_group_pin', ";
			$sql .= "'$hunt_group_call_prompt', ";
			$sql .= "'$huntgroup_caller_announce', ";
			$sql .= "'$hunt_group_user_list', ";
			$sql .= "'$hunt_group_enabled', ";
			$sql .= "'$hunt_group_descr' ";
			$sql .= ")";
			if ($v_debug) {
				echo $sql."<br />";
			}
			if ($db_type == "sqlite" || $db_type == "mysql" ) {
				$db->exec(check_sql($sql));
				$follow_me_id = $db->lastInsertId($id);
			}
			if ($db_type == "pgsql") {
				$sql .= " RETURNING hunt_group_id ";
				$prepstatement = $db->prepare(check_sql($sql));
				$prepstatement->execute();
				$result = $prepstatement->fetchAll();
				foreach ($result as &$row) {
					$follow_me_id = $row["hunt_group_id"];
				}
				unset($prepstatement, $result);
			}
			unset($sql);
		} //if ($follow_me_action == "add")

		if ($follow_me_action == "update") {
			$sql = "update v_hunt_group set ";
			$sql .= "huntgroupextension = '$hunt_group_extension', ";
			$sql .= "huntgroupname = '$huntgroup_name', ";
			$sql .= "huntgrouptype = '$hunt_group_type', ";
			$sql .= "huntgroupcontext = '$hunt_group_context', ";
			$sql .= "huntgrouptimeout = '$hunt_group_timeout', ";
			$sql .= "huntgrouptimeoutdestination = '$hunt_group_timeout_destination', ";
			$sql .= "huntgrouptimeouttype = '$hunt_group_timeout_type', ";
			$sql .= "huntgroupringback = '$hunt_group_ring_back', ";
			$sql .= "huntgroupcidnameprefix = '$hunt_group_cid_name_prefix', ";
			$sql .= "huntgrouppin = '$hunt_group_pin', ";
			$sql .= "hunt_group_call_prompt = '$hunt_group_call_prompt', ";
			$sql .= "huntgroupcallerannounce = '$huntgroup_caller_announce', ";
			$sql .= "hunt_group_user_list = '$hunt_group_user_list', ";
			$sql .= "hunt_group_enabled = '$hunt_group_enabled', ";
			$sql .= "huntgroupdescr = '$hunt_group_descr' ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and hunt_group_id = '$follow_me_id'";
			$db->exec(check_sql($sql));
			unset($sql);
		} //if ($follow_me_action == "update")

		if ($follow_me_action == "add" || $follow_me_action == "update") {
			//set the variables
				$destination_profile = 'internal';
				$destination_timeout = '';
				$destination_order = '1';
				$destination_enabled = 'true';
				$destination_descr = 'follow me';

			//delete related v_hunt_group_destinations
				$sql = "delete from v_hunt_group_destinations where hunt_group_id = '$follow_me_id' ";
				$db->exec(check_sql($sql));

			//insert the v_hunt_group_destinations set destination_data_1
				if (strlen($destination_data_1) > 0) {
					$sql = "insert into v_hunt_group_destinations ";
					$sql .= "(";
					$sql .= "v_id, ";
					$sql .= "hunt_group_id, ";
					$sql .= "destinationdata, ";
					$sql .= "destinationtype, ";
					$sql .= "destinationprofile, ";
					$sql .= "destination_timeout, ";
					$sql .= "destinationorder, ";
					$sql .= "destination_enabled, ";
					$sql .= "destinationdescr ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$v_id', ";
					$sql .= "'$follow_me_id', ";
					$sql .= "'$destination_data_1', ";
					$sql .= "'$destination_type_1', ";
					$sql .= "'$destination_profile', ";
					$sql .= "'$destination_timeout_1', ";
					$sql .= "'$destination_order', ";
					$sql .= "'$destination_enabled', ";
					$sql .= "'$destination_descr' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);
				}
				if (strlen($destination_data_2) > 0) {
					$sql = "insert into v_hunt_group_destinations ";
					$sql .= "(";
					$sql .= "v_id, ";
					$sql .= "hunt_group_id, ";
					$sql .= "destinationdata, ";
					$sql .= "destinationtype, ";
					$sql .= "destinationprofile, ";
					$sql .= "destination_timeout, ";
					$sql .= "destinationorder, ";
					$sql .= "destination_enabled, ";
					$sql .= "destinationdescr ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$v_id', ";
					$sql .= "'$follow_me_id', ";
					$sql .= "'$destination_data_2', ";
					$sql .= "'$destination_type_2', ";
					$sql .= "'$destination_profile', ";
					$sql .= "'$destination_timeout_2', ";
					$sql .= "'$destination_order', ";
					$sql .= "'$destination_enabled', ";
					$sql .= "'$destination_descr' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);
				}
				if (strlen($destination_data_3) > 0) {
					$sql = "insert into v_hunt_group_destinations ";
					$sql .= "(";
					$sql .= "v_id, ";
					$sql .= "hunt_group_id, ";
					$sql .= "destinationdata, ";
					$sql .= "destinationtype, ";
					$sql .= "destinationprofile, ";
					$sql .= "destination_timeout, ";
					$sql .= "destinationorder, ";
					$sql .= "destination_enabled, ";
					$sql .= "destinationdescr ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$v_id', ";
					$sql .= "'$follow_me_id', ";
					$sql .= "'$destination_data_3', ";
					$sql .= "'$destination_type_3', ";
					$sql .= "'$destination_profile', ";
					$sql .= "'$destination_timeout_3', ";
					$sql .= "'$destination_order', ";
					$sql .= "'$destination_enabled', ";
					$sql .= "'$destination_descr' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);
				}
				if (strlen($destination_data_4) > 0) {
					$sql = "insert into v_hunt_group_destinations ";
					$sql .= "(";
					$sql .= "v_id, ";
					$sql .= "hunt_group_id, ";
					$sql .= "destinationdata, ";
					$sql .= "destinationtype, ";
					$sql .= "destinationprofile, ";
					$sql .= "destination_timeout, ";
					$sql .= "destinationorder, ";
					$sql .= "destination_enabled, ";
					$sql .= "destinationdescr ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$v_id', ";
					$sql .= "'$follow_me_id', ";
					$sql .= "'$destination_data_4', ";
					$sql .= "'$destination_type_4', ";
					$sql .= "'$destination_profile', ";
					$sql .= "'$destination_timeout_4', ";
					$sql .= "'$destination_order', ";
					$sql .= "'$destination_enabled', ";
					$sql .= "'$destination_descr' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);
				}
				if (strlen($destination_data_5) > 0) {
					$sql = "insert into v_hunt_group_destinations ";
					$sql .= "(";
					$sql .= "v_id, ";
					$sql .= "hunt_group_id, ";
					$sql .= "destinationdata, ";
					$sql .= "destinationtype, ";
					$sql .= "destinationprofile, ";
					$sql .= "destination_timeout, ";
					$sql .= "destinationorder, ";
					$sql .= "destination_enabled, ";
					$sql .= "destinationdescr ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$v_id', ";
					$sql .= "'$follow_me_id', ";
					$sql .= "'$destination_data_5', ";
					$sql .= "'$destination_type_5', ";
					$sql .= "'$destination_profile', ";
					$sql .= "'$destination_timeout_5', ";
					$sql .= "'$destination_order', ";
					$sql .= "'$destination_enabled', ";
					$sql .= "'$destination_descr' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);
				}
		}

	//do not disturb (dnd) config
		$hunt_group_extension = $extension;
		$huntgroup_name = 'dnd_'.$extension;
		$hunt_group_type = 'dnd';
		$hunt_group_context = 'default';
		$hunt_group_timeout = '1';
		$hunt_group_timeout_destination = $extension;
		$hunt_group_timeout_type = 'voicemail';
		$hunt_group_ring_back = 'us-ring';
		//$hunt_group_cid_name_prefix = '';
		//$hunt_group_pin = '';
		//$hunt_group_call_prompt = 'false';
		$huntgroup_caller_announce = 'false';
		//$hunt_group_user_list = '';
		$hunt_group_enabled = $dnd_enabled;
		$hunt_group_descr = 'dnd '.$extension;

		//update the user_status
		if ($dnd_enabled == "true") {
			//update the call center status
				$user_status = "Logged Out";
				$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
				$switch_cmd .= "callcenter_config agent set status ".$_SESSION['username']."@".$v_domain." '".$user_status."'";
				$switch_result = event_socket_request($fp, 'api '.$switch_cmd);

			//update the database user_status
				$user_status = "Do Not Disturb";
				$sql  = "update v_users set ";
				$sql .= "user_status = '$user_status' ";
				$sql .= "where v_id = '$v_id' ";
				$sql .= "and username = '".$_SESSION['username']."' ";
				$prepstatement = $db->prepare(check_sql($sql));
				$prepstatement->execute();
		}

		if ($dnd_action == "add") {
			$sql = "insert into v_hunt_group ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "huntgroupextension, ";
			$sql .= "huntgroupname, ";
			$sql .= "huntgrouptype, ";
			$sql .= "huntgroupcontext, ";
			$sql .= "huntgrouptimeout, ";
			$sql .= "huntgrouptimeoutdestination, ";
			$sql .= "huntgrouptimeouttype, ";
			$sql .= "huntgroupringback, ";
			//$sql .= "huntgroupcidnameprefix, ";
			//$sql .= "huntgrouppin, ";
			$sql .= "hunt_group_call_prompt, ";
			$sql .= "huntgroupcallerannounce, ";
			//$sql .= "hunt_group_user_list, ";
			$sql .= "hunt_group_enabled, ";
			$sql .= "huntgroupdescr ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$hunt_group_extension', ";
			$sql .= "'$huntgroup_name', ";
			$sql .= "'$hunt_group_type', ";
			$sql .= "'$hunt_group_context', ";
			$sql .= "'$hunt_group_timeout', ";
			$sql .= "'$hunt_group_timeout_destination', ";
			$sql .= "'$hunt_group_timeout_type', ";
			$sql .= "'$hunt_group_ring_back', ";
			//$sql .= "'$hunt_group_cid_name_prefix', ";
			//$sql .= "'$hunt_group_pin', ";
			$sql .= "'$hunt_group_call_prompt', ";
			$sql .= "'$huntgroup_caller_announce', ";
			//$sql .= "'$hunt_group_user_list', ";
			$sql .= "'$hunt_group_enabled', ";
			$sql .= "'$hunt_group_descr' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);
		} //if ($dnd_action == "add")

		if ($dnd_action == "update") {
			$sql = "update v_hunt_group set ";
			$sql .= "huntgroupextension = '$hunt_group_extension', ";
			$sql .= "huntgroupname = '$huntgroup_name', ";
			$sql .= "huntgrouptype = '$hunt_group_type', ";
			$sql .= "huntgroupcontext = '$hunt_group_context', ";
			$sql .= "huntgrouptimeout = '$hunt_group_timeout', ";
			$sql .= "huntgrouptimeoutdestination = '$hunt_group_timeout_destination', ";
			$sql .= "huntgrouptimeouttype = '$hunt_group_timeout_type', ";
			$sql .= "huntgroupringback = '$hunt_group_ring_back', ";
			//$sql .= "huntgroupcidnameprefix = '$hunt_group_cid_name_prefix', ";
			//$sql .= "huntgrouppin = '$hunt_group_pin', ";
			$sql .= "hunt_group_call_prompt = '$hunt_group_call_prompt', ";
			$sql .= "huntgroupcallerannounce = 'false', ";
			//$sql .= "hunt_group_user_list = '$hunt_group_user_list', ";
			$sql .= "hunt_group_enabled = '$hunt_group_enabled', ";
			$sql .= "huntgroupdescr = '$hunt_group_descr' ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and hunt_group_id = '$dnd_id'";
			$db->exec(check_sql($sql));
			unset($sql);
		} //if ($dnd_action == "update")

		//synchronize the xml config
			sync_package_v_hunt_group();

		//synchronize the xml config
			sync_package_v_dialplan_includes();

		//reloadxml
			$cmd = 'api reloadxml';
			$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
			$response = event_socket_request($fp, $cmd);
			fclose($fp);

		//apply settings reminder
			$_SESSION["reload_xml"] = false;

		//redirect the user
			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=".PROJECT_PATH."/mod/calls/v_calls.php\">\n";
			echo "<div align='center'>\n";
			echo "Update Complete<br />\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;

} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//show the header
	require_once "includes/header.php";

//pre-populate the form
	$sql = "";
	$sql .= "select * from v_hunt_group ";
	$sql .= "where huntgroupextension = '$extension' ";
	$sql .= "and v_id = '$v_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$hunt_group_id = $row["hunt_group_id"];
		$hunt_group_extension = $row["huntgroupextension"];
		$huntgroup_name = $row["huntgroupname"];
		$hunt_group_type = $row["huntgrouptype"];
		$hunt_group_context = $row["huntgroupcontext"];
		$hunt_group_timeout = $row["huntgrouptimeout"];
		$hunt_group_timeout_destination = $row["huntgrouptimeoutdestination"];
		$hunt_group_timeout_type = $row["huntgrouptimeouttype"];
		$hunt_group_ring_back = $row["huntgroupringback"];
		$hunt_group_cid_name_prefix = $row["huntgroupcidnameprefix"];
		$hunt_group_pin = $row["huntgrouppin"];
		$hunt_group_call_prompt = $row["hunt_group_call_prompt"];
		$huntgroup_caller_announce = $row["huntgroupcallerannounce"];
		$hunt_group_user_list = $row["hunt_group_user_list"];
		$hunt_group_enabled = $row["hunt_group_enabled"];
		$hunt_group_descr = $row["huntgroupdescr"];

		if ($row["huntgrouptype"] == 'call_forward') {
			$call_forward_enabled = $hunt_group_enabled;
		}
		if ($row["huntgrouptype"] == 'follow_me_simultaneous') {
			$follow_me_enabled = $hunt_group_enabled;
			$follow_me_type = 'follow_me_simultaneous';
		}
		if ($row["huntgrouptype"] == 'follow_me_sequence') {
			$follow_me_enabled = $hunt_group_enabled;
			$follow_me_type = 'follow_me_sequence';
		}
		if ($row["huntgrouptype"] == 'dnd') {
			$dnd_enabled = $hunt_group_enabled;
		}

		if ($row["huntgrouptype"] == 'call_forward' || $row["huntgrouptype"] == 'follow_me_sequence' || $row["huntgrouptype"] == 'follow_me_simultaneous') {
			$sql = "";
			$sql .= "select * from v_hunt_group_destinations ";
			$sql .= "where hunt_group_id = '$hunt_group_id' ";
			$prep_statement2 = $db->prepare(check_sql($sql));
			$prep_statement2->execute();
			$result2 = $prep_statement2->fetchAll();
			$x=1;
			foreach ($result2 as &$row2) {
				if ($row["huntgrouptype"] == 'call_forward') {
					if (strlen($row2["destinationdata"]) > 0) {
						$call_forward_number = $row2["destinationdata"];
					}
				}
				if ($row["huntgrouptype"] == 'follow_me_sequence' || $row["huntgrouptype"] == 'follow_me_simultaneous') {
					if ($x == 1) {
						$destination_data_1 = $row2["destinationdata"];
						$destination_timeout_1 = $row2["destination_timeout"];
					}
					if ($x == 2) {
						$destination_data_2 = $row2["destinationdata"];
						$destination_timeout_2 = $row2["destination_timeout"];
					}
					if ($x == 3) {
						$destination_data_3 = $row2["destinationdata"];
						$destination_timeout_3 = $row2["destination_timeout"];
					}
					if ($x == 4) {
						$destination_data_4 = $row2["destinationdata"];
						$destination_timeout_4 = $row2["destination_timeout"];
					}
					if ($x == 5) {
						$destination_data_5 = $row2["destinationdata"];
						$destination_timeout_5 = $row2["destination_timeout"];
					}
					$x++;
				}
			}
			unset ($prep_statement2);
		}
	}
	unset ($prepstatement);


//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing=''>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "		<br>";

	echo "<form method='post' name='frm' action=''>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' width='30%' nowrap>\n";
	echo "	<b>Calls</b>\n";
	echo "</td>\n";
	echo "<td width='70%' align='right'>\n";
	echo "	<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_calls.php'\" value='Back'>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' colspan='2'>\n";
	echo "	Directs incoming calls for extension  $extension.<br /><br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	<strong>Call Forward:</strong>\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	$on_click = "document.getElementById('follow_me_enabled').checked=true;";
	$on_click .= "document.getElementById('follow_me_disabled').checked=true;";
	$on_click .= "document.getElementById('dnd_enabled').checked=false;";
	$on_click .= "document.getElementById('dnd_disabled').checked=true;";
	if ($call_forward_enabled == "true") {
		echo "	<input type='radio' name='call_forward_enabled' id='call_forward_enabled' onclick=\"$on_click\" value='true' checked='checked'/> Enabled \n";
	}
	else {
		echo "	<input type='radio' name='call_forward_enabled' id='call_forward_enabled' onclick=\"$on_click\" value='true' /> Enable \n";
	}
	if ($call_forward_enabled == "false" || $call_forward_enabled == "") {
		echo "	<input type='radio' name='call_forward_enabled' id='call_forward_disabled' onclick=\"\" value='false' checked='checked' /> Disabled \n";
	}
	else {
		echo "	<input type='radio' name='call_forward_enabled' id='call_forward_disabled' onclick=\"\" value='false' /> Disable \n";
	}
	unset($on_click);
	echo "<br />\n";
	echo "<br />\n";
	//echo "Enable or disable call forward.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='call_forward_number' maxlength='255' value=\"$call_forward_number\">\n";
	echo "<br />\n";
	//echo "Enter the call forward number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td colspan='2'>\n";
	echo "	<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	<strong>Follow Me:</strong>\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	$on_click = "document.getElementById('call_forward_enabled').checked=true;";
	$on_click .= "document.getElementById('call_forward_disabled').checked=true;";
	$on_click .= "document.getElementById('dnd_enabled').checked=false;";
	$on_click .= "document.getElementById('dnd_disabled').checked=true;";
	if ($follow_me_enabled == "true") {
		echo "	<input type='radio' name='follow_me_enabled' id='follow_me_enabled' value='true' onclick=\"$on_click\" checked='checked'/> Enabled \n";
	}
	else {
		echo "	<input type='radio' name='follow_me_enabled' id='follow_me_enabled' value='true' onclick=\"$on_click\" /> Enable \n";
	}
	if ($follow_me_enabled == "false" || $follow_me_enabled == "") {
		echo "	<input type='radio' name='follow_me_enabled' id='follow_me_disabled' value='false' onclick=\"\" checked='checked' /> Disabled \n";
	}
	else {
		echo "	<input type='radio' name='follow_me_enabled' id='follow_me_disabled' value='false' onclick=\"\" /> Disable \n";
	}
	unset($on_click);
	echo "<br />\n";
	echo "<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Ring 1st Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='destination_data_1' maxlength='255' value=\"$destination_data_1\">\n";
	echo "	Sec \n"; 
	destination_select('destination_timeout_1', $destination_timeout_1, '10');
	//echo "<br />\n";
	//echo "This number rings first.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Ring 2nd Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='destination_data_2' maxlength='255' value=\"$destination_data_2\">\n";
	echo "	Sec \n"; 
	destination_select('destination_timeout_2', $destination_timeout_2, '30');
	//echo "<br />\n";
	//echo "Enter the destination number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Ring 3rd Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='destination_data_3' maxlength='255' value=\"$destination_data_3\">\n";
	echo "	Sec \n"; 
	destination_select('destination_timeout_3', $destination_timeout_3, '30');
	//echo "<br />\n";
	//echo "Enter the destination number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Ring 4th Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='destination_data_4' maxlength='255' value=\"$destination_data_4\">\n";
	echo "	Sec \n"; 
	destination_select('destination_timeout_4', $destination_timeout_4, '30');
	//echo "<br />\n";
	//echo "Enter the destination number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Ring 5th Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='destination_data_5' maxlength='255' value=\"$destination_data_5\">\n";
	echo "	Sec \n"; 
	destination_select('destination_timeout_5', $destination_timeout_5, '30');
	//echo "<br />\n";
	//echo "Enter the destination number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Ring Order:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
    echo "<select class='formfld' name='follow_me_type'>\n";
	echo "<option value=''></option>\n";
	if ($follow_me_type == "follow_me_sequence") {
		echo "<option value='follow_me_sequence' selected='selected'>sequence</option>\n";
	}
	else {
		echo "<option value='follow_me_sequence'>sequence</option>\n";
	}
	if ($follow_me_type == "follow_me_simultaneous") {
		echo "<option value='follow_me_simultaneous' selected='selected'>simultaneous</option>\n";
	}
	else {
		echo "<option value='follow_me_simultaneous'>simultaneous</option>\n";
	}
    echo "</select>\n";
	//echo "<br />\n";
	//echo "Enter the destination number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Prompt to accept the call:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "<select class='formfld' name='hunt_group_call_prompt'>\n";
	echo "<option value=''></option>\n";
	if ($hunt_group_call_prompt == "true") {
		echo "<option value='true' selected='selected'>true</option>\n";
	}
	else {
		echo "<option value='true'>true</option>\n";
	}
	if ($hunt_group_call_prompt == "false") {
		echo "<option value='false' selected='selected'>false</option>\n";
	}
	else {
		echo "<option value='false'>false</option>\n";
	}
	echo "</select>\n";
	//echo "<br />\n";
	//echo "Enter the destination number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td colspan='2'>\n";
	echo "	<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	<strong>Do Not Disturb:</strong>\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	$on_click = "document.getElementById('call_forward_enabled').checked=true;";
	$on_click .= "document.getElementById('call_forward_disabled').checked=true;";
	$on_click .= "document.getElementById('follow_me_enabled').checked=true;";
	$on_click .= "document.getElementById('follow_me_disabled').checked=true;";
	if ($dnd_enabled == "true") {
		echo "	<input type='radio' name='dnd_enabled' id='dnd_enabled' value='true' onclick=\"$on_click\" checked='checked'/> Enabled \n";
	}
	else {
		echo "	<input type='radio' name='dnd_enabled' id='dnd_enabled' value='true' onclick=\"$on_click\"/> Enable \n";
	}
	if ($dnd_enabled == "false" || $dnd_enabled == "") {
		echo "	<input type='radio' name='dnd_enabled' id='dnd_disabled' value='false' onclick=\"\" checked='checked' /> Disabled \n";
	}
	else {
		echo "	<input type='radio' name='dnd_enabled' id='dnd_disabled' value='false' onclick=\"\" /> Disable \n";
	}
	echo "	<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td colspan='2'>\n";
	echo "	<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td colspan='2'>\n";
	echo "	<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='id' value='$extension_id'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";


require_once "includes/footer.php";
?>