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
require_once "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('virtual_tables_delete')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//get the http values
	if (count($_GET)>0) {
		$id = check_str($_GET["id"]);
		$virtual_table_uuid = check_str($_GET["virtual_table_uuid"]);
		$virtual_table_field_uuid = check_str($_GET["virtual_table_field_uuid"]);
	}

//delete the data
	if (strlen($id)>0) {
		$sql = "";
		$sql .= "delete from v_virtual_table_data_types_name_value ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and virtual_table_data_types_name_value_id = '$id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		unset($sql);
	}

//redirect the user
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=v_virtual_table_fields_edit.php?virtual_table_uuid=$virtual_table_uuid&id=$virtual_table_field_uuid\">\n";
	echo "<div align='center'>\n";
	echo "Delete Complete\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

?>