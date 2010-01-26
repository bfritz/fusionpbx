<?php
/* $Id$ */
/*
	v_voicemail_prefs_delete.php
	Copyright (C) 2008 Mark J Crane
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
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

