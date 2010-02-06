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

echo "<html>";
echo "<head>";
echo "<title>Db Browser</title>";
echo "<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">";
//echo "<link rel=\"alternate\" type=\"application/xml\" title=\"RSS\" href=\"favrss.php\">";
//echo "<link href='style.css' rel='stylesheet' type='text/css'>";

echo "<style type='text/css'>";
echo "<!--\n";

echo "body {\n";
echo "	margin-top: 10px;\n";
echo "	margin-bottom: 10px;\n";
echo "	margin-right: 10px;\n";
echo "	margin-left: 10px;\n";
echo "  background-color: #EEEEEE;\n";
echo "}\n";

echo "th {\n";
echo "	color: #5f5f5f;\n";
echo "	font-size: 12px;\n";
echo "	font-family: arial;\n";
echo "	font-weight: bold;\n";
echo "	background-color: #EFEFEF;\n";
echo "}\n";

echo "td {\n";
echo "	color: #5f5f5f;\n";
echo "	font-size: 12px;\n";
echo "	font-family: arial;\n";
//echo "	background-color: #FFFFFF;\n";
echo "}\n";

echo ".main {\n";
echo "	background-color: #FFFFFF;\n";
//echo "  border: solid 1px #CCCCCC;\n";
echo "}\n";

echo "INPUT {\n";
//echo "  color: #666666;\n";
echo "	font-family: verdana;\n";
echo "	font-size: 11px;\n";
echo "}\n";

echo "SELECT {\n";
//echo "  color: #666666;\n";hgiuutyrrejttwuturtturtry6ryurteutyuruyttttruyryyry6
echo "	font-family: verdana;\n";
echo "	font-size: 11px;\n";
echo "}\n";

echo ".border {\n";
echo "    border: solid 1px #CCCCCC;\n";
echo "}\n";

echo ".frm {\n";
echo "    color: #666666;\n";
echo "    background-color: #EFEFEF;\n";
echo "    width: 100%;\n";
echo "}\n";

echo ".smalltext {\n";
echo "	color: #666666;\n";
echo "	font-size: 11px;\n";
echo "	font-family: arial;\n";
echo "}\n";
echo "//-->\n";
echo "</style>";



echo "<SCRIPT language=\"JavaScript\">\n";
echo "<!--\n";
echo "function confirmdelete(url)\n";
echo "{\n";
echo " var confirmed = confirm(\"Are you sure want to delete this.\");\n";
echo " if (confirmed == true) {\n";
echo "      window.location=url;\n";
echo " }\n";
echo "}\n";
echo "//-->\n";
echo "</SCRIPT>";
echo "</head>";
echo "<body class='border'>";
echo "<div align='center'>";


//echo "<br>";
//echo "<h3>LiveFav</h3>";
echo "<table width='100%' class='main' border='0' cellpadding='0' cellspacing='3'>";
echo "<tr>";
//echo "<td width='125' align='center' valign='top'>";
//echo "<br><br><br>";
/*
echo "<table border='0' cellpadding='0' cellspacing='2'>";
echo "<tr><td colspan='1'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";
echo "<tr><th nowrap>Favorites Menu </th></tr>";
echo "<tr><td colspan='1'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";
echo "<tr><td><a href='index.php'>Home</a></td></tr>";
echo "<tr><td><a href='favlist.php'>List Favorites</a></td></tr>";
echo "<tr><td><a href='favadd.php'>Add Favorites</a></td></tr>";
echo "<tr><td>&nbsp;</td></tr>";
echo "</table>";
*/

//echo "</td>";
echo "<td align='center'>";
echo "<br>";

?>
