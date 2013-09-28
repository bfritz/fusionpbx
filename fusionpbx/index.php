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
	Mark J. Crane <markjcrane@fusionpbx.com>
*/
include "root.php";

//if config.php file does not exist then redirect to the install page
	if (file_exists($_SERVER['DOCUMENT_ROOT'].PROJECT_PATH."/resources/config.php")) {
		//do nothing
	} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].PROJECT_PATH."/resources/config.php")) {
		//original directory
	} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].PROJECT_PATH."/includes/config.php")) {
		//move config.php from the includes to resources directory.
		rename($_SERVER['DOCUMENT_ROOT'].PROJECT_PATH."/includes/config.php", $_SERVER['DOCUMENT_ROOT'].PROJECT_PATH."/resources/config.php");
	} elseif (file_exists("/etc/fusionpbx/config.php")){
		//linux
	} elseif (file_exists("/usr/local/etc/fusionpbx/config.php")){
		//bsd
	} else {
		header("Location: ".PROJECT_PATH."/resources/install.php");
		exit;
	}

//adds multiple includes
	require_once "resources/require.php";

//show the index page
	if (file_exists($_SERVER['DOCUMENT_ROOT'].PROJECT_PATH."/themes/".$_SESSION['domain']['template']['name']."/index.php")){
		require_once "themes/".$_SESSION['domain']['template']['name']."/index.php";
	}
	else {
		require_once "resources/require.php";
		require_once "resources/header.php";
		echo "<br /><br />\n";
		require_once "resources/footer.php";
	}
?>