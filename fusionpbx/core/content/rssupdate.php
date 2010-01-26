<?php
/* $Id$ */
/*
	rssupdate.php
	Copyright (C) 2008, 2009 Mark J Crane
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
require_once "config.php";

if (!ifgroup("admin")) {
	echo "access denied";
	return;
}

if (count($_POST)>0) {
	$rssid = check_str($_POST["rssid"]);
	//$rsscategory = check_str($_POST["rsscategory"]); //defined in local config.php
	$rsssubcategory = check_str($_POST["rsssubcategory"]);
	$rsstitle = check_str($_POST["rsstitle"]);
	$rsslink = check_str($_POST["rsslink"]);
	$rssdesc = check_str($_POST["rssdesc"]);
	$rssgroup = check_str($_POST["rssgroup"]);
	$rssorder = check_str($_POST["rssorder"]);

	//$rssdesc = str_replace ("<br />\r\n<br />", "<br />", $rssdesc);
	//$rssdesc = str_replace ("<br />\n<br />", "<br />", $rssdesc);
	//$rssdesc = str_replace ("<p>", "", $rssdesc);
	//$rssdesc = str_replace ("</p>", "<br />", $rssdesc);

	$rssimg = check_str($_POST["rssimg"]);
	$rssoptional1 = check_str($_POST["rssoptional1"]);
	$rssoptional2 = check_str($_POST["rssoptional2"]);
	//$rssoptional3 = check_str($_POST["rssoptional3"]);
	//$rssoptional4 = check_str($_POST["rssoptional4"]);
	//$rssoptional5 = check_str($_POST["rssoptional5"]);

	//sql update
	$sql  = "update v_rss set ";
	$sql .= "rsssubcategory = '$rsssubcategory', ";
	$sql .= "rsstitle = '$rsstitle', ";
	$sql .= "rsslink = '$rsslink', ";
	$sql .= "rssdesc = '$rssdesc', ";
	$sql .= "rssimg = '$rssimg', ";
	$sql .= "rssoptional1 = '$rssoptional1', ";
	$sql .= "rssoptional2 = '$rssoptional2', ";
	//$sql .= "rssoptional3 = '$rssoptional3', ";
	//$sql .= "rssoptional4 = '$rssoptional4', ";
	//$sql .= "rssoptional5 = '$rssoptional5', ";
	//$sql .= "rssadddate = '$rssadddate', ";
	$sql .= "rssgroup = '$rssgroup', ";
	$sql .= "rssorder = '$rssorder' ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and rssid = '$rssid' ";
	$sql .= "and rsscategory = '$rsscategory' ";
	//echo $sql;
	//return;
	$count = $db->exec(check_sql($sql));
	//echo $sql."<br>";
	//echo "Affected Rows: ".$count;
	//exit;

	//edit: make sure the meta redirect url is correct
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=rsslist.php\">\n";
	echo "<div align='center'>";
	echo "Update Complete";
	echo "</div>";
	require_once "includes/footer.php";
	return;
}
else {
	//get data from the db
	$rssid = $_GET["rssid"];

	$sql = "";
	$sql .= "select * from v_rss ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and rssid = '$rssid' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$rsscategory = $row["rsscategory"];
		$rsssubcategory = $row["rsssubcategory"];
		$rssoptional1 = $row["rssoptional1"];
		$rsstitle = $row["rsstitle"];
		$rsslink = $row["rsslink"];
		$rssdesc = $row["rssdesc"];

		if ($rssoptional1 == "text/html") { //type
			$rssdesc = htmlentities($rssdesc);
		}

		$rssimg = $row["rssimg"];
		$rssoptional2 = $row["rssoptional2"];
		$rssoptional3 = $row["rssoptional3"];
		$rssoptional4 = $row["rssoptional4"];
		$rssoptional5 = $row["rssoptional5"];
		$rssadddate = $row["rssadddate"];
		$rssadduser = $row["rssadduser"];
		$rssgroup = $row["rssgroup"];
		$rssorder = $row["rssorder"];
		//$rssdesc = str_replace ("\r\n", "<br>", $rssdesc);

		//echo $rssdesc;
		//return;

		break; //limit to 1 row
	}
}

	require_once "includes/header.php";
	if ($rssoptional1 == "text/html") {
		require_once "includes/wysiwyg.php";
	}

	if ($rssoptional1 == "text/javascript") {
	//--- Begin: AJAX-----------------------------------------------------------

		echo "<script type=\"text/javascript\" language=\"javascript\">\n";


		echo "// Replaces all instances of the given substring.\n";
		echo "String.prototype.replaceall = function(\n";
		echo "strTarget, \n"; // The substring you want to replace
		echo "strSubString \n"; // The string you want to replace in
		echo ")\n";
		echo "{\n";
		echo "  var strText = this;\n";
		echo "  var intIndexOfMatch = strText.indexOf( strTarget );\n";
		echo "  \n";
		echo "  // Keep looping while an instance of the target string\n";
		echo "  // still exists in the string.\n";
		echo "  while (intIndexOfMatch != -1){\n";
		echo "  // Relace out the current instance.\n";
		echo "  strText = strText.replace( strTarget, strSubString )\n";
		echo "  \n";
		echo "  // Get the index of any next matching substring.\n";
		echo "  intIndexOfMatch = strText.indexOf( strTarget );\n";
		echo "}\n";
		//echo "// Return the updated string with ALL the target strings\n";
		//echo "// replaced out with the new substring.\n";
		echo "return( strText );\n";
		echo "}\n";

		echo "function urlencode(str) {\n";
		echo "  str=escape(str); \n"; //Escape does not encode '/' and '+' character
		echo "  str=str.replaceall(\"+\", \"%2B\");\n";
		echo "  str=str.replaceall(\"/\", \"%2F\");\n";
		echo "  return str;\n";
		echo "}";
		echo "</script>\n";

		echo "<script type=\"text/javascript\" language=\"javascript\">\n";
		echo "    function makeRequest(url, strpost) {\n";
		//echo "        alert(url); \n";
		echo "        var http_request = false;\n";
		echo "\n";
		echo "        if (window.XMLHttpRequest) { // Mozilla, Safari, ...\n";
		echo "            http_request = new XMLHttpRequest();\n";
		echo "            if (http_request.overrideMimeType) {\n";
		echo "                http_request.overrideMimeType('text/xml');\n";
		echo "                // See note below about this line\n";
		echo "            }\n";
		echo "        } else if (window.ActiveXObject) { // IE\n";
		echo "            try {\n";
		echo "                http_request = new ActiveXObject(\"Msxml2.XMLHTTP\");\n";
		echo "            } catch (e) {\n";
		echo "                try {\n";
		echo "                    http_request = new ActiveXObject(\"Microsoft.XMLHTTP\");\n";
		echo "                } catch (e) {}\n";
		echo "            }\n";
		echo "        }\n";
		echo "\n";
		echo "        if (!http_request) {\n";
		echo "            alert('Giving up :( Cannot create an XMLHTTP instance');\n";
		echo "            return false;\n";
		echo "        }\n";
		echo "        http_request.onreadystatechange = function() { returnContent(http_request); };\n";
		echo "        if (http_request.overrideMimeType) {\n";
		echo "              http_request.overrideMimeType('text/html');\n";
		echo "        }\n";
		echo "        http_request.open('POST', url, true);\n";
		echo "\n";
		echo "\n";
		echo "        if (strpost.length == 0) {\n";
		//echo "            alert('none');\n";
		echo "            //http_request.send(null);\n";
		//echo "            http_request.send('name=value&foo=bar');\n";
		echo "            http_request.send(strpost);\n";

		echo "        }\n";
		echo "        else {\n";
		//echo "            alert(strpost);\n";
		echo "            http_request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');\n";
		//echo "            http_request.send('name=value&foo=bar');\n";
		echo "            http_request.send(strpost);\n";
		echo "        }\n";
		echo "\n";
		echo "    }\n";
		echo "\n";

		echo "    function load_content(local_page, strpost){\n";
		//qstring is the query string
		//makeRequest('filesave.php','file='+document.getElementById('file').value+'&content='+urlencode(content));\n";
		echo "    makeRequest(local_page,strpost);    \n";
		echo "    }\n";


		echo "    function returnContent(http_request) {\n";
		echo "\n";
		echo "        if (http_request.readyState == 4) {\n";
		echo "            if (http_request.status == 200) {\n";
		echo "                      ajaxresponse = http_request.responseText; \n";
		echo "                      document.getElementById('rssdesc').innerHTML = ajaxresponse;\n";

		//echo "  alert(http_request.responseText); \n";
		//echo "                    document.getElementById('hiddencontent').innerHTML = http_request.responseText \n";


		//echo "                  alert(document.getElementById('action').value); \n";
		//echo "                  if (document.getElementById('action').value == 'save') { \n";
		//echo "                      document.getElementById('action').value = ''; \n";
		//echo "                   }\n";
		//echo "                   else {\n";
		//echo "                      parent.editAreaLoader.setValue('edit1', http_request.responseText); \n";
		//echo "                   }\n";
		//echo "                alert(http_request.responseText);\n";
		//echo "\n";
		//echo "                //var xmldoc = http_request.responseXML;\n";
		//echo "                //var root_node = xmldoc.getElementsByTagName('doc').item(0);\n";
		//echo "                //alert(xmldoc.getElementByID('fr1').value);\n";
		//echo "                //alert(root_node.firstChild.data);\n";
		//echo "\n";
		echo "            }\n";
		echo "            else {\n";
		echo "                alert('There was a problem with the request.');\n";
		echo "            }\n";
		echo "        }\n";
		echo "\n";
		echo "    }\n";
		//echo "  alert('test1'); \n";
		echo "  load_content('rsscontent.php', 'rssid=".$rssid."');";

		echo "</script>";


	//--- End: AJAX-----------------------------------------------------------
	} //if ($rssoptional1 == "text/javascript") {



	echo "<div align='center'>";
	echo "<table border='0' width='90%' cellpadding='0' cellspacing='0'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\" width='100%'>\n";
	//echo "      <br>";


	echo "<form method='post' action=''>";
	echo "<table width='100%' cellpadding='6' cellspacing='0'>";

	echo "<tr>\n";
	echo "<td width='30%' nowrap valign='top'><b>Content Edit</b></td>\n";
	echo "<td width='70%' align='right' valign='top'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='rsslist.php'\" value='Back'><br /><br /></td>\n";
	echo "</tr>\n";

	//echo "	<tr>";
	//echo "		<td class='vncellreq'>Category:</td>";
	//echo "		<td class='vtable'><input type='text' class='formfld' name='rsscategory' value='$rsscategory'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td class='vncellreq' nowrap>Sub Category:</td>";
	//echo "		<td class='vtable'><input type='text' class='formfld' name='rsssubcategory' value='$rsssubcategory'></td>";
	//echo "	</tr>";
	echo "	<tr>";
	echo "		<td width='30%' class='vncellreq' nowrap>Title:</td>";
	echo "		<td width='70%' class='vtable' width='100%'><input type='text' class='formfld' name='rsstitle' value='$rsstitle'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>Link:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='rsslink' value='$rsslink'></td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='vncellreq'>Group:</td>";
	echo "		<td class='vtable'>";
	//echo "            <input type='text' class='formfld' name='menuparentid' value='$menuparentid'>";

	//---- Begin Select List --------------------
	$sql = "SELECT * FROM v_groups ";
	$sql .= "where v_id = '$v_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();

	echo "<select name=\"rssgroup\" class='formfld'>\n";
	echo "<option value=\"\">public</option>\n";
	$result = $prepstatement->fetchAll();
	//$count = count($result);
	foreach($result as $field) {
			if ($rssgroup == $field[groupid]) {
				echo "<option value='".$field[groupid]."' selected>".$field[groupid]."</option>\n";
			}
			else {
				echo "<option value='".$field[groupid]."'>".$field[groupid]."</option>\n";
			}
	}

	echo "</select>";
	unset($sql, $result);
	//---- End Select List --------------------

	echo "        </td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='vncellreq' nowrap>Template:</td>";
	echo "		<td class='vtable' width='100%'>";
	//---- Begin Select List --------------------
		$sql = "SELECT distinct(templatename) as templatename FROM v_templates ";
		$sql .= "where v_id = '$v_id' ";
		//echo $sql;
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		echo "<select name=\"rsssubcategory\" class='formfld'>\n";
		echo "<option value=\"\"></option>\n";
		$result = $prepstatement->fetchAll();
		//$catcount = count($result);
		foreach($result as $field) {
		//    echo "<option value='".$field[templatename]."'>".$field[templatename]."</option>\n";
			if ($rsssubcategory == $field[templatename]) {
				echo "<option value='".$field[templatename]."' selected>".$field[templatename]."</option>\n";
			}
			else {
				echo "<option value='".$field[templatename]."'>".$field[templatename]."</option>\n";
			}
		  
		}
		echo "</select>";
		unset($sql, $result);
	//---- End Select List --------------------
	echo "    </td>";
	echo "	</tr>";


	echo "	<tr>";
	echo "		<td class='vncellreq'>Type:</td>";
	echo "		<td class='vtable'>";
	echo "            <select name=\"rssoptional1\" class='formfld'>\n";
	if ($rssoptional1 == "text/html") { echo "<option value=\"text/html\" selected>text/html</option>\n"; }
	else { echo "<option value=\"text/html\">text/html</option>\n"; }

	if ($rssoptional1 == "text/javascript") { echo "<option value=\"text/javascript\" selected>text/javascript</option>\n"; }
	else { echo "<option value=\"text/javascript\">text/javascript</option>\n"; }
	echo "            </select>";
	echo "        </td>";
	echo "	</tr>";


	echo "	<tr>";
	echo "		<td class='vncellreq'>Order:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='rssorder' value='$rssorder'></td>";
	echo "	</tr>";

	echo "	<tr>";
	//echo "		<td  class='vncellreq' valign='top'></td>";
	echo "		<td  class='' colspan='2' align='left'>";
	echo "            Content: ";
	if ($rssoptional1 == "text/html") {
		echo "            &nbsp; &nbsp; &nbsp; editor &nbsp; <a href='#' title='toggle' onclick=\"toogleEditorMode('rssdesc'); return false;\">on/off</a><br>";
		echo "            <textarea name='rssdesc'  id='rssdesc' class='formfld' cols='20' style='width: 100%' rows='12' >$rssdesc</textarea>";
	}
	if ($rssoptional1 == "text/javascript") {
		echo "            <textarea name='rssdesc'  id='rssdesc' class='formfld' cols='20' style='width: 100%' rows='12' ></textarea>";
	}
	echo "        </td>";
	echo "	</tr>";

	//echo "	<tr>";
	//echo "		<td class='vncellreq'>Image:</td>";
	//echo "		<td class='vtable'><input type='text' name='rssimg' value='$rssimg'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td class='vncellreq'>Priority:</td>";
	//echo "		<td class='vtable'>";
	//echo "            <input type='text' name='rssoptional1' value='$rssoptional1'>";
	//echo "            <select name=\"rssoptional1\" class='formfld'>\n";
	//echo "            <option value=\"$rssoptional1\">$rssoptional1</option>\n";
	//echo "            <option value=\"\"></option>\n";
	//echo "            <option value=\"low\">low</option>\n";
	//echo "            <option value=\"med\">med</option>\n";
	//echo "            <option value=\"high\">high</option>\n";
	//echo "            </select>";
	//echo "        </td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td class='vncellreq'>Status:</td>";
	//echo "		<td class='vtable'>";
	//echo "            <input type='text' name='rssoptional2' value='$rssoptional2'>";
	//echo "            <select name=\"rssoptional2\" class=\"formfld\">\n";
	//echo "            <option value=\"$rssoptional2\">$rssoptional2</option>\n";
	//echo "            <option value=\"\"></option>\n";
	//echo "            <option value=\"0\">0</option>\n";
	//echo "            <option value=\"10\">10</option>\n";
	//echo "            <option value=\"20\">20</option>\n";
	//echo "            <option value=\"30\">30</option>\n";
	//echo "            <option value=\"40\">40</option>\n";
	//echo "            <option value=\"50\">50</option>\n";
	//echo "            <option value=\"60\">60</option>\n";
	//echo "            <option value=\"70\">70</option>\n";
	//echo "            <option value=\"80\">80</option>\n";
	//echo "            <option value=\"90\">90</option>\n";
	//echo "            <option value=\"100\">100</option>\n";
	//echo "            </select>";
	//echo "        </td>";
	//secho "	</tr>";
	//echo "	<tr>";
	//echo "		<td class='vncellreq'>Optional 3:</td>";
	//echo "		<td class='vtable'><input type='text' class='formfld' name='rssoptional3' value='$rssoptional3'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td class='vncellreq'>Optional 4:</td>";
	//echo "		<td class='vtable'><input type='text' class='formfld' name='rssoptional4' value='$rssoptional4'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td class='vncellreq'>Rssoptional5:</td>";
	//echo "		<td class='vtable'><input type='text' class='formfld' name='rssoptional5' value='$rssoptional5'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td class='vncellreq'>Rssadddate:</td>";
	//echo "		<td class='vtable'><input type='text' class='formfld' name='rssadddate' value='$rssadddate'></td>";
	//echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='' colspan='2' align='right'>";
	//echo "<input type=\"button\" value=\"Load\" onclick=\"document.getElementById('rssdesc').innerHTML = ajaxresponse;\" />";
	//echo "<input type=\"button\" value=\"Load\" onclick=\"ajaxLoad('rssdesc', ajaxresponse);\" />";

	echo "          <input type='hidden' name='rssid' value='$rssid'>";
	echo "          <input type='submit' class='btn' name='submit' value='Save'>";
	echo "		</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";

	if ($rssoptional1 == "text/javascript") {
		echo "<script type=\"text/javascript\" language=\"javascript\">\n";
		echo "  document.getElementById('rssdesc').innerHTML = ajaxresponse;\n";
		echo "</script>\n";
	}

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";


  require_once "includes/footer.php";
?>
