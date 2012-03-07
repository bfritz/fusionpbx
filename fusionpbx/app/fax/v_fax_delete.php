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
require_once "includes/require.php";
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
		$fax_uuid = check_str($_GET["id"]);
	}

//delete the fax extension
	if (strlen($fax_uuid)>0) {

		//start the atomic transaction
			$count = $db->exec("BEGIN;");

		//delete the fax entry
			$sql = "";
			$sql .= "delete from v_fax ";
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and fax_uuid = '$fax_uuid' ";
			$db->query($sql);
			unset($sql);

		//get the dialplan info
			$sql = "";
			$sql .= "select * from v_fax ";
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and fax_uuid = '$fax_uuid' ";
			$prep_statement_2 = $db->prepare($sql);
			$prep_statement_2->execute();
			while($row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
				$dialplan_uuid = check_str($row2['dialplan_uuid']);
			}

		//get the dialplan info
			/*
			$sql = "";
			$sql .= "select * from v_dialplans ";
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and dialplan_uuid = 'dialplan_uuid' ";
			$prep_statement_2 = $db->prepare($sql);
			$prep_statement_2->execute();
			while($row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
				$dialplan_name = check_str($row2['dialplan_name']);
				$dialplan_order = $row2['dialplan_order'];
				$dialplan_context = $row2['dialplan_context'];
				if (file_exists($_SESSION['switch']['dialplan']['dir']."/".$dialplan_context."/".$dialplan_order."_".$dialplan_name.".xml")){
					unlink($_SESSION['switch']['dialplan']['dir']."/".$dialplan_context."/".$dialplan_order."_".$dialplan_name.".xml");
				}
				break; //limit to 1 row
			}
			unset ($sql, $prep_statement_2);
			*/

		//delete the dialplan entry
			$sql = "";
			$sql .= "delete from v_dialplans ";
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
			//echo $sql."<br>\n";
			$db->query($sql);
			unset($sql);

		//delete the dialplan details
			$sql = "";
			$sql .= "delete from v_dialplan_details ";
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
			//echo $sql."<br>\n";
			$db->query($sql);
			unset($sql);

		//commit the atomic transaction
			$count = $db->exec("COMMIT;");

		//syncrhonize configuration
			save_fax_xml();
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