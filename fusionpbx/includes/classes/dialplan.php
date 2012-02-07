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

//define the dialplan class
	if (!class_exists('dialplan')) {
		class dialplan {
			//variables
			var $result;
			var $domain_uuid;
			var $dialplan_uuid;
			var $v_domain;
			var $switch_conf_dir;

			//dialplans
			var $dialplan_name;
			var $dialplan_continue;
			var $dialplan_order;
			var $context;
			var $enabled;
			var $description;

			//dialplan_details
			var $dialplan_detail_tag;
			var $dialplan_detail_order;
			var $dialplan_detail_type;
			var $dialplan_detail_data;
			var $dialplan_detail_break;
			var $dialplan_detail_inline;
			var $dialplan_detail_group;

			function dialplan_add() {
				global $db;
			}
			
			function dialplan_update() {
				global $db;
			}

			function dialplan_detail_add() {
				global $db;
				$dialplan_detail_uuid = uuid();
				$sql = "insert into v_dialplan_details ";
				$sql .= "(";
				$sql .= "domain_uuid, ";
				$sql .= "dialplan_uuid, ";
				$sql .= "dialplan_detail_tag, ";
				$sql .= "dialplan_detail_order, ";
				$sql .= "dialplan_detail_type, ";
				$sql .= "dialplan_detail_data, ";
				$sql .= "dialplan_detail_break, ";
				$sql .= "dialplan_detail_inline, ";
				$sql .= "dialplan_detail_group ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$this->domain_uuid', ";
				$sql .= "'$this->dialplan_uuid', ";
				$sql .= "'$this->dialplan_detail_tag', ";
				$sql .= "'$this->dialplan_detail_order', ";
				$sql .= "'$this->dialplan_detail_type', ";
				$sql .= "'$this->dialplan_detail_data', ";
				$sql .= "'$this->dialplan_detail_break', ";
				$sql .= "'$this->dialplan_detail_inline', ";
				if (strlen($this->dialplan_detail_group) == 0) {
					$sql .= "null ";
				}
				else {
					$sql .= "'$this->dialplan_detail_group' ";
				}
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);
			} //end function

			function dialplan_detail_update() {
				global $db;
				$sql = "";
				$sql = "update v_dialplans set ";
				$sql .= "dialplan_name = '$this->dialplan_name', ";
				$sql .= "dialplan_order = '$this->dialplan_order', ";
				$sql .= "dialplan_context = '$this->context', ";
				$sql .= "dialplan_enabled = '$this->enabled', ";
				$sql .= "dialplan_description = '$this->description' ";
				$sql .= "where domain_uuid = '$this->domain_uuid' ";
				//$sql .= "and opt_1_name = '$this->opt_1_name' ";
				//$sql .= "and opt_1_value = '$this->opt_1_value' ";
				//echo "sql: ".$sql."<br />";
				$db->query($sql);
				unset($sql);
			} //end function

			function restore_advanced_xml() {
				$v_domain = $this->v_domain;
				$switch_dialplan_dir = $this->switch_dialplan_dir;
				//get the contents of the dialplan/default.xml
					$file_default_path = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/templates/conf/dialplan/default.xml';
					$file_default_contents = file_get_contents($file_default_path);
				//prepare the file contents and the path
					if (count($_SESSION['domains']) < 2) {
						//replace the variables in the template in the future loop through all the line numbers to do a replace for each possible line number
							$file_default_contents = str_replace("{v_domain}", 'default', $file_default_contents);
						//set the file path
							$file_path = $switch_dialplan_dir.'/default.xml';
					}
					else {
						//replace the variables in the template in the future loop through all the line numbers to do a replace for each possible line number
							$file_default_contents = str_replace("{v_domain}", $v_domain, $file_default_contents);
						//set the file path
							$file_path = $switch_dialplan_dir.'/'.$v_domain.'.xml';
					}
				//write the default dialplan
					$fh = fopen($file_path,'w') or die('Unable to write to '.$file_path.'. Make sure the path exists and permissons are set correctly.');
					fwrite($fh, $file_default_contents);
					fclose($fh);
				//set the message
					$this->result['dialplan']['restore']['msg'] = "Default Restored";
			}
		}
	}
?>