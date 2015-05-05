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
	Portions created by the Initial Developer are Copyright (C) 2015
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
if (permission_exists('fax_file_delete')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//add multi-lingual support
	require_once "app_languages.php";
	foreach($text as $key => $value) {
		$text[$key] = $value[$_SESSION['domain']['language']['code']];
	}

//get the id
	if (isset($_REQUEST["id"])) {
		$id = check_str($_REQUEST["id"]);
	}

//validate the id
	if (strlen($id) > 0) {
		//get the fax file data
			$sql = "select * from v_fax_files ";
			$sql .= "where fax_file_uuid = '$id' ";
			//echo $sql."\n";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->execute();
			$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
			foreach ($result as &$row) {
				$domain_uuid = $row["domain_uuid"];
				$fax_uuid = $row["fax_uuid"];
				$fax_mode = $row["fax_mode"];
				$fax_file_path = $row["fax_file_path"];
				$fax_file_type = $row["fax_file_type"];
			}
			unset($prep_statement);

		//get the fax file data
			$sql = "select * from v_fax_files ";
			$sql .= "where fax_uuid = '$fax_uuid' ";
			$sql .= "and domain_uuid = '$domain_uuid' ";
			//echo $sql."\n";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->execute();
			$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
			foreach ($result as &$row) {
				$fax_extension = $row["fax_extension"];
			}
			unset($prep_statement);

		//delete fax_file
			$sql = "delete from v_fax_files ";
			$sql .= "where fax_file_uuid = '$id' ";
			$sql .= "and domain_uuid = '$domain_uuid' ";
			//echo $sql."\n";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->execute();
			unset($prep_statement);

		//set the type
			if ($fax_mode == 'rx') {
				$type = 'inbox';
			}
			if ($fax_mode == 'tx') {
				$type = 'sent';
			}

		//set the fax directory
			$fax_dir = $_SESSION['switch']['storage']['dir'].'/fax'.((count($_SESSION["domains"]) > 1) ? '/'.$_SESSION['domain_name'] : null);
			$file = basename($row['fax_file_path']);
			$file_ext = substr($file, -3);
			$dir_fax = $fax_dir.'/'.$fax_extension.'/'.$type;
			if (strtolower(substr($file, -3)) == "tif" || strtolower(substr($file, -3)) == "pdf") {
				$file_name = substr($file, 0, (strlen($file) -4));
			}

		//if the file does not exist then remove temp/ out of the path
			if (!file_exists($fax_file_path)) {
				$file = str_replace("temp/", $type."/", $file);
			}

		//delete the files
			unlink($dir_fax.'/'.$file_name.'.tif');
			unlink($dir_fax.'/'.$file_name.'.pdf');
	}

//redirect the user
	$_SESSION['message'] = $text['message-delete'];
	header('Location: fax_files.php?id='.$fax_uuid.'&box='.$type);

?>