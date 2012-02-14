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
if (permission_exists('call_forward_delete')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (count($_GET)>0) {
	$id = $_GET["id"];
	$dialplan_uuid = check_str($_REQUEST["id2"]);
}

if (strlen($id)>0) {

	$sql = "";
	$sql .= "delete from v_dialplan_details ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and dialplan_detail_uuid = '$id' ";
	$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
	$db->query($sql);
	unset($sql);
	
	//synchronize the xml config
	sync_package_v_dialplan();

}

require_once "includes/header.php";
echo "<meta http-equiv=\"refresh\" content=\"2;url=v_call_forward_edit.php?id=".$dialplan_uuid."\">\n";
echo "<div align='center'>\n";
echo "Delete Complete\n";
echo "</div>\n";

require_once "includes/footer.php";
return;

?>