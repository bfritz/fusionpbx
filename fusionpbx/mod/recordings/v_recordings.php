<?php
/* $Id$ */
/*
	v_recordings.php
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

require_once "includes/paging.php";
require_once "includes/v_dialplan_entry_exists.php";

recording_js();

$dir_music_on_hold_8000 = $v_dir.'/sounds/music/8000/';
ini_set(max_execution_time,7200);

$orderby = $_GET["orderby"];
$order = $_GET["order"];
if (!function_exists('thorderby')) {
	//html table header order by
	function thorderby($fieldname, $columntitle, $orderby, $order) {

		$html .= "<th class='' nowrap>&nbsp; &nbsp; ";
		if (strlen($orderby)==0) {
			$html .= "<a href='?orderby=$fieldname&order=desc' title='ascending'>$columntitle</a>";
		}
		else {
		  if ($order=="asc") {
				$html .= "<a href='?orderby=$fieldname&order=desc' title='ascending'>$columntitle</a>";
		  }
		  else {
				$html .= "<a href='?orderby=$fieldname&order=asc' title='descending'>$columntitle</a>";
		  }
		}
		$html .= "&nbsp; &nbsp; </th>";

		return $html;
	}
}


if ($_GET['a'] == "download") {

	session_cache_limiter('public');

	if ($_GET['type'] = "rec") {

		if (file_exists($v_recordings_dir.'/'.base64_decode($_GET['filename']))) {
			$fd = fopen($v_recordings_dir.'/'.base64_decode($_GET['filename']), "rb");

			if ($_GET['t'] == "bin") {
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header("Content-Type: application/download");
				header("Content-Description: File Transfer");
				header('Content-Disposition: attachment; filename="'.base64_decode($_GET['filename']).'"');
			}
			else {
				$file_ext = substr($_GET['filename'], -3);
				if ($file_ext == "wav") {
				  header("Content-Type: audio/x-wav");
				}
				if ($file_ext == "mp3") {
				  header("Content-Type: audio/mp3");
				}
			}
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
			header("Content-Length: " . filesize($v_recordings_dir.'/'.base64_decode($_GET['filename'])));
			fpassthru($fd);
		}
	}

	if ($_GET['type'] = "moh") {
		if  (file_exists($dir_music_on_hold_8000.$_GET['filename'])) {
			$fd = fopen($dir_music_on_hold_8000.$_GET['filename'], "rb");
			if ($_GET['t'] == "bin") {
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header("Content-Type: application/download");
				header("Content-Description: File Transfer");
				header('Content-Disposition: attachment; filename="'.$_GET['filename'].'"');
			}
			else {
				$file_ext = substr($_GET['filename'], -3);
				if ($file_ext == "wav") {
				  header("Content-Type: audio/x-wav");
				}
				if ($file_ext == "mp3") {
				  header("Content-Type: audio/mp3");
				}
			}
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
			header("Content-Length: " . filesize($dir_music_on_hold_8000.$_GET['filename']));
			fpassthru($fd);
		}
	}

	exit;
}
else {
	//echo $v_recordings_dir.'/'.$_GET['filename'];
}


if (($_POST['submit'] == "Upload") && is_uploaded_file($_FILES['ulfile']['tmp_name'])) {

	if ($_POST['type'] == 'moh') {
		move_uploaded_file($_FILES['ulfile']['tmp_name'], $dir_music_on_hold_8000 . $_FILES['ulfile']['name']);
		$savemsg = "Uploaded file to $dir_music_on_hold_8000" . htmlentities($_FILES['ulfile']['name']);
		//system('chmod -R 744 $dir_music_on_hold_8000*');
		unset($_POST['txtCommand']);
	}
	if ($_POST['type'] == 'rec') {
		move_uploaded_file($_FILES['ulfile']['tmp_name'], $v_recordings_dir.'/' . $_FILES['ulfile']['name']);
		$savemsg = "Uploaded file to ".$v_recordings_dir."/". htmlentities($_FILES['ulfile']['name']);
		//system('chmod -R 744 $v_recordings_dir*');
		unset($_POST['txtCommand']);
	}

}


if ($_GET['act'] == "del") {

	if ($_GET['type'] == 'moh') {
		unlink($dir_music_on_hold_8000.$_GET['filename']);
		header("Location: v_recordings.php");
		exit;
	}

}

//build a list of recordings
	$config_recording_list = '|';
	$i = 0;
	$sql = "";
	$sql .= "select * from v_recordings ";
	$sql .= "where v_id = '$v_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		//$filename = $row["filename"];
		//$recordingname = $row["recordingname"];
		//$recordingid = $row["recordingid"];
		//$descr = $row["descr"];
		$config_recording_list .= $row['filename']."|";
	}
	unset ($prepstatement);

if (is_dir($v_recordings_dir.'/')) {
	if ($dh = opendir($v_recordings_dir.'/')) {
		while (($file = readdir($dh)) !== false) {

			if (filetype($v_recordings_dir.'/' . $file) == "file") {
				if (strpos($config_recording_list, "|".$file) === false) {
					//echo "The $file was not found<br/>";
					//file not found add it to the database
					$a_file = explode("\.", $file);

					$sql = "insert into v_recordings ";
					$sql .= "(";
					$sql .= "v_id, ";
					$sql .= "filename, ";
					$sql .= "recordingname, ";
					//$sql .= "recordingid, ";
					$sql .= "descr ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$v_id', ";
					$sql .= "'$file', ";
					$sql .= "'".$a_file[0]."', ";
					//$sql .= "'".guid()."', ";
					$sql .= "'auto' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					echo $sql;
					//$lastinsertid = $db->lastInsertId($id);
					unset($sql);

					//$recordingent = array();
					//$recordingent['filename'] = $file;
					//$recordingent['recordingname'] = $a_file[0];
					//$recordingent['recordingid'] = guid();
					//$recordingent['descr'] = 'Auto';

				}
				else {
					//echo "The $file was found.<br/>";
				}

			}
		}
		closedir($dh);
	}
}

require_once "includes/header.php";

	echo "<script>\n";
	echo "function EvalSound(soundobj) {\n";
	echo "  var thissound= eval(\"document.\"+soundobj);\n";
	echo "  thissound.Play();\n";
	echo "}\n";
	echo "</script>";

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";


	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "      <br>";


	echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
	echo "  <tr>\n";
	echo "    <td align='left'><p><span class=\"vexpl\"><span class=\"red\"><strong>Recordings:<br>\n";
	echo "        </strong></span>\n";
	echo "        To make a recording dial *732673 (record) or you can make a\n";
	echo "        16bit 8khz/16khz Mono WAV file then copy it to the\n";
	echo "        following directory then refresh the page to play it back.\n";
	echo "        Click on the 'Filename' to download it or the 'Recording Name' to\n";
	echo "        play the audio.\n";
	echo "        </span></p></td>\n";
	echo "  </tr>\n";
	echo "</table>";

	echo "<br />\n";

	echo "<form action=\"\" method=\"POST\" enctype=\"multipart/form-data\" name=\"frmUpload\" onSubmit=\"\">\n";
	echo "	<table border='0' width='100%'>\n";
	echo "	<tr>\n";
	echo "		<td align='left' width='50%'>\n";
	if ($v_path_show) {
		echo "<b>location:</b> \n";
		echo $v_recordings_dir.'/';
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
	echo "</div>\n";
	echo "</form>";

	$sql = "";
	$sql .= "select * from v_recordings ";
	$sql .= "where v_id = '$v_id' ";
	if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$numrows = count($result);
	unset ($prepstatement, $result, $sql);

	$rowsperpage = 10;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
	list($pagingcontrols, $rowsperpage, $var3) = paging($numrows, $param, $rowsperpage); 
	$offset = $rowsperpage * $page; 

	$sql = "";
	$sql .= "select * from v_recordings ";
	$sql .= "where v_id = '$v_id' ";
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

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr>\n";
	echo thorderby('filename', 'Filename (download)', $orderby, $order);
	echo thorderby('recordingname', 'Recording Name (play)', $orderby, $order);
	echo "<th width=\"10%\" class=\"listhdr\" nowrap>Size</th>\n";
	echo thorderby('descr', 'Description', $orderby, $order);
	echo "<td align='right' width='42'>\n";
	echo "	<a href='v_recordings_edit.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	echo "</td>\n";
	echo "<tr>\n";

	if ($resultcount == 0) { //no results
	}
	else { //received results

		foreach($result as $row) {
			//print_r( $row );
			$tmp_filesize = filesize($v_recordings_dir.'/'.$row[filename]);
			$tmp_filesize = byte_convert($tmp_filesize);

			echo "<tr >\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>";
			echo "		<a href=\"v_recordings.php?a=download&type=rec&t=bin&filename=".base64_encode($row[filename])."\">\n";
			echo $row[filename];
			echo "	  </a>";
			echo "	</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>";
			echo "	  <a href=\"javascript:void(0);\" onclick=\"window.open('v_recordings_play.php?a=download&type=moh&filename=".$row[filename]."', 'play',' width=420,height=40,menubar=no,status=no,toolbar=no')\">\n";
			echo $row[recordingname];
			echo "	  </a>";
			echo 	"</td>\n";
			echo "	<td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
			echo "	".$tmp_filesize;
			echo "	</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."' width='30%'>".$row[descr]."</td>\n";
			echo "	<td valign='top' align='right'>\n";
			echo "		<a href='v_recordings_edit.php?id=".$row[recording_id]."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' border='0' alt='edit'></a>\n";
			echo "		<a href='v_recordings_delete.php?id=".$row[recording_id]."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\"><img src='".$v_icon_delete."' width='17' height='17' border='0' alt='delete'></a>\n";
			echo "	</td>\n";
			echo "</tr>\n";

			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='5'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$pagingcontrols</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	echo "			<a href='v_recordings_edit.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "</div>";


	echo "    <br />\n";
	echo "    <br />\n";
	echo "    <br />\n";
	echo "    <br />\n";
	echo "\n";
	
	echo "  	<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
	echo "      <tr>\n";
	echo "        <td align='left'><p><span class=\"vexpl\"><span class=\"red\"><strong>Music on Hold:<br>\n";
	echo "            </strong></span>\n";
	echo "            Music on hold can be in WAV or MP3 format. To play an MP3 file you must have\n";
	echo "            mod_shout enabled on the 'Modules' tab. You can adjust the volume of the MP3\n";
	echo "            audio from the 'Settings' tab. For best performance upload 16bit 8khz/16khz Mono WAV files.\n";
	//echo "            <!--Click on the 'Filename' to download it or the 'Recording Name' to\n";
	//echo "            play the audio.-->\n";
	echo "            </span></p></td>\n";
	echo "      </tr>\n";
	echo "    </table>\n";
	echo "\n";
	echo "    <br />\n";
	echo "\n";
	echo "  	<form action=\"\" method=\"POST\" enctype=\"multipart/form-data\" name=\"frmUpload\" onSubmit=\"\">\n";
	echo "  	<table width='100%' border='0'>\n";
	echo "  		<tr>\n";
	echo "  		<td align='left' width='50%'>";

	if ($v_path_show) {
		echo "<b>location:</b> ";
		echo $dir_music_on_hold_8000;
	}

	echo "			</td>\n";
	echo "			<td valign=\"top\" class=\"label\">\n";
	echo "				<input name=\"type\" type=\"hidden\" value=\"moh\">\n";
	echo "			</td>\n";
	echo "  		<td valign=\"top\" align='right' class=\"label\" nowrap>\n";
	echo "  			File to upload:\n";
	echo "  			<input name=\"ulfile\" type=\"file\" class=\"button\" id=\"ulfile\">\n";
	echo "  			<input name=\"submit\" type=\"submit\"  class=\"btn\" id=\"upload\" value=\"Upload\">\n";
	echo "  		</td>\n";
	echo "  		</tr>\n";
	echo "  	</table>\n";
	echo "  	</form>\n";
	echo "\n";
	echo "\n";
	echo "	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<th width=\"30%\" class=\"listhdrr\">File Name (download)</th>\n";
	echo "		<th width=\"30%\" class=\"listhdrr\">Name (play)</th>\n";
	echo "		<th width=\"30%\" class=\"listhdr\">Last Modified</th>\n";
	echo "		<th width=\"10%\" class=\"listhdr\" nowrap>Size</th>\n";
	echo "		<td width='22px' class=\"\" nowrap>&nbsp;</td>\n";	
	echo "		</tr>";

	if ($handle = opendir($dir_music_on_hold_8000)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && is_file($dir_music_on_hold_8000.$file)) {

				$tmp_filesize = filesize($dir_music_on_hold_8000.$file);
				$tmp_filesize = byte_convert($tmp_filesize);

				echo "<tr>\n";
				echo "	<td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
				echo "		<a href=\"v_recordings.php?a=download&type=moh&t=bin&filename=".$file."\">\n";
				echo "		$file";
				echo "		</a>";
				echo "	</td>\n";
				echo "	<td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
				echo "	  <a href=\"javascript:void(0);\" onclick=\"window.open('v_recordings_play.php?a=download&type=moh&filename=".$file."', 'play',' width=420,height=40,menubar=no,status=no,toolbar=no')\">\n";
				$tmp_file_array = explode("\.",$file);
				echo "    	".$tmp_file_array[0];
				echo "	  </a>";
				echo "  </td>\n";
				echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
				echo 		date ("F d Y H:i:s", filemtime($dir_music_on_hold_8000.$file));
				echo "  </td>\n";
				echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
				echo "	".$tmp_filesize;
				echo "  </td>\n";
				echo "  <td valign=\"middle\" width='22' nowrap class=\"list\">\n";
				echo "    <table border=\"0\" cellspacing=\"0\" cellpadding=\"5\">\n";
				echo "      <tr>\n";
				//echo "        <td valign=\"middle\"><a href=\"v_recordings.php?id=$i\"><img src=\"/themes/".$g['theme']."/images/icons/icon_e.gif\" width=\"17\" height=\"17\" border=\"0\"></a></td>\n";
				echo "        <td><a href=\"v_recordings.php?type=moh&act=del&filename=".$file."\" onclick=\"return confirm('Do you really want to delete this file?')\"><img src=\"".$v_icon_delete."\" width=\"17\" height=\"17\" border=\"0\"></a></td>\n";
				echo "      </tr>\n";
				echo "   </table>\n";
				echo "  </td>\n";
				echo "</tr>\n";
				if ($c==0) { $c=1; } else { $c=0; }

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
	echo "<br>\n";
	echo "<br>\n";
	echo "<br>\n";
	echo "<br>\n";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br><br>";


require_once "includes/footer.php";
unset ($resultcount);
unset ($result);
unset ($key);
unset ($val);
unset ($c);
?>
