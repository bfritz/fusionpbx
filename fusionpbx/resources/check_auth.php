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
	Portions created by the Initial Developer are Copyright (C) 2008-2013
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
require_once "resources/require.php";

//for compatability require this library if less than version 5.5
	if (version_compare(phpversion(), '5.5', '<')) {
		require_once "resources/functions/password.php";
	}

//start the session
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
			if (strlen(check_str($_REQUEST["username"])) == 0 && strlen(check_str($_REQUEST["key"])) == 0) {
				$php_self = $_SERVER["PHP_SELF"];
				$msg = "username required";
				header("Location: ".PROJECT_PATH."/login.php?path=".urlencode($php_self)."&msg=".urlencode($msg));
				exit;
			}

		//get the domain name
			if (count($_SESSION["domains"]) > 1) {
				//get the domain from the url
					$domain_name = $_SERVER["HTTP_HOST"];
				//get the domain name from the username
					$username_array = explode("@", check_str($_REQUEST["username"]));
					if (count($username_array) > 1) {
						$domain_name = $username_array[count($username_array) -1];
						$_REQUEST["username"] = substr(check_str($_REQUEST["username"]), 0, -(strlen($domain_name)+1));
					}
				//get the domain name from the http value
					if (strlen(check_str($_REQUEST["domain_name"])) > 0) {
						$domain_name = check_str($_REQUEST["domain_name"]);
					}
				//set the domain information
					if (strlen($domain_name) > 0) {
						require_once "resources/classes/domains.php";
						foreach ($_SESSION['domains'] as &$row) {
							if ($row['domain_name'] == $domain_name) {
								//set the domain session variables
									$domain_uuid = $row["domain_uuid"];
									$_SESSION["domain_uuid"] = $row["domain_uuid"];
									$_SESSION['domains'][$row['domain_uuid']]['domain_uuid'] = $row['domain_uuid'];
									$_SESSION['domains'][$row['domain_uuid']]['domain_name'] = $domain_name;
									$_SESSION["domain_name"] = $domain_name;

								//set the setting arrays
									$domain = new domains();
									$domain->db = $db;
									$domain->set();
							}
						}
					}
			}

		//get the username or key
			$username = check_str($_REQUEST["username"]);
			if (file_exists($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/app/api/app_config.php')) {
				$key = check_str($_REQUEST["key"]);
			}

		//ldap authentication
			if ($_SESSION["ldap"]["authentication"]["boolean"] == "true") {
				//use ldap to validate the user credentials
					if (strlen(check_str($_REQUEST["domain_name"])) > 0) {
						$domain_name = check_str($_REQUEST["domain_name"]);
					}
					$ad = ldap_connect("ldap://".$_SESSION["ldap"]["server_host"]["text"].":".$_SESSION["ldap"]["server_port"]["numeric"])
						or die("Couldn't connect to AD!");
					ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
					$bd = ldap_bind($ad,$username."@".$domain_name,check_str($_REQUEST["password"]));
					if ($bd) {
						//echo "success\n";
						$auth_failed = false;
					}
					else {
						//echo "failed\n";
						$auth_failed = true;
					}

				//check to see if the user exists
					if (!$auth_failed) {
						$sql = "select * from v_users ";
						$sql .= "where username=:username ";
						if (count($_SESSION["domains"]) > 1) {
							$sql .= "and domain_uuid=:domain_uuid ";
						}
						$prep_statement = $db->prepare(check_sql($sql));
						if (count($_SESSION["domains"]) > 1) {
							$prep_statement->bindParam(':domain_uuid', $domain_uuid);
						}
						$prep_statement->bindParam(':username', $username);
						$prep_statement->execute();
						$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
						if (count($result) == 0) {
							//salt used with the password to create a one way hash
								$salt = generate_password('20', '4');
								$password = generate_password('20', '4');

							//prepare the uuids
								$user_uuid = uuid();
								$contact_uuid = uuid();

							//add the user
								$sql = "insert into v_users ";
								$sql .= "(";
								$sql .= "domain_uuid, ";
								$sql .= "user_uuid, ";
								$sql .= "contact_uuid, ";
								$sql .= "username, ";
								$sql .= "password, ";
								$sql .= "salt, ";
								$sql .= "add_date, ";
								$sql .= "add_user, ";
								$sql .= "user_enabled ";
								$sql .= ") ";
								$sql .= "values ";
								$sql .= "(";
								$sql .= "'$domain_uuid', ";
								$sql .= "'$user_uuid', ";
								$sql .= "'$contact_uuid', ";
								$sql .= "'".strtolower($username)."', ";
								$sql .= "'".md5($salt.$password)."', ";
								$sql .= "'".$salt."', ";
								$sql .= "now(), ";
								$sql .= "'".strtolower($username)."', ";
								$sql .= "'true' ";
								$sql .= ")";
								$db->exec(check_sql($sql));
								unset($sql);

							//add the user to group user
								$group_name = 'user';
								$sql = "insert into v_group_users ";
								$sql .= "(";
								$sql .= "group_user_uuid, ";
								$sql .= "domain_uuid, ";
								$sql .= "group_name, ";
								$sql .= "user_uuid ";
								$sql .= ")";
								$sql .= "values ";
								$sql .= "(";
								$sql .= "'".uuid()."', ";
								$sql .= "'$domain_uuid', ";
								$sql .= "'$group_name', ";
								$sql .= "'$user_uuid' ";
								$sql .= ")";
								$db->exec(check_sql($sql));
								unset($sql);
						}
					}
			}
		//database authentication
			else {
				//check the username and password if they don't match then redirect to the login
					$sql = "select * from v_users ";
					//$sql .= "where domain_uuid='".$domain_uuid."' ";
					$sql .= "where domain_uuid=:domain_uuid ";
					if (strlen($key) > 0) {
						$sql .= "and api_key=:key ";
						//$sql .= "and api_key='".$key."' ";
					}
					else {
						$sql .= "and username=:username ";
						//$sql .= "and username='".$username."' ";
					}
					$sql .= "and (user_enabled = 'true' or user_enabled is null) ";
					$prep_statement = $db->prepare(check_sql($sql));
					$prep_statement->bindParam(':domain_uuid', $domain_uuid);
					if (strlen($key) > 0) {
						$prep_statement->bindParam(':key', $key);
					}
					else {
						$prep_statement->bindParam(':username', $username);
					}
					$prep_statement->execute();
					$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
					if (count($result) == 0) {
						$auth_failed = true;
					}
					else {
						if (strlen($key) > 0) {
							$auth_failed = false;
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
					}
			}
			if ($auth_failed) {
				//log the failed auth attempt to the system, to be available for fail2ban.
					openlog('FusionPBX', LOG_NDELAY, LOG_AUTH);
					syslog(LOG_WARNING, '['.$_SERVER['REMOTE_ADDR']."] authentication failed for ".check_str($_REQUEST["username"]));
					closelog();
				//redirect the user to the login page
					$php_self = $_SERVER["PHP_SELF"];
					$msg = "incorrect account information";
					header("Location: ".PROJECT_PATH."/login.php?path=".urlencode($php_self)."&msg=".urlencode($msg));
					exit;
			}
			foreach ($result as &$row) {
				//allow the user to choose a template only if the template has not been assigned by the superadmin
					if (strlen($_SESSION['domain']['template']['name']) == 0) {
						$_SESSION['domain']['template']['name'] = $row["user_template_name"];
					}
				//user defined time zone
					$_SESSION["time_zone"]["user"] = '';
					if (strlen($row["user_time_zone"]) > 0) {
						//user defined time zone
						$_SESSION["time_zone"]["user"] = $row["user_time_zone"];
					}
				// add session variables
					$_SESSION["user_uuid"] = $row["user_uuid"];
					$_SESSION["username"] = $row["username"];
				// user session array
					$_SESSION["user"]["username"] = $row["username"];
					$_SESSION["user"]["user_uuid"] = $row["user_uuid"];
					$_SESSION["user"]["contact_uuid"] = $row["contact_uuid"];
			}

		//get the groups assigned to the user and then set the groups in $_SESSION["groups"]
			$sql = "SELECT * FROM v_group_users ";
			//$sql .= "where domain_uuid='".$domain_uuid."' ";
			//$sql .= "and user_uuid='".$_SESSION["user_uuid"]."' ";
			$sql .= "where domain_uuid=:domain_uuid ";
			$sql .= "and user_uuid=:user_uuid ";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->bindParam(':domain_uuid', $domain_uuid);
			$prep_statement->bindParam(':user_uuid', $_SESSION["user_uuid"]);
			$prep_statement->execute();
			$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
			$_SESSION["groups"] = $result;
			unset($sql, $row_count, $prep_statement);

		//get the permissions assigned to the groups that the user is a member of set the permissions in $_SESSION['permissions']
			$x = 0;
			$sql = "select distinct(permission_name) from v_group_permissions ";
			foreach($_SESSION["groups"] as $field) {
				if (strlen($field['group_name']) > 0) {
					if ($x == 0) {
						$sql .= "where (domain_uuid = '".$domain_uuid."' and group_name = '".$field['group_name']."') ";
					}
					else {
						$sql .= "or (domain_uuid = '".$domain_uuid."' and group_name = '".$field['group_name']."') ";
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
	if (!if_group("superadmin")) {
		$v_path_show = false;
	}

?>