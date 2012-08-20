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
if (permission_exists('extension_delete')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (count($_GET)>0) {
	$id = $_GET["id"];
}

//delete the extension
	if (strlen($id)>0) {
		$sql = "update v_extensions set ";
		$sql .= "unique_id = null, ";
		$sql .= "dial_user = null, ";
		$sql .= "dial_domain = null, ";
		$sql .= "dial_string = null ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and extension_uuid = '$id' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		unset($prep_statement, $sql);
	}

//redirect the user
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=index.php\">\n";
	echo "<br />\n";
	echo "<div align='center'>\n";
	echo "	<table width='40%'>\n";
	echo "		<tr>\n";
	echo "			<th align='left'>Message</th>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td class='row_style1'><strong>Delete Complete</strong></td>\n";
	echo "		</tr>\n";
	echo "	</table>\n";
	echo "	<br />\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

?>