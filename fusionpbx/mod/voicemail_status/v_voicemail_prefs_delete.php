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
require "includes/config.php";
require_once "includes/checkauth.php";
if (ifgroup("admin") || ifgroup("superadmin") || ifgroup("member")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (count($_GET)>0) {
	$id = $_GET["id"];
}

try {
	unset($db);
	//$db = new PDO('sqlite::memory:'); //sqlite 3
	$db = new PDO('sqlite:'.$v_db_dir.'/voicemail_default.db'); //sqlite 3
}
catch (PDOException $error) {
	print "error: " . $error->getMessage() . "<br/>";
	die();
}

if (strlen($id)>0) {
	$sql = "";
	$sql .= "delete from voicemail_prefs ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and username = '$v_id' ";
	//echo $sql;
	$count = $db->exec(check_sql($sql));
	//$sql .= "and extension_id = '$id' ";
	//$prepstatement = $db->prepare(check_sql($sql));
	//$prepstatement->execute();
	unset($sql);
}

require_once "includes/header.php";
echo "<meta http-equiv=\"refresh\" content=\"2;url=v_voicemail.php\">\n";
echo "<div align='center'>\n";
echo "Voicemail Preferences set to default\n";
echo "</div>\n";

require "includes/config.php";
require_once "includes/footer.php";
return;

?>

