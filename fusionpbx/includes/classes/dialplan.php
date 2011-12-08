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
			var $result;
			var $v_id;
			var $dialplan_include_id;
			var $tag;
			var $fieldorder;
			var $fieldtype;
			var $fielddata;
			var $fieldbreak;
			var $field_inline;
			var $field_group;
			var $extensionname;
			var $dialplanorder;
			var $context;
			var $enabled;
			var $opt1name;
			var $opt1value;
			var $descr;
			var $v_domain;
			var $v_conf_dir;

			function dialplan_add() {
				global $db;
			}
			
			function dialplan_update() {
				global $db;
			}

			function dialplan_detail_add() {
				global $db;
				$sql = "insert into v_dialplan_includes_details ";
				$sql .= "(";
				$sql .= "v_id, ";
				$sql .= "dialplan_include_id, ";
				$sql .= "tag, ";
				$sql .= "fieldorder, ";
				$sql .= "fieldtype, ";
				$sql .= "fielddata, ";
				$sql .= "fieldbreak, ";
				$sql .= "field_inline, ";
				$sql .= "field_group ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$this->v_id', ";
				$sql .= "'$this->dialplan_include_id', ";
				$sql .= "'$this->tag', ";
				$sql .= "'$this->fieldorder', ";
				$sql .= "'$this->fieldtype', ";
				$sql .= "'$this->fielddata', ";
				$sql .= "'$this->fieldbreak', ";
				$sql .= "'$this->field_inline', ";
				if (strlen($this->field_group) == 0) {
					$sql .= "null ";
				}
				else {
					$sql .= "'$this->field_group' ";
				}
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);
			} //end function

			function dialplan_detail_update() {
				global $db;
				$sql = "";
				$sql = "update v_dialplan_includes set ";
				$sql .= "extensionname = '$this->extensionname', ";
				$sql .= "dialplanorder = '$this->dialplanorder', ";
				$sql .= "context = '$this->context', ";
				$sql .= "enabled = '$this->enabled', ";
				$sql .= "descr = '$this->descr' ";
				$sql .= "where v_id = '$this->v_id' ";
				$sql .= "and opt1name = '$this->opt1name' ";
				$sql .= "and opt1value = '$this->opt1value' ";
				//echo "sql: ".$sql."<br />";
				$db->query($sql);
				unset($sql);
			} //end function

			function restore_advanced_xml() {
				$v_domain = $this->v_domain;
				$v_conf_dir = $this->v_conf_dir;
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
					$fh = fopen($file_path,'w') or die('Unable to write to '.$file_path.'. Make sure the path exists and permissons are set correctly.');
					fwrite($fh, $file_default_contents);
					fclose($fh);
				//set the message
					$this->result['dialplan']['restore']['msg'] = "Default Restored";
			}
		} //class
	}
?>