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
require_once "config.php";

if (!ifgroup("admin")) {
	header("Location: /index.php");
	return;
}

if (count($_POST)>0) {

	$rsssubcategory = check_str($_POST["rsssubcategory"]);
	$rsstitle = check_str($_POST["rsstitle"]);
	$rsslink = check_str($_POST["rsslink"]);
	$rssdesc = check_str($_POST["rssdesc"]);
	$rssimg = check_str($_POST["rssimg"]);
	$rssoptional1 = check_str($_POST["rssoptional1"]);
	$rssoptional2 = check_str($_POST["rssoptional2"]);
	$rssoptional3 = check_str($_POST["rssoptional3"]);
	$rssoptional4 = check_str($_POST["rssoptional4"]);
	$rssoptional5 = check_str($_POST["rssoptional5"]);
	$rssgroup = check_str($_POST["rssgroup"]);
	$rssorder = check_str($_POST["rssorder"]);

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
	$db->exec(check_sql($sql));
	//echo $sql;
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
	if (is_dir($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/tiny_mce')) {
		require_once "includes/wysiwyg.php";
	}
	else {
		//--- Begin: Edit Area -----------------------------------------------------
			echo "    <script language=\"javascript\" type=\"text/javascript\" src=\"".PROJECT_PATH."/includes/edit_area/edit_area_full.js\"></script>\n";
			echo "    <!-- -->\n";

			echo "	<script language=\"Javascript\" type=\"text/javascript\">\n";
			echo "		editAreaLoader.init({\n";
			echo "			id: \"rssdesc\" // id of the textarea to transform //, |, help\n";
			echo "			,start_highlight: true\n";
			echo "			,font_size: \"8\"\n";
			echo "			,allow_toggle: false\n";
			echo "			,language: \"en\"\n";
			echo "			,syntax: \"html\"\n";
			echo "			,toolbar: \"search, go_to_line,|, fullscreen, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help\" //new_document,\n";
			echo "			,plugins: \"charmap\"\n";
			echo "			,charmap_default: \"arrows\"\n";
			echo "    });\n";
			echo "    </script>";
		//--- End: Edit Area -------------------------------------------------------
	}
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
	$prepstatement = $db->prepare(check_sql($sql));
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
	$prepstatement = $db->prepare(check_sql($sql));
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

	if (is_dir($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/tiny_mce')) {
		echo "	<tr>";
		echo "		<td colspan='2' valign='top'>Content: editor &nbsp; <a href=\"#\" title=\"toggle\" onclick=\"toogleEditorMode('rssdesc'); return false;\">on/off</a></td>";
		echo "    </tr>";
	}
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
