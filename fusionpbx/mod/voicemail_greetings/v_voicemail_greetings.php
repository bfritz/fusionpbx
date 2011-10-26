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
if (permission_exists('voicemail_greetings_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/paging.php";

//set the max php execution time
	ini_set(max_execution_time,7200);

//get the http get values and set them as php variables
	$user_id = check_str($_REQUEST["id"]);
	$orderby = $_GET["orderby"];
	$order = $_GET["order"];

//allow admins, superadmins and users that are assigned to the extension to view the page
	if (ifgroup("superadmin") || ifgroup("admin")) {
		//access granted
	}
	else {
		//get the extensions that are assigned to this user
		$user_extension_array = explode("|", $_SESSION['user_extension_list']);
		//print_r($user_extension_array);
		if (!in_array($user_id, $user_extension_array)) {
			echo "access denied";
			return;
		}
	}

//set the greeting directory
	$v_greeting_dir = $v_storage_dir.'/voicemail/default/'.$_SESSION['domains'][$v_id]['domain'].'/'.$user_id;

//save the selected greeting
	if ($_REQUEST['submit'] == "Save") {
		$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
		if ($fp) {
			// vm_fsdb_pref_greeting_set,<profile> <domain> <user> <slot> [file-path],vm_fsdb_pref_greeting_set,mod_voicemail
			$switch_cmd = "vm_fsdb_pref_greeting_set default ".$_SESSION['domains'][$v_id]['domain']." ".$user_id." ".substr($_REQUEST['greeting'], -5, 1)." ".$v_greeting_dir."/".$_REQUEST['greeting'];
			$greeting = trim(event_socket_request($fp, 'api '.$switch_cmd));
		}
	}

//download the voicemail greeting
	if ($_GET['a'] == "download") { // && permission_exists('voicemail_greetings_download')) {
		session_cache_limiter('public');
		if ($_GET['type'] = "rec") {
			if (file_exists($v_greeting_dir.'/'.base64_decode($_GET['filename']))) {
				$fd = fopen($v_greeting_dir.'/'.base64_decode($_GET['filename']), "rb");
				if ($_GET['t'] == "bin") {
					header("Content-Type: application/force-download");
					header("Content-Type: application/octet-stream");
					header("Content-Type: application/download");
					header("Content-Description: File Transfer");
					header('Content-Disposition: attachment; filename="'.base64_decode($_GET['filename']).'"');
				}
				else {
					$file_ext = substr(base64_decode($_GET['filename']), -3);
					if ($file_ext == "wav") {
						header("Content-Type: audio/x-wav");
					}
					if ($file_ext == "mp3") {
						header("Content-Type: audio/mp3");
					}
				}
				header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
				header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
				header("Content-Length: " . filesize($v_greeting_dir.'/'.base64_decode($_GET['filename'])));
				fpassthru($fd);
			}
		}
		exit;
	}

//upload the recording
	if (($_POST['submit'] == "Upload") && is_uploaded_file($_FILES['ulfile']['tmp_name']) && permission_exists('voicemail_greeting_upload')) {
		if ($_POST['type'] == 'rec') {
			move_uploaded_file($_FILES['ulfile']['tmp_name'], $v_voicemail_greetings_dir.'/'.$_FILES['ulfile']['name']);
			$savemsg = "Uploaded file to ".$v_greeting_dir."/". htmlentities($_FILES['ulfile']['name']);
			//system('chmod -R 744 $v_voicemail_greetings_dir*');
			unset($_POST['txtCommand']);
		}
	}

//build a list of voicemail greetings
	$config_voicemail_greeting_list = '|';
	$i = 0;
	$sql = "";
	$sql .= "select * from v_voicemail_greetings ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and user_id = '$user_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$config_greeting_list = "|";
	foreach ($result as &$row) {
		$config_greeting_list .= $row['greeting_name']."|";
	}
	unset ($prepstatement);

//add recordings to the database
	if (is_dir($v_greeting_dir.'/')) {
		if ($dh = opendir($v_greeting_dir.'/')) {
			while (($file = readdir($dh)) !== false) {
				if (filetype($v_greeting_dir."/".$file) == "file") {
					if (strpos($config_greeting_list, "|".$file) === false) {
						if (substr($file, 0, 8) == "greeting") {
							//file not found add it to the database
							$a_file = explode("\.", $file);

							$sql = "insert into v_voicemail_greetings ";
							$sql .= "(";
							$sql .= "v_id, ";
							$sql .= "user_id, ";
							$sql .= "greeting_name, ";
							$sql .= "greeting_description ";
							$sql .= ")";
							$sql .= "values ";
							$sql .= "(";
							$sql .= "'$v_id', ";
							$sql .= "'$user_id', ";
							$sql .= "'".$a_file[0]."', ";
							$sql .= "'' ";
							$sql .= ")";
							$db->exec(check_sql($sql));
							unset($sql);
							//echo $sql."<br />\n";
						}
					}
					else {
						//echo "The $file was found.<br/>";
					}
				}
			}
			closedir($dh);
		}
	}

//use event socket to get the current greeting
	if (!$fp) {
		$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
	}
	if ($fp) {
		// vm_prefs,[profile/]<user>@<domain>[|[name_path|greeting_path|password]],vm_prefs,mod_voicemail
		$switch_cmd = "vm_prefs default/".$user_id."@".$_SESSION['domains'][$v_id]['domain'];
		$greeting = trim(event_socket_request($fp, 'api '.$switch_cmd));
	}

//include the header
	require_once "includes/header.php";

//begin the content
	echo "<script>\n";
	echo "function EvalSound(soundobj) {\n";
	echo "  var thissound= eval(\"document.\"+soundobj);\n";
	echo "  thissound.Play();\n";
	echo "}\n";
	echo "</script>";

	echo "<form method='post' name='ifrm' action=''>\n";
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "		<br>";

	echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<td align='left' width=\"50%\">\n";
	echo "			<strong>Voicemail Greetings:</strong><br>\n";
	echo "		</td>";
	echo "		<td width='50%' align='right'>\n";
	echo "			<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "			<input type='button' class='btn' name='' alt='back' onclick=\"javascript:history.back();\" value='Back'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td align='left' colspan='2'>\n";
	echo "			Select the active greeting message to play for extension $user_id. <br />\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";

	echo "<br />\n";

	/*
	echo "<form action=\"\" method=\"POST\" enctype=\"multipart/form-data\" name=\"frmUpload\" onSubmit=\"\">\n";
	echo "	<table border='0' width='100%'>\n";
	echo "	<tr>\n";
	echo "		<td align='left' width='50%'>\n";
	if ($v_path_show) {
		echo "<b>location:</b> \n";
		//usr/local/freeswitch/storage/voicemail/default/".$_SESSION['domains'][$v_id]['domain']."/1004/greeting_2.wav 
		echo $v_storage_dir.'/voicemail/default/'.$_SESSION['domains'][$v_id]['domain'].'/'.$user_id;
	}
	echo "		</td>\n";
	echo "		<td valign=\"top\" class=\"label\">\n";
	echo "			<input name=\"type\" type=\"hidden\" value=\"rec\">\n";
	echo "		</td>\n";
	echo "		<td valign=\"top\" align='right' class=\"label\" nowrap>\n";
	echo "			File to upload:\n";
	echo "			<input name=\"ulfile\" type=\"file\" class=\"btn\" id=\"ulfile\">\n";
	echo "			<input name=\"submit\" type=\"submit\"  class=\"btn\" id=\"upload\" value=\"Upload\">\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "</form>";
	*/

	//get the number of rows in v_extensions 
		$sql = "";
		$sql .= " select count(*) as num_rows from v_voicemail_greetings ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and user_id = '$user_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		if ($prepstatement) {
			$prepstatement->execute();
			$row = $prepstatement->fetch(PDO::FETCH_ASSOC);
			if ($row['num_rows'] > 0) {
				$num_rows = $row['num_rows'];
			}
			else {
				$num_rows = '0';
			}
		}
		unset($prepstatement, $result);

	//prepare to page the results
		$rowsperpage = 100;
		$param = "";
		$page = $_GET['page'];
		if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
		list($pagingcontrols, $rowsperpage, $var3) = paging($num_rows, $param, $rowsperpage); 
		$offset = $rowsperpage * $page; 

	//get the greetings list
		$sql = "";
		$sql .= "select * from v_voicemail_greetings ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and user_id = '$user_id' ";
		if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }
		$sql .= " limit $rowsperpage offset $offset ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		$resultcount = count($result);
		unset ($prepstatement, $sql);

	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<th>Choose</th>\n";
	echo thorderby('greeting_name', 'Name', $orderby, $order);
	echo "<th align='right'>Download</th>\n";
	echo "<th width=\"50px\" class=\"listhdr\" nowrap=\"nowrap\">Size</th>\n";
	echo thorderby('greeting_description', 'Description', $orderby, $order);
	echo "<td align='right' width='42'>\n";
	//if (permission_exists('voicemail_greetings_add')) {
	//	echo "	<a href='v_voicemail_greetings_edit.php?&user_id=".$user_id."' alt='add'>$v_link_label_add</a>\n";
	//}
	echo "</td>\n";
	echo "</tr>\n";

	if ($resultcount > 0) {
		foreach($result as $row) {
			$tmp_filesize = filesize($v_greeting_dir.'/'.$row['greeting_name']);
			$tmp_filesize = byte_convert($tmp_filesize);

			echo "<tr >\n";
			echo "	<td class='".$rowstyle[$c]."' ondblclick=\"\" width='30px;' valign='top'>\n";
			if ($v_greeting_dir.'/'.$row['greeting_name'] == $greeting) {
				echo "		<input type=\"radio\" name=\"greeting\" value=\"".$row['greeting_name']."\" checked=\"checked\">\n";
			}
			else {
				echo "		<input type=\"radio\" name=\"greeting\" value=\"".$row['greeting_name']."\">\n";
			}
			echo "	</td>\n";

			echo "	<td valign='top' class='".$rowstyle[$c]."'>";
			echo $row['greeting_name'];
			echo 	"</td>\n";

			echo "	<td valign='top' class='".$rowstyle[$c]."'>";
			echo "		<a href=\"v_voicemail_greetings.php?id=$user_id&a=download&type=rec&t=bin&filename=".base64_encode($row['greeting_name'])."\">\n";
			echo "		download";
			echo "		</a>";
			//echo "		&nbsp;\n";
			//echo "		<a href=\"javascript:void(0);\" onclick=\"window.open('v_voicemail_greetings_play.php?id=$user_id&a=download&type=rec&filename=".base64_encode($row['greeting_name'])."', 'play',' width=420,height=40,menubar=no,status=no,toolbar=no')\">\n";
			//echo "		play";
			//echo "		</a>";
			echo 	"</td>\n";

			echo "	<td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
			echo "	".$tmp_filesize;
			echo "	</td>\n";

			echo "	<td valign='top' class='rowstylebg'>".$row['greeting_description']."&nbsp;</td>\n";

			echo "	<td valign='top' align='right'>\n";
			if (permission_exists('voicemail_greetings_edit')) {
				echo "		<a href='v_voicemail_greetings_edit.php?id=".$row['greeting_id']."&user_id=".$user_id."' alt='edit'>$v_link_label_edit</a>\n";
			}
			if (permission_exists('voicemail_greetings_delete')) {
				echo "		<a href='v_voicemail_greetings_delete.php?id=".$row['greeting_id']."&user_id=".$user_id."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
			}
			echo "	</td>\n";
			echo "</tr>\n";

			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results
	echo "</table>\n";

	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$pagingcontrols</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	//if (permission_exists('voicemail_greetings_add')) {
	//	echo "			<a href='v_voicemail_greetings_edit.php?user_id=".$user_id."' alt='add'>$v_link_label_add</a>\n";
	//}
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>";
	echo "</div>";
	echo "				<input type='hidden' name='id' value='$user_id'>\n";
	echo "</form>";

	echo "<br>\n";
	echo "<br>\n";
	echo "<br>\n";
	echo "<br>\n";

//include the footer
	require_once "includes/footer.php";

?>