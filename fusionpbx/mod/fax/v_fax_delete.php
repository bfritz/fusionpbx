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
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('fax_extension_delete')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//get the http get value and set it as a php variable
	if (count($_GET)>0) {
		$fax_id = check_str($_GET["id"]);
	}

//delete the fax extension
	if (strlen($fax_id)>0) {

		//delete the fax entry
			$sql = "";
			$sql .= "delete from v_fax ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and fax_id = '$fax_id' ";
			//echo $sql."<br>\n";
			$db->query($sql);
			unset($sql);

		//get the dialplan info
			$sql = "";
			$sql .= "select * from v_dialplan_includes ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and opt1name = 'faxid' ";
			$sql .= "and opt1value = '".$fax_id."' ";
			//echo $sql."<br>\n";
			$prepstatement2 = $db->prepare($sql);
			$prepstatement2->execute();
			while($row2 = $prepstatement2->fetch(PDO::FETCH_ASSOC)) {
				$dialplan_include_id = $row2['dialplan_include_id'];
				$extensionname = check_str($row2['extensionname']);
				$order = $row2['order'];
				$context = $row2['context'];
				$enabled = $row2['enabled'];
				$descr = check_str($row2['descr']);
				$opt1name = $row2['opt1name'];
				$opt1value = $row2['opt1value'];
				$id = $i;
				if (file_exists($v_dialplan_default_dir."/".$order."_".$extensionname.".xml")){
					unlink($v_dialplan_default_dir."/".$order."_".$extensionname.".xml");
				}
				break; //limit to 1 row
			}
			unset ($sql, $prepstatement2);

		//delete the dialplan entry
			$sql = "";
			$sql .= "delete from v_dialplan_includes ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and dialplan_include_id = '$dialplan_include_id' ";
			//echo $sql."<br>\n";
			$db->query($sql);
			unset($sql);

		//delete the dialplan details
			$sql = "";
			$sql .= "delete from v_dialplan_includes_details ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and dialplan_include_id = '$dialplan_include_id' ";
			//echo $sql."<br>\n";
			$db->query($sql);
			unset($sql);

		//syncrhonize configuration
			sync_package_v_fax();
	}

//redirect the user
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=v_fax.php\">\n";
	echo "<div align='center'>\n";
	echo "Delete Complete\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

?>
