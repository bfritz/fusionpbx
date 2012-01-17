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

//define the database class
	if (!class_exists('database')) {
		class database {
			var $result;
			var $type;

			public function connect() {
				var $db_host
				var $db_port;
				var $db_name;
				var $db_username;
				var $db_password;

				if ($this->type == "sqlite") {
					if (strlen($db_file_name) == 0) {
						$server_name = $_SERVER["SERVER_NAME"];
						$server_name = str_replace ("www.", "", $server_name);
						$server_name = str_replace ("example.net", "example.com", $server_name);
						$db_file_name_short = $server_name;
						$db_file_name = $server_name.'.db';
					}
					else {
						$db_file_name_short = $db_file_name;
					}

					$file_path = $v_secure;
					$db_file_path = $v_secure;
					$db_file_path = realpath($db_file_path);
					if (file_exists($db_file_path.'/'.$db_file_name)) {
						//echo "main file exists<br>";
					}
					else {
						$file_name = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/sql/sqlite.sql';
						$file_contents = file_get_contents($file_name);
						try {
							//$db = new PDO('sqlite2:example.db'); //sqlite 2
							//$dbimg = new PDO('sqlite::memory:'); //sqlite 3
							$db_sql = new PDO('sqlite:'.$db_file_path.'/'.$db_file_name); //sqlite 3
							$db_sql->beginTransaction();
						}
						catch (PDOException $error) {
							print "error: " . $error->getMessage() . "<br/>";
							die();
						}

						//replace \r\n with \n then explode on \n
						$file_contents = str_replace("\r\n", "\n", $file_contents);

						//loop line by line through all the lines of sql code
						$stringarray = explode("\n", $file_contents);
						$x = 0;
						foreach($stringarray as $sql) {
							try {
								$db_sql->query($sql);
							}
							catch (PDOException $error) {
								echo "error: " . $error->getMessage() . " sql: $sql<br/>";
							}
							$x++;
						}
						unset ($file_contents, $sql);
						$db_sql->commit();

						if (is_writable($db_file_path.'/'.$db_file_name)) {
							//is writable - use database in current location
						}
						else { 
							//not writable
							echo "The database ".$db_file_path."/".$db_file_name." is not writeable.";
							exit;
						}
					}
					try {
						//$db = new PDO('sqlite2:example.db'); //sqlite 2
						//$db = new PDO('sqlite::memory:'); //sqlite 3
						$db = new PDO('sqlite:'.$db_file_path.'/'.$db_file_name); //sqlite 3

						//Add additional functions to SQLite so that they are accessible inside SQL
						//bool PDO::sqliteCreateFunction ( string function_name, callback callback [, int num_args] )
						$db->sqliteCreateFunction('md5', 'php_md5', 1);
						$db->sqliteCreateFunction('unix_timestamp', 'php_unix_time_stamp', 1);
						$db->sqliteCreateFunction('now', 'php_now', 0);
						$db->sqliteCreateFunction('str_left', 'php_left', 2);
						$db->sqliteCreateFunction('str_right', 'php_right', 2);
						return $db;
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
						die();
					}
				}

				if ($this->type == "mysql") {
					try {
						//required for mysql_real_escape_string
							if (function_exists(mysql_connect)) {
								$mysql_connection = mysql_connect($this->db_host, $this->db_username, $this->db_password);
							}
						//mysql pdo connection
							if (strlen($this->db_host) == 0 && strlen($this->db_port) == 0) {
								//if both host and port are empty use the unix socket
								$db = new PDO("mysql:host=$this->db_host;unix_socket=/var/run/mysqld/mysqld.sock;dbname=$this->db_name", $db_username, $db_password);
							}
							else {
								if (strlen($this->db_port) == 0) {
									//leave out port if it is empty
									$db = new PDO("mysql:host=$this->db_host;dbname=$this->db_name;", $this->db_username, $this->db_password, array(
									PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION
									));
								}
								else {
									$db = new PDO("mysql:host=$this->db_host;port=$this->db_port;dbname=$this->db_name;", $this->db_username, $this->db_password, array(
									PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION
									));
								}
							}
							return $db;
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
						die();
					}
				}

				if ($this->type == "pgsql") {
					//database connection
					try {
						if (strlen($this->db_host) > 0) {
							if (strlen($this->db_port) == 0) { $this->db_port = "5432"; }
							$db = new PDO("pgsql:host=$this->db_host port=$this->db_port dbname=$this->db_name user=$this->db_username password=$this->db_password");
						}
						else {
							$db = new PDO("pgsql:dbname=$this->db_name user=$this->db_username password=$this->db_password");
						}
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
						die();
					}
					return $db;
				}
			}

			//public function disconnect() {
			//	return null;
			//}

			public function select() {
				var $type;
				var $connection;
				var $table;
				var $where;
				var $order_by;
				var $limit;
				var $offset;

				$sql = "";
				$sql .= " select * from ".$this->table;
				if ($this->where) {
					$sql .= $this->where." ";
				}
				if ($this->orderby) {
					$sql .= $this->order_by." ";
				}
				if ($this->limit) {
					$sql .= " limit $limit offset $offset ";
				}
				$prep_statement = $db->prepare($sql);
				if ($prep_statement) {
					$prep_statement->execute();
					$result = $prep_statement->fetchAll();
					$this->result = $result;
					$this->count = count($result);
				}
			}

			public function insert($named_array){
				var $type;
				var $connection;
				$sql = "insert into ".$this->table;
				$sql .= "(";
				//loop through the filed names
				//$sql .= "domain_uuid, ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				//loop through the values
				//$sql .= "'$domain_uuid', ";
				$sql .= ")";
				$db->exec($sql);
				unset($sql);
			}

			public function delete(){
				var $type;
				var $connection;
				var $where;
				if ($this->type == "pdo") {
					$sql = "";
					$sql .= "delete from ".$this->table." ";
					if ($this->where) {
						$sql .= $this->where;
					}
					$prep_statement = $this->connection->prepare($sql);
					$prep_statement->execute();
					unset($sql);
				}
			}

			public function update() {
				var $type;
				var $connection;
				var $table;
				var $where;

				$sql = "update ".$this->table." set ";
				//loop through the filed name and value pairs
				//$sql .= "zzz = '$zzz', ";

				if ($this->where) {
					$sql .= $this->where;
				}
				$db->exec(check_sql($sql));
				unset($sql);
			}

			public function count() {
				var $type;
				var $connection;
				var $table;
				var $where;

				$sql = "";
				$sql .= " select count(*) as num_rows from ".$this->table;
				if ($this->where) {
					$sql .= $this->where." ";
				}
				$prepstatement = $this->connect->prepare(check_sql($sql));
				if ($prepstatement) {
					$prepstatement->execute();
					$row = $prepstatement->fetch(PDO::FETCH_ASSOC);
					if ($row['num_rows'] > 0) {
						return $row['num_rows'];
					}
					else {
						return = 0;
					}
				}
				unset($prepstatement, $result);
			}

			if (!function_exists('php_md5')) {
				private function php_md5($string) {
					return md5($string);
				}
			}

			if (!function_exists('php_unix_time_stamp')) {
				private function php_unix_time_stamp($string) {
					return strtotime($string);
				}
			}

			if (!function_exists('php_now')) {
				private function php_now() {
					//return date('r');
					return date("Y-m-d H:i:s");
				}
			}

			if (!function_exists('php_left')) {
				private function php_left($string, $num) {
					return substr($string, 0, $num);
				}
			}

			if (!function_exists('php_right')) {
				private function php_right($string, $num) {
					return substr($string, (strlen($string)-$num), strlen($string));
				}
			}

		}
	}
?>