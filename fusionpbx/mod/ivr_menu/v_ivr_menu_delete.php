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
if (permission_exists('ivr_menu_delete')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (count($_GET)>0) {
	$id = check_str($_GET["id"]);
}

if (strlen($id)>0) {

	//delete child data
		$sql = "";
		$sql .= "delete from v_ivr_menu_options ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and ivr_menu_id = '$id' ";
		$db->query($sql);
		unset($sql);

	//delete parent data
		$sql = "";
		$sql .= "delete from v_ivr_menu ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and ivr_menu_id = '$id' ";
		$db->query($sql);
		unset($sql);

	//delete the dialplan entries
		$sql = "";
		$sql .= "select * from v_dialplan_includes ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and opt1name = 'ivr_menu_id' ";
		$sql .= "and opt1value = '".$id."' ";
		$prepstatement2 = $db->prepare($sql);
		$prepstatement2->execute();
		while($row2 = $prepstatement2->fetch()) {
			$dialplan_include_id = $row2['dialplan_include_id'];
			//echo "dialplan_include_id: ".$dialplan_include_id."<br />\n";
			break; //limit to 1 row
		}
		unset ($sql, $prepstatement2);

		//delete the child dialplan information
			$sql = "";
			$sql = "delete from v_dialplan_includes_details ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and dialplan_include_id = '$dialplan_include_id' ";
			//echo "sql: ".$sql."<br />\n";
			$db->query($sql);
			unset($sql);

		//delete the parent dialplan information
			$sql = "";
			$sql .= "delete from v_dialplan_includes ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and opt1name = 'ivr_menu_id' ";
			$sql .= "and opt1value = '".$id."' ";
			//echo "sql: ".$sql."<br />\n";
			$db->query($sql);
			unset ($sql);

	//synchronize the xml config
		sync_package_v_ivr_menu();

	//synchronize the xml config
		sync_package_v_dialplan_includes();
}

require_once "includes/header.php";
echo "<meta http-equiv=\"refresh\" content=\"2;url=v_ivr_menu.php\">\n";
echo "<div align='center'>\n";
echo "Delete Complete\n";
echo "</div>\n";

require_once "includes/footer.php";
return;

?>

