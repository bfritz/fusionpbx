<?php
/* $Id$ */
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
	Ken Rice     <krice@tollfreegateway.com>
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists('xmpp_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";
require_once "includes/paging.php";

if ($_SESSION['db_tables']['v_xmpp'] != 'valid') {
	if ($db_type == "pgsql") {
		$sql = "select count(*) from pg_tables where schemaname='public' and table_name = 'v_xmpp'";
	} elseif ($db_type == "mysql") {
		$sql = "select count(*) as count from information_schema.tables where TABLE_SCHEMA='" . $db_name . "' and TABLE_NAME='roomlist';";
	} elseif ($db_type == "sqlite") {
		$sql = "select count(*) as count from sqlite_master WHERE type IN ('table','view') AND name = 'registrations';";
	}

	$row = $db->query($sql)->fetch();

	if ($row['count'] < 1) {
		include "db_create.php";
		$db->exec(sql_tables($db_type));
		// $create = $db->query(sql_tables($db_type))->fetch();
		$_SESSION['db_tables']['v_xmpp'] = 'valid';
	}
}

//get a list of assigned extensions for this user
$sql = "";
$sql .= "select * from v_xmpp ";
$sql .= "where domain_uuid = '$domain_uuid' ";
$prep_statement = $db->prepare(check_sql($sql));
$prep_statement->execute();
$x = 0;
$result = $prep_statement->fetchAll();
foreach ($result as &$row) {
	$profiles_array[$x] = $row;
	$x++;
}
unset ($prep_statement);

//include the view
include "profile_list.php";

//include the footer
require_once "includes/footer.php";

?>
