<?php
/* $Id$ */
/*
	clipadd.php
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
    $clipname = checkstr($_POST["clipname"]);
    $clipfolder = checkstr($_POST["clipfolder"]);
    $cliptextstart = checkstr($_POST["cliptextstart"]);
    $cliptextend = checkstr($_POST["cliptextend"]);
    $clipdesc = checkstr($_POST["clipdesc"]);
    $cliporder = checkstr($_POST["cliporder"]);
    

    $sql = "insert into tblcliplibrary ";
    $sql .= "(";
    $sql .= "clipname, ";
    $sql .= "clipfolder, ";
    $sql .= "cliptextstart, ";
    $sql .= "cliptextend, ";
    $sql .= "clipdesc, ";
    $sql .= "cliporder ";
    $sql .= ")";
    $sql .= "values ";
    $sql .= "(";
    $sql .= "'$clipname', ";
    $sql .= "'$clipfolder', ";
    $sql .= "'$cliptextstart', ";
    $sql .= "'$cliptextend', ";
    $sql .= "'$clipdesc', ";
    $sql .= "'$cliporder' ";
    $sql .= ")";
    $db->exec($sql);
    $lastinsertid = $db->lastInsertId($id);
    unset($sql,$db);

    require_once "header.php";
    echo "<meta http-equiv=\"refresh\" content=\"1;url=clipoptions.php\">\n";
    echo "Add Complete";
    require_once "footer.php";
    return;
}

    require_once "header.php";
    echo "<div align='left'>";
    echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

    echo "<tr class='border'>\n";
    echo "	<td align=\"left\">\n";

    //echo "Clip Library";
    //echo "<hr size='1'>";
    echo "<form method='post' action=''>";
    echo "<table width='100%' border='0'>";
      echo "	<tr>";
      echo "		<td>Name:</td>";
      echo "		<td><input type='text' class='txt' name='clipname'></td>";
      echo "	</tr>";
      
      echo "	<tr>";
      echo "		<td>Folder:</td>";
      echo "		<td><input type='text' class='txt' name='clipfolder'></td>";
      echo "	</tr>";
      
      echo "	<tr>";
      echo "		<td colspan='2'>Before Selection:<br>";
      echo "		  <textarea name='cliptextstart' class='txt'></textarea>";
      echo "		</td>";
      echo "	</tr>";
      
      echo "	<tr>";
      echo "		<td colspan='2'>After Selection:<br>";
      echo "		  <textarea name='cliptextend' class='txt'></textarea>";
      echo "		</td>";
      echo "	</tr>";
      
      echo "	<tr>";
      echo "		<td colspan='2'>Notes:<br>";
      echo "		  <textarea name='clipdesc' class='txt'></textarea>";
      echo "		</td>";
      echo "	</tr>";

    echo "		<td colspan='2' align='right'><input type='submit' name='submit' value='Add'></td>";
    echo "	</tr>";
    echo "</table>";
    echo "</form>";


    echo "	</td>";
    echo "	</tr>";
    echo "</table>";
    echo "</div>";


require_once "footer.php";
?>
