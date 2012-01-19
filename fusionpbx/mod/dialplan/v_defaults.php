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

//if the dialplan default directory doesn't exist then create it
	if (!is_dir($v_dialplan_default_dir)) { mkdir($v_dialplan_default_dir,0777,true); }
//write the dialplan/default.xml if it does not exist
	//get the contents of the dialplan/default.xml
		$file_default_path = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/templates/conf/dialplan/default.xml';
		$file_default_contents = file_get_contents($file_default_path);
	//prepare the file contents and the path
		if (count($_SESSION['domains']) < 2) {
			//replace the variables in the template in the future loop through all the line numbers to do a replace for each possible line number
				$file_default_contents = str_replace("{v_domain}", 'default', $file_default_contents);
			//set the file path
				$file_path = $v_conf_dir.'/dialplan/default.xml';
		}
		else {
			//replace the variables in the template in the future loop through all the line numbers to do a replace for each possible line number
				$file_default_contents = str_replace("{v_domain}", $v_domain, $file_default_contents);
			//set the file path
				$file_path = $v_conf_dir.'/dialplan/'.$v_domain.'.xml';
		}

	//write the default dialplan
		if (!file_exists($file_path)) {
			$fh = fopen($file_path,'w') or die('Unable to write to '.$file_path.'. Make sure the path exists and permissons are set correctly.');
			fwrite($fh, $file_default_contents);
			fclose($fh);
		}

// add a recordings dialplan entry if it doesn't exist
	$v_recording_action = 'add';
	$sql = "";
	$sql .= "select * from v_dialplan_includes ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and opt_1_name = 'recordings' ";
	$sql .= "and (opt_1_value = '732' or opt_1_value = '732673') ";
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
		$extension_name = 'Recordings';
		$dialplan_order ='900';
		$context = 'default';
		$enabled = 'true';
		$descr = '*732 Recordings';
		$opt_1_name = 'recordings';
		$opt_1_value = '732';
		$dialplan_include_uuid = v_dialplan_includes_add($domain_uuid, $extension_name, $dialplan_order, $context, $enabled, $descr, $opt_1_name, $opt_1_value);

		$tag = 'condition'; //condition, action, antiaction
		$field_type = 'destination_number';
		$field_data = '^\*(732)$';
		$field_order = '000';
		v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

		$tag = 'action'; //condition, action, antiaction
		$field_type = 'set';
		$field_data = 'recordings_dir='.$v_recordings_dir;
		$field_order = '001';
		v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

		$tag = 'action'; //condition, action, antiaction
		$field_type = 'set';
		$field_data = 'recording_slots=true';
		$field_order = '002';
		v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

		$tag = 'action'; //condition, action, antiaction
		$field_type = 'set';
		$field_data = 'recording_prefix=recording';
		$field_order = '003';
		v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

		$tag = 'action'; //condition, action, antiaction
		$field_type = 'set';
		$field_data = 'pin_number='.generate_password(6, 1);
		$field_order = '004';
		v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

		$tag = 'action'; //condition, action, antiaction
		$field_type = 'lua';
		$field_data = 'recordings.lua';
		$field_order = '005';
		v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
	}
	else {
		if ($display_type == "text") {
			echo "	Dialplan Recording: 	no change\n";
		}
	}

// add a disa dialplan entry if it doesn't exist
	$v_disa_action = 'add';
	$sql = "";
	$sql .= "select * from v_dialplan_includes ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and opt_1_name = 'disa' ";
	$sql .= "and (opt_1_value = '347' or opt_1_value = '3472') ";
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
		$extension_name = 'DISA';
		$dialplan_order ='900';
		$context = $_SESSION['context'];
		$enabled = 'false';
		$descr = '*3472 Direct Inward System Access ';
		$opt_1_name = 'disa';
		$opt_1_value = '3472';
		$dialplan_include_uuid = v_dialplan_includes_add($domain_uuid, $extension_name, $dialplan_order, $context, $enabled, $descr, $opt_1_name, $opt_1_value);

		$tag = 'condition'; //condition, action, antiaction
		$field_type = 'destination_number';
		$field_data = '^\*(3472)$';
		$field_order = '000';
		v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

		$tag = 'action'; //condition, action, antiaction
		$field_type = 'set';
		$field_data = 'pin_number='.generate_password(6, 1);
		$field_order = '001';
		v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

		$tag = 'action'; //condition, action, antiaction
		$field_type = 'set';
		$field_data = 'context='.$_SESSION['context'];
		$field_order = '002';
		v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

		$tag = 'action'; //condition, action, antiaction
		$field_type = 'lua';
		$field_data = 'disa.lua';
		$field_order = '003';
		v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
	}
	else {
		if ($display_type == "text") {
			echo "	Dialplan DISA: 		no change\n";
		}
	}

// add a wake up call dialplan entry if it doesn't exist
	$v_wake_up_action = 'add';
	$sql = "";
	$sql .= "select * from v_dialplan_includes ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and opt_1_name = 'wake up' ";
	$sql .= "and opt_1_value = '923' ";
	$prep_statement = $db->prepare($sql);
	$prep_statement->execute();
	while($sub_row = $prep_statement->fetch(PDO::FETCH_ASSOC)) {
		$v_wake_up_action = 'update';
		break; //limit to 1 row
	}
	unset ($sql, $prep_statement);
	if ($v_wake_up_action == 'add') {
		if ($display_type == "text") {
			echo "	Wake Up Calls: 	added\n";
		}
		$extension_name = 'Wake-Up';
		$dialplan_order ='900';
		$context = 'default';
		$enabled = 'true';
		$descr = '*923 Wake Up Calls';
		$opt_1_name = 'wake up';
		$opt_1_value = '923';
		$dialplan_include_uuid = v_dialplan_includes_add($domain_uuid, $extension_name, $dialplan_order, $context, $enabled, $descr, $opt_1_name, $opt_1_value);

		$tag = 'condition'; //condition, action, antiaction
		$field_type = 'destination_number';
		$field_data = '^\*(923)$';
		$field_order = '000';
		v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

		$tag = 'action'; //condition, action, antiaction
		$field_type = 'set';
		$field_data = 'pin_number='.generate_password(6, 1);
		$field_order = '005';
		v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

		$tag = 'action'; //condition, action, antiaction
		$field_type = 'set';
		$field_data = 'time_zone_offset=-7';
		$field_order = '010';
		v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

		$tag = 'action'; //condition, action, antiaction
		$field_type = 'lua';
		$field_data = 'wakeup.lua';
		$field_order = '015';
		v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
	}
	else {
		if ($display_type == "text") {
			echo "	Wake Up Calls: 	no change\n";
		}
	}

// synchronize the dialplan
	if ($v_recording_action == 'add' || $v_disa_action == 'add') {
		sync_package_v_dialplan_includes();
	}
?>