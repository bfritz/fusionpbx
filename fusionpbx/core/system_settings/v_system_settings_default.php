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
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('system_settings_default')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//action add or update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$domain_uuid = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//set the required directories
	require_once "includes/lib_system_settings_default.php";

//add to v_system settings
	$action = "add";
	if ($action == "add") {
		$sql = "insert into v_system_settings ";
		$sql .= "(";
		$sql .= "v_domain, ";
		$sql .= "php_dir, ";
		$sql .= "tmp_dir, ";
		$sql .= "switch_bin_dir, ";
		$sql .= "startup_script_dir, ";
		//$sql .= "v_package_version, ";
		$sql .= "v_build_version, ";
		$sql .= "v_build_revision, ";
		$sql .= "v_label, ";
		$sql .= "v_name, ";
		$sql .= "switch_base_dir, ";
		$sql .= "parent_dir, ";
		$sql .= "backup_dir, ";
		$sql .= "web_dir, ";
		$sql .= "web_root, ";
		$sql .= "relative_url, ";
		$sql .= "switch_conf_dir, ";
		$sql .= "switch_db_dir, ";
		$sql .= "switch_htdocs_dir, ";
		$sql .= "switch_log_dir, ";
		$sql .= "switch_extensions_dir, ";
		$sql .= "switch_gateways_dir, ";
		$sql .= "v_dialplan_public_dir, ";
		$sql .= "v_dialplan_default_dir, ";
		$sql .= "switch_mod_dir, ";
		$sql .= "switch_scripts_dir, ";
		$sql .= "switch_grammar_dir, ";
		$sql .= "switch_storage_dir, ";
		$sql .= "switch_voicemail_dir, ";
		$sql .= "switch_recordings_dir, ";
		$sql .= "switch_sounds_dir, ";
		//$sql .= "v_download_path, ";
		$sql .= "provisioning_tftp_dir, ";
		$sql .= "provisioning_ftp_dir, ";
		$sql .= "provisioning_https_dir, ";
		$sql .= "provisioning_http_dir ";
		$sql .= ")";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$v_domain', ";
		$sql .= "'$php_dir', ";
		$sql .= "'$tmp_dir', ";
		$sql .= "'$switch_bin_dir', ";
		$sql .= "'$startup_script_dir', ";
		//$sql .= "'$v_package_version', ";
		$sql .= "'$v_build_version', ";
		$sql .= "'$v_build_revision', ";
		$sql .= "'$v_label', ";
		$sql .= "'$v_name', ";
		$sql .= "'$switch_base_dir', ";
		$sql .= "'$parent_dir', ";
		$sql .= "'$backup_dir', ";
		$sql .= "'$web_dir', ";
		$sql .= "'$web_root', ";
		$sql .= "'$relative_url', ";
		$sql .= "'$switch_conf_dir', ";
		$sql .= "'$switch_db_dir', ";
		$sql .= "'$switch_htdocs_dir', ";
		$sql .= "'$switch_log_dir', ";
		$sql .= "'$switch_extensions_dir', ";
		$sql .= "'$switch_gateways_dir', ";
		$sql .= "'$v_dialplan_public_dir', ";
		$sql .= "'$v_dialplan_default_dir', ";
		$sql .= "'$switch_mod_dir', ";
		$sql .= "'$switch_scripts_dir', ";
		$sql .= "'$switch_grammar_dir', ";
		$sql .= "'$switch_storage_dir', ";
		$sql .= "'$switch_voicemail_dir', ";
		$sql .= "'$switch_recordings_dir', ";
		$sql .= "'$switch_sounds_dir', ";
		//$sql .= "'$v_download_path', ";
		$sql .= "'$provisioning_tftp_dir', ";
		$sql .= "'$provisioning_ftp_dir', ";
		$sql .= "'$provisioning_https_dir', ";
		$sql .= "'$provisioning_http_dir' ";
		$sql .= ")";
		$db->exec(check_sql($sql));
		unset($sql);
	}

//restore the defaults in the database
	if ($action == "update") {
		$sql = "update v_system_settings set ";
		$sql .= "php_dir = '$install_php_dir', ";
		$sql .= "tmp_dir = '$install_tmp_dir', ";
		$sql .= "switch_bin_dir = '$switch_bin_dir', ";
		$sql .= "startup_script_dir = '$startup_script_dir', ";
		$sql .= "v_package_version = '$v_package_version', ";
		$sql .= "v_build_version = '$v_build_version', ";
		$sql .= "v_build_revision = '$v_build_revision', ";
		$sql .= "v_label = '$v_label', ";
		$sql .= "v_name = '$v_name', ";
		$sql .= "switch_base_dir = '$install_switch_base_dir', ";
		$sql .= "parent_dir = '$parent_dir', ";
		$sql .= "backup_dir = '$install_backup_dir', ";
		$sql .= "web_dir = '$web_dir', ";
		$sql .= "web_root = '$web_root', ";
		$sql .= "relative_url = '$relative_url', ";
		$sql .= "switch_conf_dir = '$switch_conf_dir', ";
		$sql .= "switch_db_dir = '$switch_db_dir', ";
		$sql .= "switch_htdocs_dir = '$switch_htdocs_dir', ";
		$sql .= "switch_log_dir = '$switch_log_dir', ";
		$sql .= "switch_mod_dir = '$switch_mod_dir', ";
		$sql .= "switch_extensions_dir = '$switch_extensions_dir', ";
		$sql .= "switch_gateways_dir = '$switch_gateways_dir', ";
		$sql .= "v_dialplan_public_dir = '$v_dialplan_public_dir', ";
		$sql .= "v_dialplan_default_dir = '$v_dialplan_default_dir', ";
		$sql .= "switch_scripts_dir = '$switch_scripts_dir', ";
		$sql .= "switch_grammar_dir = '$switch_grammar_dir', ";
		$sql .= "switch_storage_dir = '$switch_storage_dir', ";
		$sql .= "switch_voicemail_dir = '$switch_voicemail_dir', ";
		$sql .= "switch_recordings_dir = '$switch_recordings_dir', ";
		$sql .= "switch_sounds_dir = '$switch_sounds_dir', ";
		$sql .= "v_download_path = '$v_download_path' ";
		//$sql .= "provisioning_tftp_dir = '$provisioning_tftp_dir', ";
		//$sql .= "provisioning_ftp_dir = '$provisioning_ftp_dir', ";
		//$sql .= "provisioning_https_dir = '$provisioning_https_dir', ";
		//$sql .= "provisioning_http_dir = '$provisioning_http_dir' ";
		$sql .= "where domain_uuid = '$domain_uuid'";
		$db->exec($sql);
		unset($sql);
	}

//redirect to the system settings page
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=v_system_settings.php\">\n";
	echo "<div align='center'>\n";
	echo "Restore Complete\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

?>