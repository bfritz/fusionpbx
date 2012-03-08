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

//if the dialplan default directory doesn't exist then create it
	if (!is_dir($_SESSION['switch']['dialplan']['dir'])) { mkdir($_SESSION['switch']['dialplan']['dir'],0777,true); }
//write the dialplan/default.xml if it does not exist
	//get the contents of the dialplan/default.xml
		$file_default_path = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/templates/conf/dialplan/default.xml';
		$file_default_contents = file_get_contents($file_default_path);

	//prepare the file contents and the path
		//replace the variables in the template in the future loop through all the line numbers to do a replace for each possible line number
			$file_default_contents = str_replace("{v_domain}", $context, $file_default_contents);
		//set the file path
			$file_path = $_SESSION['switch']['conf']['dir'].'/dialplan/'.$context.'.xml';

	//write the default dialplan
		if (!file_exists($file_path)) {
			$fh = fopen($file_path,'w') or die('Unable to write to '.$file_path.'. Make sure the path exists and permissons are set correctly.');
			fwrite($fh, $file_default_contents);
			fclose($fh);
		}

// add a recordings dialplan entry if it doesn't exist
	$v_recording_action = 'add';
	$app_uuid = '430737df-5385-42d1-b933-22600d3fb79e';
	$sql = "";
	$sql .= "select * from v_dialplans ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and app_uuid = '$app_uuid' ";
	$prep_statement = $db->prepare($sql);
	$prep_statement->execute();
	while($sub_row = $prep_statement->fetch(PDO::FETCH_ASSOC)) {
		$v_recording_action = 'update';
		break; //limit to 1 row
	}
	unset ($sql, $prep_statement);
	if ($v_recording_action == 'add') {
		if ($display_type == "text") {
			echo "	Dialplan Recording: 	added\n";
		}
		$dialplan_name = 'Recordings';
		$dialplan_order ='900';
		$dialplan_context = $context;
		$dialplan_enabled = 'true';
		$dialplan_description = '*732 Recordings';
		$dialplan_uuid = uuid();
		dialplan_add($domain_uuid, $dialplan_uuid, $dialplan_name, $dialplan_order, $dialplan_context, $dialplan_enabled, $dialplan_description, $app_uuid);

		$dialplan_detail_tag = 'condition'; //condition, action, antiaction
		$dialplan_detail_type = 'destination_number';
		$dialplan_detail_data = '^\*(732)$';
		$dialplan_detail_order = '000';
		dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_type, $dialplan_detail_data);

		$dialplan_detail_tag = 'action'; //condition, action, antiaction
		$dialplan_detail_type = 'set';
		$dialplan_detail_data = 'recordings_dir='.$switch_recordings_dir.'/'.$domain_name;
		$dialplan_detail_order = '001';
		dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_type, $dialplan_detail_data);

		$dialplan_detail_tag = 'action'; //condition, action, antiaction
		$dialplan_detail_type = 'set';
		$dialplan_detail_data = 'recording_slots=true';
		$dialplan_detail_order = '002';
		dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_type, $dialplan_detail_data);

		$dialplan_detail_tag = 'action'; //condition, action, antiaction
		$dialplan_detail_type = 'set';
		$dialplan_detail_data = 'recording_prefix=recording';
		$dialplan_detail_order = '003';
		dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_type, $dialplan_detail_data);

		$dialplan_detail_tag = 'action'; //condition, action, antiaction
		$dialplan_detail_type = 'set';
		$dialplan_detail_data = 'pin_number='.generate_password(6, 1);
		$dialplan_detail_order = '004';
		dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_type, $dialplan_detail_data);

		$dialplan_detail_tag = 'action'; //condition, action, antiaction
		$dialplan_detail_type = 'lua';
		$dialplan_detail_data = 'recordings.lua';
		$dialplan_detail_order = '005';
		dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_type, $dialplan_detail_data);
	}
	else {
		if ($display_type == "text") {
			echo "	Dialplan Recording: 	no change\n";
		}
	}

// add a disa dialplan entry if it doesn't exist
	$v_disa_action = 'add';
	$app_uuid = '3ade2d9a-f55d-4240-bb60-b4a3ab36951c';
	$sql = "";
	$sql .= "select * from v_dialplans ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and app_uuid = '$app_uuid' ";
	$prep_statement = $db->prepare($sql);
	$prep_statement->execute();
	while($sub_row = $prep_statement->fetch(PDO::FETCH_ASSOC)) {
		$v_disa_action = 'update';
		break; //limit to 1 row
	}
	unset ($sql, $prep_statement);
	if ($v_disa_action == 'add') {
		if ($display_type == "text") {
			echo "	Dialplan DISA: 		added\n";
		}
		$dialplan_name = 'DISA';
		$dialplan_order ='900';
		$dialplan_context = $context;
		$dialplan_enabled = 'false';
		$dialplan_description = '*3472 Direct Inward System Access ';
		$dialplan_uuid = uuid();
		dialplan_add($domain_uuid, $dialplan_uuid, $dialplan_name, $dialplan_order, $dialplan_context, $dialplan_enabled, $dialplan_description, $app_uuid);

		$dialplan_detail_tag = 'condition'; //condition, action, antiaction
		$dialplan_detail_type = 'destination_number';
		$dialplan_detail_data = '^\*(3472)$';
		$dialplan_detail_order = '000';
		dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_type, $dialplan_detail_data);

		$dialplan_detail_tag = 'action'; //condition, action, antiaction
		$dialplan_detail_type = 'set';
		$dialplan_detail_data = 'pin_number='.generate_password(6, 1);
		$dialplan_detail_order = '001';
		dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_type, $dialplan_detail_data);

		$dialplan_detail_tag = 'action'; //condition, action, antiaction
		$dialplan_detail_type = 'set';
		$dialplan_detail_data = 'dialplan_context='.$context;
		$dialplan_detail_order = '002';
		dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_type, $dialplan_detail_data);

		$dialplan_detail_tag = 'action'; //condition, action, antiaction
		$dialplan_detail_type = 'lua';
		$dialplan_detail_data = 'disa.lua';
		$dialplan_detail_order = '003';
		dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_type, $dialplan_detail_data);
	}
	else {
		if ($display_type == "text") {
			echo "	Dialplan DISA: 		no change\n";
		}
	}

// add a wake up call dialplan entry if it doesn't exist
	$v_wake_up_action = 'add';
	$app_uuid = 'e27abe68-41c0-4188-bb0f-67d93de0c610';
	$sql = "";
	$sql .= "select * from v_dialplans ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and app_uuid = '$app_uuid' ";
	$prep_statement = $db->prepare($sql);
	$prep_statement->execute();
	while($sub_row = $prep_statement->fetch(PDO::FETCH_ASSOC)) {
		$v_wake_up_action = 'update';
		break; //limit to 1 row
	}
	unset ($sql, $prep_statement);
	if ($v_wake_up_action == 'add') {
		if ($display_type == "text") {
			echo "	Wake Up Calls:		added\n";
		}
		$dialplan_name = 'Wake-Up';
		$dialplan_order ='900';
		$dialplan_context = $context;
		$dialplan_enabled = 'true';
		$dialplan_description = '*923 Wake Up Calls';
		$dialplan_uuid = uuid();
		dialplan_add($domain_uuid, $dialplan_uuid, $dialplan_name, $dialplan_order, $dialplan_context, $dialplan_enabled, $dialplan_description, $app_uuid);

		$dialplan_detail_tag = 'condition'; //condition, action, antiaction
		$dialplan_detail_type = 'destination_number';
		$dialplan_detail_data = '^\*(923)$';
		$dialplan_detail_order = '000';
		dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_type, $dialplan_detail_data);

		$dialplan_detail_tag = 'action'; //condition, action, antiaction
		$dialplan_detail_type = 'set';
		$dialplan_detail_data = 'pin_number='.generate_password(6, 1);
		$dialplan_detail_order = '005';
		dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_type, $dialplan_detail_data);

		$dialplan_detail_tag = 'action'; //condition, action, antiaction
		$dialplan_detail_type = 'set';
		$dialplan_detail_data = 'time_zone_offset=-7';
		$dialplan_detail_order = '010';
		dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_type, $dialplan_detail_data);

		$dialplan_detail_tag = 'action'; //condition, action, antiaction
		$dialplan_detail_type = 'lua';
		$dialplan_detail_data = 'wakeup.lua';
		$dialplan_detail_order = '015';
		dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_type, $dialplan_detail_data);
	}
	else {
		if ($display_type == "text") {
			echo "	Wake Up Calls: 		no change\n";
		}
	}

// synchronize the dialplan
	if ($v_recording_action == 'add' || $v_disa_action == 'add') {
		save_dialplan_xml();
	}

?>