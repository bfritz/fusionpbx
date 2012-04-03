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
if (permission_exists('xml_cdr_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//additional includes
	require_once "xml_cdr_inc.php";

//set the http headers
	header('Content-type: application/octet-binary');
	header('Content-Disposition: attachment; filename=cdr.csv');

//set the csv headers
	$z = 0;
	foreach($result[0] as $key => $val) {
		if ($key != "xml_cdr") {
			if ($z == 0) {
				echo '"'.$key.'"';
			}
			else {
				echo ',"'.$key.'"';
			}
		}
		$z++;
	}
	echo "\n";

//show the csv data
	$x=0;
	while(true) {
		$z = 0;
		foreach($result[0] as $key => $val) {
			if ($key != "xml_cdr") {
				if ($z == 0) {
					echo '"'.$result[$x][$key].'"';
				}
				else {
					echo ',"'.$result[$x][$key].'"';
				}
			}
			$z++;
		}
		echo "\n";
		++$x;
		if ($x > ($result_count-1)) {
			break;
		}
		//$row++;
	}

?>