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
if (permission_exists('public_includes_delete')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (count($_GET)>0) {
	$id = $_GET["id"];
}

if (strlen($id)>0) {

	$sql = "";
	$sql .= "select * from v_public ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and public_uuid = '$id' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	foreach ($result as &$row) {
		$extension_name = $row["extension_name"];
		$public_order = $row["public_order"];
		//$enabled = $row["enabled"];
		break; //limit to 1 row
	}
	unset ($prep_statement, $sql);

	$publicincludefilename = $public_order."_".$extension_name.".xml";
	if (file_exists($v_conf_dir."/dialplan/public/".$publicincludefilename)) {
		unlink($v_conf_dir."/dialplan/public/".$publicincludefilename);
	}
	unset($publicincludefilename, $public_order, $extension_name);

	//delete child data
	$sql = "";
	$sql .= "delete from v_public_details ";
	$sql .= "where public_uuid = '$id' ";
	$sql .= "and domain_uuid = '$domain_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	unset($sql);

	//delete parent data
	$sql = "";
	$sql .= "delete from v_public ";
	$sql .= "where public_uuid = '$id' ";
	$sql .= "and domain_uuid = '$domain_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	unset($sql);

	//synchronize the xml config
	sync_package_v_public();
}

//redirect the user
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=v_public.php\">\n";
	echo "<div align='center'>\n";
	echo "Delete Complete\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

?>