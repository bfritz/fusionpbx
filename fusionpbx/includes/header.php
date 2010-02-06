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

ob_end_clean();
ob_start();

//$header = ''; //disable alternate header
//$strheader = str_replace ("<!--{menu}-->", $_SESSION["menu"], $strheader);

$strheadertop ='';
if (isset($_SERVER['HTTP_USER_AGENT']) && 
(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
	//return true;
	$strheadertop .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
else {
	//return false;
}
//$strheadertop .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\">\n";


?>
