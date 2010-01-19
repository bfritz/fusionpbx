<?php
/* $Id$ */
/*
	rsscontent.php
	Copyright (C) 2008, 2009 Mark J Crane
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
require_once "includes/config.php";
require_once "includes/checkauth.php";
require_once "config.php";

if (!ifgroup("admin")) {
	echo "access denied";
	return;
}

//get data from the db
$rssid = $_REQUEST["rssid"];

$sql = "";
$sql .= "select * from v_rss ";
$sql .= "where v_id = '$v_id' ";
$sql .= "and rssid = '$rssid' ";
//echo $sql;
$prepstatement = $db->prepare($sql);
$prepstatement->execute();
$result = $prepstatement->fetchAll();
foreach ($result as &$row) {
	$rsscategory = $row["rsscategory"];
	$rsssubcategory = $row["rsssubcategory"];
	$rsstitle = $row["rsstitle"];
	$rsslink = $row["rsslink"];
	$rssdesc = $row["rssdesc"];
	$rssimg = $row["rssimg"];
	$rssoptional1 = $row["rssoptional1"];
	$rssoptional2 = $row["rssoptional2"];
	$rssoptional3 = $row["rssoptional3"];
	$rssoptional4 = $row["rssoptional4"];
	$rssoptional5 = $row["rssoptional5"];
	$rssadddate = $row["rssadddate"];
	$rssadduser = $row["rssadduser"];
	$rssgroup = $row["rssgroup"];
	$rssorder = $row["rssorder"];
	//$rssdesc = str_replace ("\r\n", "<br>", $rssdesc);

	echo $rssdesc;
	//return;

	break; //limit to 1 row
}

?>
