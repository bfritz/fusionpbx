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
require_once "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('contacts_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//action add or update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$contacts_adr_uuid = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

if (strlen($_GET["contact_uuid"]) > 0) {
	$contact_uuid = check_str($_GET["contact_uuid"]);
}

//get http post variables and set them to php variables
	if (count($_POST)>0) {
		$adr_type = check_str($_POST["adr_type"]);
		$adr_street = check_str($_POST["adr_street"]);
		$adr_extended = check_str($_POST["adr_extended"]);
		$adr_locality = check_str($_POST["adr_locality"]);
		$adr_region = check_str($_POST["adr_region"]);
		$adr_postal_code = check_str($_POST["adr_postal_code"]);
		$adr_country = check_str($_POST["adr_country"]);
		$adr_latitude = check_str($_POST["adr_latitude"]);
		$adr_longitude = check_str($_POST["adr_longitude"]);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update") {
		$contacts_adr_uuid = check_str($_POST["contacts_adr_uuid"]);
	}

	//check for all required data
		//if (strlen($domain_uuid) == 0) { $msg .= "Please provide: domain_uuid<br>\n"; }
		//if (strlen($adr_type) == 0) { $msg .= "Please provide: Address Type<br>\n"; }
		//if (strlen($adr_street) == 0) { $msg .= "Please provide: Street Address<br>\n"; }
		//if (strlen($adr_extended) == 0) { $msg .= "Please provide: Extended Address<br>\n"; }
		//if (strlen($adr_locality) == 0) { $msg .= "Please provide: City<br>\n"; }
		//if (strlen($adr_region) == 0) { $msg .= "Please provide: State / Province<br>\n"; }
		//if (strlen($adr_postal_code) == 0) { $msg .= "Please provide: Postal Code<br>\n"; }
		//if (strlen($adr_country) == 0) { $msg .= "Please provide: Country<br>\n"; }
		//if (strlen($adr_latitude) == 0) { $msg .= "Please provide: Latitude<br>\n"; }
		//if (strlen($adr_longitude) == 0) { $msg .= "Please provide: Longitude<br>\n"; }
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

	//add or update the database
	if ($_POST["persistformvar"] != "true") {
		if ($action == "add") {
			$sql = "insert into v_contacts_adr ";
			$sql .= "(";
			$sql .= "contact_uuid, ";
			$sql .= "domain_uuid, ";
			$sql .= "adr_type, ";
			$sql .= "adr_street, ";
			$sql .= "adr_extended, ";
			$sql .= "adr_locality, ";
			$sql .= "adr_region, ";
			$sql .= "adr_postal_code, ";
			$sql .= "adr_country, ";
			$sql .= "adr_latitude, ";
			$sql .= "adr_longitude ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$contact_uuid', ";
			$sql .= "'$domain_uuid', ";
			$sql .= "'$adr_type', ";
			$sql .= "'$adr_street', ";
			$sql .= "'$adr_extended', ";
			$sql .= "'$adr_locality', ";
			$sql .= "'$adr_region', ";
			$sql .= "'$adr_postal_code', ";
			$sql .= "'$adr_country', ";
			$sql .= "'$adr_latitude', ";
			$sql .= "'$adr_longitude' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_contacts_edit.php?id=$contact_uuid\">\n";
			echo "<div align='center'>\n";
			echo "Add Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "add")

		if ($action == "update") {
			$sql = "update v_contacts_adr set ";
			$sql .= "contact_uuid = '$contact_uuid', ";
			$sql .= "adr_type = '$adr_type', ";
			$sql .= "adr_street = '$adr_street', ";
			$sql .= "adr_extended = '$adr_extended', ";
			$sql .= "adr_locality = '$adr_locality', ";
			$sql .= "adr_region = '$adr_region', ";
			$sql .= "adr_postal_code = '$adr_postal_code', ";
			$sql .= "adr_country = '$adr_country', ";
			$sql .= "adr_latitude = '$adr_latitude', ";
			$sql .= "adr_longitude = '$adr_longitude' ";
			$sql .= "where domain_uuid = '$domain_uuid'";
			$sql .= "and contacts_adr_uuid = '$contacts_adr_uuid'";
			$db->exec(check_sql($sql));
			unset($sql);

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_contacts_edit.php?id=$contact_uuid\">\n";
			echo "<div align='center'>\n";
			echo "Update Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "update")
	} //if ($_POST["persistformvar"] != "true") 
} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
		$contacts_adr_uuid = $_GET["id"];
		$sql = "";
		$sql .= "select * from v_contacts_adr ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and contacts_adr_uuid = '$contacts_adr_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			$adr_type = $row["adr_type"];
			$adr_street = $row["adr_street"];
			$adr_extended = $row["adr_extended"];
			$adr_locality = $row["adr_locality"];
			$adr_region = $row["adr_region"];
			$adr_postal_code = $row["adr_postal_code"];
			$adr_country = $row["adr_country"];
			$adr_latitude = $row["adr_latitude"];
			$adr_longitude = $row["adr_longitude"];
			break; //limit to 1 row
		}
		unset ($prep_statement);
	}

//show the header
	require_once "includes/header.php";

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing=''>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "	  <br>";

	echo "<form method='post' name='frm' action=''>\n";
	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
	echo "<tr>\n";
	if ($action == "add") {
		echo "<td align='left' width='30%' nowrap='nowrap'><b>Contacts Address Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap='nowrap'><b>Contacts Address Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_contacts_edit.php?id=$contact_uuid'\" value='Back'></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan='2'>\n";
	echo "Contact address information.<br /><br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Address Type:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='adr_type'>\n";
	echo "	<option value=''></option>\n";
	if ($adr_type == "Home") { 
		echo "	<option value='Home' SELECTED >home</option>\n";
	}
	else {
		echo "	<option value='Home'>home</option>\n";
	}
	if ($adr_type == "Work") { 
		echo "	<option value='Work' SELECTED >work</option>\n";
	}
	else {
		echo "	<option value='Work'>work</option>\n";
	}
	echo "	</select>\n";
	echo "<br />\n";
	echo "Enter the address type.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Street Address:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='adr_street' maxlength='255' value=\"$adr_street\">\n";
	echo "<br />\n";
	echo "Enter the street address.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Extended Address:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='adr_extended' maxlength='255' value=\"$adr_extended\">\n";
	echo "<br />\n";
	echo "Enter the extended address.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	City:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='adr_locality' maxlength='255' value=\"$adr_locality\">\n";
	echo "<br />\n";
	echo "Enter the city.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Region:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='adr_region' maxlength='255' value=\"$adr_region\">\n";
	echo "<br />\n";
	echo "Enter the state or province.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Postal Code:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='adr_postal_code' maxlength='255' value=\"$adr_postal_code\">\n";
	echo "<br />\n";
	echo "Enter the postal code.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Country:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='adr_country' maxlength='255' value=\"$adr_country\">\n";
	echo "<br />\n";
	echo "Enter the country.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Latitude:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='adr_latitude' maxlength='255' value=\"$adr_latitude\">\n";
	echo "<br />\n";
	echo "Enter the latitude\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Longitude:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='adr_longitude' maxlength='255' value=\"$adr_longitude\">\n";
	echo "<br />\n";
	echo "Enter the longitude\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	echo "				<input type='hidden' name='contact_uuid' value='$contact_uuid'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='contacts_adr_uuid' value='$contacts_adr_uuid'>\n";
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

//include the footer
	require_once "includes/footer.php";
?>
