<?php
/* $Id$ */
/*
	v_fax_edit.php
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
	$fax_id = checkstr($_REQUEST["id"]);
}
else {
	$action = "add";
}

//POST to PHP variables
if (count($_POST)>0) {
	//$v_id = checkstr($_POST["v_id"]);
	$faxextension = checkstr($_POST["faxextension"]);
	$faxname = checkstr($_POST["faxname"]);
	$faxemail = checkstr($_POST["faxemail"]);
	$faxdomain = checkstr($_POST["faxdomain"]);
	$faxdescription = checkstr($_POST["faxdescription"]);
}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	////recommend moving this to the config.php file
	$uploadtempdir = $_ENV["TEMP"]."\\";
	ini_set('upload_tmp_dir', $uploadtempdir);
	////$imagedir = $_ENV["TEMP"]."\\";
	////$filedir = $_ENV["TEMP"]."\\";

	if ($action == "update") {
		$fax_id = checkstr($_POST["fax_id"]);
	}

	//check for all required data
		if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		if (strlen($faxextension) == 0) { $msg .= "Please provide: Extension<br>\n"; }
		if (strlen($faxname) == 0) { $msg .= "Please provide: Name<br>\n"; }
		//if (strlen($faxemail) == 0) { $msg .= "Please provide: Email<br>\n"; }
		if (strlen($faxdomain) == 0) { $msg .= "Please provide: Domain<br>\n"; }
		//if (strlen($faxdescription) == 0) { $msg .= "Please provide: Description<br>\n"; }
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
	$tmp .= "v_id: $v_id\n";
	$tmp .= "Extension: $faxextension\n";
	$tmp .= "Name: $faxname\n";
	$tmp .= "Email: $faxemail\n";
	$tmp .= "Domain: $faxdomain\n";
	$tmp .= "Description: $faxdescription\n";


//Add or update the database
if ($_POST["persistformvar"] != "true") {
	if ($action == "add") {
		$sql = "insert into v_fax ";
		$sql .= "(";
		$sql .= "v_id, ";
		$sql .= "faxextension, ";
		$sql .= "faxname, ";
		$sql .= "faxemail, ";
		$sql .= "faxdomain, ";
		$sql .= "faxdescription ";
		$sql .= ")";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$v_id', ";
		$sql .= "'$faxextension', ";
		$sql .= "'$faxname', ";
		$sql .= "'$faxemail', ";
		$sql .= "'$faxdomain', ";
		$sql .= "'$faxdescription' ";
		$sql .= ")";
		$db->exec($sql);
		//$lastinsertid = $db->lastInsertId($id);
		unset($sql);

		sync_package_v_fax();

		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_fax.php\">\n";
		echo "<div align='center'>\n";
		echo "Add Complete\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;
	} //if ($action == "add")

	if ($action == "update") {
		$sql = "update v_fax set ";
		$sql .= "v_id = '$v_id', ";
		$sql .= "faxextension = '$faxextension', ";
		$sql .= "faxname = '$faxname', ";
		$sql .= "faxemail = '$faxemail', ";
		$sql .= "faxdomain = '$faxdomain', ";
		$sql .= "faxdescription = '$faxdescription' ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and fax_id = '$fax_id' ";
		$db->exec($sql);
		unset($sql);

		sync_package_v_fax();

		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_fax.php\">\n";
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
	$fax_id = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_fax ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and fax_id = '$fax_id' ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	while($row = $prepstatement->fetch()) {
		$v_id = $row["v_id"];
		$faxextension = $row["faxextension"];
		$faxname = $row["faxname"];
		$faxemail = $row["faxemail"];
		$faxdomain = $row["faxdomain"];
		$faxdescription = $row["faxdescription"];
		break; //limit to 1 row
	}
	unset ($prepstatement);
}


	require_once "includes/header.php";

	echo "<script language='javascript' src='/includes/calendar_popcalendar.js'></script>\n";
	echo "<script language='javascript' src='/includes/calendar_lw_layers.js'></script>\n";
	echo "<script language='javascript' src='/includes/calendar_lw_menu.js'></script>\n";

	echo "<div align='center'>";
	echo "<table border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";



	echo "<form method='post' name='frm' action=''>\n";

	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

	echo "<tr>\n";
	if ($action == "add") {
		echo "<td align='left' width='30%' nowrap><b>Fax Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap><b>Fax Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_fax.php'\" value='Back'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Extension:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='faxextension' maxlength='255' value=\"$faxextension\">\n";
	echo "<br />\n";
	echo "Enter the fax extension here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='faxname' maxlength='255' value=\"$faxname\">\n";
	echo "<br />\n";
	echo "Enter the name here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Email:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='faxemail' maxlength='255' value=\"$faxemail\">\n";
	echo "<br />\n";
	echo "Optional: Enter the email address to send the FAX to.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Domain:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='faxdomain' maxlength='255' value=\"$faxdomain\">\n";
	echo "<br />\n";
	echo "Enter the domain here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='faxdescription' maxlength='255' value=\"$faxdescription\">\n";
	echo "<br />\n";
	echo "Enter the description here.\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='fax_id' value='$fax_id'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";

	echo "<br />\n";
	echo "<br />\n";

	echo "	<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<td align='left' width='30%'>\n";
	echo "			<span class=\"vexpl\"><span class=\"red\"><strong>Send</strong></span>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align='left'>\n";
	//pkg_add -r ghostscript8-nox11; rehash
	echo "			To send a fax you can upload a .tif file or if ghost script has been installed then you can also send a fax by uploading a PDF. \n";
	echo "			When sending a fax you can view status of the transmission by viewing the logs from the Status tab or by watching the response from the console.\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align='right' nowrap>\n";
	echo "			<form action=\"\" method=\"POST\" enctype=\"multipart/form-data\" name=\"frmUpload\" onSubmit=\"\">\n";
	echo "			  <table border='0' cellpadding='3' cellspacing='0' width='100%'>\n";
	echo "				<tr>\n";
	echo "					<td width='30%' align='left' valign=\"middle\" class=\"label\">\n";
	echo "						Fax Number\n";
	//echo "					</td>\n";
	//echo "					<td width='30%' valign=\"top\" class=\"label\">\n";
	echo "						<input type=\"text\" name=\"fax_number\" class='formfld' style='width: 175px' value=\"\">\n";
	echo "					</td>\n";
	//echo "					<td align=\"right\">Upload:</td>\n";
	echo "					<td width='30%' valign=\"middle\" align='center' class=\"label\">\n";
	echo "						Upload:\n";
	echo "						<input name=\"id\" type=\"hidden\" value=\"\$id\">\n";
	echo "						<input name=\"type\" type=\"hidden\" value=\"fax_send\">\n";
	echo "						<input name=\"fax_file\" type=\"file\" class=\"btn\" id=\"fax_file\">\n";
	echo "					</td>\n";
	//echo "						<td class=\"label\">\n";
	//echo "					</td>\n";
	echo "					<td width='30%' align='right' valign=\"middle\" class=\"label\">";
	echo "						Gateway\n";
	$tablename = 'v_gateways'; $fieldname = 'gateway'; $sqlwhereoptional = "where v_id = $v_id"; $fieldcurrentvalue = '$fax_gateway';
	echo htmlselect($db, $tablename, $fieldname, $sqlwhereoptional, $fieldcurrentvalue);

	echo "					</td>\n";
	echo "					<td align='right'>\n";
	echo "						<input name=\"submit\" type=\"submit\" class=\"btn\" id=\"upload\" value=\"Send\">\n";
	echo "					</td>\n";
	echo "				</tr>\n";
	echo "			  </table>\n";
	echo "			</div>\n";
	echo "			</form>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "    </table>\n";
	echo "\n";
	echo "\n";
	echo "\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "\n";
	echo "  	<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<td align='left'>\n";
	echo "			<span class=\"vexpl\"><span class=\"red\"><strong>Inbox</strong></span>\n";
	echo "		</td>\n";
	echo "		<td align='right'>";

	if ($v_path_show) {
		echo "<b>location:</b> ";
		echo $dir_fax_inbox;
	}

	echo "		</td>\n";
	echo "	</tr>\n";
	echo "    </table>\n";
	echo "\n";

	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "	<div id=\"\">\n";
	echo "	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<th width=\"50%\" class=\"listhdrr\">File Name (download)</td>\n";
	echo "		<th width=\"10%\" class=\"listhdrr\">Download</td>\n";
	echo "		<th width=\"10%\" class=\"listhdrr\">View</td>\n";
	echo "		<th width=\"20%\" class=\"listhdr\">Last Modified</td>\n";
	echo "		<th width=\"10%\" class=\"listhdr\" nowrap>Size</td>\n";
	echo "	</tr>";

	if ($handle = opendir($dir_fax_inbox)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && is_file($dir_fax_inbox.$file)) {

				$tmp_filesize = filesize($dir_fax_inbox.$file);
				$tmp_filesize = byte_convert($tmp_filesize);

				$tmp_file_array = split("\.",$file);
				$file_name = $tmp_file_array[0];
				$file_ext = $tmp_file_array[1];

				if ($file_ext == "tif") {

					echo "<tr>\n";
					echo "  <td class=\"vtable\" ondblclick=\"\">\n";
					echo "	  <a href=\"v_fax_edit.php?id=".$id."&a=download&type=fax_inbox&t=bin&filename=".$file."\">\n";
					echo "    	$file";
					echo "	  </a>";
					echo "  </td>\n";
					echo "  <td class=\"listlr\" ondblclick=\"\">\n";
					echo "	  <a href=\"v_fax_edit.php?id=".$id."&a=download&type=fax_inbox&t=bin&filename=".$file_name.".pdf\">\n";
					echo "    	pdf";
					echo "	  </a>";
					echo "  </td>\n";
					echo "  <td class=\"listlr\" ondblclick=\"\">\n";
					echo "	  <a href=\"v_fax_edit.php?id=".$id."&a=download&type=fax_inbox&t=png&filename=".$file_name.".png\" target=\"_blank\">\n";
					echo "    	png";
					echo "	  </a>";
					echo "  </td>\n";
					echo "  <td class=\"listlr\" ondblclick=\"\">\n";
					echo 		date ("F d Y H:i:s", filemtime($dir_fax_inbox.$file));
					echo "  </td>\n";
					echo "  <td class=\"listlr\" ondblclick=\"\">\n";
					echo "	".$tmp_filesize;
					echo "  </td>\n";
					echo "  <td valign=\"middle\" nowrap class=\"list\">\n";
					echo "    <table border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n";
					echo "      <tr>\n";
					//echo "        <td valign=\"middle\"><a href=\"v_fax_edit.php?id=$i\"><img src=\"/themes/".$g['theme']."/images/icons/icon_e.gif\" width=\"17\" height=\"17\" border=\"0\"></a></td>\n";
					echo "        <td><a href=\"v_fax_edit.php?id=".$id."&type=fax_inbox&act=del&filename=".$file."\" onclick=\"return confirm('Do you really want to delete this file?')\"><img src=\"/themes/". $g['theme']."/images/icons/icon_x.gif\" width=\"17\" height=\"17\" border=\"0\"></a></td>\n";
					echo "      </tr>\n";
					echo "   </table>\n";
					echo "  </td>\n";
					echo "</tr>\n";
				}

			}
		}
		closedir($handle);
	}


	echo "	<tr>\n";
	echo "		<td class=\"list\" colspan=\"3\"></td>\n";
	echo "		<td class=\"list\"></td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "\n";
	echo "  	<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<td align='left'>\n";
	echo "			<span class=\"vexpl\"><span class=\"red\"><strong>Sent</strong></span>\n";
	echo "		</td>\n";
	echo "		<td align='right'>\n";

	if ($v_path_show) {
		echo "<b>location:</b>\n";
		echo $dir_fax_sent."\n";
	}

	echo "		</td>\n";
	echo "	</tr>\n";
	echo "    </table>\n";
	echo "\n";
	echo "    <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "    <tr>\n";
	echo "		<th width=\"50%\">File Name (download)</td>\n";
	echo "		<th width=\"10%\">Download</td>\n";
	echo "		<th width=\"10%\">View</td>\n";
	echo "		<th width=\"20%\">Last Modified</td>\n";
	echo "		<th width=\"10%\" nowrap>Size</td>\n";
	echo "		</tr>";

	if ($handle = opendir($dir_fax_sent)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && is_file($dir_fax_sent.$file)) {

				$tmp_filesize = filesize($dir_fax_sent.$file);
				$tmp_filesize = byte_convert($tmp_filesize);

				$tmp_file_array = split("\.",$file);
				$file_name = $tmp_file_array[0];
				$file_ext = $tmp_file_array[1];

				if ($file_ext == "tif") {

					echo "<tr>\n";
					echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
					echo "	  <a href=\"v_fax_edit.php?id=".$id."&a=download&type=fax_sent&t=bin&filename=".$file."\">\n";
					echo "    	$file";
					echo "	  </a>";
					echo "  </td>\n";
					echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
					echo "	  <a href=\"v_fax_edit.php?id=".$id."&a=download&type=fax_sent&t=bin&filename=".$file_name.".pdf\">\n";
					echo "    	pdf";
					echo "	  </a>";
					echo "  </td>\n";
					echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
					echo "	  <a href=\"v_fax_edit.php?id=".$id."&a=download&type=fax_sent&t=png&filename=".$file_name.".png\" target=\"_blank\">\n";
					echo "    	png";
					echo "	  </a>";
					echo "  </td>\n";
					echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
					echo 		date ("F d Y H:i:s", filemtime($dir_fax_sent.$file));
					echo "  </td>\n";
					echo "  <td class=\"listlr\" ondblclick=\"\">\n";
					echo "	".$tmp_filesize;
					echo "  </td>\n";
					echo "  <td class='".$rowstyle[$c]."' valign=\"middle\" nowrap>\n";
					echo "    <table border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n";
					echo "      <tr>\n";
					//echo "        <td valign=\"middle\"><a href=\"v_fax_edit.php?id=$i\"><img src=\"/themes/".$g['theme']."/images/icons/icon_e.gif\" width=\"17\" height=\"17\" border=\"0\"></a></td>\n";
					echo "        <td><a href=\"v_fax_edit.php?id=".$id."&type=fax_sent&act=del&filename=".$file."\" onclick=\"return confirm('Do you really want to delete this file?')\"><img src=\"/themes/". $g['theme']."/images/icons/icon_x.gif\" width=\"17\" height=\"17\" border=\"0\"></a></td>\n";
					echo "      </tr>\n";
					echo "   </table>\n";
					echo "  </td>\n";
					echo "</tr>\n";
				}

			}
		}
		closedir($handle);
	}


	echo "     <tr>\n";
	echo "       <td class=\"list\" colspan=\"3\"></td>\n";
	echo "       <td class=\"list\"></td>\n";
	echo "     </tr>\n";
	echo "     </table>\n";
	echo "\n";
	echo "\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "	<br />\n";


	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";


require_once "includes/footer.php";
?>
