<?php
/* $Id$ */
/*
	clipsearch.php
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
require_once "config.php";
require_once "includes/checkauth.php";
if (ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (count($_POST)>0) {
    $id = $_POST["id"];
    $clipname = $_POST["clipname"];
    $clipfolder = $_POST["clipfolder"];
    $cliptextstart = $_POST["cliptextstart"];
    $cliptextend = $_POST["cliptextend"];
    $clipdesc = $_POST["clipdesc"];
    $cliporder = $_POST["cliporder"];


    require_once "header.php";
    echo "<div align='left'>";
    echo "<table width='175' border='0' cellpadding='0' cellspacing='2'>\n";

    echo "<tr class='border'>\n";
    echo "	<td align=\"left\">\n";
    echo "      <br>";


    $sql = "";
    $sql .= "select * from tblcliplibrary ";
    $sql .= "where ";
    if (strlen($id) > 0) { $sql .= "and id like '%$id%' "; }
    if (strlen($clipname) > 0) { $sql .= "and clipname like '%$clipname%' "; }
    if (strlen($clipfolder) > 0) { $sql .= "and clipfolder like '%$clipfolder%' "; }
    if (strlen($cliptextstart) > 0) { $sql .= "and cliptextstart like '%$cliptextstart%' "; }
    if (strlen($cliptextend) > 0) { $sql .= "and cliptextend like '%$cliptextend%' "; }
    if (strlen($clipdesc) > 0) { $sql .= "and clipdesc like '%$clipdesc%' "; }
    if (strlen($cliporder) > 0) { $sql .= "and cliporder like '%$cliporder%' "; }


    $sql = trim($sql);
    if (substr($sql, -5) == "where"){ $sql = substr($sql, 0, (strlen($sql)-5)); }
    $sql = str_replace ("where and", "where", $sql);
    $prepstatement = $db->prepare(check_sql($sql));
    $prepstatement->execute();
    $result = $prepstatement->fetchAll();
    $resultcount = count($result);

    $c = 0;
    $rowstyle["0"] = "background-color: #F5F5DC;";
    $rowstyle["1"] = "background-color: #FFFFFF;";

    echo "<div align='left'>\n";
    echo "<table border='0' cellpadding='1' cellspacing='1'>\n";
    echo "<tr><td colspan='1'><img src='/xml_edit/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";

    if ($resultcount == 0) { //no results
        echo "<tr><td>&nbsp;</td></tr>";
    }
    else { //received results

        echo "<tr>";
          //echo "<th nowrap>&nbsp; &nbsp; Id&nbsp; &nbsp; </th>";
          echo "<th nowrap>&nbsp; &nbsp; Clipname Search &nbsp; &nbsp; </th>";
          //echo "<th nowrap>&nbsp; &nbsp; Clipfolder&nbsp; &nbsp; </th>";
          //echo "<th nowrap>&nbsp; &nbsp; Cliptextstart&nbsp; &nbsp; </th>";
          //echo "<th nowrap>&nbsp; &nbsp; Cliptextend&nbsp; &nbsp; </th>";
          //echo "<th nowrap>&nbsp; &nbsp; Clipdesc&nbsp; &nbsp; </th>";
          //echo "<th nowrap>&nbsp; &nbsp; Cliporder&nbsp; &nbsp; </th>";
        echo "</tr>";
        echo "<tr><td colspan='1'><img src='images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";

        foreach($result as $row) {
        //print_r( $row );
            echo "<tr style='".$rowstyle[$c]."'>\n";
                //echo "<td valign='top'><a href='update.php?id=".$row[id]."'>".$row[id]."</a></td>";
                echo "<td valign='top'><a href='clipupdate.php?id=".$row[id]."'>".$row[clipname]."</a></td>";
                //echo "<td valign='top'>".$row[clipfolder]."</td>";
                //echo "<td valign='top'>".$row[cliptextstart]."</td>";
                //echo "<td valign='top'>".$row[cliptextend]."</td>";
                //echo "<td valign='top'>".$row[clipdesc]."</td>";
                //echo "<td valign='top'>".$row[cliporder]."</td>";
            echo "</tr>";

            echo "<tr><td colspan='1'><img src='images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";
            if ($c==0) { $c=1; } else { $c=0; }
        } //end foreach        unset($sql, $result, $rowcount);

        echo "</table>\n";
        echo "</div>\n";


        echo "  <br><br>";
        echo "  </td>\n";
        echo "</tr>\n";

    } //end if results

    echo "</table>\n";
    echo "</div>";

    echo "<br><br>";
    require_once "footer.php";

    unset ($resultcount);
    unset ($result);
    unset ($key);
    unset ($val);
    unset ($c);

    }
    else {

    require_once "header.php";
    echo "<div align='left'>";
    echo "<table with='175' border='0' cellpadding='0' cellspacing='2'>\n";

    echo "<tr class='border'>\n";
    echo "	<td align=\"left\">\n";
    echo "      <br>";


    echo "<form method='post' action=''>";
    echo "<table>";
      echo "	<tr>";
      echo "		<td>Name:</td>";
      echo "		<td><input type='text' class='txt' name='clipname'></td>";
      echo "	</tr>";
      echo "	<tr>";
      echo "		<td>Folder:</td>";
      echo "		<td><input type='text' class='txt' name='clipfolder'></td>";
      echo "	</tr>";
      echo "	<tr>";
      echo "		<td>Start:</td>";
      echo "		<td><input type='text' class='txt' name='cliptextstart'></td>";
      echo "	</tr>";
      echo "	<tr>";
      echo "		<td>End:</td>";
      echo "		<td><input type='text' class='txt' name='cliptextend'></td>";
      echo "	</tr>";
      echo "	<tr>";
      echo "		<td>Desc:</td>";
      echo "		<td><input type='text' class='txt' name='clipdesc'></td>";
      echo "	</tr>";
      //echo "	<tr>";
      //echo "		<td>Cliporder:</td>";
      //echo "		<td><input type='text' class='txt' name='cliporder'></td>";
      //echo "	</tr>";
    echo "	<tr>";
    echo "		<td colspan='2' align='right'><input type='submit' name='submit' value='Search'></td>";
    echo "	</tr>";
    echo "</table>";
    echo "</form>";


    echo "	</td>";
    echo "	</tr>";
    echo "</table>";
    echo "</div>";


require_once "footer.php";

} //end if not post
?>
