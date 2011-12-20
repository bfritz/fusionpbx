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

//check the permission
	if(defined('STDIN')) {
		$document_root = str_replace("\\", "/", $_SERVER["PHP_SELF"]);
		preg_match("/^(.*)\/mod\/.*$/", $document_root, $matches);
		$document_root = $matches[1];
		echo "document_root: ".$document_root."\n";
		set_include_path($document_root);
		require_once "includes/config.php";
		$_SERVER["DOCUMENT_ROOT"] = $document_root;
		$display_type = 'text'; //html, text
	}
	else {
		echo "access denied";
		exit;
	}

//get the list of installed apps from the core and mod directories
		//$xml_cdr_list = glob($v_log_dir."/xml_cdr/archive/*/*/*/*.xml");
		$xml_cdr_list = glob($v_log_dir."/xml_cdr/archive/2011/*/*/*.xml");
		echo "count: ".count($xml_cdr_list)."\n";
		//print_r($xml_cdr_list);
		$x = 0;
		$z = 0;
		//start the transaction
			$db->beginTransaction();
		//loop through the xml cdr records
			foreach ($xml_cdr_list as $xml_cdr) {
				//save each set of records and begin a new transaction
					if ($x > 5000) {
						//save the transaction
							$db->commit();
						//start the transaction
							$db->beginTransaction();
						//reset the count
							$x = 0;
					}
				//get the xml cdr string
					$xml_string = file_get_contents($xml_cdr);
				//parse the xml to get the call detail record info
					try {
						$xml = simplexml_load_string($xml_string);
					}
					catch(Exception $e) {
						echo $e->getMessage();
					}
				//get the values from the xml and set at variables
					$uuid = urldecode($xml->variables->uuid);
					$waitsec = urldecode($xml->variables->waitsec);
	
				//get the count of the rows in v_xml_cdr
					/*
					$sql = "";
					$sql .= "select count(*) as num_rows from v_xml_cdr ";
					$sql .= "where uuid = '$uuid' ";
					$sql .= "and waitsec is null ";
					$prepstatement = $db->prepare($sql);
					if ($prepstatement) {
						$prepstatement->execute();
						$row = $prepstatement->fetch(PDO::FETCH_ASSOC);
						if ($row['num_rows'] > 0) {
							$num_rows = $row['num_rows'];
						}
						else {
							$num_rows = '0';
						}
					}
					unset($prepstatement, $result);
					*/
	
				//update the database
					//if ($num_rows == "0" && strlen($waitsec) > 0) {
					if (strlen($waitsec) > 0) {
						$sql = "";
						$sql .= "update v_xml_cdr ";
						$sql .= "set waitsec = '$waitsec' ";
						$sql .= "where uuid = '$uuid' ";
						echo $sql."\n";
						$db->exec($sql);
						$x++;
					}
				$z++;
			}
		//save the transaction
			$db->commit();
		//echo finished
			echo "completed\n";
