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
require_once "config.php";
if (permission_exists('content_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}


//get data from the db
$rss_uuid = $_REQUEST["rss_uuid"];

$sql = "";
$sql .= "select * from v_rss ";
$sql .= "where domain_uuid = '$domain_uuid' ";
$sql .= "and rss_uuid = '$rss_uuid' ";
//echo $sql;
$prepstatement = $db->prepare(check_sql($sql));
$prepstatement->execute();
$result = $prepstatement->fetchAll();
foreach ($result as &$row) {
	$rss_category = $row["rss_category"];
	$rss_sub_category = $row["rss_sub_category"];
	$rss_title = $row["rss_title"];
	$rss_link = $row["rss_link"];
	$rss_desc = $row["rss_desc"];
	$rss_img = $row["rss_img"];
	$rss_optional_1 = $row["rss_optional_1"];
	$rss_optional_2 = $row["rss_optional_2"];
	$rss_optional_3 = $row["rss_optional_3"];
	$rss_optional_4 = $row["rss_optional_4"];
	$rss_optional_5 = $row["rss_optional_5"];
	$rss_add_date = $row["rss_add_date"];
	$rss_add_user = $row["rss_add_user"];
	$rss_group = $row["rss_group"];
	$rss_order = $row["rss_order"];
	//$rss_desc = str_replace ("\r\n", "<br>", $rss_desc);

	echo $rss_desc;
	//return;

	break; //limit to 1 row
}

?>
