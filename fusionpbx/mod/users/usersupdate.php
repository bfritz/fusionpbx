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
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
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
	$password = check_str($_POST["password"]);
	$confirmpassword = check_str($_POST["confirmpassword"]);
	$userfirstname = check_str($_POST["userfirstname"]);
	$userlastname = check_str($_POST["userlastname"]);
	$usercompanyname = check_str($_POST["usercompanyname"]);
	$userphysicaladdress1 = check_str($_POST["userphysicaladdress1"]);
	$userphysicaladdress2 = check_str($_POST["userphysicaladdress2"]);
	$userphysicalcity = check_str($_POST["userphysicalcity"]);
	$userphysicalstateprovince = check_str($_POST["userphysicalstateprovince"]);
	$userphysicalcountry = check_str($_POST["userphysicalcountry"]);
	$userphysicalpostalcode = check_str($_POST["userphysicalpostalcode"]);
	$usermailingaddress1 = check_str($_POST["usermailingaddress1"]);
	$usermailingaddress2 = check_str($_POST["usermailingaddress2"]);
	$usermailingcity = check_str($_POST["usermailingcity"]);
	$usermailingstateprovince = check_str($_POST["usermailingstateprovince"]);
	$usermailingcountry = check_str($_POST["usermailingcountry"]);
	$usermailingpostalcode = check_str($_POST["usermailingpostalcode"]);
	$userbillingaddress1 = check_str($_POST["userbillingaddress1"]);
	$userbillingaddress2 = check_str($_POST["userbillingaddress2"]);
	$userbillingcity = check_str($_POST["userbillingcity"]);
	$userbillingstateprovince = check_str($_POST["userbillingstateprovince"]);
	$userbillingcountry = check_str($_POST["userbillingcountry"]);
	$userbillingpostalcode = check_str($_POST["userbillingpostalcode"]);
	$usershippingaddress1 = check_str($_POST["usershippingaddress1"]);
	$usershippingaddress2 = check_str($_POST["usershippingaddress2"]);
	$usershippingcity = check_str($_POST["usershippingcity"]);
	$usershippingstateprovince = check_str($_POST["usershippingstateprovince"]);
	$usershippingcountry = check_str($_POST["usershippingcountry"]);
	$usershippingpostalcode = check_str($_POST["usershippingpostalcode"]);
	$userurl = check_str($_POST["userurl"]);
	$userphone1 = check_str($_POST["userphone1"]);
	$userphone1ext = check_str($_POST["userphone1ext"]);
	$userphone2 = check_str($_POST["userphone2"]);
	$userphone2ext = check_str($_POST["userphone2ext"]);
	$userphonemobile = check_str($_POST["userphonemobile"]);
	$userphonefax = check_str($_POST["userphonefax"]);
	$user_template_name = check_str($_POST["user_template_name"]);
	$useremail = check_str($_POST["useremail"]);
	$groupmember = check_str($_POST["groupmember"]);

	//if (strlen($password) == 0) { $msgerror .= "Password cannot be blank.<br>\n"; }
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
	if (strlen($password) > 0 && $confirmpassword == $password) {
		$sql .= "password = '".md5($salt.$password)."', ";
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
	$sql .= "user_template_name = '$user_template_name', ";
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
	$count = $db->exec(check_sql($sql));
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
				  $db->exec(check_sql($sql));
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
				  $db->exec(check_sql($sql));
				  unset($sql);
			  }
			  */
		}
	} //if (strlen($groupmember) > 0) {


	//edit: make sure the meta redirect url is correct
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=".PROJECT_PATH."/\">\n";
	echo "<div align='center'>Update Complete</div>";
	require_once "includes/footer.php";
	return;
}
else {

      $sql = "";
      $sql .= "select * from v_users ";
      $sql .= "where v_id = '$v_id' ";
      $sql .= "and username = '$username' ";
      //if (ifgroup("admin")) {
      //    //allow admin access
      //    if (strlen($id)> 0) {
      //        $sql .= "where id = '$id' ";
      //    }
      //    else {
      //        $sql .= "where username = '$username' ";
      //    }
      //}
      //else {
      //      $sql .= "where username = '$username' ";
      //}
      //echo $sql;
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
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
		$user_template_name = $row["user_template_name"];
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

	echo "<br>";
	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "<tr>\n";
	echo "	<th class='th' colspan='2' align='left'>User Info</th>\n";
	echo "</tr>\n";
	echo "	<tr>";
	echo "		<td width='30%' class='vncellreq'>Username:</td>";
	echo "		<td width='70%' class='vtable'>$username</td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='vncell'>Password:</td>";
	echo "		<td class='vtable'><input type='password' autocomplete='off' class='formfld' name='password' value=\"\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Confirm Password:</td>";
	echo "		<td class='vtable'><input type='password' autocomplete='off' class='formfld' name='confirmpassword' value=\"\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>First Name:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userfirstname' value=\"$userfirstname\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Last Name:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userlastname' value=\"$userlastname\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Company Name:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='usercompanyname' value=\"$usercompanyname\"></td>";
	echo "	</tr>";
	echo "    </table>";
	echo "    </div>";
	echo "<br>";

	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "<tr>\n";
	echo "	<th class='th' colspan='2' align='left'>Physical Address</th>\n";
	echo "</tr>\n";
	echo "	<tr>";
	echo "		<td class='vncell' width='30%'>Address 1:</td>";
	echo "		<td class='vtable' width='70%'><input type='text' class='formfld' name='userphysicaladdress1' value=\"$userphysicaladdress1\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Address 2:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphysicaladdress2' value=\"$userphysicaladdress2\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>City:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphysicalcity' value=\"$userphysicalcity\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>State/Province:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphysicalstateprovince' value=\"$userphysicalstateprovince\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Country:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphysicalcountry' value=\"$userphysicalcountry\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Postal Code:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphysicalpostalcode' value=\"$userphysicalpostalcode\"></td>";
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

	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>\n";
	echo "	<th class='th' colspan='2' align='left'>Additional Info</th>\n";
	echo "	</tr>\n";
	echo "	<tr>";
	echo "		<td class='vncell'width='30%'>Website:</td>";
	echo "		<td class='vtable' width='70%'><input type='text' class='formfld' name='userurl' value=\"$userurl\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Phone 1:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphone1' value=\"$userphone1\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Phone 1 Ext:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphone1ext' value=\"$userphone1ext\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Phone 2:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphone2' value=\"$userphone2\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Phone 2 Ext:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphone2ext' value=\"$userphone2ext\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Mobile:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphonemobile' value=\"$userphonemobile\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Fax:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='userphonefax' value=\"$userphonefax\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Email:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='useremail' value=\"$useremail\"></td>";
	echo "	</tr>";
	echo "	<tr>\n";
	echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
	echo "		Template: \n";
	echo "	</td>\n";
	echo "	<td class=\"vtable\">\n";
	echo "<select id='user_template_name' name='user_template_name' class='formfld' style=''>\n";
	echo "<option value=''></option>\n";
	$theme_dir = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/themes';
	if ($handle = opendir($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/themes')) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && $file != ".svn" && is_dir($theme_dir.'/'.$file)) {
				if ($file == $user_template_name) {
					echo "<option value='$file' selected='selected'>$file</option>\n";
				}
				else {
					echo "<option value='$file'>$file</option>\n";
				}
			}
		}
		closedir($handle);
	}
	echo "	</select>\n";
	echo "	<br />\n";
	echo "	Select a template to set as the default and then press save.<br />\n";
	echo "	</td>\n";
	echo "	</tr>\n";
	echo "    </table>";
	echo "    </div>";

	echo "<br>";


	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth>";
	echo "	<tr>";
	echo "		<td colspan='2' align='right'>";
	echo "			<input type='hidden' name='id' value=\"$id\">";
	echo "			<input type='hidden' name='username' value=\"$username\">";
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
