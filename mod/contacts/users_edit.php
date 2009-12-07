<?php
/* $Id$ */
/*
	users_edit.php
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
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}


//Action add or update
if (isset($_REQUEST["id"])) {
	$action = "update";
	$id = checkstr($_REQUEST["id"]);
}
else {
	$action = "add";
}

//POST to PHP variables
if (count($_POST)>0) {
	$username = checkstr($_POST["username"]);
	$password = checkstr($_POST["password"]);
	$usertype = checkstr($_POST["usertype"]);
	$usercategory = checkstr($_POST["usercategory"]);
	$userfirstname = checkstr($_POST["userfirstname"]);
	$userlastname = checkstr($_POST["userlastname"]);
	$usercompanyname = checkstr($_POST["usercompanyname"]);
	$userphysicaladdress1 = checkstr($_POST["userphysicaladdress1"]);
	$userphysicaladdress2 = checkstr($_POST["userphysicaladdress2"]);
	$userphysicalcity = checkstr($_POST["userphysicalcity"]);
	$userphysicalstateprovince = checkstr($_POST["userphysicalstateprovince"]);
	$userphysicalpostalcode = checkstr($_POST["userphysicalpostalcode"]);
	$userphysicalcountry = checkstr($_POST["userphysicalcountry"]);
	$usermailingaddress1 = checkstr($_POST["usermailingaddress1"]);
	$usermailingaddress2 = checkstr($_POST["usermailingaddress2"]);
	$usermailingcity = checkstr($_POST["usermailingcity"]);
	$usermailingstateprovince = checkstr($_POST["usermailingstateprovince"]);
	$usermailingpostalcode = checkstr($_POST["usermailingpostalcode"]);
	$usermailingcountry = checkstr($_POST["usermailingcountry"]);
	$userbillingaddress1 = checkstr($_POST["userbillingaddress1"]);
	$userbillingaddress2 = checkstr($_POST["userbillingaddress2"]);
	$userbillingcity = checkstr($_POST["userbillingcity"]);
	$userbillingstateprovince = checkstr($_POST["userbillingstateprovince"]);
	$userbillingpostalcode = checkstr($_POST["userbillingpostalcode"]);
	$userbillingcountry = checkstr($_POST["userbillingcountry"]);
	$usershippingaddress1 = checkstr($_POST["usershippingaddress1"]);
	$usershippingaddress2 = checkstr($_POST["usershippingaddress2"]);
	$usershippingcity = checkstr($_POST["usershippingcity"]);
	$usershippingstateprovince = checkstr($_POST["usershippingstateprovince"]);
	$usershippingpostalcode = checkstr($_POST["usershippingpostalcode"]);
	$usershippingcountry = checkstr($_POST["usershippingcountry"]);
	$userphone1 = checkstr($_POST["userphone1"]);
	$userphone1ext = checkstr($_POST["userphone1ext"]);
	$userphone2 = checkstr($_POST["userphone2"]);
	$userphone2ext = checkstr($_POST["userphone2ext"]);
	$userphonemobile = checkstr($_POST["userphonemobile"]);
	$userphonefax = checkstr($_POST["userphonefax"]);
	$userphoneemergencymobile = checkstr($_POST["userphoneemergencymobile"]);
	$useremailemergency = checkstr($_POST["useremailemergency"]);
	$useremail = checkstr($_POST["useremail"]);
	$userurl = checkstr($_POST["userurl"]);
	$usernotes = checkstr($_POST["usernotes"]);
	$useroptional1 = checkstr($_POST["useroptional1"]);
	$useradduser = checkstr($_POST["useradduser"]);
	$useradddate = checkstr($_POST["useradddate"]);
}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	////recommend moving this to the config.php file
	$uploadtempdir = $_ENV["TEMP"]."\\";
	ini_set('upload_tmp_dir', $uploadtempdir);
	////$imagedir = $_ENV["TEMP"]."\\";
	////$filedir = $_ENV["TEMP"]."\\";

	if ($action == "update") {
		$id = checkstr($_POST["id"]);
	}

	//check for all required data
		//if (strlen($username) == 0) { $msg .= "Please provide: Username<br>\n"; }
		//if (strlen($password) == 0) { $msg .= "Please provide: Password<br>\n"; }
		//if (strlen($usertype) == 0) { $msg .= "Please provide: Type<br>\n"; }
		//if (strlen($usercategory) == 0) { $msg .= "Please provide: Category<br>\n"; }
		//if (strlen($userfirstname) == 0) { $msg .= "Please provide: First Name<br>\n"; }
		//if (strlen($userlastname) == 0) { $msg .= "Please provide: Last Name<br>\n"; }
		//if (strlen($usercompanyname) == 0) { $msg .= "Please provide: Organization<br>\n"; }
		//if (strlen($userphysicaladdress1) == 0) { $msg .= "Please provide: Address 1<br>\n"; }
		//if (strlen($userphysicaladdress2) == 0) { $msg .= "Please provide: Address 2<br>\n"; }
		//if (strlen($userphysicalcity) == 0) { $msg .= "Please provide: City<br>\n"; }
		//if (strlen($userphysicalstateprovince) == 0) { $msg .= "Please provide: State/Province<br>\n"; }
		//if (strlen($userphysicalpostalcode) == 0) { $msg .= "Please provide: Postal Code<br>\n"; }
		//if (strlen($userphysicalcountry) == 0) { $msg .= "Please provide: Country<br>\n"; }
		//if (strlen($usermailingaddress1) == 0) { $msg .= "Please provide: Address 1<br>\n"; }
		//if (strlen($usermailingaddress2) == 0) { $msg .= "Please provide: Address 2<br>\n"; }
		//if (strlen($usermailingcity) == 0) { $msg .= "Please provide: City<br>\n"; }
		//if (strlen($usermailingstateprovince) == 0) { $msg .= "Please provide: State/Province<br>\n"; }
		//if (strlen($usermailingpostalcode) == 0) { $msg .= "Please provide: Postal Code<br>\n"; }
		//if (strlen($usermailingcountry) == 0) { $msg .= "Please provide: Country<br>\n"; }
		//if (strlen($userbillingaddress1) == 0) { $msg .= "Please provide: Address 1<br>\n"; }
		//if (strlen($userbillingaddress2) == 0) { $msg .= "Please provide: Address 2<br>\n"; }
		//if (strlen($userbillingcity) == 0) { $msg .= "Please provide: City<br>\n"; }
		//if (strlen($userbillingstateprovince) == 0) { $msg .= "Please provide: State/Province<br>\n"; }
		//if (strlen($userbillingpostalcode) == 0) { $msg .= "Please provide: Postal Code<br>\n"; }
		//if (strlen($userbillingcountry) == 0) { $msg .= "Please provide: Country<br>\n"; }
		//if (strlen($usershippingaddress1) == 0) { $msg .= "Please provide: Address 1<br>\n"; }
		//if (strlen($usershippingaddress2) == 0) { $msg .= "Please provide: Address 2<br>\n"; }
		//if (strlen($usershippingcity) == 0) { $msg .= "Please provide: City<br>\n"; }
		//if (strlen($usershippingstateprovince) == 0) { $msg .= "Please provide: State/Province<br>\n"; }
		//if (strlen($usershippingpostalcode) == 0) { $msg .= "Please provide: Postal Code<br>\n"; }
		//if (strlen($usershippingcountry) == 0) { $msg .= "Please provide: Country<br>\n"; }
		//if (strlen($userphone1) == 0) { $msg .= "Please provide: Phone 1<br>\n"; }
		//if (strlen($userphone1ext) == 0) { $msg .= "Please provide: Ext 1<br>\n"; }
		//if (strlen($userphone2) == 0) { $msg .= "Please provide: Phone 2<br>\n"; }
		//if (strlen($userphone2ext) == 0) { $msg .= "Please provide: Ext 2<br>\n"; }
		//if (strlen($userphonemobile) == 0) { $msg .= "Please provide: Mobile<br>\n"; }
		//if (strlen($userphonefax) == 0) { $msg .= "Please provide: FAX<br>\n"; }
		//if (strlen($userphoneemergencymobile) == 0) { $msg .= "Please provide: Emergency Mobile<br>\n"; }
		//if (strlen($useremailemergency) == 0) { $msg .= "Please provide: Emergency Email<br>\n"; }
		//if (strlen($useremail) == 0) { $msg .= "Please provide: Email<br>\n"; }
		//if (strlen($userurl) == 0) { $msg .= "Please provide: URL<br>\n"; }
		//if (strlen($usernotes) == 0) { $msg .= "Please provide: Notes<br>\n"; }
		//if (strlen($useroptional1) == 0) { $msg .= "Please provide: Optional 1<br>\n"; }
		//if (strlen($useradduser) == 0) { $msg .= "Please provide: Add User<br>\n"; }
		//if (strlen($useradddate) == 0) { $msg .= "Please provide: Add Date<br>\n"; }
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "includes/header.php";
			require_once "includes/persistformvar.php";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg."<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		}

	$tmp = "\n";
	$tmp .= "Username: $username\n";
	$tmp .= "Password: $password\n";
	$tmp .= "Type: $usertype\n";
	$tmp .= "Category: $usercategory\n";
	$tmp .= "First Name: $userfirstname\n";
	$tmp .= "Last Name: $userlastname\n";
	$tmp .= "Organization: $usercompanyname\n";
	$tmp .= "Address 1: $userphysicaladdress1\n";
	$tmp .= "Address 2: $userphysicaladdress2\n";
	$tmp .= "City: $userphysicalcity\n";
	$tmp .= "State/Province: $userphysicalstateprovince\n";
	$tmp .= "Postal Code: $userphysicalpostalcode\n";
	$tmp .= "Country: $userphysicalcountry\n";
	$tmp .= "Address 1: $usermailingaddress1\n";
	$tmp .= "Address 2: $usermailingaddress2\n";
	$tmp .= "City: $usermailingcity\n";
	$tmp .= "State/Province: $usermailingstateprovince\n";
	$tmp .= "Postal Code: $usermailingpostalcode\n";
	$tmp .= "Country: $usermailingcountry\n";
	$tmp .= "Address 1: $userbillingaddress1\n";
	$tmp .= "Address 2: $userbillingaddress2\n";
	$tmp .= "City: $userbillingcity\n";
	$tmp .= "State/Province: $userbillingstateprovince\n";
	$tmp .= "Postal Code: $userbillingpostalcode\n";
	$tmp .= "Country: $userbillingcountry\n";
	$tmp .= "Address 1: $usershippingaddress1\n";
	$tmp .= "Address 2: $usershippingaddress2\n";
	$tmp .= "City: $usershippingcity\n";
	$tmp .= "State/Province: $usershippingstateprovince\n";
	$tmp .= "Postal Code: $usershippingpostalcode\n";
	$tmp .= "Country: $usershippingcountry\n";
	$tmp .= "Phone 1: $userphone1\n";
	$tmp .= "Ext 1: $userphone1ext\n";
	$tmp .= "Phone 2: $userphone2\n";
	$tmp .= "Ext 2: $userphone2ext\n";
	$tmp .= "Mobile: $userphonemobile\n";
	$tmp .= "FAX: $userphonefax\n";
	$tmp .= "Emergency Mobile: $userphoneemergencymobile\n";
	$tmp .= "Emergency Email: $useremailemergency\n";
	$tmp .= "Email: $useremail\n";
	$tmp .= "URL: $userurl\n";
	$tmp .= "Notes: $usernotes\n";
	$tmp .= "Optional 1: $useroptional1\n";
	$tmp .= "Add User: $useradduser\n";
	$tmp .= "Add Date: $useradddate\n";



	//Add or update the database
	if ($_POST["persistformvar"] != "true") {
		if ($action == "add") {
			$sql = "insert into v_users ";
			$sql .= "(";
			//$sql .= "username, ";
			//$sql .= "password, ";
			$sql .= "usertype, ";
			$sql .= "usercategory, ";
			$sql .= "userfirstname, ";
			$sql .= "userlastname, ";
			$sql .= "usercompanyname, ";
			$sql .= "userphysicaladdress1, ";
			$sql .= "userphysicaladdress2, ";
			$sql .= "userphysicalcity, ";
			$sql .= "userphysicalstateprovince, ";
			$sql .= "userphysicalpostalcode, ";
			$sql .= "userphysicalcountry, ";
			$sql .= "usermailingaddress1, ";
			$sql .= "usermailingaddress2, ";
			$sql .= "usermailingcity, ";
			$sql .= "usermailingstateprovince, ";
			$sql .= "usermailingpostalcode, ";
			$sql .= "usermailingcountry, ";
			$sql .= "userbillingaddress1, ";
			$sql .= "userbillingaddress2, ";
			$sql .= "userbillingcity, ";
			$sql .= "userbillingstateprovince, ";
			$sql .= "userbillingpostalcode, ";
			$sql .= "userbillingcountry, ";
			$sql .= "usershippingaddress1, ";
			$sql .= "usershippingaddress2, ";
			$sql .= "usershippingcity, ";
			$sql .= "usershippingstateprovince, ";
			$sql .= "usershippingpostalcode, ";
			$sql .= "usershippingcountry, ";
			$sql .= "userphone1, ";
			$sql .= "userphone1ext, ";
			$sql .= "userphone2, ";
			$sql .= "userphone2ext, ";
			$sql .= "userphonemobile, ";
			$sql .= "userphonefax, ";
			$sql .= "userphoneemergencymobile, ";
			$sql .= "useremailemergency, ";
			$sql .= "useremail, ";
			$sql .= "userurl, ";
			$sql .= "usernotes, ";
			$sql .= "useroptional1, ";
			$sql .= "useradduser, ";
			$sql .= "useradddate ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			//$sql .= "'$username', ";
			//$sql .= "'$password', ";
			$sql .= "'$usertype', ";
			$sql .= "'$usercategory', ";
			$sql .= "'$userfirstname', ";
			$sql .= "'$userlastname', ";
			$sql .= "'$usercompanyname', ";
			$sql .= "'$userphysicaladdress1', ";
			$sql .= "'$userphysicaladdress2', ";
			$sql .= "'$userphysicalcity', ";
			$sql .= "'$userphysicalstateprovince', ";
			$sql .= "'$userphysicalpostalcode', ";
			$sql .= "'$userphysicalcountry', ";
			$sql .= "'$usermailingaddress1', ";
			$sql .= "'$usermailingaddress2', ";
			$sql .= "'$usermailingcity', ";
			$sql .= "'$usermailingstateprovince', ";
			$sql .= "'$usermailingpostalcode', ";
			$sql .= "'$usermailingcountry', ";
			$sql .= "'$userbillingaddress1', ";
			$sql .= "'$userbillingaddress2', ";
			$sql .= "'$userbillingcity', ";
			$sql .= "'$userbillingstateprovince', ";
			$sql .= "'$userbillingpostalcode', ";
			$sql .= "'$userbillingcountry', ";
			$sql .= "'$usershippingaddress1', ";
			$sql .= "'$usershippingaddress2', ";
			$sql .= "'$usershippingcity', ";
			$sql .= "'$usershippingstateprovince', ";
			$sql .= "'$usershippingpostalcode', ";
			$sql .= "'$usershippingcountry', ";
			$sql .= "'$userphone1', ";
			$sql .= "'$userphone1ext', ";
			$sql .= "'$userphone2', ";
			$sql .= "'$userphone2ext', ";
			$sql .= "'$userphonemobile', ";
			$sql .= "'$userphonefax', ";
			$sql .= "'$userphoneemergencymobile', ";
			$sql .= "'$useremailemergency', ";
			$sql .= "'$useremail', ";
			$sql .= "'$userurl', ";
			$sql .= "'$usernotes', ";
			$sql .= "'$useroptional1', ";
			$sql .= "'$useradduser', ";
			$sql .= "'$useradddate' ";
			$sql .= ")";
			$db->exec($sql);
			//$lastinsertid = $db->lastInsertId($id);
			unset($sql);

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=users.php\">\n";
			echo "<div align='center'>\n";
			echo "Add Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "add")

		if ($action == "update") {
			$sql = "update v_users set ";
			//$sql .= "username = '$username', ";
			//if (strlen($password) > 0) {
			//	$sql .= "password = '$password', ";
			//}
			$sql .= "usertype = '$usertype', ";
			$sql .= "usercategory = '$usercategory', ";
			$sql .= "userfirstname = '$userfirstname', ";
			$sql .= "userlastname = '$userlastname', ";
			$sql .= "usercompanyname = '$usercompanyname', ";
			$sql .= "userphysicaladdress1 = '$userphysicaladdress1', ";
			$sql .= "userphysicaladdress2 = '$userphysicaladdress2', ";
			$sql .= "userphysicalcity = '$userphysicalcity', ";
			$sql .= "userphysicalstateprovince = '$userphysicalstateprovince', ";
			$sql .= "userphysicalpostalcode = '$userphysicalpostalcode', ";
			$sql .= "userphysicalcountry = '$userphysicalcountry', ";
			$sql .= "usermailingaddress1 = '$usermailingaddress1', ";
			$sql .= "usermailingaddress2 = '$usermailingaddress2', ";
			$sql .= "usermailingcity = '$usermailingcity', ";
			$sql .= "usermailingstateprovince = '$usermailingstateprovince', ";
			$sql .= "usermailingpostalcode = '$usermailingpostalcode', ";
			$sql .= "usermailingcountry = '$usermailingcountry', ";
			$sql .= "userbillingaddress1 = '$userbillingaddress1', ";
			$sql .= "userbillingaddress2 = '$userbillingaddress2', ";
			$sql .= "userbillingcity = '$userbillingcity', ";
			$sql .= "userbillingstateprovince = '$userbillingstateprovince', ";
			$sql .= "userbillingpostalcode = '$userbillingpostalcode', ";
			$sql .= "userbillingcountry = '$userbillingcountry', ";
			$sql .= "usershippingaddress1 = '$usershippingaddress1', ";
			$sql .= "usershippingaddress2 = '$usershippingaddress2', ";
			$sql .= "usershippingcity = '$usershippingcity', ";
			$sql .= "usershippingstateprovince = '$usershippingstateprovince', ";
			$sql .= "usershippingpostalcode = '$usershippingpostalcode', ";
			$sql .= "usershippingcountry = '$usershippingcountry', ";
			$sql .= "userphone1 = '$userphone1', ";
			$sql .= "userphone1ext = '$userphone1ext', ";
			$sql .= "userphone2 = '$userphone2', ";
			$sql .= "userphone2ext = '$userphone2ext', ";
			$sql .= "userphonemobile = '$userphonemobile', ";
			$sql .= "userphonefax = '$userphonefax', ";
			$sql .= "userphoneemergencymobile = '$userphoneemergencymobile', ";
			$sql .= "useremailemergency = '$useremailemergency', ";
			$sql .= "useremail = '$useremail', ";
			$sql .= "userurl = '$userurl', ";
			$sql .= "usernotes = '$usernotes', ";
			$sql .= "useroptional1 = '$useroptional1' ";
			//$sql .= "useradduser = '$useradduser', ";
			//$sql .= "useradddate = '$useradddate' ";
			$sql .= "where id = '$id'";
			//echo $sql;
			$db->exec($sql);
			unset($sql);

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=users.php\">\n";
			echo "<div align='center'>\n";
			echo "Update Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "update")
	} //if ($_POST["persistformvar"] != "true") { 

	} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

	//Pre-populate the form
	if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
		$id = $_GET["id"];
		$sql = "";
		$sql .= "select * from v_users ";
		$sql .= "where id = '$id' ";
		$prepstatement = $db->prepare($sql);
		$prepstatement->execute();
		while($row = $prepstatement->fetch()) {
			//$username = $row["username"];
			//$password = $row["password"];
			$usertype = $row["usertype"];
			$usercategory = $row["usercategory"];
			$userfirstname = $row["userfirstname"];
			$userlastname = $row["userlastname"];
			$usercompanyname = $row["usercompanyname"];
			$userphysicaladdress1 = $row["userphysicaladdress1"];
			$userphysicaladdress2 = $row["userphysicaladdress2"];
			$userphysicalcity = $row["userphysicalcity"];
			$userphysicalstateprovince = $row["userphysicalstateprovince"];
			$userphysicalpostalcode = $row["userphysicalpostalcode"];
			$userphysicalcountry = $row["userphysicalcountry"];
			$usermailingaddress1 = $row["usermailingaddress1"];
			$usermailingaddress2 = $row["usermailingaddress2"];
			$usermailingcity = $row["usermailingcity"];
			$usermailingstateprovince = $row["usermailingstateprovince"];
			$usermailingpostalcode = $row["usermailingpostalcode"];
			$usermailingcountry = $row["usermailingcountry"];
			$userbillingaddress1 = $row["userbillingaddress1"];
			$userbillingaddress2 = $row["userbillingaddress2"];
			$userbillingcity = $row["userbillingcity"];
			$userbillingstateprovince = $row["userbillingstateprovince"];
			$userbillingpostalcode = $row["userbillingpostalcode"];
			$userbillingcountry = $row["userbillingcountry"];
			$usershippingaddress1 = $row["usershippingaddress1"];
			$usershippingaddress2 = $row["usershippingaddress2"];
			$usershippingcity = $row["usershippingcity"];
			$usershippingstateprovince = $row["usershippingstateprovince"];
			$usershippingpostalcode = $row["usershippingpostalcode"];
			$usershippingcountry = $row["usershippingcountry"];
			$userphone1 = $row["userphone1"];
			$userphone1ext = $row["userphone1ext"];
			$userphone2 = $row["userphone2"];
			$userphone2ext = $row["userphone2ext"];
			$userphonemobile = $row["userphonemobile"];
			$userphonefax = $row["userphonefax"];
			$userphoneemergencymobile = $row["userphoneemergencymobile"];
			$useremailemergency = $row["useremailemergency"];
			$useremail = $row["useremail"];
			$userurl = $row["userurl"];
			$usernotes = $row["usernotes"];
			$useroptional1 = $row["useroptional1"];
			//$useradduser = $row["useradduser"];
			//$useradddate = $row["useradddate"];
			break; //limit to 1 row
		}
		unset ($prepstatement);
	}


	require_once "includes/header.php";

	echo "<script language='javascript' src='/includes/calendar_popcalendar.js'></script>\n";
	echo "<script language='javascript' src='/includes/calendar_lw_layers.js'></script>\n";
	echo "<script language='javascript' src='/includes/calendar_lw_menu.js'></script>\n";

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing=''>\n";

	echo "<tr class=''>\n";
	echo "	<td align=\"left\">\n";
	echo "	  <br>";


	$tablewidth = "width='100%'";


	echo "<form method='post' name='frm' action=''>\n";


	echo "<table width='100%'  border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	if ($action == "add") {
		echo "<td width='30%' nowrap><b>Contact Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td width='30%' nowrap><b>Contact Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='users.php'\" value='Back'></td>\n";
	echo "</tr>\n";
	echo "<tr><td colspan='2'>\n";
	if ($action == "add") {
		echo "Add the contact information to the fields below.</td>\n";
	}
	if ($action == "update") {
		echo "Edit the contact information using the fields below.\n";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<br />\n";

echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
echo "<tr>\n";
echo "<td valign='top'>\n";

	echo "<b>User Info</b><br>";
	echo "<div align='center' class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";

	//echo "<tr>\n";
	//echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
	//echo "	Username:\n";
	//echo "</td>\n";
	//echo "<td width='70%' class='vtable' align='left'>\n";
	//echo "	<input style='width: 80%;' class='formfld' type='text' name='username' autocomplete='off' maxlength='255' value=\"$username\">\n";
	//echo "<br />\n";
	//echo "\n";
	//echo "</td>\n";
	//echo "</tr>\n";

	//echo "<tr>\n";
	//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	//echo "	Password:\n";
	//echo "</td>\n";
	//echo "<td class='vtable' align='left'>\n";
	//echo "	<input style='width: 80%;' class='formfld' type='password' name='password' autocomplete='off' maxlength='255' value=\"$password\">\n";
	//echo "<br />\n";
	//echo "\n";
	//echo "</td>\n";
	//echo "</tr>\n";

	echo "<tr>\n";
	echo "<td  width='30%' class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Type:\n";
	echo "</td>\n";
	echo "<td width='70%' class='vtable' align='left'>\n";
	echo "	<select style='width: 80%;' class='formfld' name='usertype'>\n";
	echo "	<option value=''></option>\n";
	if ($usertype == "Individual") {
		echo "	<option value='Individual' selected>Individual</option>\n";
	}
	else {
		echo "	<option value='Individual'>Individual</option>\n";
	}
	if ($usertype == "Organization") {
		echo "	<option value='Organization' selected>Organization</option>\n";
	}
	else {
		echo "	<option value='Organization'>Organization</option>\n";
	}
	echo "	</select>\n";

	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Category:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='usercategory' maxlength='255' value=\"$usercategory\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	First Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userfirstname' maxlength='255' value=\"$userfirstname\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Last Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userlastname' maxlength='255' value=\"$userlastname\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Organization:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='usercompanyname' maxlength='255' value=\"$usercompanyname\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>\n";


	echo "<b>Contact Information</b><br>";
	echo "<div align='center' class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "<tr>\n";
	echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Phone 1:\n";
	echo "</td>\n";
	echo "<td width='70%' class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userphone1' maxlength='255' value=\"$userphone1\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Ext 1:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userphone1ext' maxlength='255' value=\"$userphone1ext\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Phone 2:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userphone2' maxlength='255' value=\"$userphone2\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Ext 2:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userphone2ext' maxlength='255' value=\"$userphone2ext\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Mobile:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userphonemobile' maxlength='255' value=\"$userphonemobile\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	FAX:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userphonefax' maxlength='255' value=\"$userphonefax\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Emergency Mobile:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userphoneemergencymobile' maxlength='255' value=\"$userphoneemergencymobile\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Emergency Email:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='useremailemergency' maxlength='255' value=\"$useremailemergency\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>\n";

	echo "<b>Additional Information</b><br>";
	echo "<div align='center' class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "<tr>\n";
	echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Email:\n";
	echo "</td>\n";
	echo "<td width='70%' class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='useremail' maxlength='255' value=\"$useremail\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	URL:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userurl' maxlength='255' value=\"$userurl\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Notes:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<textarea style='width: 80%;' class='formfld' type='text' name='usernotes' rows='5'>$usernotes</textarea>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	//echo "<tr>\n";
	//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	//echo "	Optional 1:\n";
	//echo "</td>\n";
	//echo "<td class='vtable' align='left'>\n";
	//echo "	<input style='width: 80%;' class='formfld' type='text' name='useroptional1' maxlength='255' value=\"$useroptional1\">\n";
	//echo "<br />\n";
	//echo "\n";
	//echo "</td>\n";
	//echo "</tr>\n";

	//echo "<tr>\n";
	//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	//echo "	Optional 1:\n";
	//echo "</td>\n";
	//echo "<td class='vtable' align='left'>\n";
	//echo "	<input style='width: 80%;' class='formfld' type='text' name='useroptional2' maxlength='255' value=\"$useroptional2\">\n";
	//echo "<br />\n";
	//echo "\n";
	//echo "</td>\n";
	//echo "</tr>\n";

	echo "</table>\n";
	echo "</div>\n";

echo "</td>\n";
echo "<td valign='top'>\n";

	echo "<b>Physical Address</b><br>";
	echo "<div align='center' class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "<tr>\n";
	echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Address 1:\n";
	echo "</td>\n";
	echo "<td width='70%' class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userphysicaladdress1' maxlength='255' value=\"$userphysicaladdress1\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Address 2:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userphysicaladdress2' maxlength='255' value=\"$userphysicaladdress2\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	City:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userphysicalcity' maxlength='255' value=\"$userphysicalcity\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	State/Province:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userphysicalstateprovince' maxlength='255' value=\"$userphysicalstateprovince\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Postal Code:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userphysicalpostalcode' maxlength='255' value=\"$userphysicalpostalcode\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Country:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userphysicalcountry' maxlength='255' value=\"$userphysicalcountry\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>\n";

	echo "<b>Postal Address</b><br>";
	echo "<div align='center' class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "<tr>\n";
	echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Address 1:\n";
	echo "</td>\n";
	echo "<td width='70%' class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='usermailingaddress1' maxlength='255' value=\"$usermailingaddress1\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Address 2:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='usermailingaddress2' maxlength='255' value=\"$usermailingaddress2\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	City:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='usermailingcity' maxlength='255' value=\"$usermailingcity\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	State/Province:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='usermailingstateprovince' maxlength='255' value=\"$usermailingstateprovince\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Postal Code:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='usermailingpostalcode' maxlength='255' value=\"$usermailingpostalcode\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Country:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='usermailingcountry' maxlength='255' value=\"$usermailingcountry\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>\n";

	echo "<b>Billing Address</b><br>";
	echo "<div align='center' class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "<tr>\n";
	echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Address 1:\n";
	echo "</td>\n";
	echo "<td width='70%' class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userbillingaddress1' maxlength='255' value=\"$userbillingaddress1\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Address 2:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userbillingaddress2' maxlength='255' value=\"$userbillingaddress2\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	City:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userbillingcity' maxlength='255' value=\"$userbillingcity\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	State/Province:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userbillingstateprovince' maxlength='255' value=\"$userbillingstateprovince\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Postal Code:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userbillingpostalcode' maxlength='255' value=\"$userbillingpostalcode\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Country:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='userbillingcountry' maxlength='255' value=\"$userbillingcountry\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>\n";

	echo "<b>Shipping Address</b><br>";
	echo "<div align='center' class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "<tr>\n";
	echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Address 1:\n";
	echo "</td>\n";
	echo "<td width='70%' class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='usershippingaddress1' maxlength='255' value=\"$usershippingaddress1\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Address 2:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='usershippingaddress2' maxlength='255' value=\"$usershippingaddress2\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	City:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='usershippingcity' maxlength='255' value=\"$usershippingcity\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	State/Province:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='usershippingstateprovince' maxlength='255' value=\"$usershippingstateprovince\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Postal Code:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='usershippingpostalcode' maxlength='255' value=\"$usershippingpostalcode\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Country:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input style='width: 80%;' class='formfld' type='text' name='usershippingcountry' maxlength='255' value=\"$usershippingcountry\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

	//echo "<tr>\n";
	//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	//echo "	Add User:\n";
	//echo "</td>\n";
	//echo "<td class='vtable' align='left'>\n";
	//echo "	<input style='width: 80%;' class='formfld' type='text' name='useradduser' maxlength='255' value=\"$useradduser\">\n";
	//echo "<br />\n";
	//echo "\n";
	//echo "</td>\n";
	//echo "</tr>\n";

	//echo "<tr>\n";
	//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	//echo "	Add Date:\n";
	//echo "</td>\n";
	//echo "<td class='vtable' align='left'>\n";
	//echo "	<input style='width: 80%;' class='formfld' type='text' name='useradddate' maxlength='255' value=\"$useradddate\">\n";
	//echo "<br />\n";
	//echo "\n";
	//echo "</td>\n";
	//echo "</tr>\n";

	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='id' value='$id'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";


	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

	echo "<br />\n";
	echo "<br />\n";

require_once "includes/footer.php";
?>
