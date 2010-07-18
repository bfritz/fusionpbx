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
}

var requestTime = function() {
	var url = 'v_calls_active_extensions_inc.php?rows=<?php echo $rows; ?>&url=<?php echo $url; ?>&c=<?php echo trim($_REQUEST["c"]); ?>';
	new loadXmlHttp(url, 'ajax_reponse');
	setInterval(function(){new loadXmlHttp(url, 'ajax_reponse');}, 1222);
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
echo "	<td align=\"left\">\n";
echo "		<div id=\"ajax_reponse\"></div>\n";
echo "		<div id=\"time_stamp\" style=\"visibility:hidden\">".date('Y-m-d-s')."</div>\n";
echo "	</td>";
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
