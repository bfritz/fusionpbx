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

//http get and set variables
	$event_type = $_GET['event_type']; //open_window //iframe
	if ($event_type=="iframe") {
		$iframe_width = $_GET['iframe_width'];
		$iframe_height = $_GET['iframe_height'];
		$iframe_postition = $_GET['iframe_postition'];
		if (strlen($iframe_postition) > 0) { $iframe_postition = 'right'; }
		if (strlen($iframe_width) > 0) { $iframe_width = '25%'; }
		if (strlen($iframe_height) > 0) { $iframe_height = '100%'; }
	}
	if (strlen($_GET['url']) > 0) {
		$url = $_GET['url'];
	}
	if (strlen($_GET['rows']) > 0) {
		$rows = $_GET['rows'];
	}
	else {
		$rows = 0;
	}

$conference_name = trim($_REQUEST["c"]);
$tmp_conference_name = str_replace("_", " ", $conference_name);

require_once "includes/header.php";
?>

<script type="text/javascript">
<!--

//declare variables
var previous_uuid_1 = '';
var previous_uuid_2 = '';
var url = '<?php echo $url; ?>';

//define the ajax function
function loadXmlHttp(url, id) {
	var f = this;
	f.xmlHttp = null;
	/*@cc_on @*/ // used here and below, limits try/catch to those IE browsers that both benefit from and support it
	/*@if(@_jscript_version >= 5) // prevents errors in old browsers that barf on try/catch & problems in IE if Active X disabled
	try {f.ie = window.ActiveXObject}catch(e){f.ie = false;}
	@end @*/
	if (window.XMLHttpRequest&&!f.ie||/^http/.test(window.location.href))
		f.xmlHttp = new XMLHttpRequest(); // Firefox, Opera 8.0+, Safari, others, IE 7+ when live - this is the standard method
	else if (/(object)|(function)/.test(typeof createRequest))
		f.xmlHttp = createRequest(); // ICEBrowser, perhaps others
	else {
		f.xmlHttp = null;
		 // Internet Explorer 5 to 6, includes IE 7+ when local //
		/*@cc_on @*/
		/*@if(@_jscript_version >= 5)
		try{f.xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");}
		catch (e){try{f.xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");}catch(e){f.xmlHttp=null;}}
		@end @*/
	}
	if(f.xmlHttp != null){
		f.el = document.getElementById(id);
		f.xmlHttp.open("GET",url,true);
		f.xmlHttp.onreadystatechange = function(){f.stateChanged();};
		f.xmlHttp.send(null);
	}
}

loadXmlHttp.prototype.stateChanged=function () {
if (this.xmlHttp.readyState == 4 && (this.xmlHttp.status == 200 || !/^http/.test(window.location.href)))
	//this.el.innerHTML = this.xmlHttp.responseText;
	document.getElementById('ajax_reponse').innerHTML = this.xmlHttp.responseText;

	uuid_1 = document.getElementById('uuid_1').innerHTML;
	direction_1 = document.getElementById('direction_1').innerHTML;
	cid_name_1 = document.getElementById('cid_name_1').innerHTML;
	cid_num_1 = document.getElementById('cid_num_1').innerHTML;


	if (previous_uuid_1 != uuid_1) {
		if (uuid_1.length > 0) {
			if (direction_1 == "outbound") {
				//$url = "http://fusionpbx.com/?cid_name={cid_name}&cid_num={cid_num}&uuid={uuid}";
				//echo urlencode($url);

				//alert('new call: '+uuid_1+'\n direction: '+direction_1+'\n cid_name: '+cid_name_1+'\n cid_num: '+cid_num_1+'\n url: '+url);
				var new_url = url;
				new_url = new_url.replace("{cid_name}", cid_name_1);
				new_url = new_url.replace("{cid_num}", cid_num_1);
				new_url = new_url.replace("{uuid}", uuid_1);

				previous_uuid_1 = uuid_1;
<?php 
				if ($event_type=="open_window") {
					echo "open_window = window.open(new_url,'_blank','width='+window.innerWidth+',height='+window.innerHeight+',left=0px;toolbar=yes,location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,copyhistory=yes,resizable=yes');";
					echo "if (window.focus) {open_window.focus()}\n";
				}
				if ($event_type=="iframe") {
					echo "document.getElementById('iframe1').src = new_url;\n";
					//iframe_postition
					//iframe_width
					//iframe_height
				}
?>
			}
		}
		else {
			//hangup or initial page load detected
		}
		previous_uuid_1 = uuid_1;
	}
}

var requestTime = function() {
	var url = 'v_calls_active_extensions_inc.php?<?php echo $_SERVER["QUERY_STRING"]; ?>';
	new loadXmlHttp(url, 'ajax_reponse');
	setInterval(function(){new loadXmlHttp(url, 'ajax_reponse');}, 750);
}

if (window.addEventListener) {
	window.addEventListener('load', requestTime, false);
}
else if (window.attachEvent) {
	window.attachEvent('onload', requestTime);
}

function send_cmd(url) {
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("GET",url,false);
	xmlhttp.send(null);
	document.getElementById('cmd_reponse').innerHTML=xmlhttp.responseText;
}

var record_count = 0;
var cmd;
var destination;
// -->
</script>

<?php

echo "<div align='center'>";

echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
echo "	<tr>\n";
echo "	<td align='left'><b>Active Extensions</b><br>\n";
echo "		Use this to view all extensions and monitor and interact with active calls.\n";
echo "	</td>\n";
echo "	<td align='right'>\n";

echo "		<table>\n";
echo "		<td align='left' valign='middle'>\n";
echo "			<div id=\"form_label\"><strong>Transfer To</strong></div>\n";
echo "			<div id=\"url\"></div>\n";
echo "		</td>\n";
echo "		<td align='left' valign='middle'>\n";
echo "			<input type=\"text\" id=\"form_value\" name=\"form_value\" />\n";
echo "		</td>\n";
echo "		</tr>\n";
echo "		</table>\n";

echo "	</td>\n";
echo "	</tr>\n";
echo "</table>\n";

echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
echo "	<tr class='border'>\n";
if ($event_type=="iframe") {
	echo "	<td align=\"left\" width='".$iframe_width."'>\n";
}
else {
	echo "	<td align=\"left\" width='100%'>\n";
}
echo "		<div id=\"ajax_reponse\"></div>\n";
echo "		<div id=\"time_stamp\" style=\"visibility:hidden\">".date('Y-m-d-s')."</div>\n";
echo "	</td>\n";

if ($event_type=="iframe") {
	echo "</td>\n";
	echo "<td width='".$iframe_width."' height='".$iframe_height."'>\n";
	echo "	<iframe src ='$url' width='100%' id='iframe1' height='100%' frameborder=0>\n";
	echo "		<p>Your browser does not support iframes.</p>\n";
	echo "	</iframe>\n";
	echo "</td>\n";
}

echo "	</tr>";
echo "</table>";

echo "</div>\n";

echo "<script type=\"text/javascript\">\n";
echo "<!--\n";
echo "function get_transfer_cmd(uuid) {\n";
echo "	destination = document.getElementById('form_value').value;\n";
echo "	cmd = \"uuid_transfer \"+uuid+\" -bleg \"+destination+\" xml default\";\n";
echo "	return escape(cmd);\n";
echo "}\n";
echo "\n";
echo "function get_park_cmd(uuid) {\n";
echo "	cmd = \"uuid_transfer \"+uuid+\" -bleg *5900 xml default\";\n";
echo "	return escape(cmd);\n";
echo "}\n";
echo "\n";
echo "function get_record_cmd(uuid, prefix, name) {\n";
echo "	cmd = \"uuid_record \"+uuid+\" start ".$v_recordings_dir."/\"+prefix+\"\"+name+\"_recording.wav\";\n";
echo "	return escape(cmd);\n";
echo "}\n";
echo "-->\n";
echo "</script>\n";

require_once "includes/footer.php";
?>
