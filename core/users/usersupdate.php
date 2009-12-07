<?php
/* $Id$ */
/*
	usersupdate.php
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

if (ifgroup("admin") || ifgroup("superadmin")) {
	//access allowed
}
else {
	echo "access denied";
	return;
}


//get data from the db
	if (strlen($_GET["id"])> 0) {
		$id = $_GET["id"];
	}
	else {
		if (strlen($_SESSION["username"]) > 0) {
			//if (!ifgroup("member")) {
			  $username = $_SESSION["username"];
			//}
		}
	}


//get the username from v_users
	$sql = "";
	$sql .= "select * from v_users ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and id = '$id' ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	while($row = $prepstatement->fetch()) {
		$username = $row["username"];
		break; //limit to 1 row
	}
	unset ($prepstatement);

//required to be a superadmin to update an account that is a member of the superadmin group
	$superadminlist = superadminlist($db);
	if (ifsuperadmin($superadminlist, $username)) {
		if (!ifgroup("superadmin")) { 
			echo "access denied";
			return;
		}
	}


if (count($_POST)>0 && $_POST["persistform"] != "1") {
	$id = $_POST["id"];
	//if (ifgroup("admin") && strlen($_POST["username"])> 0) {
		$username = $_POST["username"];
	//}
	$password = $_POST["password"];
	$confirmpassword = $_POST["confirmpassword"];
	$userfirstname = $_POST["userfirstname"];
	$userlastname = $_POST["userlastname"];
	$usercompanyname = $_POST["usercompanyname"];
	$userphysicaladdress1 = $_POST["userphysicaladdress1"];
	$userphysicaladdress2 = $_POST["userphysicaladdress2"];
	$userphysicalcity = $_POST["userphysicalcity"];
	$userphysicalstateprovince = $_POST["userphysicalstateprovince"];
	$userphysicalcountry = $_POST["userphysicalcountry"];
	$userphysicalpostalcode = $_POST["userphysicalpostalcode"];
	$usermailingaddress1 = $_POST["usermailingaddress1"];
	$usermailingaddress2 = $_POST["usermailingaddress2"];
	$usermailingcity = $_POST["usermailingcity"];
	$usermailingstateprovince = $_POST["usermailingstateprovince"];
	$usermailingcountry = $_POST["usermailingcountry"];
	$usermailingpostalcode = $_POST["usermailingpostalcode"];
	$userbillingaddress1 = $_POST["userbillingaddress1"];
	$userbillingaddress2 = $_POST["userbillingaddress2"];
	$userbillingcity = $_POST["userbillingcity"];
	$userbillingstateprovince = $_POST["userbillingstateprovince"];
	$userbillingcountry = $_POST["userbillingcountry"];
	$userbillingpostalcode = $_POST["userbillingpostalcode"];
	$usershippingaddress1 = $_POST["usershippingaddress1"];
	$usershippingaddress2 = $_POST["usershippingaddress2"];
	$usershippingcity = $_POST["usershippingcity"];
	$usershippingstateprovince = $_POST["usershippingstateprovince"];
	$usershippingcountry = $_POST["usershippingcountry"];
	$usershippingpostalcode = $_POST["usershippingpostalcode"];
	$userurl = $_POST["userurl"];
	$userphone1 = $_POST["userphone1"];
	$userphone1ext = $_POST["userphone1ext"];
	$userphone2 = $_POST["userphone2"];
	$userphone2ext = $_POST["userphone2ext"];
	$userphonemobile = $_POST["userphonemobile"];
	$userphonefax = $_POST["userphonefax"];
	$useremail = $_POST["useremail"];
	$groupmember = $_POST["groupmember"];

	if (strlen($password) == 0) { $msgerror .= "Password cannot be blank.<br>\n"; }
	if ($password != $confirmpassword) { $msgerror .= "Passwords did not match.<br>\n"; }
	if (strlen($userfirstname) == 0) { $msgerror .= "Please provide a first name.<br>\n"; }
	if (strlen($userlastname) == 0) { $msgerror .= "Please provide a last name $userlastname.<br>\n"; }
	//if (strlen($usercompanyname) == 0) { $msgerror .= "Please provide a company name.<br>\n"; }
	//if (strlen($userphysicaladdress1) == 0) { $msgerror .= "Please provide a address.<br>\n"; }
	//if (strlen($userphysicaladdress2) == 0) { $msgerror .= "Please provide a userphysicaladdress2.<br>\n"; }
	//if (strlen($userphysicalcity) == 0) { $msgerror .= "Please provide a city.<br>\n"; }
	//if (strlen($userphysicalstateprovince) == 0) { $msgerror .= "Please provide a state.<br>\n"; }
	//if (strlen($userphysicalcountry) == 0) { $msgerror .= "Please provide a country.<br>\n"; }
	//if (strlen($userphysicalpostalcode) == 0) { $msgerror .= "Please provide a postal code.<br>\n"; }
	//if (strlen($userurl) == 0) { $msgerror .= "Please provide a url.<br>\n"; }
	//if (strlen($userphone1) == 0) { $msgerror .= "Please provide a phone number.<br>\n"; }
	//if (strlen($userphone2) == 0) { $msgerror .= "Please provide a userphone2.<br>\n"; }
	//if (strlen($userphonemobile) == 0) { $msgerror .= "Please provide a mobile number.<br>\n"; }
	//if (strlen($userphoneemergencymobile) == 0) { $msgerror .= "Please provide a emergency mobile.<br>\n"; }
	//if (strlen($userphonefax) == 0) { $msgerror .= "Please provide a fax number.<br>\n"; }
	//if (strlen($useremail) == 0) { $msgerror .= "Please provide an email.<br>\n"; }
	//if (strlen($useremailemergency) == 0) { $msgerror .= "Please provide an emergency email.<br>\n"; }


	if (strlen($msgerror) > 0) {
		require_once "includes/header.php";
		echo "<div align='center'>";
		echo "<table><tr><td>";
		echo $msgerror;
		echo "</td></tr></table>";
		echo "<br />\n";
		require_once "includes/persistform.php";
		echo persistform($_POST);
		echo "</div>";
		require_once "includes/footer.php";
		return;
	}

	//sql update
	$sql  = "update v_users set ";
	if (ifgroup("admin") && strlen($_POST["username"])> 0) {
		$sql .= "username = '$username', ";
	}
	if (strlen($password) > 0) {
		$sql .= "password = '".md5('e3.7d.12'.$password)."', ";
	}
	$sql .= "userfirstname = '$userfirstname', ";
	$sql .= "userlastname = '$userlastname', ";
	$sql .= "usercompanyname = '$usercompanyname', ";
	$sql .= "userphysicaladdress1 = '$userphysicaladdress1', ";
	$sql .= "userphysicaladdress2 = '$userphysicaladdress2', ";
	$sql .= "userphysicalcity = '$userphysicalcity', ";
	$sql .= "userphysicalstateprovince = '$userphysicalstateprovince', ";
	$sql .= "userphysicalcountry = '$userphysicalcountry', ";
	$sql .= "userphysicalpostalcode = '$userphysicalpostalcode', ";
	$sql .= "usermailingaddress1 = '$usermailingaddress1', ";
	$sql .= "usermailingaddress2 = '$usermailingaddress2', ";
	$sql .= "usermailingcity = '$usermailingcity', ";
	$sql .= "usermailingstateprovince = '$usermailingstateprovince', ";
	$sql .= "usermailingcountry = '$usermailingcountry', ";
	$sql .= "usermailingpostalcode = '$usermailingpostalcode', ";
	$sql .= "userbillingaddress1 = '$userbillingaddress1', ";
	$sql .= "userbillingaddress2 = '$userbillingaddress2', ";
	$sql .= "userbillingcity = '$userbillingcity', ";
	$sql .= "userbillingstateprovince = '$userbillingstateprovince', ";
	$sql .= "userbillingcountry = '$userbillingcountry', ";
	$sql .= "userbillingpostalcode = '$userbillingpostalcode', ";
	$sql .= "usershippingaddress1 = '$usershippingaddress1', ";
	$sql .= "usershippingaddress2 = '$usershippingaddress2', ";
	$sql .= "usershippingcity = '$usershippingcity', ";
	$sql .= "usershippingstateprovince = '$usershippingstateprovince', ";
	$sql .= "usershippingcountry = '$usershippingcountry', ";
	$sql .= "usershippingpostalcode = '$usershippingpostalcode', ";
	$sql .= "userurl = '$userurl', ";
	$sql .= "userphone1 = '$userphone1', ";
	$sql .= "userphone1ext = '$userphone1ext', ";
	$sql .= "userphone2 = '$userphone2', ";
	$sql .= "userphone2ext = '$userphone2ext', ";
	$sql .= "userphonemobile = '$userphonemobile', ";
	$sql .= "userphonefax = '$userphonefax', ";
	$sql .= "useremail = '$useremail' ";
	if (strlen($id)> 0) {
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and id = $id ";
	}
	else {
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and username = '$username' ";
	}

	//echo $sql;
	$count = $db->exec($sql);
	//echo "Affected Rows: ".$count;

	//todo: show only if admin
	if (strlen($groupmember) > 0) {

		//groupmemberlist function defined in config.php
		$groupmemberlist = groupmemberlist($db, $username);

		if (ifgroupmember($groupmemberlist, "customer".$groupmember)) {
			//if the group provided from the html form is in the groupmemberlist
			//then the user is already in the group
		}
		else {
			//group is not in the database it needs to be added
			//remove the old group and add the new group

			/*
			if (ifgroup("admin")) {

				  $sql = "delete from v_group_members ";
				  $sql .= "where username = '$username' and groupid = 'customerbronze' ";
				  $sql .= "or username = '$username' and groupid = 'customersilver' ";
				  $sql .= "or username = '$username' and groupid = 'customergold' ";
				  $db->exec($sql);
				  unset($sql);

				  $sql = "insert into v_group_members ";
				  $sql .= "(";
				  $sql .= "groupid, ";
				  $sql .= "username ";
				  $sql .= ")";
				  $sql .= "values ";
				  $sql .= "(";
				  $sql .= "'$groupid', ";
				  $sql .= "'$username' ";
				  $sql .= ")";
				  $db->exec($sql);
				  //$lastinsertid = $db->lastInsertId($id);
				  unset($sql);
			  }
			  */
		}
	} //if (strlen($groupmember) > 0) {


	//edit: make sure the meta redirect url is correct
	require_once "includes/header.php";
	if (ifgroup("admin")) {
		echo "<meta http-equiv=\"refresh\" content=\"2;url=index.php\">\n";
	}
	else {
		echo "<meta http-equiv=\"refresh\" content=\"2;url=index.php\">\n";
	}
	echo "<div align='center'>Update Complete</div>";
	require_once "includes/footer.php";
	return;
}
else {

	$sql = "";
	$sql .= "select * from v_users ";
	if (ifgroup("admin")) {
		//allow admin access
		if (strlen($id)> 0) {
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and id = '$id' ";
		}
		else {
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and username = '$username' ";
		}
	}
	else {
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and username = '$username' ";
	}
	//echo $sql;
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();

	while($row = $prepstatement->fetch()) {
		if (ifgroup("admin")) {
			$username = $row["username"];
		}
		$password = $row["password"];
		$userfirstname = $row["userfirstname"];
		$userlastname = $row["userlastname"];
		$usercompanyname = $row["usercompanyname"];
		$userphysicaladdress1 = $row["userphysicaladdress1"];
		$userphysicaladdress2 = $row["userphysicaladdress2"];
		$userphysicalcity = $row["userphysicalcity"];
		$userphysicalstateprovince = $row["userphysicalstateprovince"];
		$userphysicalcountry = $row["userphysicalcountry"];
		$userphysicalpostalcode = $row["userphysicalpostalcode"];
		$usermailingaddress1 = $row["usermailingaddress1"];
		$usermailingaddress2 = $row["usermailingaddress2"];
		$usermailingcity = $row["usermailingcity"];
		$usermailingstateprovince = $row["usermailingstateprovince"];
		$usermailingcountry = $row["usermailingcountry"];
		$usermailingpostalcode = $row["usermailingpostalcode"];
		$userbillingaddress1 = $row["userbillingaddress1"];
		$userbillingaddress2 = $row["userbillingaddress2"];
		$userbillingcity = $row["userbillingcity"];
		$userbillingstateprovince = $row["userbillingstateprovince"];
		$userbillingcountry = $row["userbillingcountry"];
		$userbillingpostalcode = $row["userbillingpostalcode"];
		$usershippingaddress1 = $row["usershippingaddress1"];
		$usershippingaddress2 = $row["usershippingaddress2"];
		$usershippingcity = $row["usershippingcity"];
		$usershippingstateprovince = $row["usershippingstateprovince"];
		$usershippingcountry = $row["usershippingcountry"];
		$usershippingpostalcode = $row["usershippingpostalcode"];
		$userurl = $row["userurl"];
		$userphone1 = $row["userphone1"];
		$userphone1ext = $row["userphone1ext"];
		$userphone2 = $row["userphone2"];
		$userphone2ext = $row["userphone2ext"];
		$userphonemobile = $row["userphonemobile"];
		$userphonefax = $row["userphonefax"];
		$useremail = $row["useremail"];
		break; //limit to 1 row
	  }

	  //get the groups the user is a member of
	  //groupmemberlist function defined in config.php
	  $groupmemberlist = groupmemberlist($db, $username);
	  //echo "groupmemberlist $groupmemberlist";

}


	require_once "includes/header.php";
	echo "<div align='center'>";
	echo "<table width='90%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";


	$tablewidth ='width="100%"';
	echo "<form method='post' action=''>";

	echo "<b>User Info</b><br>";
	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>";
	echo "		<td width='30%' class='vncellreq'>Username:</td>";
	echo "		<td width='70%' class='vtable'>$username</td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='vncellreq'>Password:</td>";
	echo "		<td class='vtable'><input type='password' autocomplete='off' class='formfld' name='password' value=''></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>Confirm Password:</td>";
	echo "		<td class='vtable'><input type='password' autocomplete='off' class='formfld' name='confirmpassword' value=''></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>First Name:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userfirstname' value='$userfirstname'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Last Name:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userlastname' value='$userlastname'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Company Name:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='usercompanyname' value='$usercompanyname'></td>";
	echo "	</tr>";
	echo "    </table>";
	echo "    </div>";
	echo "<br>";

	echo "<b>Physical Address</b><br>";
	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>";
	echo "		<td class='vncell' width='30%'>Address 1:</td>";
	echo "		<td class='vtable' width='70%'><input type='text' class='formfld' name='userphysicaladdress1' value='$userphysicaladdress1'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Address 2:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphysicaladdress2' value='$userphysicaladdress2'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>City:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphysicalcity' value='$userphysicalcity'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>State/Province:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphysicalstateprovince' value='$userphysicalstateprovince'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Country:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphysicalcountry' value='$userphysicalcountry'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Postal Code:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphysicalpostalcode' value='$userphysicalpostalcode'></td>";
	echo "	</tr>";
	echo "    </table>";
	echo "    </div>";
	echo "<br>";

	/*
	echo "<b>Mailing Address</b><br>";
	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>";
	echo "		<td class='vncell' width='40%'>Address 1:</td>";
	echo "		<td class='vtable' width='60%'><input type='text' class='formfld' name='usermailingaddress1' value='$usermailingaddress1'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Address 2:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='usermailingaddress2' value='$usermailingaddress2'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>City:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='usermailingcity' value='$usermailingcity'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>State/Province:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='usermailingstateprovince' value='$usermailingstateprovince'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Country:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='usermailingcountry' value='$usermailingcountry'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Postal Code:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='usermailingpostalcode' value='$usermailingpostalcode'></td>";
	echo "	</tr>";
	echo "    </table>";
	echo "    </div>";
	echo "<br>";

	echo "<b>Billing Address</b><br>";
	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>";
	echo "		<td class='vncell' width='30%'>Address 1:</td>";
	echo "		<td class='vtable' width='70%'><input type='text' class='formfld' name='userbillingaddress1' value='$userbillingaddress1'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Address 2:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userbillingaddress2' value='$userbillingaddress2'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>City:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userbillingcity' value='$userbillingcity'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>State/Province:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userbillingstateprovince' value='$userbillingstateprovince'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Country:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userbillingcountry' value='$userbillingcountry'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Postal Code:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userbillingpostalcode' value='$userbillingpostalcode'></td>";
	echo "	</tr>";
	echo "    </table>";
	echo "    </div>";
	echo "<br>";

	echo "<b>Shipping Address</b><br>";
	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>";
	echo "		<td class='vncell' width='30%'>Address 1:</td>";
	echo "		<td class='vtable' width='70%'><input type='text' class='formfld' name='usershippingaddress1' value='$usershippingaddress1'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Address 2:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='usershippingaddress2' value='$usershippingaddress2'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>City:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='usershippingcity' value='$usershippingcity'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>State/Province:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='usershippingstateprovince' value='$usershippingstateprovince'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Country:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='usershippingcountry' value='$usershippingcountry'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Postal Code:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='usershippingpostalcode' value='$usershippingpostalcode'></td>";
	echo "	</tr>";
	echo "    </table>";
	echo "    </div>";
	echo "<br>";
	*/

	echo "<b>Additional Info</b><br>";
	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>";
	echo "		<td class='vncell'width='30%'>Website:</td>";
	echo "		<td class='vtable' width='70%'><input type='text' class='formfld' name='userurl' value='$userurl'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Phone 1:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphone1' value='$userphone1'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Phone 1 Ext:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphone1ext' value='$userphone1ext'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Phone 2:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphone2' value='$userphone2'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Phone 2 Ext:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphone2ext' value='$userphone2ext'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Mobile:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphonemobile' value='$userphonemobile'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Fax:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphonefax' value='$userphonefax'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Email:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='useremail' value='$useremail'></td>";
	echo "	</tr>";

	echo "    </table>";
	echo "    </div>";



	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth>";
	echo "	<tr>";
	echo "		<td colspan='2' align='right'>";
	echo "			<input type='hidden' name='id' value='$id'>";
	echo "			<input type='hidden' name='username' value='$username'>";
	echo "			<input type='submit' name='submit' class='btn' value='Save'>";
	echo "		</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";


	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";


  require_once "includes/footer.php";
?>
