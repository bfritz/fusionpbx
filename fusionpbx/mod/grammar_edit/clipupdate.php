<?php
/* $Id$ */
/*
	clipupdate.php
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
if (ifgroup("admin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "config.php";

if (count($_POST)>0) {
    $id = checkstr($_POST["id"]);
    $clipname = checkstr($_POST["clipname"]);
    $clipfolder = checkstr($_POST["clipfolder"]);
    $cliptextstart = checkstr($_POST["cliptextstart"]);
    $cliptextend = checkstr($_POST["cliptextend"]);
    $clipdesc = checkstr($_POST["clipdesc"]);
    $cliporder = checkstr($_POST["cliporder"]);

    //sql update
    $sql  = "update tblcliplibrary set ";
    $sql .= "clipname = '$clipname', ";
    $sql .= "clipfolder = '$clipfolder', ";
    $sql .= "cliptextstart = '$cliptextstart', ";
    $sql .= "cliptextend = '$cliptextend', ";
    $sql .= "clipdesc = '$clipdesc', ";
    $sql .= "cliporder = '$cliporder' ";
    $sql .= "where id = '$id' ";
    $count = $db->exec($sql);
    //echo "Affected Rows: ".$count;


    //edit: make sure the meta redirect url is correct 
    require_once "header.php";
    echo "<meta http-equiv=\"refresh\" content=\"1;url=clipoptions.php\">\n";
    echo "Update Complete";
    require_once "footer.php";
    return;
}
else {
  //get data from the db
      $id = $_GET["id"];

      $sql = "";
      $sql .= "select * from tblcliplibrary ";
      $sql .= "where id = '$id' ";
      $prepstatement = $db->prepare($sql);
      $prepstatement->execute();

	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
          $clipname = $row["clipname"];
          $clipfolder = $row["clipfolder"];
          $cliptextstart = $row["cliptextstart"];
          $cliptextend = $row["cliptextend"];
          $clipdesc = $row["clipdesc"];
          $cliporder = $row["cliporder"];
          break; //limit to 1 row
      }
      echo "</table>";
      echo "<div>";}

    require_once "header.php";
    echo "<div align='left'>";
    echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

    echo "<tr class='border'>\n";
    echo "	<td align=\"left\">\n";

    echo "<form method='post' action=''>";
    echo "<table border='0' width='100%'>";
      echo "	<tr>";
      echo "		<td>Name:</td>";
      echo "		<td><input type='text' class='txt' name='clipname' value='$clipname'></td>";
      echo "	</tr>";
      
      echo "	<tr>";
      echo "		<td>Folder:</td>";
      echo "		<td><input type='text' class='txt'  name='clipfolder' value='$clipfolder'></td>";
      echo "	</tr>";
      
      echo "	<tr>";
      echo "		<td colspan='2'>Before Selection:<br>";
      echo "		  <textarea  class='txt' name='cliptextstart'>$cliptextstart</textarea>";
      echo "		</td>";
      echo "	</tr>";
      
      echo "	<tr>";
      echo "		<td colspan='2'>After Selection:<br>";
      echo "		  <textarea  class='txt' name='cliptextend'>$cliptextend</textarea>";
      echo "		</td>";
      echo "	</tr>";

      echo "	<tr>";
      echo "		<td colspan='2'>Notes:<br>";
      echo "		  <textarea  class='txt' name='clipdesc'>$clipdesc</textarea>";
      echo "		</td>";
      echo "	</tr>";


    echo "	<tr>";
    echo "		<td colspan='2' align='right'>";
    echo "     <input type='hidden' name='id' value='$id'>";
    echo "     <input type='submit' name='submit' value='Update'>";
    echo "		</td>";
    echo "	</tr>";
    echo "</table>";
    echo "</form>";


    echo "	</td>";
    echo "	</tr>";
    echo "</table>";
    echo "</div>";


  require_once "footer.php";
?>
