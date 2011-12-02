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
	James Rose <james.o.rose@gmail.com>
*/
include "root.php";
require_once "includes/config.php";
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
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

if (permission_exists('log_download')) {
	if ($_GET['a'] == "download") {
		if ($_GET['t'] == "logs") {
			$tmp = $v_log_dir.'/';
			$filename = $v_name.'.log';
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
echo "<tr>\n";
echo "<td align=\"left\" width='100%'>\n";
echo "	<b>Log Viewer</b><br />\n";
echo "</td>\n";
echo "<td width='50%' align='right'>\n";
if (permission_exists('log_download')) {
	echo "  <input type='button' class='btn' value='download logs' onclick=\"document.location.href='v_log_viewer.php?a=download&t=logs';\" />\n";
}
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td colspan='2'>";

if (permission_exists('log_view')) {

	$MAXEL = 3; //pattern2, pattern3|color2, color3 etc...

	$user_filesize = '0';
	$default_color = 'white';
	$default_type = 'normal';
	$default_font = 'monospace';
	$background_color = 'black';
	$default_fsize = '512000';
	$log_file = "$v_log_dir/$v_name.log";

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

	if (isset($_POST['submit'])) {
		if (strlen($_POST['fs']) == 0) { $_POST['fs'] = "512"; }
	}

	echo "<table style=\"width: 100%\;\" width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
	echo "	<tbody><tr><th colspan=\"2\" style=\"text-alight: left\;\">Adjust Log Display</th></tr>\n";
	echo "<tr><td style=\"text-align: left;\" class=\"rowstylebg\" width=\"50%\">\n";

	echo 'Log File Size: ' . $file_size . ' bytes<br>';

	//user input here.
	echo "Set a number the input box to get the last Kilobytes of the log file.<br>\n";
	echo "</td>\n";
	echo "<td style=\"text-align: left;\" class=\"vtable;\" width=\"50%\" valign=\"bottom\">\n";
	echo "	<br />\n";
	echo "	<form action=\"v_log_viewer.php\" method=\"POST\">\n";
	echo "		<input type=\"text\" class=\"formfld\" name=\"fs\" value=\"".$_POST['fs']."\">\n";
	echo "		<input type=\"submit\" class=\"btn\" name=\"submit\" value=\"reload\">\n";
	echo "	</form>\n";
	echo "</td>\n";
	echo "</tr></table><br>";

	echo "<table style=\"width: 100%\;\" width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">";
	echo "<tbody><tr><th colspan=\"2\" style=\"text-alight: left\;\">Syntax Highlighted</th></tr>";
	echo "<tr><td style=\"text-align: left;\" class=\"rowstylebg\">";

	$user_filesize = '512000';
	if (isset($_POST['submit'])) {
		if (strlen($_POST['fs']) == 0) { $_POST['fs'] = "512"; }
		if (!is_numeric($_POST['fs'])){
			echo "<font color=\"red\" face=\"bold\" size =\"5\">";
			echo "Just what do you think you're doing, Dave?<br>";
			echo "</font>";
			//should generate log warning here...
			$user_filesize='1000';
		}
		else {
			$user_filesize = $_POST['fs'] * 1000;
		}
	}
	echo "Viewing the last " . $user_filesize . " bytes of the log.<br><HR>";

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
				//just open the file
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
			//just open the file
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

				echo "<font color=\"{$v1[color]}\" face=\"{$v1[font]}\">" ;
				/* testing to see if style is what crashes firefox on large logfiles...
				echo "<p style=\"font-weight: {$v1[type]};
				color: {$v1[color]};
				font-family:{$v1[font]};\">";*/
				echo $log_line;
				echo "</font><br>";
				$noprint = true;
			}
		}
		if ($noprint !== true){
			//more firefox workaround...
			//echo "<p style=\"background-color:$background_color;color:$default_color;font-wieght:$default_type;font-family:$default_font\">";
			echo "<font color=\"$default_color\" face=\"$default_font\">" ;
			echo $log_line;
			//echo "</p>";	
			echo "</font><br>";
		}
	}

	fclose($file);
	echo "</tr></td>";
}

echo "</table>\n";
echo "</div>\n";

require_once "includes/footer.php";

?>
