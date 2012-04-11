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

if (strlen($_SESSION['switch']['scripts']['dir']) > 0) {

	//if the resource scripts resource directory does not exist then create it
		if (!is_dir($_SESSION['switch']['scripts']['dir']."/resources")) { mkdir($_SESSION['switch']['scripts']['dir']."/resources",0755,true); }

	//config.lua
		$fout = fopen($_SESSION['switch']['scripts']['dir']."/resources/config.lua","w");
		$tmp = "\n";
		$tmp .= "--switch directories\n";
		if (strlen($_SESSION['switch']['sounds']['dir']) > 0) {
			$tmp .= "	sounds_dir = \"".$_SESSION['switch']['sounds']['dir']."\";\n";
		}
		if (strlen($_SESSION['switch']['recordings']['dir']) > 0) {
			$tmp .= "	recordings_dir = \"".$_SESSION['switch']['recordings']['dir']."\";\n";
		}
		$tmp .= "\n";
		$tmp .= "--database connection info\n\n";
		if (strlen($db_type) > 0) {	
			$tmp .= "	db_type = \"".$db_type."\";\n";
		}
		if (strlen($db_name) > 0) {	
			$tmp .= "	db_name = \"".$db_name."\";\n";
		}
		if (strlen($db_path) > 0) {	
			$tmp .= "	db_path = \"".$db_path."\";\n";
		}
		echo "\n";
		$tmp .= "//additional info\n";
		$tmp .= "	tmp_dir = \"".$tmp_dir."\";\n";
		fwrite($fout, $tmp);
		unset($tmp);
		fclose($fout);

	//config.js
		$fout = fopen($_SESSION['switch']['scripts']['dir']."/resources/config.js","w");
		$tmp = "\n";
		$tmp .= "//switch directories\n";
		$tmp .= "	var admin_pin = \"".$row["admin_pin"]."\";\n";
		$tmp .= "	var sounds_dir = \"".$_SESSION['switch']['sounds']['dir']."\";\n";
		$tmp .= "	var recordings_dir = \"".$_SESSION['switch']['recordings']['dir']."\";\n";
		$tmp .= "\n";
		$tmp = "//database connection info\n";
		if (strlen($db_type) > 0) {	
			$tmp .= "var db_type = \"".$db_type."\";\n";
		}
		if (strlen($db_name) > 0) {	
			$tmp .= "var db_name = \"".$db_name."\";\n";
		}
		if (strlen($db_path) > 0) {	
			$tmp .= "var db_path = \"".$db_path."\";\n";
		}
		$tmp .= "\n";
		$tmp .= "//additional info\n";
		$tmp .= "	var tmp_dir = \"".$tmp_dir."\";\n";
		fwrite($fout, $tmp);
		unset($tmp);
		fclose($fout);
}
?>