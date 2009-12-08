<?php
/* $Id$ */
/*
	rssadd.php
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
	header("Location: /index.php");
	return;
}

if (count($_POST)>0) {

	$rsssubcategory = checkstr($_POST["rsssubcategory"]);
	$rsstitle = checkstr($_POST["rsstitle"]);
	$rsslink = checkstr($_POST["rsslink"]);
	$rssdesc = checkstr($_POST["rssdesc"]);
	$rssimg = checkstr($_POST["rssimg"]);
	$rssoptional1 = checkstr($_POST["rssoptional1"]);
	$rssoptional2 = checkstr($_POST["rssoptional2"]);
	$rssoptional3 = checkstr($_POST["rssoptional3"]);
	$rssoptional4 = checkstr($_POST["rssoptional4"]);
	$rssoptional5 = checkstr($_POST["rssoptional5"]);
	$rssgroup = checkstr($_POST["rssgroup"]);
	$rssorder = checkstr($_POST["rssorder"]);

	$sql = "insert into v_rss ";
	$sql .= "(";
	$sql .= "v_id, ";
	$sql .= "rsscategory, ";
	$sql .= "rsssubcategory, ";
	$sql .= "rsstitle, ";
	$sql .= "rsslink, ";
	$sql .= "rssdesc, ";
	$sql .= "rssimg, ";
	$sql .= "rssoptional1, ";
	$sql .= "rssoptional2, ";
	$sql .= "rssoptional3, ";
	$sql .= "rssoptional4, ";
	$sql .= "rssoptional5, ";
	$sql .= "rssgroup, ";
	$sql .= "rssorder, ";
	$sql .= "rssadddate, ";
	$sql .= "rssadduser ";
	$sql .= ")";
	$sql .= "values ";
	$sql .= "(";
	$sql .= "'$v_id', ";
	$sql .= "'$rsscategory', ";
	$sql .= "'$rsssubcategory', ";
	$sql .= "'$rsstitle', ";
	$sql .= "'$rsslink', ";
	$sql .= "'$rssdesc', ";
	$sql .= "'$rssimg', ";
	$sql .= "'$rssoptional1', ";
	$sql .= "'$rssoptional2', ";
	$sql .= "'$rssoptional3', ";
	$sql .= "'$rssoptional4', ";
	$sql .= "'$rssoptional5', ";
	$sql .= "'$rssgroup', ";
	$sql .= "'$rssorder', ";
	$sql .= "now(), ";
	$sql .= "'".$_SESSION["username"]."' ";
	$sql .= ")";
	$db->exec($sql);
	//echo $sql;
	$lastinsertid = $db->lastInsertId($id);
	unset($sql);

	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=rsslist.php\">\n";
	echo "<div align='center'>";
	echo "Add Complete";
	echo "</div>";
	require_once "includes/footer.php";
	return;
}

	require_once "includes/header.php";
	require_once "includes/wysiwyg.php";
	echo "<div align='center'>";
	echo "<table border='0' width='100%' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";


	echo "<form method='post' action=''>";
	echo "<table width='100%'>";
	//echo "	<tr>";
	//echo "		<td width='20%'>Category:</td>";
	//echo "		<td width='80%'><input type='text' class='txt' name='rsscategory'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Sub Category:</td>";
	//echo "		<td><input type='text' class='txt' name='rsssubcategory'></td>";
	//echo "	</tr>";
	echo "	<tr>";
	echo "		<td nowrap>Title:</td>";
	echo "		<td width='100%'><input type='text' class='txt' name='rsstitle'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td>Link:</td>";
	echo "		<td><input type='text' class='txt' name='rsslink'></td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td>Group:</td>";
	echo "		<td>";
	//echo "            <input type='text' class='txt' name='menuparentid' value='$menuparentid'>";

	//---- Begin Select List --------------------
	$sql = "SELECT * FROM v_groups ";
	$sql .= "where v_id = '$v_id' ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();

	echo "<select name=\"rssgroup\" class='txt'>\n";
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
	echo "		<td nowrap>Template:</td>";
	echo "		<td width='100%'>";
	//---- Begin Select List --------------------
	$sql = "SELECT distinct(templatename) as templatename FROM v_templates ";
	$sql .= "where v_id = '$v_id' ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();

	echo "<select name=\"rsssubcategory\" class='txt'>\n";
	echo "<option value=\"\"></option>\n";
	$result = $prepstatement->fetchAll();
	//$catcount = count($result);
	foreach($result as $field) {
	  echo "<option value='".$field[templatename]."'>".$field[templatename]."</option>\n";
	}

	echo "</select>";
	unset($sql, $result);
	//---- End Select List --------------------
	echo "    </td>";
	echo "	</tr>";


	echo "	<tr>";
	echo "		<td>Type:</td>";
	echo "		<td>";
	echo "            <select name=\"rssoptional1\" class='txt'>\n";
	if ($rssoptional1 == "text/html") { echo "<option value=\"html\" selected>text/html</option>\n"; }
	else { echo "<option value=\"text/html\">text/html</option>\n"; }

	if ($rssoptional1 == "text/javascript") { echo "<option value=\"text/javascript\" selected>text/javascript</option>\n"; }
	else { echo "<option value=\"text/javascript\">text/javascript</option>\n"; }
	echo "            </select>";
	echo "        </td>";
	echo "	</tr>";


	echo "	<tr>";
	echo "		<td>Order:</td>";
	echo "		<td><input type='text' class='txt' name='rssorder' value='$rssorder'></td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td colspan='2' valign='top'>Content: editor &nbsp; <a href=\"#\" title=\"toggle\" onclick=\"toogleEditorMode('rssdesc'); return false;\">on/off</a></td>";
	echo "    </tr>";
	echo "    <tr>";


	echo "		<td colspan='2'>";
	echo "            <textarea name='rssdesc' id='rssdesc' cols='20' style='width: 100%' rows='12' class='txt'></textarea>";
	echo "        </td>";
	echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Image:</td>";
	//echo "		<td><input type='text' class='txt' name='rssimg'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Priority:</td>";
	//echo "		<td>";
	//echo "          <input type='text' name='rssoptional1'>";
	//echo "            <select name=\"rssoptional1\" class='txt'>\n";
	//echo "            <option value=\"\"></option>\n";
	//echo "            <option value=\"low\">low</option>\n";
	//echo "            <option value=\"med\">med</option>\n";
	//echo "            <option value=\"high\">high</option>\n";
	//echo "            </select>";
	//echo "        </td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Status:</td>";
	//echo "		<td>";
	//echo "            <input type='text' name='rssoptional2'>";
	//echo "            <select name=\"rssoptional2\" class='txt'>\n";
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
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Optional 3:</td>";
	//echo "		<td><input type='text' class='txt' name='rssoptional3'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Optional 4:</td>";
	//echo "		<td><input type='text' class='txt' name='rssoptional4'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Optional 5:</td>";
	//echo "		<td><input type='text' class='txt' name='rssoptional5'></td>";
	//echo "	</tr>";

	//echo "	<tr>";
	//echo "	<td>example:</td>";
	//echo "	<td><textarea name='example'></textarea></td>";
	//echo "	</tr>";    echo "	<tr>";
	echo "		<td colspan='2' align='right'>\n";
	echo "          <input type='submit' class='btn' name='submit' value='Add $moduletitle'>\n";
	echo "      </td>";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";


	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";


require_once "includes/footer.php";
?>
