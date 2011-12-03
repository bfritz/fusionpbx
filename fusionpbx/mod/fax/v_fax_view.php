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
if (permission_exists('fax_extension_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//get the fax_extension and save it as a variable
	if (strlen($_REQUEST["fax_extension"]) > 0) {
		$fax_extension = check_str($_REQUEST["fax_extension"]);
	}

//pre-populate the form
	if (strlen($_GET['id']) > 0 && $_POST["persistformvar"] != "true") {
		$fax_id = check_str($_GET["id"]);
		$sql = "";
		$sql .= "select * from v_fax ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and fax_id = '$fax_id' ";
		if (ifgroup("superadmin")) {
			//show all fax extensions
		}
		else if (ifgroup("admin")) {
			//show all fax extensions
		}
		else {
			//show only assigned fax extensions
			$sql .= "and fax_user_list like '%|".$_SESSION["username"]."|%' ";
		}
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		if (count($result) == 0) {
			echo "access denied";
			exit;
		}
		foreach ($result as &$row) {
			//set database fields as variables
				$fax_extension = $row["faxextension"];
				$fax_name = $row["faxname"];
				$fax_email = $row["faxemail"];
				$fax_pin_number = $row["fax_pin_number"];
				$fax_caller_id_name = $row["fax_caller_id_name"];
				$fax_caller_id_number = $row["fax_caller_id_number"];
				$fax_forward_number = $row["fax_forward_number"];
				$fax_user_list = $row["fax_user_list"];
				$fax_description = $row["faxdescription"];
			//limit to one row
				break;
		}
		unset ($prepstatement);
	}

//set the fax directory
	if (count($_SESSION["domains"]) > 1) {
		$v_fax_dir = $v_storage_dir.'/fax/'.$v_domain;
	}
	else {
		$v_fax_dir = $v_storage_dir.'/fax';
	}

//delete a fax
	if ($_GET['a'] == "del" && permission_exists('fax_inbox_delete')) {
		$file_name = substr(check_str($_GET['filename']), 0, -4);
		$file_ext = substr(check_str($_GET['filename']), -3);
		if ($_GET['type'] == "fax_inbox") {
			unlink($v_fax_dir.'/'.$fax_extension.'/inbox/'.$file_name.".tif");
			unlink($v_fax_dir.'/'.$fax_extension.'/inbox/'.$file_name.".pdf");
		}
		if ($_GET['type'] == "fax_sent") {
			unlink($v_fax_dir.'/'.$fax_extension.'/sent/'.$file_name.".tif");
			unlink($v_fax_dir.'/'.$fax_extension.'/sent/'.$file_name.".pdf");
		}
		unset($file_name);
		unset($file_ext);
	}

//download a fax
	if ($_GET['a'] == "download") {
		session_cache_limiter('public');
		//test to see if it is in the inbox or sent directory.
		if ($_GET['type'] == "fax_inbox") {
			if (file_exists($v_fax_dir.'/'.check_str($_GET['ext']).'/inbox/'.check_str($_GET['filename']))) {
				$tmp_faxdownload_file = "".$v_fax_dir.'/'.check_str($_GET['ext']).'/inbox/'.check_str($_GET['filename']);
			}
		}
		else if ($_GET['type'] == "fax_sent") {
			if  (file_exists($v_fax_dir.'/'.check_str($_GET['ext']).'/sent/'.check_str($_GET['filename']))) {
				$tmp_faxdownload_file = "".$v_fax_dir.'/'.check_str($_GET['ext']).'/sent/'.check_str($_GET['filename']);
			}
		}
		//let's see if we found it.
		if (strlen($tmp_faxdownload_file) > 0) {
			$fd = fopen($tmp_faxdownload_file, "rb");
			if ($_GET['t'] == "bin") {
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header("Content-Type: application/download");
				header("Content-Description: File Transfer");
				header('Content-Disposition: attachment; filename="'.check_str($_GET['filename']).'"');
			}
			else {
				$file_ext = substr(check_str($_GET['filename']), -3);
				if ($file_ext == "tif") {
				  header("Content-Type: image/tiff");
				}
				else if ($file_ext == "png") {
				  header("Content-Type: image/png");
				}
				else if ($file_ext == "jpg") {
				  header('Content-Type: image/jpeg');
				}
				else if ($file_ext == "pdf") {
				  header("Content-Type: application/pdf");
				}
			}
			header('Accept-Ranges: bytes');
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // date in the past
			header("Content-Length: " . filesize($tmp_faxdownload_file));
			fpassthru($fd);
		}
		else {
			echo "File not found.";
		}
		exit;
	}

//get the fax extension
	if (strlen($fax_extension) > 0) {
		//set the fax directories. example /usr/local/freeswitch/storage/fax/329/inbox
			$dir_fax_inbox = $v_fax_dir.'/'.$fax_extension.'/inbox';
			$dir_fax_sent = $v_fax_dir.'/'.$fax_extension.'/sent';
			$dir_fax_temp = $v_fax_dir.'/'.$fax_extension.'/temp';

		//make sure the directories exist
			if (!is_dir($v_storage_dir)) {
				mkdir($v_storage_dir);
				chmod($dir_fax_sent,0774);
			}
			if (!is_dir($v_fax_dir.'/'.$fax_extension)) {
				mkdir($v_fax_dir.'/'.$fax_extension,0774,true);
				chmod($v_fax_dir.'/'.$fax_extension,0774);
			}
			if (!is_dir($dir_fax_inbox)) { 
				mkdir($dir_fax_inbox,0774,true); 
				chmod($dir_fax_inbox,0774);
			}
			if (!is_dir($dir_fax_sent)) { 
				mkdir($dir_fax_sent,0774,true); 
				chmod($dir_fax_sent,0774);
			}
			if (!is_dir($dir_fax_temp)) { 
				mkdir($dir_fax_temp,0774,true); 
				chmod($dir_fax_temp,0774);
			}
	}

//set the action as an add or an update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$fax_id = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//get the http post values and set them as php variables
	if (count($_POST)>0) {
		$fax_name = check_str($_POST["fax_name"]);
		$fax_email = check_str($_POST["fax_email"]);
		$fax_pin_number = check_str($_POST["fax_pin_number"]);
		$fax_caller_id_name = check_str($_POST["fax_caller_id_name"]);
		$fax_caller_id_number = check_str($_POST["fax_caller_id_number"]);
		$fax_forward_number = check_str($_POST["fax_forward_number"]);
		if (strlen($fax_forward_number) > 0) {
			$fax_forward_number = preg_replace("~[^0-9]~", "",$fax_forward_number);
		}

		//prepare the user list for the database
		$fax_user_list = check_str(trim($_POST["fax_user_list"]));
		if (strlen($fax_user_list) > 0) {
			$fax_user_list_array = explode("\n", $fax_user_list);
			if (count($fax_user_list_array) == 0) {
				$fax_user_list = '';
			}
			else {
				$fax_user_list = '|';
				foreach($fax_user_list_array as $user){
					if(strlen(trim($user)) > 0) {
						$fax_user_list .= check_str(trim($user))."|";
					}
				}
			}
		}

		$fax_description = check_str($_POST["fax_description"]);
	}

//clear file status cache
	clearstatcache(); 

//upload and send the fax
	if (($_POST['type'] == "fax_send") && is_uploaded_file($_FILES['fax_file']['tmp_name'])) {

		$fax_number = check_str($_POST['fax_number']);
		if (strlen($fax_number) > 0) {
			$fax_number = preg_replace("~[^0-9]~", "",$fax_number);
		}
		$fax_name = $_FILES['fax_file']['name'];
		$fax_name = str_replace(" ", "_", $fax_name);
		$fax_name = str_replace(".tif", "", $fax_name);
		$fax_name = str_replace(".tiff", "", $fax_name);
		$fax_name = str_replace(".pdf", "", $fax_name);
		$provider_type = check_str($_POST['provider_type']);
		$fax_id = check_str($_POST["id"]);

		$fax_caller_id_name = check_str($_POST['fax_caller_id_name']);
		$fax_caller_id_number = check_str($_POST['fax_caller_id_number']);
		$fax_forward_number = check_str($_POST['fax_forward_number']);
		if (strlen($fax_forward_number) > 0) {
			$fax_forward_number = preg_replace("~[^0-9]~", "",$fax_forward_number);
		}

		//get the fax file extension
			$fax_file_extension = substr($dir_fax_temp.'/'.$_FILES['fax_file']['name'], -4);

		//upload the file
			move_uploaded_file($_FILES['fax_file']['tmp_name'], $dir_fax_temp.'/'.$fax_name.$fax_file_extension);

			if ($fax_file_extension == ".pdf") {
				chdir($dir_fax_temp);
				exec("gs -q -sDEVICE=tiffg3 -r204x98 -dNOPAUSE -sOutputFile=".$fax_name.".tif -- ".$fax_name.".pdf -c quit");
				//exec("rm ".$dir_fax_temp.'/'.$fax_name.".pdf");
			}
			if ($fax_file_extension == ".tiff") {
				chdir($dir_fax_temp);
				exec("cp ".$dir_fax_temp.'/'.$fax_name.".tiff ".$dir_fax_temp.'/'.$fax_name.".tif");
				exec("rm ".$dir_fax_temp.'/'.$fax_name.".tiff");
			}

		//send the fax
			$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
			if ($fp) {
				//prepare the fax originate command
					$route_array = outbound_route_to_bridge($fax_number);
					$fax_file = $dir_fax_temp."/".$fax_name.".tif";
					if (count($route_array) == 0) {
						//send the internal call to the registered extension
							$fax_uri = "user/".$fax_number."@".$v_domain;
					}
					else {
						//send the external call
							$fax_uri = $route_array[0];
					}
					$cmd = "api originate {origination_caller_id_name='".$fax_caller_id_name."',origination_caller_id_number=".$fax_caller_id_number.",fax_uri=$fax_uri,fax_file='".$fax_file."',fax_retry_attempts=1,fax_retry_limit=20,fax_retry_sleep=180,fax_verbose=true,fax_use_ecm=off,api_hangup_hook='lua fax_retry.lua'}$fax_uri &txfax('".$fax_file."')";
				//send the command to event socket
					$response = event_socket_request($fp, $cmd);
					$response = str_replace("\n", "", $response);
					$uuid = str_replace("+OK ", "", $response);
					fclose($fp);
			}

		//wait for a few seconds
			sleep(5);

		//copy the .tif to the sent directory
			exec("cp ".$dir_fax_temp.'/'.$fax_name.".tif ".$dir_fax_sent.'/'.$fax_name.".tif");

		//convert the tif to pdf
			chdir($dir_fax_sent);
			exec("gs -q -sDEVICE=tiffg3 -g1728x1078 -dNOPAUSE -sOutputFile=".$fax_name.".pdf -- ".$fax_name.".tif -c quit");

		//delete the .tif from the temp directory
			//exec("rm ".$dir_fax_temp.'/'.$fax_name.".tif");

		//convert the tif to pdf and png
			chdir($dir_fax_sent);
			//which tiff2pdf
			if (is_file("/usr/local/bin/tiff2png")) {
				exec("".bin_dir."/tiff2png ".$dir_fax_sent.$fax_name.".tif");
				exec("".bin_dir."/tiff2pdf -f -o ".$fax_name.".pdf ".$dir_fax_sent.$fax_name.".tif");
			}

		header("Location: v_fax_view.php?id=".$fax_id."&msg=".$response);
		exit;
	} //end upload and send fax

//delete the fax
	if ($_GET['a'] == "del") {
		$fax_extension = check_str($_GET["fax_extension"]);
		if ($_GET['type'] == "fax_inbox" && permission_exists('fax_inbox_delete')) {
			unlink($v_fax_dir.'/'.$fax_extension.'/inbox/'.check_str($_GET['filename']));
		}
		if ($_GET['type'] == "fax_sent" && permission_exists('fax_sent_delete')) {
			unlink($v_fax_dir.'/'.$fax_extension.'/sent/'.check_str($_GET['filename']));
		}
	}

//download the fax
	if ($_GET['a'] == "download") {
		session_cache_limiter('public');
		//test to see if it is in the inbox or sent directory.
			if ($_GET['type'] == "fax_inbox" && permission_exists('fax_inbox_view')) {
				if (file_exists($v_fax_dir.'/'.check_str($_GET['ext']).'/inbox/'.check_str($_GET['filename']))) {
					$tmp_faxdownload_file = "".$v_fax_dir.'/'.check_str($_GET['ext']).'/inbox/'.check_str($_GET['filename']);
				}
			}else if ($_GET['type'] == "fax_sent" && permission_exists('fax_sent_view')) {
				if  (file_exists($v_fax_dir.'/'.check_str($_GET['ext']).'/sent/'.check_str($_GET['filename']))) {
					$tmp_faxdownload_file = "".$v_fax_dir.'/'.check_str($_GET['ext']).'/sent/'.check_str($_GET['filename']);
				}
			}
		//check to see if it was found.
			if (strlen($tmp_faxdownload_file) > 0) {
				$fd = fopen($tmp_faxdownload_file, "rb");
				if ($_GET['t'] == "bin") {
					header("Content-Type: application/force-download");
					header("Content-Type: application/octet-stream");
					header("Content-Type: application/download");
					header("Content-Description: File Transfer");
					header('Content-Disposition: attachment; filename="'.check_str($_GET['filename']).'"');
				}
				else {
					$file_ext = substr(check_str($_GET['filename']), -3);
					if ($file_ext == "tif") {
						header("Content-Type: image/tiff");
					} else if ($file_ext == "png") {
						header("Content-Type: image/png");
					} else if ($file_ext == "jpg") {
						header('Content-Type: image/jpeg');
					} else if ($file_ext == "pdf") {
						header("Content-Type: application/pdf");
					}
				}
				header('Accept-Ranges: bytes');
				header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
				header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
				header("Content-Length: " . filesize($tmp_faxdownload_file));
				fpassthru($fd);
			}
			else {
				echo "File not found.";
			}
		//exit the code execution
			exit;
	}

//show the header
	require_once "includes/header.php";

//fax extension form
	echo "<div align='center'>";
	echo "<table border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "		<br>";

	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "		<td align='left' width='30%'>\n";
	echo "			<span class=\"vexpl\"><span class=\"red\"><strong>Fax Server</strong></span>\n";
	echo "		</td>\n";
	echo "		<td width='70%' align='right'>\n";
	if (permission_exists('fax_extension_add') || permission_exists('fax_extension_edit')) {
		echo "			<input type='button' class='btn' name='' alt='settings' onclick=\"window.location='v_fax_edit.php?id=$fax_id'\" value='Settings'>\n";
	}
	echo "			<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_fax.php'\" value='Back'>\n";
	echo "		</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>\n";

	echo "<form action=\"\" method=\"POST\" enctype=\"multipart/form-data\" name=\"frmUpload\" onSubmit=\"\">\n";
	echo "<div align='center'>\n";
	echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='left'>\n";
	//pkg_add -r ghostscript8-nox11; rehash
	echo "			To send a fax you can upload a .tif file or if ghost script has been installed then you can also send a fax by uploading a PDF. \n";
	echo "			When sending a fax you can view status of the transmission by viewing the logs from the Status tab or by watching the response from the console.\n";
	echo "			<br /><br />\n";
	echo "		</td>\n";
	echo "	</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "		Fax Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "		<input type=\"text\" name=\"fax_number\" class='formfld' style='' value=\"\">\n";
	echo "<br />\n";
	echo "Enter the Number here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "	Upload:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input name=\"id\" type=\"hidden\" value=\"\$id\">\n";
	echo "	<input name=\"type\" type=\"hidden\" value=\"fax_send\">\n";
	echo "	<input name=\"fax_file\" type=\"file\" class=\"btn\" id=\"fax_file\">\n";
	echo "	<br />\n";
	echo "	Select the file to upload and send as a fax.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	echo "			<input type=\"hidden\" name=\"fax_caller_id_name\" value=\"".$fax_caller_id_name."\">\n";
	echo "			<input type=\"hidden\" name=\"fax_caller_id_number\" value=\"".$fax_caller_id_number."\">\n";
	echo "			<input type=\"hidden\" name=\"fax_extension\" value=\"".$fax_extension."\">\n";
	echo "			<input type=\"hidden\" name=\"id\" value=\"".$fax_id."\">\n";
	echo "			<input name=\"submit\" type=\"submit\" class=\"btn\" id=\"upload\" value=\"Send\">\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</div>\n";
	echo "</form>\n";

//show the inbox
	if (permission_exists('fax_inbox_view')) {
		echo "\n";
		echo "\n";
		echo "	<br />\n";
		echo "\n";
		echo "	<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">\n";
		echo "	<tr>\n";
		echo "		<td align='left'>\n";
		echo "			<span class=\"vexpl\"><span class=\"red\"><strong>Inbox $fax_extension</strong></span>\n";
		echo "		</td>\n";
		echo "		<td align='right'>";
		if ($v_path_show) {
			echo "<b>location:</b>&nbsp;";
			echo $dir_fax_inbox."&nbsp; &nbsp; &nbsp;";
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
		echo "		<th width=\"60%\" class=\"listhdrr\">File Name (download)</td>\n";
		echo "		<th width=\"10%\" class=\"listhdrr\">View</td>\n";
		echo "		<th width=\"20%\" class=\"listhdr\">Last Modified</td>\n";
		echo "		<th width=\"10%\" class=\"listhdr\" nowrap>Size</td>\n";
		echo "	</tr>";


		if ($handle = opendir($dir_fax_inbox)) {
			//build an array of the files in the inbox
				$i = 0;
				$files = array();
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != ".." && is_file($dir_fax_inbox.'/'.$file)) {
						$file_path = $dir_fax_inbox.'/'.$file;
						$modified = filemtime($file_path);
						$index = $modified.$file;
						$files[$index]['file'] = $file;
						$files[$index]['name'] = substr($file, 0, -4);
						$files[$index]['ext'] = substr($file, -3);
						//$files[$index]['path'] = $file_path;
						$files[$index]['size'] = filesize($file_path);
						$files[$index]['size_bytes'] = byte_convert(filesize($file_path));
						$files[$index]['modified'] = filemtime($file_path);
						$file_name_array[$i++] = $index;
					}
				}
				closedir($handle);
			//order the index array
				sort($file_name_array,SORT_STRING);

			//loop through the file array
				foreach($file_name_array as $i) {
					if (strtolower($files[$i]['ext']) == "tif") {
						$file = $files[$i]['file'];
						$file_name = $files[$i]['name'];
						$file_ext = $files[$i]['ext'];
						$file_modified = $files[$i]['modified'];
						$file_size_bytes = byte_convert($files[$i]['size']);
						if (!file_exists($dir_fax_inbox.'/'.$file_name.".pdf")) {
							//convert the tif to pdf
								chdir($dir_fax_inbox);
								if (is_file("/usr/local/bin/tiff2pdf")) {
									exec("/usr/local/bin/tiff2pdf -f -o ".$file_name.".pdf ".$dir_fax_inbox.'/'.$file_name.".tif");
								}
								if (is_file("/usr/bin/tiff2pdf")) {
									exec("/usr/bin/tiff2pdf -f -o ".$file_name.".pdf ".$dir_fax_inbox.'/'.$file_name.".tif");
								}
						}
						//if (!file_exists($dir_fax_inbox.'/'.$file_name.".jpg")) {
						//	//convert the tif to jpg
						//		chdir($dir_fax_inbox);
						//		if (is_file("/usr/local/bin/tiff2rgba")) {
						//			exec("/usr/local/bin/tiff2rgba ".$file_name.".tif ".$dir_fax_inbox.'/'.$file_name.".jpg");
						//		}
						//		if (is_file("/usr/bin/tiff2rgba")) {
						//			exec("/usr/bin/tiff2rgba ".$file_name.".tif ".$dir_fax_inbox.'/'.$file_name.".jpg");
						//		}
						//}
						echo "<tr>\n";
						echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
						echo "	  <a href=\"v_fax_view.php?id=".$fax_id."&a=download&type=fax_inbox&t=bin&ext=".urlencode($fax_extension)."&filename=".urlencode($file)."\">\n";
						echo "    	$file_name";
						echo "	  </a>";
						echo "  </td>\n";

						echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
						if (file_exists($dir_fax_inbox.'/'.$file_name.".pdf")) {
							echo "	  <a href=\"v_fax_view.php?id=".$fax_id."&a=download&type=fax_inbox&t=bin&ext=".urlencode($fax_extension)."&filename=".urlencode($file_name).".pdf\">\n";
							echo "    	PDF";
							echo "	  </a>";
						}
						else {
							echo "&nbsp;\n";
						}
						echo "  </td>\n";

						//echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
						//if (file_exists($dir_fax_inbox.'/'.$file_name.".jpg")) {
						//	echo "	  <a href=\"v_fax_view.php?id=".$fax_id."&a=download&type=fax_inbox&t=jpg&ext=".$fax_extension."&filename=".$file_name.".jpg\" target=\"_blank\">\n";
						//	echo "    	jpg";
						//	echo "	  </a>";
						//}
						//else {
						//	echo "&nbsp;\n";
						//}
						//echo "  &nbsp;</td>\n";

						echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
						echo "		".date("F d Y H:i:s", $file_modified);
						echo "  </td>\n";

						echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
						echo "	".$file_size_bytes;
						echo "  </td>\n";

						echo "  <td valign=\"middle\" nowrap class=\"list\">\n";
						echo "    <table border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n";
						echo "      <tr>\n";
						if (permission_exists('fax_inbox_delete')) {
							echo "        <td><a href=\"v_fax_view.php?id=".$fax_id."&type=fax_inbox&a=del&fax_extension=".urlencode($fax_extension)."&filename=".urlencode($file)."\" onclick=\"return confirm('Do you really want to delete this file?')\">$v_link_label_delete</a></td>\n";
						}
						echo "      </tr>\n";
						echo "   </table>\n";
						echo "  </td>\n";
						echo "</tr>\n";
					}
				}
		}
		echo "	<tr>\n";
		echo "		<td class=\"list\" colspan=\"3\"></td>\n";
		echo "		<td class=\"list\"></td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
		echo "\n";
		echo "	<br />\n";
		echo "	<br />\n";
		echo "\n";
	}

//show the sent box
	if (permission_exists('fax_sent_view')) {
		echo "  <table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">\n";
		echo "	<tr>\n";
		echo "		<td align='left'>\n";
		echo "			<span class=\"vexpl\"><span class=\"red\"><strong>Sent</strong></span>\n";
		echo "		</td>\n";
		echo "		<td align='right'>\n";
		if ($v_path_show) {
			echo "<b>location: </b>\n";
			echo $dir_fax_sent."&nbsp; &nbsp; &nbsp;\n";
		}
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "    </table>\n";
		echo "\n";
		echo "    <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
		echo "    <tr>\n";
		echo "		<th width=\"60%\">File Name (download)</td>\n";
		echo "		<th width=\"10%\">View</td>\n";
		echo "		<th width=\"20%\">Last Modified</td>\n";
		echo "		<th width=\"10%\" nowrap>Size</td>\n";
		echo "		</tr>";

		if ($handle = opendir($dir_fax_sent)) {
			//build an array of the files in the inbox
				$i = 0;
				$files = array();
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != ".." && is_file($dir_fax_sent.'/'.$file)) {
						$file_path = $dir_fax_sent.'/'.$file;
						$modified = filemtime($file_path);
						$index = $modified.$file;
						$files[$index]['file'] = $file;
						$files[$index]['name'] = substr($file, 0, -4);
						$files[$index]['ext'] = substr($file, -3);
						//$files[$index]['path'] = $file_path;
						$files[$index]['size'] = filesize($file_path);
						$files[$index]['size_bytes'] = byte_convert(filesize($file_path));
						$files[$index]['modified'] = filemtime($file_path);
						$file_name_array[$i++] = $index;
					}
				}
				closedir($handle);
			//order the index array
				sort($file_name_array,SORT_STRING);

			//loop through the file array
				foreach($file_name_array as $i) {
					if (strtolower($files[$i]['ext']) == "tif") {
						$file = $files[$i]['file'];
						$file_name = $files[$i]['name'];
						$file_ext = $files[$i]['ext'];
						$file_modified = $files[$i]['modified'];
						$file_size_bytes = byte_convert($files[$i]['size']);

						if (!file_exists($dir_fax_sent.'/'.$file_name.".pdf")) {
							//convert the tif to pdf
								chdir($dir_fax_sent);
								if (is_file("/usr/local/bin/tiff2pdf")) {
									exec("/usr/local/bin/tiff2pdf -f -o ".$file_name.".pdf ".$dir_fax_sent.'/'.$file_name.".tif");
								}
								if (is_file("/usr/bin/tiff2pdf")) {
									exec("/usr/bin/tiff2pdf -f -o ".$file_name.".pdf ".$dir_fax_sent.'/'.$file_name.".tif");
								}
						}
						if (!file_exists($dir_fax_sent.'/'.$file_name.".jpg")) {
							//convert the tif to jpg
								//chdir($dir_fax_sent);
								//if (is_file("/usr/local/bin/tiff2rgba")) {
								//	exec("/usr/local/bin/tiff2rgba -c jpeg -n ".$file_name.".tif ".$dir_fax_sent.'/'.$file_name.".jpg");
								//}
								//if (is_file("/usr/bin/tiff2rgba")) {
								//	exec("/usr/bin/tiff2rgba -c lzw -n ".$file_name.".tif ".$dir_fax_sent.'/'.$file_name.".jpg");
								//}
						}
						echo "<tr>\n";
						echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
						echo "	  <a href=\"v_fax_view.php?id=".$fax_id."&a=download&type=fax_sent&t=bin&ext=".urlencode($fax_extension)."&filename=".urlencode($file)."\">\n";
						echo "    	$file";
						echo "	  </a>";
						echo "  </td>\n";
						echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
						if (file_exists($dir_fax_sent.'/'.$file_name.".pdf")) {
							echo "	  <a href=\"v_fax_view.php?id=".$fax_id."&a=download&type=fax_sent&t=bin&ext=".urlencode($fax_extension)."&filename=".urlencode($file_name).".pdf\">\n";
							echo "    	PDF";
							echo "	  </a>";
						}
						else {
							echo "&nbsp;\n";
						}
						echo "  </td>\n";
						//echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
						//if (file_exists($dir_fax_sent.'/'.$file_name.".jpg")) {
						//	echo "	  <a href=\"v_fax_view.php?id=".$fax_id."&a=download&type=fax_sent&t=jpg&ext=".$fax_extension."&filename=".$file_name.".jpg\" target=\"_blank\">\n";
						//	echo "    	jpg";
						//	echo "	  </a>";
						//}
						//else {
						//	echo "&nbsp;\n";
						//}
						//echo "  </td>\n";
						echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
						echo "		".date("F d Y H:i:s", $file_modified);
						echo "  </td>\n";

						echo "  <td class=\"".$rowstyle[$c]."\" ondblclick=\"list\">\n";
						echo "	".$file_size_bytes;
						echo "  </td>\n";

						echo "  <td class='' valign=\"middle\" nowrap>\n";
						echo "    <table border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n";
						echo "      <tr>\n";
						if (permission_exists('fax_sent_delete')) {
							echo "        <td><a href=\"v_fax_view.php?id=".$fax_id."&type=fax_sent&a=del&fax_extension=".urlencode($fax_extension)."&filename=".urlencode($file)."\" onclick=\"return confirm('Do you really want to delete this file?')\">$v_link_label_delete</a></td>\n";
						}
						echo "      </tr>\n";
						echo "   </table>\n";
						echo "  </td>\n";
						echo "</tr>\n";
						if ($c==0) { $c=1; } else { $c=0; }
					} //check if the file is a .tif file
				}
		}
		echo "     <tr>\n";
		echo "       <td class=\"list\" colspan=\"3\"></td>\n";
		echo "       <td class=\"list\"></td>\n";
		echo "     </tr>\n";
		echo "     </table>\n";
		echo "\n";
		echo "	<br />\n";
		echo "	<br />\n";
		echo "	<br />\n";
		echo "	<br />\n";
	}
	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

//show the footer
	require_once "includes/footer.php";
?>
