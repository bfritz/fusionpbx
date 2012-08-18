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
	James Rose <james.o.rose@gmail.com>
*/
include "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists('log_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//define variables
	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

//set a default line number value (off)
	if ($_POST['ln'] == '') { $_POST['ln'] = 0; }

//set a default ordinal (descending)
	if ($_POST['ord'] == '') { $_POST['ord'] = "asc"; }

//set a default file size
	if (strlen($_POST['fs']) == 0) { $_POST['fs'] = "32"; }

if (permission_exists('log_download')) {
	if ($_GET['a'] == "download") {
		if ($_GET['t'] == "logs") {
			$tmp = $_SESSION['switch']['log']['dir'].'/';
			$filename = 'freeswitch.log';
		}
		session_cache_limiter('public');
		$fd = fopen($tmp.$filename, "rb");
		header("Content-Type: binary/octet-stream");
		header("Content-Length: " . filesize($tmp.$filename));
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		fpassthru($fd);
		exit;
	}
}

require_once "includes/header.php";

echo "<br />\n";
echo "<div align='center'>\n";

echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
echo "	<tr>\n";
echo "		<td align=\"left\" valign='middle' width='100%'>\n";
echo "			<b>Log Viewer</b><br />\n";
echo "		</td>\n";
echo "		<form action=\"log_viewer.php\" method=\"POST\">\n";
echo "		<td align='right' valign='middle' nowrap>\n";
echo "				<label for='ln' style='margin-right: 30px;'><input type='checkbox' name='ln' id='ln' value='1' ".(($_POST['ln'] == 1) ? 'checked' : null)."> Show Line Numbers</label>";
echo "				<label for='ord' style='margin-right: 30px;'><input type='checkbox' name='ord' id='ord' value='desc' ".(($_POST['ord'] == 'desc') ? 'checked' : null)."> Sort Descending</label>";
echo "				Display <input type=\"text\" class=\"formfld\" style=\"width: 50px; text-align: center;\" name=\"fs\" value=\"".$_POST['fs']."\" onclick=\"this.select();\"> KB";
echo "				<input type=\"submit\" class=\"btn\" style=\"margin-left: 30px;\" name=\"submit\" value=\"reload\">";
echo "		</td>\n";
echo "		</form>\n";
echo "		<td width='125' align='right' valign='middle' nowrap='nowrap'>\n";
if (permission_exists('log_download')) {
	echo "			<input type='button' class='btn' value='download logs' onclick=\"document.location.href='log_viewer.php?a=download&t=logs';\" />\n";
}
echo "		</td>\n";
echo "	</tr>\n";
echo "	<tr>\n";
echo "		<td colspan='3'>";

if (permission_exists('log_view')) {

	$MAXEL = 3; //pattern2, pattern3|color2, color3 etc...

	$user_filesize = '0';
	$default_color = 'white';
	$default_type = 'normal';
	$default_font = 'monospace';
	$background_color = 'black';
	$default_fsize = '512000';
	$log_file = $_SESSION['switch']['log']['dir']."/freeswitch.log";

	//put the color matches here...
	$arr_filter[0]['pattern'] = '[NOTICE]';
	$arr_filter[0]['color'] = 'cyan';
	$arr_filter[0]['type'] = 'normal';
	$arr_filter[0]['font'] = 'monospace';

	$arr_filter[1]['pattern'] = '[INFO]';
	$arr_filter[1]['color'] = 'chartreuse';
	$arr_filter[1]['type'] = 'normal';
	$arr_filter[1]['font'] = 'monospace';

	$arr_filter[2]['pattern'] = 'Dialplan:';
	$arr_filter[2]['color'] = 'burlywood';
	$arr_filter[2]['type'] = 'normal';
	$arr_filter[2]['font'] = 'monospace';
	$arr_filter[2]['pattern2'] = 'Regex (PASS)';
	$arr_filter[2]['color2'] = 'chartreuse';
	$arr_filter[2]['pattern3'] = 'Regex (FAIL)';
	$arr_filter[2]['color3'] = 'red';

	$arr_filter[3]['pattern'] = '[WARNING]';
	$arr_filter[3]['color'] = 'fuchsia';
	$arr_filter[3]['type'] = 'normal';
	$arr_filter[3]['font'] = 'monospace';

	$arr_filter[4]['pattern'] = '[ERR]';
	$arr_filter[4]['color'] = 'red';
	$arr_filter[4]['type'] = 'bold';
	$arr_filter[4]['font'] = 'monospace';

	$arr_filter[5]['pattern'] = '[DEBUG]';
	$arr_filter[5]['color'] = 'gold';
	$arr_filter[5]['type'] = 'bold';
	$arr_filter[5]['font'] = 'monospace';

	$file_size = filesize($log_file);

	/*
	// removed: duplicate of above
	if (isset($_POST['submit'])) {
		if (strlen($_POST['fs']) == 0) { $_POST['fs'] = "32"; }
	}
	*/

	echo "<table style=\"width: 100%\;\" width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">";
	echo "<tbody><tr><th colspan=\"2\" style=\"text-alight: left\;\">Syntax Highlighted</th></tr>";
	echo "<tr><td style=\"text-align: left;\" class=\"row_stylebg\">";

	$user_filesize = '32768';
	if (isset($_POST['submit'])) {
		if (!is_numeric($_POST['fs'])){
			echo "<font color=\"red\" face=\"bold\" size =\"5\">";
			echo "Just what do you think you're doing, Dave?<br>";
			echo "</font>";
			//should generate log warning here...
			$user_filesize='1000';
		}
		else {
			$user_filesize = $_POST['fs'] * 1024;
		}
	}

	//echo "Log File Size: " . $file_size . " bytes. <br />";
	echo "<div align='right'>Displaying the last " . number_format($user_filesize,0,'.',',') . " of " . number_format($file_size,0,'.',',') . " bytes. </div><br><hr>";

	$file = fopen($log_file, "r") or exit("Unable to open file!");

	//set pointer in file
	if ($user_filesize >= '0') {
		if ($user_filesize == '0'){
			$user_filesize = $default_fsize;
		}
		if ( $file_size >= $user_filesize ){
			//set an offset on fopen
			$bytecount=$file_size-$user_filesize;
			fseek($file, $bytecount);
			//echo "opening at " . $bytecount . " bytes<br>";
		}
		else {
			if ( $file_size >= $default_fsize ){
				//set an offset on fopen
				$bytecount=$file_size-$default_fsize;
				fseek($file, $bytecount);
				echo "opening at " . $bytecount . " bytes<br>";
		}
			else {
				//open the file
				$bytecount='0';
				fseek($file, 0);
				echo "<br>opening entire file<br>";
			}
		}
	}
	else {
		if ( $file_size >= $default_fsize ){
			//set an offset on fopen
			$bytecount=$file_size-$default_fsize;
			fseek($file, $bytecount);
			echo "opening at " . $bytecount . " bytes<br>";
		}
		else {
			//open the file
			$bytecount='0';
			fseek($file, 0);
			echo "<br>opening entire file<br>";
		}
	}

	//start processing
	while(!feof($file))
	{
		$log_line = fgets($file);
		$byte_count++;
		$noprint = false;
		foreach ($arr_filter as $v1) {
			$pos = strpos($log_line, $v1['pattern']);
			//echo "</br> POS is: '$pos'</br>";
			if ($pos !== false){
				//color adjustments on words in log line
				for ($i=2; $i<=$MAXEL; $i++){
					if (isset ($v1["pattern".$i])){
						$log_line = str_replace($v1["pattern".$i], "<font color=\"{$v1["color".$i]}\">{$v1["pattern".$i]}</font>", $log_line);
					}
				}

				$ary_output[] = "<font color=\"{$v1[color]}\" face=\"{$v1[font]}\">".$log_line."</font><br>";
				$noprint = true;
			}
		}

		if ($noprint !== true){
			//more firefox workaround...
			//echo "<p style=\"background-color:$background_color;color:$default_color;font-wieght:$default_type;font-family:$default_font\">";

			$ary_output[] = "<font color=\"$default_color\" face=\"$default_font\">".$log_line."</font><br>";
		}
	}

	// output according to ordinal selected
	if ($_POST['ord'] == 'desc') {
		$ary_output = array_reverse($ary_output);
		$adj_index = 0;
	}
	else {
		$adj_index = 1;
	}
	foreach ($ary_output as $index => $line) {
		if ($line != '<font color="white" face="monospace"></font><br>') {
			if ($_POST['ln']) {
				$line_num = "<span style='font-family: courier; color: #aaa; font-size: 10px;'>".($index + $adj_index)."&nbsp;&nbsp;&nbsp;</span>";
			}
			echo $line_num." ".$line;
		}
	}

	fclose($file);
	echo "</tr></td>";
}

echo "</table>\n";
echo "</div>\n";

require_once "includes/footer.php";

?>
