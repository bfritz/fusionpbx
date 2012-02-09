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
require_once "includes/config.php";
session_start();

//if the username session is not set the check username and password
	if (strlen($_SESSION["username"]) == 0) {
		//clear the menu
			$_SESSION["menu"] = "";

		//clear the template only if the template has not been assigned by the superadmin
			if (strlen($_SESSION['domain']['template']['name']) == 0) {
				$_SESSION["template_content"] = '';
			}

		//if the username from the form is not provided then send to login.php
			if (strlen(check_str($_REQUEST["username"])) == 0) {
				$php_self = $_SERVER["PHP_SELF"];
				$msg = "username required";
				header("Location: ".PROJECT_PATH."/login.php?path=".urlencode($php_self)."&msg=".urlencode($msg));
				exit;
			}

		//check the username and password if they don't match then redirect back to login
			$sql = "select * from v_users ";
			$sql .= "where domain_uuid=:domain_uuid ";
			$sql .= "and username=:username ";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->bindParam(':domain_uuid', $domain_uuid);
			$prep_statement->bindParam(':username', check_str($_REQUEST["username"]));
			$prep_statement->execute();
			$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
			if (count($result) == 0) {
				$auth_failed = true;
			}
			else {
				foreach ($result as &$row) {
					//get the salt from the database
						$salt = $row["salt"];
					//if salt is not defined then use the default salt for backwards compatibility
						if (strlen($salt) == 0) {
							$salt = 'e3.7d.12';
						}
					//compare the password provided by the user with the one in the database
						if (md5($salt.check_str($_REQUEST["password"])) != $row["password"]) {
							$auth_failed = true;
						}
					//end the loop
						break;
				}
			}
			if ($auth_failed) {
				//log the failed auth attempt to the system, to be available for fail2ban.
					openlog('FusionPBX', LOG_NDELAY, LOG_AUTH);
					syslog(LOG_WARNING, '['.$_SERVER['REMOTE_ADDR']."] authentication failed for ".$_REQUEST["username"]);
					closelog();
				//redirect the user to the login page
					$php_self = $_SERVER["PHP_SELF"];
					$msg = "incorrect account information";
					header("Location: ".PROJECT_PATH."/login.php?path=".urlencode($php_self)."&msg=".urlencode($msg));
					exit;
			}
			$_SESSION["username"] = check_str($_REQUEST["username"]);
			foreach ($result as &$row) {
				//allow the user to choose a template only if the template has not been assigned by the superadmin
				if (strlen($_SESSION['domain']['template']['name']) == 0) {
					$_SESSION['domain']['template']['name'] = $row["user_template_name"];
				}
				$_SESSION["time_zone"]["user"] = '';
				if (strlen($row["user_time_zone"]) > 0) {
					//user defined time zone
					$_SESSION["time_zone"]["user"] = $row["user_time_zone"];
				}
				// add the user_uuid to the session
				$_SESSION['user_uuid'] = $row['user_uuid'];
				break;
			}
			//echo "username: ".$_SESSION["username"]." and password are correct";

		//get the groups assigned to the user and then set the groups in $_SESSION["groups"]
			$sql = "SELECT * FROM v_group_members ";
			$sql .= "where domain_uuid=:domain_uuid ";
			$sql .= "and username=:username ";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->bindParam(':domain_uuid', $domain_uuid);
			$prep_statement->bindParam(':username', $_SESSION["username"]);
			$prep_statement->execute();
			$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
			$_SESSION["groups"] = $result;
			unset($sql, $row_count, $prep_statement);

		//get the permissions assigned to the groups that the user is a member of set the permissions in $_SESSION['permissions']
			$x = 0;
			$sql = "select distinct(permission_id) from v_group_permissions ";
			foreach($_SESSION["groups"] as $field) {
				if (strlen($field['group_id']) > 0) {
					if ($x == 0) {
						$sql .= "where (domain_uuid = '".$domain_uuid."' and group_id = '".$field['group_id']."') ";
					}
					else {
						$sql .= "or (domain_uuid = '".$domain_uuid."' and group_id = '".$field['group_id']."') ";
					}
					$x++;
				}
			}
			$prep_statement_sub = $db->prepare($sql);
			$prep_statement_sub->execute();
			$_SESSION['permissions'] = $prep_statement_sub->fetchAll(PDO::FETCH_NAMED);
			unset($sql, $prep_statement_sub);

		//redirect the user
			if (check_str($_REQUEST["rdr"]) !== 'n'){
				$path = check_str($_POST["path"]);
				if(isset($path) && !empty($path) && $path!="index2.php" && $path!="/install.php") {
					header("Location: ".$path);
					exit();
				}
			}
	}

//set the time zone
	if (strlen($_SESSION["time_zone"]["user"]) == 0) {
		//set the domain time zone as the default time zone
		date_default_timezone_set($_SESSION['domain']['time_zone']['name']);
	}
	else {
		//set the user defined time zone
		date_default_timezone_set($_SESSION["time_zone"]["user"]);
	}

//hide the path unless logged in as a superadmin.
	if (!ifgroup("superadmin")) {
		$v_path_show = false;
	}

?>