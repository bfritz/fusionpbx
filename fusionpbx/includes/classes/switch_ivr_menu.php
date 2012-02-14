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
	class switch_ivr_menu {
		//set the variables
			public $db;
			public $domain_uuid;
			public $domain_name;
			public $dialplan_uuid;
			public $ivr_menu_uuid;
			public $ivr_menu_name;
			public $ivr_menu_extension;
			public $ivr_menu_greet_long;
			public $ivr_menu_greet_short;
			public $ivr_menu_invalid_sound;
			public $ivr_menu_exit_sound;
			public $ivr_menu_confirm_macro;
			public $ivr_menu_confirm_key;
			public $ivr_menu_tts_engine;
			public $ivr_menu_tts_voice;
			public $ivr_menu_confirm_attempts;
			public $ivr_menu_timeout;
			public $ivr_menu_exit_app;
			public $ivr_menu_exit_data;
			public $ivr_menu_inter_digit_timeout;
			public $ivr_menu_max_failures;
			public $ivr_menu_max_timeouts;
			public $ivr_menu_digit_len;
			public $ivr_menu_direct_dial;
			public $ivr_menu_enabled;
			public $ivr_menu_desc;
			public $ivr_menu_option_uuid;
			public $ivr_menu_options_digits;
			public $ivr_menu_options_action;
			public $ivr_menu_options_param;
			public $ivr_menu_options_order;
			public $ivr_menu_options_desc;

		// set and get domain_uuid
			public function get_domain_uuid() {
				return $this->domain_uuid;
			}
			public function set_domain_uuid($domain_uuid){
				$this->domain_uuid = $domain_uuid;
			}

		public function add() {

			//set the app_uuid
				$app_uuid = 'a5788e9b-58bc-bd1b-df59-fff5d51253ab';

			//add the ivr menu
				if (strlen($this->ivr_menu_options_action) == 0) {
					//create the ivr menu dialplan extension
						$dialplan_name = $this->ivr_menu_name;
						$dialplan_order ='999';
						$dialplan_context = $_SESSION['context'];
						$dialplan_enabled = $this->ivr_menu_enabled;
						$dialplan_description = $this->ivr_menu_desc;

					//add the dialplan entry
						$this->dialplan_uuid = v_dialplan_add($this->domain_uuid, $dialplan_name, $dialplan_order, $dialplan_context, $dialplan_enabled, $dialplan_description, $app_uuid);

					//add the ivr menu
						$ivr_menu_uuid = uuid();
						$sql = "insert into v_ivr_menus ";
						$sql .= "(";
						$sql .= "domain_uuid, ";
						$sql .= "dialplan_uuid, ";
						$sql .= "ivr_menu_uuid, ";
						$sql .= "ivr_menu_name, ";
						$sql .= "ivr_menu_extension, ";
						$sql .= "ivr_menu_greet_long, ";
						$sql .= "ivr_menu_greet_short, ";
						$sql .= "ivr_menu_invalid_sound, ";
						$sql .= "ivr_menu_exit_sound, ";
						$sql .= "ivr_menu_confirm_macro, ";
						$sql .= "ivr_menu_confirm_key, ";
						$sql .= "ivr_menu_tts_engine, ";
						$sql .= "ivr_menu_tts_voice, ";
						$sql .= "ivr_menu_confirm_attempts, ";
						$sql .= "ivr_menu_timeout, ";
						$sql .= "ivr_menu_exit_app, ";
						$sql .= "ivr_menu_exit_data, ";
						$sql .= "ivr_menu_inter_digit_timeout, ";
						$sql .= "ivr_menu_max_failures, ";
						$sql .= "ivr_menu_max_timeouts, ";
						$sql .= "ivr_menu_digit_len, ";
						$sql .= "ivr_menu_direct_dial, ";
						$sql .= "ivr_menu_enabled, ";
						$sql .= "ivr_menu_desc ";
						$sql .= ")";
						$sql .= "values ";
						$sql .= "(";
						$sql .= "'".$this->domain_uuid."', ";
						$sql .= "'".$this->dialplan_uuid."', ";
						$sql .= "'".$this->ivr_menu_uuid."', ";
						$sql .= "'".$this->ivr_menu_name."', ";
						$sql .= "'".$this->ivr_menu_extension."', ";
						$sql .= "'".$this->ivr_menu_greet_long."', ";
						$sql .= "'".$this->ivr_menu_greet_short."', ";
						$sql .= "'".$this->ivr_menu_invalid_sound."', ";
						$sql .= "'".$this->ivr_menu_exit_sound."', ";
						$sql .= "'".$this->ivr_menu_confirm_macro."', ";
						$sql .= "'".$this->ivr_menu_confirm_key."', ";
						$sql .= "'".$this->ivr_menu_tts_engine."', ";
						$sql .= "'".$this->ivr_menu_tts_voice."', ";
						$sql .= "'".$this->ivr_menu_confirm_attempts."', ";
						$sql .= "'".$this->ivr_menu_timeout."', ";
						$sql .= "'".$this->ivr_menu_exit_app."', ";
						$sql .= "'".$this->ivr_menu_exit_data."', ";
						$sql .= "'".$this->ivr_menu_inter_digit_timeout."', ";
						$sql .= "'".$this->ivr_menu_max_failures."', ";
						$sql .= "'".$this->ivr_menu_max_timeouts."', ";
						$sql .= "'".$this->ivr_menu_digit_len."', ";
						$sql .= "'".$this->ivr_menu_direct_dial."', ";
						$sql .= "'".$this->ivr_menu_enabled."', ";
						$sql .= "'".$this->ivr_menu_desc."' ";
						$sql .= ")";
						$this->db->exec(check_sql($sql));
						unset($sql);
				}
			
			//add the ivr menu option			
				if (strlen($this->ivr_menu_options_action) > 0) {
					$ivr_menu_option_uuid = uuid();
					$sql = "insert into v_ivr_menu_options ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "ivr_menu_uuid, ";
					$sql .= "ivr_menu_option_uuid, ";
					$sql .= "ivr_menu_options_digits, ";
					$sql .= "ivr_menu_options_action, ";
					$sql .= "ivr_menu_options_param, ";
					$sql .= "ivr_menu_options_order, ";
					$sql .= "ivr_menu_options_desc ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'".$this->domain_uuid."', ";
					$sql .= "'".$this->ivr_menu_uuid."', ";
					$sql .= "'".$this->ivr_menu_option_uuid."', ";
					$sql .= "'".$this->ivr_menu_options_digits."', ";
					$sql .= "'".$this->ivr_menu_options_action."', ";
					$sql .= "'".$this->ivr_menu_options_param."', ";
					$sql .= "'".$this->ivr_menu_options_order."', ";
					$sql .= "'".$this->ivr_menu_options_desc."' ";
					$sql .= ")";
					$this->db->exec(check_sql($sql));
					unset($sql);
				}
		}

		public function update() {

			//udate the ivr menu
				if (strlen($this->ivr_menu_options_action) == 0) {
					//create the ivr menu dialplan extension
						$dialplan_name = $this->ivr_menu_name;
						$dialplan_order ='999';
						$dialplan_context = $_SESSION['context'];
						$dialplan_enabled = $this->ivr_menu_enabled;
						$dialplan_description = $this->ivr_menu_desc;

					//update the database
						$sql = "update v_ivr_menus set ";
						$sql .= "domain_uuid = '".$this->domain_uuid."', ";
						$sql .= "ivr_menu_name = '".$this->ivr_menu_name."', ";
						$sql .= "ivr_menu_extension = '".$this->ivr_menu_extension."', ";
						$sql .= "ivr_menu_greet_long = '".$this->ivr_menu_greet_long."', ";
						$sql .= "ivr_menu_greet_short = '".$this->ivr_menu_greet_short."', ";
						$sql .= "ivr_menu_invalid_sound = '".$this->ivr_menu_invalid_sound."', ";
						$sql .= "ivr_menu_exit_sound = '".$this->ivr_menu_exit_sound."', ";
						$sql .= "ivr_menu_confirm_macro = '".$this->ivr_menu_confirm_macro."', ";
						$sql .= "ivr_menu_confirm_key = '".$this->ivr_menu_confirm_key."', ";
						$sql .= "ivr_menu_tts_engine = '".$this->ivr_menu_tts_engine."', ";
						$sql .= "ivr_menu_tts_voice = '".$this->ivr_menu_tts_voice."', ";
						$sql .= "ivr_menu_confirm_attempts = '".$this->ivr_menu_confirm_attempts."', ";
						$sql .= "ivr_menu_timeout = '".$this->ivr_menu_timeout."', ";
						$sql .= "ivr_menu_exit_app = '".$this->ivr_menu_exit_app."', ";
						$sql .= "ivr_menu_exit_data = '".$this->ivr_menu_exit_data."', ";
						$sql .= "ivr_menu_inter_digit_timeout = '".$this->ivr_menu_inter_digit_timeout."', ";
						$sql .= "ivr_menu_max_failures = '".$this->ivr_menu_max_failures."', ";
						$sql .= "ivr_menu_max_timeouts = '".$this->ivr_menu_max_timeouts."', ";
						$sql .= "ivr_menu_digit_len = '".$this->ivr_menu_digit_len."', ";
						$sql .= "ivr_menu_direct_dial = '".$this->ivr_menu_direct_dial."', ";
						$sql .= "ivr_menu_enabled = '".$this->ivr_menu_enabled."', ";
						$sql .= "ivr_menu_desc = '".$this->ivr_menu_desc."' ";
						$sql .= "where domain_uuid = '".$this->domain_uuid."' ";
						$sql .= "and ivr_menu_uuid = '".$this->ivr_menu_uuid."' ";
						$this->db->exec(check_sql($sql));
						unset($sql);

					//get the dialplan_uuid
						$sql = "";
						$sql .= "select * from v_ivr_menus ";
						$sql .= "where domain_uuid = '".$this->domain_uuid."' ";
						$sql .= "and ivr_menu_uuid = '".$ivr_menu_uuid."' ";
						$prep_statement = $this->db->prepare(check_sql($sql));
						$prep_statement->execute();
						$result = $prep_statement->fetchAll();
						foreach ($result as &$row) {				
							$dialplan_uuid = $row["dialplan_uuid"];
						}
						unset ($prep_statement);

					//update the dialplan
						$sql = "";
						$sql = "update v_dialplans set ";
						$sql .= "dialplan_name = '".$dialplan_name."', ";
						$sql .= "dialplan_order = '".$dialplan_order."', ";
						$sql .= "dialplan_context = '".$dialplan_context."', ";
						$sql .= "dialplan_enabled = '".$dialplan_enabled."', ";
						$sql .= "dialplan_description = '".$dialplan_description."' ";
						$sql .= "where domain_uuid = '".$domain_uuid."' ";
						$sql .= "and dialplan_uuid = '".$dialplan_uuid."' ";
						$this->db->query($sql);
						unset($sql);
				}
			
			//update the ivr menu option
				if (strlen($this->ivr_menu_options_action) > 0) {
					$sql = "update v_ivr_menu_options set ";
					$sql .= "domain_uuid = '".$this->domain_uuid."', ";
					$sql .= "ivr_menu_uuid = '".$this->ivr_menu_uuid."', ";
					$sql .= "ivr_menu_options_digits = '".$this->ivr_menu_options_digits."', ";
					$sql .= "ivr_menu_options_action = '".$this->ivr_menu_options_action."', ";
					$sql .= "ivr_menu_options_param = '".$this->ivr_menu_options_param."', ";
					$sql .= "ivr_menu_options_order = '".$this->ivr_menu_options_order."', ";
					$sql .= "ivr_menu_options_desc = '".$this->ivr_menu_options_desc."' ";
					$sql .= "where ivr_menu_option_uuid = '".$this->ivr_menu_option_uuid."' ";
					$this->db->exec(check_sql($sql));
					unset($sql);
				}
		}

		function delete() {
			//start the atomic transaction
				$count = $this->db->exec("BEGIN;");

			//delete the ivr menu option
				if (strlen($this->ivr_menu_option_uuid) > 0) {
					$sql = "";
					$sql .= "delete from v_ivr_menu_options ";
					$sql .= "where domain_uuid = '".$this->domain_uuid."' ";
					$sql .= "and ivr_menu_option_uuid = '".$this->ivr_menu_option_uuid."' ";
					$prep_statement = $this->db->prepare(check_sql($sql));
					$prep_statement->execute();
					unset($sql);
				}

			//delete the ivr menu
				if (strlen($this->ivr_menu_option_uuid) == 0) {
					//delete the dialplan entries
						$sql = "";
						$sql .= "select * from v_ivr_menus ";
						$sql .= "where domain_uuid = '".$this->domain_uuid."' ";
						$sql .= "and ivr_menu_uuid = '".$this->ivr_menu_uuid."' ";
						$prep_statement = $this->db->prepare($sql);
						$prep_statement->execute();
						while($row2 = $prep_statement->fetch()) {
							$dialplan_uuid = $row2['dialplan_uuid'];
							break;
						}
						unset ($sql, $prep_statement);

					//delete child data
						$sql = "";
						$sql .= "delete from v_ivr_menu_options ";
						$sql .= "where domain_uuid = '".$this->domain_uuid."' ";
						$sql .= "and ivr_menu_uuid = '".$this->ivr_menu_uuid."' ";
						$this->db->query($sql);
						unset($sql);

					//delete parent data
						$sql = "";
						$sql .= "delete from v_ivr_menus ";
						$sql .= "where domain_uuid = '".$this->domain_uuid."' ";
						$sql .= "and ivr_menu_uuid = '".$this->ivr_menu_uuid."' ";
						$this->db->query($sql);
						unset($sql);

					//delete the child dialplan information
						$sql = "";
						$sql = "delete from v_dialplan_details ";
						$sql .= "where domain_uuid = '".$this->domain_uuid."' ";
						$sql .= "and dialplan_uuid = '".$this->dialplan_uuid."' ";
						$this->db->query($sql);
						unset($sql);

					//commit the transaction
						$count = $this->db->exec("COMMIT;");
				}
		}

		function get_xml(){

			return $xml;
		}

		function save_xml($xml){

			return $xml;
		}
		function xml_save_all() {
			
		}
	} //class

?>