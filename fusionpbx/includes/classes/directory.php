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

//define the directory class
	class directory {
		var $v_id;
		var $v_domain;
		var $db_type;
		var $extension;
		var $number_alias;
		var $password;
		var $vm_password;
		var $user_list;
		var $accountcode;
		var $effective_caller_id_name;
		var $effective_caller_id_number;
		var $outbound_caller_id_name;
		var $outbound_caller_id_number;
		var $limit_max;
		var $limit_destination;
		var $vm_enabled;
		var $vm_mailto;
		var $vm_attach_file;
		var $vm_keep_local_after_email;
		var $user_context;
		var $range;
		var $autogen_users;
		var $toll_allow;
		var $callgroup;
		var $hold_music;
		var $auth_acl;
		var $cidr;
		var $sip_force_contact;
		var $sip_force_expires;
		var $nibble_account;
		var $mwi_account;
		var $sip_bypass_media;
		var $enabled;
		var $description;

		function sql_add() {
			global $db;
			$v_id = $this->v_id;
			$v_domain = $this->v_domain;
			$extension = $this->extension;
			$number_alias = $this->number_alias;
			$password = $this->password;
			$autogen_users = $this->autogen_users;
			$user_list = $this->user_list;
			$provisioning_list = $this->provisioning_list;
			$vm_password = $this->vm_password;
			$accountcode = $this->accountcode;
			$effective_caller_id_name = $this->effective_caller_id_name;
			$effective_caller_id_number = $this->effective_caller_id_number;
			$outbound_caller_id_name = $this->outbound_caller_id_name;
			$outbound_caller_id_number = $this->outbound_caller_id_number;
			$limit_max = $this->limit_max;
			$limit_destination = $this->limit_destination;
			$vm_enabled = $this->vm_enabled;
			$vm_mailto = $this->vm_mailto;
			$vm_attach_file = $this->vm_attach_file;
			$vm_keep_local_after_email = $this->vm_keep_local_after_email;
			$user_context = $this->user_context;
			$toll_allow = $this->toll_allow;
			$callgroup = $this->callgroup;
			$hold_music = $this->hold_music;
			$auth_acl = $this->auth_acl;
			$cidr = $this->cidr;
			$sip_force_contact = $this->sip_force_contact;
			$sip_force_expires = $this->sip_force_expires;
			$nibble_account = $this->nibble_account;
			$mwi_account = $this->mwi_account;
			$sip_bypass_media = $this->sip_bypass_media;
			$enabled = $this->enabled;
			$description = $this->description;

			$db->beginTransaction();
			for ($i=1; $i<=$range; $i++) {
				if (extension_exists($extension)) {
					//extension exists
				}
				else {
					//extension does not exist add it
					$password = generate_password();
					$sql = "insert into v_extensions ";
					$sql .= "(";
					$sql .= "v_id, ";
					$sql .= "extension, ";
					$sql .= "number_alias, ";
					$sql .= "password, ";
					$sql .= "user_list, ";
					$sql .= "provisioning_list, ";
					$sql .= "vm_password, ";
					$sql .= "accountcode, ";
					$sql .= "effective_caller_id_name, ";
					$sql .= "effective_caller_id_number, ";
					$sql .= "outbound_caller_id_name, ";
					$sql .= "outbound_caller_id_number, ";
					$sql .= "limit_max, ";
					$sql .= "limit_destination, ";
					$sql .= "vm_enabled, ";
					$sql .= "vm_mailto, ";
					$sql .= "vm_attach_file, ";
					$sql .= "vm_keep_local_after_email, ";
					$sql .= "user_context, ";
					$sql .= "toll_allow, ";
					$sql .= "callgroup, ";
					$sql .= "hold_music, ";
					$sql .= "auth_acl, ";
					$sql .= "cidr, ";
					$sql .= "sip_force_contact, ";
					if (strlen($sip_force_expires) > 0) {
						$sql .= "sip_force_expires, ";
					}
					if (strlen($nibble_account) > 0) {
						$sql .= "nibble_account, ";
					}
					if (strlen($mwi_account) > 0) {
						$sql .= "mwi_account, ";
					}
					$sql .= "sip_bypass_media, ";
					$sql .= "enabled, ";
					$sql .= "description ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$v_id', ";
					$sql .= "'$extension', ";
					$sql .= "'$number_alias', ";
					$sql .= "'$password', ";
					if ($autogen_users == "true") { 
						$sql .= "'|".$extension."|', ";
					} else {
						$sql .= "'$user_list', ";
					}
					$sql .= "'$provisioning_list', ";
					$sql .= "'user-choose', ";
					$sql .= "'$accountcode', ";
					$sql .= "'$effective_caller_id_name', ";
					$sql .= "'$effective_caller_id_number', ";
					$sql .= "'$outbound_caller_id_name', ";
					$sql .= "'$outbound_caller_id_number', ";
					$sql .= "'$limit_max', ";
					$sql .= "'$limit_destination', ";
					$sql .= "'$vm_enabled', ";
					$sql .= "'$vm_mailto', ";
					$sql .= "'$vm_attach_file', ";
					$sql .= "'$vm_keep_local_after_email', ";
					$sql .= "'$user_context', ";
					$sql .= "'$toll_allow', ";
					$sql .= "'$callgroup', ";
					$sql .= "'$hold_music', ";
					$sql .= "'$auth_acl', ";
					$sql .= "'$cidr', ";
					$sql .= "'$sip_force_contact', ";
					if (strlen($sip_force_expires) > 0) {
						$sql .= "'$sip_force_expires', ";
					}
					if (strlen($nibble_account) > 0) {
						$sql .= "'$nibble_account', ";
					}
					if (strlen($mwi_account) > 0) {
						if (strpos($mwi_account, '@') === false) {
							if (count($_SESSION["domains"]) > 1) {
								$mwi_account .= "@".$v_domain;
							}
							else {
								$mwi_account .= "@\$\${domain}";
							}
						}
						$sql .= "'$mwi_account', ";
					}
					$sql .= "'$sip_bypass_media', ";
					$sql .= "'$enabled', ";
					$sql .= "'$description' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);
				}
				$extension++;
			}
			$db->commit();

			//syncrhonize configuration
				sync_package_v_extensions();
		} //end function

		function sql_update() {
			global $db;

			$v_id = $this->v_id;
			$v_domain = $this->v_domain;
			$extension = $this->extension;
			$number_alias = $this->number_alias;
			$password = $this->password;
			$autogen_users = $this->autogen_users;
			$user_list = $this->user_list;
			$provisioning_list = $this->provisioning_list;
			$vm_password = $this->vm_password;
			$accountcode = $this->accountcode;
			$effective_caller_id_name = $this->effective_caller_id_name;
			$effective_caller_id_number = $this->effective_caller_id_number;
			$outbound_caller_id_name = $this->outbound_caller_id_name;
			$outbound_caller_id_number = $this->outbound_caller_id_number;
			$limit_max = $this->limit_max;
			$limit_destination = $this->limit_destination;
			$vm_enabled = $this->vm_enabled;
			$vm_mailto = $this->vm_mailto;
			$vm_attach_file = $this->vm_attach_file;
			$vm_keep_local_after_email = $this->vm_keep_local_after_email;
			$user_context = $this->user_context;
			$toll_allow = $this->toll_allow;
			$callgroup = $this->callgroup;
			$hold_music = $this->hold_music;
			$auth_acl = $this->auth_acl;
			$cidr = $this->cidr;
			$sip_force_contact = $this->sip_force_contact;
			$sip_force_expires = $this->sip_force_expires;
			$nibble_account = $this->nibble_account;
			$mwi_account = $this->mwi_account;
			$sip_bypass_media = $this->sip_bypass_media;
			$enabled = $this->enabled;
			$description = $this->description;

			$userfirstname='extension';$userlastname=$extension;$useremail='';
			$user_list_array = explode("|", $user_list);
			foreach($user_list_array as $tmp_user){
				$user_password = generate_password();
				if (strlen($tmp_user) > 0) {
					user_add($tmp_user, $user_password, $userfirstname, $userlastname, $useremail);
				}
			}
			unset($tmp_user);

			if (strlen($password) == 0) {
				$password = generate_password();
			}

			//sql update
				$sql = "update v_extensions set ";
				$sql .= "extension = '$extension', ";
				$sql .= "number_alias = '$number_alias', ";
				$sql .= "password = '$password', ";
				$sql .= "user_list = '$user_list', ";
				$sql .= "provisioning_list = '$provisioning_list', ";
				if (strlen($vm_password) > 0) {
					$sql .= "vm_password = '$vm_password', ";
				}
				else {
					$sql .= "vm_password = 'user-choose', ";
				}
				$sql .= "accountcode = '$accountcode', ";
				$sql .= "effective_caller_id_name = '$effective_caller_id_name', ";
				$sql .= "effective_caller_id_number = '$effective_caller_id_number', ";
				$sql .= "outbound_caller_id_name = '$outbound_caller_id_name', ";
				$sql .= "outbound_caller_id_number = '$outbound_caller_id_number', ";
				$sql .= "limit_max = '$limit_max', ";
				$sql .= "limit_destination = '$limit_destination', ";
				$sql .= "vm_enabled = '$vm_enabled', ";
				$sql .= "vm_mailto = '$vm_mailto', ";
				$sql .= "vm_attach_file = '$vm_attach_file', ";
				$sql .= "vm_keep_local_after_email = '$vm_keep_local_after_email', ";
				$sql .= "user_context = '$user_context', ";
				$sql .= "toll_allow = '$toll_allow', ";
				$sql .= "callgroup = '$callgroup', ";
				$sql .= "hold_music = '$hold_music', ";
				$sql .= "auth_acl = '$auth_acl', ";
				$sql .= "cidr = '$cidr', ";
				$sql .= "sip_force_contact = '$sip_force_contact', ";
				if (strlen($sip_force_expires) == 0) {
					$sql .= "sip_force_expires = null, ";
				}
				else {
					$sql .= "sip_force_expires = '$sip_force_expires', ";
				}
				if (strlen($nibble_account) == 0) {
					$sql .= "nibble_account = null, ";
				}
				else {
					$sql .= "nibble_account = '$nibble_account', ";
				}
				if (strlen($mwi_account) > 0) {
					if (strpos($mwi_account, '@') === false) {
						if (count($_SESSION["domains"]) > 1) {
							$mwi_account .= "@".$v_domain;
						}
						else {
							$mwi_account .= "@\$\${domain}";
						}
					}
				}
				$sql .= "mwi_account = '$mwi_account', ";
				$sql .= "sip_bypass_media = '$sip_bypass_media', ";
				$sql .= "enabled = '$enabled', ";
				$sql .= "description = '$description' ";
				$sql .= "where v_id = '$v_id' ";
				$sql .= "and extension_id = '$extension_id'";
				$db->exec(check_sql($sql));
				unset($sql);

			//syncrhonize configuration
				sync_package_v_extensions();
		} //end function

		function xml_save_all() {
			global $db, $config;
			$v_id = $this->v_id;
			$v_domain = $this->v_domain;

			//get the system settings paths and set them as variables
				$v_settings_array = v_settings();
				foreach($v_settings_array as $name => $value) {
					$$name = $value;
				}

			//determine the extensions parent directory
				$v_extensions_dir_array = explode("/", $v_extensions_dir);
				$extension_parent_dir = "";
				$x=1;
				foreach ($v_extensions_dir_array as $tmp_dir) {
					if (count($v_extensions_dir_array) > $x) {
						$extension_parent_dir .= $tmp_dir."/";
					}
					else {
						$extension_dir_name = $tmp_dir; 
					}
					$x++;
				}
				$extension_parent_dir = rtrim($extension_parent_dir, "/");

			// delete all old extensions to prepare for new ones
				if($dh = opendir($v_extensions_dir)) {
					$files = Array();
					while($file = readdir($dh)) {
						if($file != "." && $file != ".." && $file[0] != '.') {
							if(is_dir($dir . "/" . $file)) {
								//this is a directory do nothing
							} else {
								//check if file is an extension; verify the file numeric and the extension is xml
								if (substr($file,0,2) == 'v_' && substr($file,-4) == '.xml') {
									unlink($v_extensions_dir."/".$file);
								}
							}
						}
					}
					closedir($dh);
				}

			$sql = "";
			$sql .= "select * from v_extensions ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "order by callgroup asc ";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->execute();
			$i = 0;
			$extension_xml_condensed = false;
			if ($extension_xml_condensed) {
				$fout = fopen($v_extensions_dir."/v_extensions.xml","w");
				$tmp_xml = "<include>\n";
			}
			while($row = $prep_statement->fetch(PDO::FETCH_ASSOC)) {
				$callgroup = $row['callgroup'];
				$callgroup = str_replace(";", ",", $callgroup);
				$tmp_array = explode(",", $callgroup);
				foreach ($tmp_array as &$tmp_callgroup) {
					if (strlen($tmp_callgroup) > 0) {
						if (strlen($callgroups_array[$tmp_callgroup]) == 0) {
							$callgroups_array[$tmp_callgroup] = $row['extension'];
						}
						else {
							$callgroups_array[$tmp_callgroup] = $callgroups_array[$tmp_callgroup].','.$row['extension'];
						}
					}
					$i++;
				}
				$vm_password = $row['vm_password'];
				$vm_password = str_replace("#", "", $vm_password); //preserves leading zeros

				if ($row['enabled'] != "false") {
					//remove invalid characters from the file names
					$extension = $row['extension'];
					$extension = str_replace(" ", "_", $extension);
					$extension = preg_replace("/[\*\:\\/\<\>\|\'\"\?]/", "", $extension);

					if (!$extension_xml_condensed) {
						$fout = fopen($v_extensions_dir."/v_".$extension.".xml","w");
						$tmp_xml .= "<include>\n";
					}
					$cidr = '';
					if (strlen($row['cidr']) > 0) {
						$cidr = " cidr=\"" . $row['cidr'] . "\"";
					}
					$number_alias = '';
					if (strlen($row['number_alias']) > 0) {
						$number_alias = " number-alias=\"".$row['number_alias']."\"";
					}
					$tmp_xml .= "  <user id=\"".$row['extension']."\"".$cidr."".$number_alias.">\n";
					$tmp_xml .= "    <params>\n";
					$tmp_xml .= "      <param name=\"password\" value=\"" . $row['password'] . "\"/>\n";
					$tmp_xml .= "      <param name=\"vm-password\" value=\"" . $vm_password . "\"/>\n";
					if ($row['vm_enabled'] == "true" || $row['vm_enabled'] == "false") {
						$tmp_xml .= "      <param name=\"vm-enabled\" value=\"".$row['vm_enabled']."\"/>\n";
					}
					else {
						$tmp_xml .= "      <param name=\"vm-enabled\" value=\"true\"/>\n";
					}
					if (strlen($row['vm_mailto']) > 0) {
						$tmp_xml .= "      <param name=\"vm-email-all-messages\" value=\"true\"/>\n";
						if ($row['vm_attach_file'] == "true" || $row['vm_attach_file'] == "false") {
							$tmp_xml .= "      <param name=\"vm-attach-file\" value=\"".$row['vm_attach_file']."\"/>\n";
						}
						else {
							$tmp_xml .= "      <param name=\"vm-attach-file\" value=\"true\"/>\n";
						}
						if ($row['vm_keep_local_after_email'] == "true" || $row['vm_keep_local_after_email'] == "false") {
							$tmp_xml .= "      <param name=\"vm-keep-local-after-email\" value=\"".$row['vm_keep_local_after_email']."\"/>\n";
						}
						else {
							$tmp_xml .= "      <param name=\"vm-keep-local-after-email\" value=\"true\"/>\n";
						}
						$tmp_xml .= "      <param name=\"vm-mailto\" value=\"" . $row['vm_mailto'] . "\"/>\n";
					}
					if (strlen($row['mwi_account']) > 0) {
						$tmp_xml .= "      <param name=\"MWI-Account\" value=\"" . $row['mwi_account'] . "\"/>\n";
					}
					if (strlen($row['auth-acl']) > 0) {
						$tmp_xml .= "      <param name=\"auth-acl\" value=\"" . $row['auth_acl'] . "\"/>\n";
					}
					$tmp_xml .= "    </params>\n";
					$tmp_xml .= "    <variables>\n";
					if (strlen($row['hold_music']) > 0) {
						$tmp_xml .= "      <variable name=\"hold_music\" value=\"" . $row['hold_music'] . "\"/>\n";
					}
					$tmp_xml .= "      <variable name=\"toll_allow\" value=\"" . $row['toll_allow'] . "\"/>\n";
					if (strlen($v_account_code) > 0) {
						$tmp_xml .= "      <variable name=\"accountcode\" value=\"" . $v_account_code . "\"/>\n";
					}
					else {
						$tmp_xml .= "      <variable name=\"accountcode\" value=\"" . $row['accountcode'] . "\"/>\n";
					}
					$tmp_xml .= "      <variable name=\"user_context\" value=\"" . $row['user_context'] . "\"/>\n";
					if (strlen($row['effective_caller_id_name']) > 0) {
						$tmp_xml .= "      <variable name=\"effective_caller_id_name\" value=\"" . $row['effective_caller_id_name'] . "\"/>\n";
					}
					if (strlen($row['outbound_caller_id_number']) > 0) {
						$tmp_xml .= "      <variable name=\"effective_caller_id_number\" value=\"" . $row['effective_caller_id_number'] . "\"/>\n";
					}
					if (strlen($row['outbound_caller_id_name']) > 0) {
						$tmp_xml .= "      <variable name=\"outbound_caller_id_name\" value=\"" . $row['outbound_caller_id_name'] . "\"/>\n";
					}
					if (strlen($row['outbound_caller_id_number']) > 0) {
						$tmp_xml .= "      <variable name=\"outbound_caller_id_number\" value=\"" . $row['outbound_caller_id_number'] . "\"/>\n";
					}
					if (strlen($row['limit_max']) > 0) {
						$tmp_xml .= "      <variable name=\"limit_max\" value=\"" . $row['limit_max'] . "\"/>\n";
					}
					else {
						$tmp_xml .= "      <variable name=\"limit_max\" value=\"5\"/>\n";
					}
					if (strlen($row['limit_destination']) > 0) {
						$tmp_xml .= "      <variable name=\"limit_destination\" value=\"" . $row['limit_destination'] . "\"/>\n";
					}
					if (strlen($row['sip_force_contact']) > 0) {
						$tmp_xml .= "      <variable name=\"sip-force-contact\" value=\"" . $row['sip_force_contact'] . "\"/>\n";
					}
					if (strlen($row['sip_force_expires']) > 0) {
						$tmp_xml .= "      <variable name=\"sip-force-expires\" value=\"" . $row['sip_force_expires'] . "\"/>\n";
					}
					if (strlen($row['nibble_account']) > 0) {
						$tmp_xml .= "      <variable name=\"nibble_account\" value=\"" . $row['nibble_account'] . "\"/>\n";
					}
					switch ($row['sip_bypass_media']) {
						case "bypass-media":
								$tmp_xml .= "      <variable name=\"bypass_media\" value=\"true\"/>\n";
								break;
						case "bypass-media-after-bridge":
								$tmp_xml .= "      <variable name=\"bypass_media_after_bridge\" value=\"true\"/>\n";
								break;
						case "proxy-media":
								$tmp_xml .= "      <variable name=\"proxy_media\" value=\"true\"/>\n";
								break;
					}

					$tmp_xml .= "    </variables>\n";
					$tmp_xml .= "  </user>\n";

					if (!$extension_xml_condensed) {
						$tmp_xml .= "</include>\n";
						fwrite($fout, $tmp_xml);
						unset($tmpxml);
						fclose($fout);
					}
				}
			}
			unset ($prep_statement);
			if ($extension_xml_condensed) {
				$tmp_xml .= "</include>\n";
				fwrite($fout, $tmp_xml);
				unset($tmpxml);
				fclose($fout);
			}

			//define the group members
				$tmp_xml = "<!--\n";
				$tmp_xml .= "	NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE\n";
				$tmp_xml .= "\n";
				$tmp_xml .= "	FreeSWITCH works off the concept of users and domains just like email.\n";
				$tmp_xml .= "	You have users that are in domains for example 1000@domain.com.\n";
				$tmp_xml .= "\n";
				$tmp_xml .= "	When freeswitch gets a register packet it looks for the user in the directory\n";
				$tmp_xml .= "	based on the from or to domain in the packet depending on how your sofia profile\n";
				$tmp_xml .= "	is configured.  Out of the box the default domain will be the IP address of the\n";
				$tmp_xml .= "	machine running FreeSWITCH.  This IP can be found by typing \"sofia status\" at the\n";
				$tmp_xml .= "	CLI.  You will register your phones to the IP and not the hostname by default.\n";
				$tmp_xml .= "	If you wish to register using the domain please open vars.xml in the root conf\n";
				$tmp_xml .= "	directory and set the default domain to the hostname you desire.  Then you would\n";
				$tmp_xml .= "	use the domain name in the client instead of the IP address to register\n";
				$tmp_xml .= "	with FreeSWITCH.\n";
				$tmp_xml .= "\n";
				$tmp_xml .= "	NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE\n";
				$tmp_xml .= "-->\n";
				$tmp_xml .= "\n";
				$tmp_xml .= "<include>\n";
				$tmp_xml .= "	<!--the domain or ip (the right hand side of the @ in the addr-->\n";
				if ($extension_dir_name == "default") { 
					$tmp_xml .= "	<domain name=\"\$\${domain}\">\n";
				}
				else {
					$tmp_xml .= "	<domain name=\"".$extension_dir_name."\">\n";
				}
				$tmp_xml .= "		<params>\n";
				$tmp_xml .= "			<param name=\"dial-string\" value=\"{sip_invite_domain=\${domain_name},presence_id=\${dialed_user}@\${dialed_domain}}\${sofia_contact(\${dialed_user}@\${dialed_domain})}\"/>\n";
				$tmp_xml .= "		</params>\n";
				$tmp_xml .= "\n";
				$tmp_xml .= "		<variables>\n";
				$tmp_xml .= "			<variable name=\"record_stereo\" value=\"true\"/>\n";
				$tmp_xml .= "			<variable name=\"default_gateway\" value=\"\$\${default_provider}\"/>\n";
				$tmp_xml .= "			<variable name=\"default_areacode\" value=\"\$\${default_areacode}\"/>\n";
				$tmp_xml .= "			<variable name=\"transfer_fallback_extension\" value=\"operator\"/>\n";
				$tmp_xml .= "			<variable name=\"export_vars\" value=\"domain_name\"/>\n";
				$tmp_xml .= "		</variables>\n";
				$tmp_xml .= "\n";
				$tmp_xml .= "		<groups>\n";
				$tmp_xml .= "			<group name=\"".$extension_dir_name."\">\n";
				$tmp_xml .= "			<users>\n";
				$tmp_xml .= "				<X-PRE-PROCESS cmd=\"include\" data=\"".$extension_dir_name."/*.xml\"/>\n";
				$tmp_xml .= "			</users>\n";
				$tmp_xml .= "			</group>\n";
				$tmp_xml .= "\n";
				$previous_callgroup = "";
				foreach ($callgroups_array as $key => $value) {
					$callgroup = $key;
					$extension_list = $value;
					if (strlen($callgroup) > 0) {
						if ($previous_callgroup != $callgroup) {
							$tmp_xml .= "			<group name=\"$callgroup\">\n";
							$tmp_xml .= "				<users>\n";
							$tmp_xml .= "					<!--\n";
							$tmp_xml .= "					type=\"pointer\" is a pointer so you can have the\n";
							$tmp_xml .= "					same user in multiple groups.  It basically means\n";
							$tmp_xml .= "					to keep searching for the user in the directory.\n";
							$tmp_xml .= "					-->\n";
							$extension_array = explode(",", $extension_list);
							foreach ($extension_array as &$tmp_extension) {
								$tmp_xml .= "					<user id=\"$tmp_extension\" type=\"pointer\"/>\n";
							}
							$tmp_xml .= "				</users>\n";
							$tmp_xml .= "			</group>\n";
							$tmp_xml .= "\n";
						}
						$previous_callgroup = $callgroup;
					}
					unset($callgroup);
				}
				$tmp_xml .= "		</groups>\n";
				$tmp_xml .= "\n";
				$tmp_xml .= "	</domain>\n";
				$tmp_xml .= "</include>";

			//remove invalid characters from the file names
				$extension_dir_name = str_replace(" ", "_", $extension_dir_name);
				$extension_dir_name = preg_replace("/[\*\:\\/\<\>\|\'\"\?]/", "", $extension_dir_name);

			//write the xml file
				$fout = fopen($extension_parent_dir."/".$extension_dir_name.".xml","w");
				fwrite($fout, $tmp_xml);
				unset($tmpxml);
				fclose($fout);

			//syncrhonize the phone directory
				sync_directory();

			//apply settings reminder
				$_SESSION["reload_xml"] = true;

			//call reloadxml direct
				//$cmd = "api reloadxml";
				//event_socket_request_cmd($cmd);
				//unset($cmd);

		}  //end function
	} //class

?>