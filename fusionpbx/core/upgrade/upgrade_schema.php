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

//check the permission
	if(defined('STDIN')) {
		$document_root = str_replace("\\", "/", $_SERVER["PHP_SELF"]);
		preg_match("/^(.*)\/core\/.*$/", $document_root, $matches);
		$document_root = $matches[1];
		set_include_path($document_root);
		require_once "includes/config.php";
		$_SERVER["DOCUMENT_ROOT"] = $document_root;
		$display_type = 'text'; //html, text
	}
	else {
		include "root.php";
		require_once "includes/config.php";
		require_once "includes/checkauth.php";
		if (permission_exists('upgrade_schema') || ifgroup("superadmin")) {
			//echo "access granted";
		}
		else {
			echo "access denied";
			exit;
		}
		require_once "includes/header.php";
		$display_type = 'html'; //html, text
	}

//set the default
	if (!isset($display_results)) {
		$display_results = true;
	}

//copy the files and directories from includes/install
	require_once "includes/classes/install.php";
	$install = new install;
	$install->domain_uuid = $domain_uuid;
	$install->v_domain = $domain;
	$install->switch_conf_dir = $switch_conf_dir;
	$install->switch_scripts_dir = $switch_scripts_dir;
	$install->switch_sounds_dir = $switch_sounds_dir;
	$install->switch_recordings_dir = $switch_recordings_dir;
	$install->copy();
	//print_r($install->result);

//load the default database into memory and compare it with the active database
	require_once "includes/lib_schema.php";
	db_upgrade_schema ($db, $db_type, $db_name, $display_results);
	unset($apps);

//get the list of installed apps from the core and mod directories
	$config_list = glob($_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . "/*/*/v_config.php");
	$x=0;
	foreach ($config_list as &$config_path) {
		include($config_path);
		$x++;
	}

//loop through all domains
	$sql = "";
	$sql .= "select * from v_domains ";
	$v_prep_statement = $db->prepare(check_sql($sql));
	$v_prep_statement->execute();
	$main_result = $v_prep_statement->fetchAll(PDO::FETCH_ASSOC);
	foreach ($main_result as &$row) {
		//get the values from database and set them as php variables
			$domain_uuid = $row["domain_uuid"];
			$v_domain = $row["domain_name"];
			/*
			$v_account_code = $row["v_account_code"];
			$server_protocol = $row["server_protocol"];
			$server_port = $row["server_port"];
			$php_dir = $row["php_dir"];
			$tmp_dir = $row["tmp_dir"];
			$switch_bin_dir = $row["switch_bin_dir"];
			$startup_script_dir = $row["startup_script_dir"];
			$v_package_version = $row["v_package_version"];
			$v_build_version = $row["v_build_version"];
			$v_build_revision = $row["v_build_revision"];
			$v_label = $row["v_label"];
			$v_name = $row["v_name"];
			$switch_base_dir = $row["switch_base_dir"];
			$parent_dir = $row["parent_dir"];
			$backup_dir = $row["backup_dir"];
			$web_dir = $row["web_dir"];
			$web_root = $row["web_root"];
			$relative_url = $row["relative_url"];
			$switch_conf_dir = $row["switch_conf_dir"];
			$switch_db_dir = $row["switch_db_dir"];
			$switch_htdocs_dir = $row["switch_htdocs_dir"];
			$switch_log_dir = $row["switch_log_dir"];
			$switch_extensions_dir = $row["switch_extensions_dir"];
			$switch_gateways_dir = $row["switch_gateways_dir"];
			$v_dialplan_public_dir = $row["v_dialplan_public_dir"];
			$v_dialplan_default_dir = $row["v_dialplan_default_dir"];
			$switch_mod_dir = $row["switch_mod_dir"];
			$switch_scripts_dir = $row["switch_scripts_dir"];
			$switch_grammar_dir = $row["switch_grammar_dir"];
			$switch_storage_dir = $row["switch_storage_dir"];
			$switch_voicemail_dir = $row["switch_voicemail_dir"];
			$switch_recordings_dir = $row["switch_recordings_dir"];
			$switch_sounds_dir = $row["switch_sounds_dir"];
			$v_download_path = $row["v_download_path"];
			$provisioning_tftp_dir = $row["provisioning_tftp_dir"];
			$provisioning_ftp_dir = $row["provisioning_ftp_dir"];
			$provisioning_https_dir = $row["provisioning_https_dir"];
			$provisioning_http_dir = $row["provisioning_http_dir"];
			$v_template_name = $row["v_template_name"];
			$v_time_zone = $row["v_time_zone"];
			$v_description = $row["v_description"];
			*/

		//show the domain when display_type is set to text
			if ($display_type == "text") {
				echo "\n";
				echo $v_domain;
				echo "\n";
			}

		//get the list of installed apps from the core and mod directories and execute the php code in v_defaults.php
			$default_list = glob($_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . "/*/*/v_defaults.php");
			foreach ($default_list as &$default_path) {
				include($default_path);
			}

	} //end the loop
	unset ($v_prep_statement);

if ($display_results && $display_type == "html") {
	echo "<br />\n";
	echo "<br />\n";
	require_once "includes/footer.php";
}

?>