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
if (ifgroup("agent") || ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//get the queue_name and set it as a variable
	$queue_name = $_GET[queue_name];

//get the header
	require_once "includes/header.php";
?><script type="text/javascript">
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
	document.getElementById('ajax_response').innerHTML = this.xmlHttp.responseText;
}

var requestTime = function() {
	var url = 'v_call_center_active_inc.php?queue_name=<?php echo $queue_name.'@'.$v_domain; ?>';
	new loadXmlHttp(url, 'ajax_response');
	setInterval(function(){new loadXmlHttp(url, 'ajax_response');}, 1777);
}

if (window.addEventListener) {
	window.addEventListener('load', requestTime, false);
}
else if (window.attachEvent) {
	window.attachEvent('onload', requestTime);
}

function send_cmd(url) {
	if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("GET",url,false);
	xmlhttp.send(null);
	//document.getElementById('cmd_response').innerHTML=xmlhttp.responseText;
}

</script>

<?php

echo "<script type=\"text/javascript\" language=\"JavaScript\">\n";
echo "\n";
echo "function show_div(div_id) {\n";
//echo "	document.getElementById(\"zzz\").innerHTML='';\n";
echo "	aodiv = document.getElementById(div_id);\n";
echo "	if (aodiv) {\n";
echo "		aodiv.style.display = \"block\";\n";
echo "	}\n";
echo "}\n";
echo "\n";
echo "function hide_div(div_id) {\n";
//echo "	document.getElementById(\"zzz\").innerHTML='';\n";
echo "	aodiv = document.getElementById(div_id);\n";
echo "	if (aodiv) {\n";
echo "		aodiv.style.display = \"none\";\n";
echo "	}\n";
echo "}\n";
echo "</script>";

echo "<div align='center'>";

echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
echo "	<tr>\n";
echo "	<td valign='top' align='left'><b>Call Center</b><br />\n";
echo "		List the call center queue information.<br />\n";
echo "	</td>\n";
echo "	<td valign='top' align='right'>\n";
echo "		<table width='100%' border='0'>\n";
echo "			<tr>\n";

//get the xml config
	$tmp_file =  $v_conf_dir.'/autoload_configs/callcenter.conf.xml';
	$xml_string = file_get_contents($tmp_file);

//parse the xml to get the call detail record info
	try {
		$xml = simplexml_load_string($xml_string);
	}
	catch(Exception $e) {
		echo $e->getMessage();
	}

//get the variables from the xml and create an xml list
	if (strlen($queue_name) == 0) {
		echo "				<td align='right' valign='middle'>\n";
		echo "					Queue &nbsp; \n";
		echo "				</td>\n";
		echo "				<td align='left' valign='bottom'>\n";
		echo "<form method='get' name='frm_queue' action=''>\n";
		echo "<select id='queue_name' name='queue_name' class='formfld' style='width:200px;' onchange='this.form.submit();'>\n";
		echo "<option value=''></option>\n";
		$x = 0;
		foreach ($xml->queues->queue as $row) {
			$xml_queue_name = ($row->attributes()->name);
			$xml_queue_name_array = explode('@', $xml_queue_name);
			if ($xml_queue_name == $queue_name) {
				echo "<option value='".$xml_queue_name_array[0]."' selected='selected'>".$xml_queue_name_array[0]."</option>\n";
			}
			else {
				echo "<option value='".$xml_queue_name_array[0]."'>".$xml_queue_name_array[0]."</option>\n";
			}
		}
		unset($x);
		echo "</select>\n";
		echo "</form>\n";
	}
	else {
		echo "		<input type=\"hidden\" id=\"queue_name\" name=\"queue_name\" class='formfld' value=\"".$queue_name."\"/>\n";
	}

echo "				</td>\n";
echo "				<td width='50%;'>\n";
echo "					&nbsp; \n";
echo "				</td>\n";
echo "				<td align='right' valign='top'>\n";
echo "					<div id=\"div_btn_agent\"><input type=\"button\" class='btn' onClick=\"hide_div('div_tier');show_div('div_agent');show_div('div_hide_agent');hide_div('div_btn_agent');show_div('div_hide_agent');\" value=\"Show Agents\"/></div>\n";
echo "					<div id=\"div_hide_agent\" style=\"display:none\"><input type=\"button\" class='btn' onClick=\"hide_div('div_agent');hide_div('div_tier');hide_div('div_hide_agent');hide_div('div_btn_agent');show_div('div_btn_agent');\" value=\"Hide Agents\"/></div>\n";
echo "				</td>\n";
if (ifgroup("admin") || ifgroup("superadmin")) {
	echo "				<td align='right' valign='top'>\n";
	echo "					<div id=\"div_btn_tier\"><input type=\"button\" class='btn' onClick=\"hide_div('div_agent');show_div('div_tier');show_div('div_hide_tier');hide_div('div_btn_tier');\" value=\"Show Tiers\"/></div>\n";
	echo "					<div id=\"div_hide_tier\" style=\"display:none\"><input type=\"button\" class='btn' onClick=\"hide_div('div_agent');hide_div('div_tier');hide_div('div_hide_tier');show_div('div_btn_tier');\" value=\"Hide Tiers\"/></div>\n";
	echo "				</td>\n";
}
echo "			</tr>\n";
echo "		</table>\n";
echo "	</td>\n";
echo "	</tr>\n";
echo "	<tr>\n";
echo "	<td valign='top' align='left' colspan='2'>\n";

	//add an agent
	echo "	<div id=\"div_agent\" style=\"display:none\">\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td align='left' valign='middle'>\n";
	echo "			<div id=\"form_label\"><strong>Agent</strong> </div>\n";
	echo "		</td>\n";
	echo "	<tr>\n";

		echo "	<tr>\n";
		echo "		<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "			Agent:\n";
		echo "		</td>\n";
		echo "		<td class='vtable' align='left'>\n";
		if (ifgroup("admin") || ifgroup("superadmin")) {
			//---- Begin Select List --------------------
			$sql = "SELECT * FROM v_users ";
			$sql .= "where v_id = '$v_id' ";
			$prepstatement = $db->prepare(check_sql($sql));
			$prepstatement->execute();

			echo "<select id=\"agent_name\" name=\"agent_name\" class='formfld'>\n";
			echo "<option value=\"\"></option>\n";
			$result = $prepstatement->fetchAll();
			//$catcount = count($result);
			foreach($result as $field) {
				echo "<option value='".$field[username]."'>".$field[username]."</option>\n";
			}
			echo "</select>";
			unset($sql, $result);
			//---- End Select List --------------------
		}
		else {
			if (ifgroup("agent")) {
				echo "		".$_SESSION['username']."\n";
				echo "		<input type=\"hidden\" id=\"agent_name\" name=\"agent_name\" class='formfld' value=\"".$_SESSION['username']."\"/>\n";
			}
		}
		echo "			<br />\n";
		if (ifgroup("admin") || ifgroup("superadmin")) {
			echo "			Select the agent username.\n";
		}
		echo "		</td>\n";
		echo "	</tr>\n";

	echo "	<tr>\n";
	echo "		<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "			Status:\n";
	echo "		</td>\n";
	echo "		<td class='vtable' align='left'>\n";
	echo "			<select id='agent_status' name='agent_status' class='formfld'>\n";
	echo "				<option value=''></option>\n";
	echo "				<option value='Available'>Available</option>\n";
	echo "				<option value='Available+(On+Demand)'>Available (On Demand)</option>\n";
	echo "				<option value='Logged+Out'>Logged Out</option>\n";
	echo "				<option value='On+Break'>On Break</option>\n";
	echo "			</select>\n";
	echo "			<br />\n";
	echo "			Select the agent status.\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	if (ifgroup("admin") || ifgroup("superadmin")) {
		echo "	<tr>\n";
		echo "		<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "			Contact:\n";
		echo "		</td>\n";
		echo "		<td class='vtable' align='left'>\n";

		//switch_select_destination(select_type, select_label, select_name, select_value, select_style, action);
		switch_select_destination("call_center_contact", "", "agent_contact", $agent_contact, "", "");

		echo "			<br />\n";
		echo "			Select the contact number.<br />\n";
		echo "		</td>\n";
		echo "	</tr>\n";

		echo "	<tr>\n";
		echo "		<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "			Call Timeout:\n";
		echo "		</td>\n";
		echo "		<td class='vtable' align='left'>\n";
		echo "			<input type=\"text\" id=\"agent_call_timeout\" name=\"agent_call_timeout\" class='formfld' value=\"10\"/>\n";
		echo "			<br />\n";
		echo "			Enter the call timeout.<br >\n";
		echo "		</td>\n";
		echo "	</tr>\n";
	}

	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";

	echo "<script type='text/javascript'>\n";
	echo "	function agent_add() {\n";
	echo "		agent_name = document.getElementById('agent_name').value;\n";
	echo "		username = agent_name;\n";
	echo "		agent_name = agent_name+'@".$v_domain."';\n";
	echo "		agent_status = document.getElementById('agent_status').value;\n";
	if (ifgroup("admin") || ifgroup("superadmin")) {
		echo "		agent_contact = document.getElementById('agent_contact').value;\n";
		echo "		agent_contact = agent_contact+'@".$v_domain."';\n";
		echo "		agent_call_timeout = document.getElementById('agent_call_timeout').value;\n";
		echo "		agent_call_timeout = '[call_timeout='+agent_call_timeout+']';\n";
	}

	//add the agent
		if (ifgroup("admin") || ifgroup("superadmin")) {
			echo "		agent_add_str = 'callcenter_config+agent+add+'+agent_name+'+callback';\n";
			echo "		send_cmd('v_call_center_exec.php?cmd='+agent_add_str);\n";
			echo "\n";
		}

	//set the contact number
		if (ifgroup("admin") || ifgroup("superadmin")) {
			echo "		agent_set_str_1 = 'callcenter_config+agent+set+contact+'+agent_name+'+'+agent_call_timeout+agent_contact;\n";
			echo "		send_cmd('v_call_center_exec.php?cmd='+agent_set_str_1);\n";
			echo "\n";
		}

	//set the agent status
		echo "		agent_set_str_2 = \"callcenter_config+agent+set+status+\"+agent_name+\"+'\"+agent_status+\"'\";\n";
		echo "		result = send_cmd(\"v_call_center_exec.php?action=user_status&data='\"+agent_status+\"'&username=\"+username+\"&cmd=\"+agent_set_str_2);\n";
		echo "\n";

	echo "	}\n";
	echo "</script>\n";
	echo "			<input type='submit' name='submit' onclick=\"agent_add();\" class='btn' value='Save' >\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>\n";
	echo "	</div>\n";

	//add an agent to a tier
	if (ifgroup("admin") || ifgroup("superadmin")) {
		echo "<div id=\"div_tier\" style=\"display:none\">\n";
		echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
		echo "	<tr>\n";
		echo "		<td align='left' valign='middle'>\n";
		echo "			<div id=\"form_label\"><strong>Tier</strong> </div>\n";
		echo "		</td>\n";
		echo "		<td align='right' valign='middle'>\n";
		echo "		</td>\n";
		echo "	<tr>\n";
		echo "	<tr>\n";
		echo "		<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "			Agent:\n";
		echo "		</td>\n";
		echo "		<td class='vtable' align='left'>\n";
		//echo "			<input type=\"text\" id=\"tier_agent_name\" name=\"agent_name\" class='formfld' value=\"\"/>\n";

		//---- Begin Select List --------------------
		$sql = "SELECT * FROM v_users ";
		$sql .= "where v_id = '$v_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();

		echo "<select id=\"tier_agent_name\" name=\"tier_agent_name\" class='formfld'>\n";
		echo "<option value=\"\"></option>\n";
		$result = $prepstatement->fetchAll();
		//$catcount = count($result);
		foreach($result as $field) {
			echo "<option value='".$field[username]."'>".$field[username]."</option>\n";
		}
		echo "</select>";
		unset($sql, $result);
		//---- End Select List --------------------

		echo "			<br />\n";
		echo "			Select the agent username.\n";
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "			Level:\n";
		echo "		</td>\n";
		echo "		<td class='vtable' align='left'>\n";
		echo "			<input type=\"text\" id=\"tier_level\" name=\"level\" class='formfld' value=\"1\"/>\n";
		echo "			<br />\n";
		echo "			Enter the level.\n";
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "			Position:\n";
		echo "		</td>\n";
		echo "		<td class='vtable' align='left'>\n";
		echo "			<input type=\"text\" id=\"tier_position\" name=\"position\" class='formfld' value=\"1\"/>\n";
		echo "			<br />\n";
		echo "			Enter the position.\n";
		echo "		</td>\n";
		echo "	</tr>\n";

		echo "	<tr>\n";
		echo "		<td colspan='2' align='right'>\n";
		echo "<script type='text/javascript'>\n";
		echo "	function tier_add() {\n";
		echo "		queue_name = document.getElementById('queue_name').value;\n";
		echo "		queue_name = queue_name+'@".$v_domain."';\n";
		echo "		tier_agent_name = document.getElementById('tier_agent_name').value;\n";
		echo "		tier_agent_name = tier_agent_name+'@".$v_domain."';\n";
		echo "		tier_level = document.getElementById('tier_level').value;\n";
		echo "		tier_position = document.getElementById('tier_position').value;\n";
		echo "		tier_add_str = 'callcenter_config+tier+add+'+queue_name+'+'+tier_agent_name+'+'+tier_level+'+'+tier_position;\n";
		echo "		send_cmd('v_call_center_exec.php?cmd='+tier_add_str);\n";
		echo "	}\n";
		echo "</script>\n";
		echo "			<input type='submit' name='submit' onclick=\"tier_add();\" class='btn' value='Save'>\n";
		echo "		</td>\n";
		echo "	</tr>";
		echo "</table>\n";
		echo "	</div>\n";
	}

echo "	</td>\n";
echo "	</tr>\n";
echo "</table>\n";

//echo "<br />\n";
//echo "<br />\n";

echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
echo "<tr class='border'>\n";
echo "	<td align=\"left\">\n";

echo "	<div id=\"ajax_response\">\n";
echo "	</div>\n";

echo "	</td>";
echo "	</tr>";
echo "</table>";
echo "</div>";

require_once "includes/footer.php";
?>
