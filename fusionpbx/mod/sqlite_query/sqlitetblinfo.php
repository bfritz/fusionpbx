<?php
/* $Id$ */
/*
	sqlitetblinfo.php
	Copyright (C) 2008 Mark J Crane
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
if (ifgroup("superadmin")) {
  //access granted
}
else {
  echo "access denied";
  return;
}
//require_once "admin/includes/paging.php";
//require_once("config.php");

//require_once "admin/header.php";


//--- Begin SQLite in Memory ---------------------------

try {
    //$db = new PDO('sqlite2:example.db'); //sqlite 2
    $dbmem = new PDO('sqlite::memory:'); //sqlite 3
    //$db = new PDO('sqlite:example.db'); //sqlite 3
}
catch (PDOException $error) {
   print "error: " . $error->getMessage() . "<br/>";
   die();
}



//--- End SQLite in Memory ---------------------------

//echo "  <div align='right'>";
//echo "  <input type='button' class='btn' name='' onclick=\"window.location='rmrlist.php'\" value='Home'>&nbsp; &nbsp;\n";
//echo "  <input type='button' class='btn' name='' onclick=\"window.location='rmradd.php'\" value='Add'>&nbsp; &nbsp;\n";
//echo "  <input type='button' class='btn' name='' onclick=\"window.location='rmrsearch.php'\" value='Search'>\n";
//echo "  </div>";

function createtblinfo($db, $dbmem) {

    $sql = "CREATE TABLE tblinfo ";
    $sql .= "(";
    $sql .= "id INTEGER PRIMARY KEY, ";
    $sql .= "tblname TEXT, ";
    $sql .= "cid TEXT, ";
    $sql .= "name TEXT, ";
    $sql .= "type TEXT, ";
    //$sql .= "notnull TEXT, ";
    $sql .= "dflt_value TEXT, ";
    $sql .= "pk TEXT ";
    $sql .= ");";
    //echo $sql."<br>";
    $prepstatement = $dbmem->prepare($sql);
    $prepstatement->execute();
    unset ($sql, $prepstatement);

    //List the tables names
    $sql = "SELECT name FROM sqlite_master";
    $prepstatementtbl = $db->prepare($sql);
    $prepstatementtbl->execute();
    
    
    while($rowtbl = $prepstatementtbl->fetch()) {
        //print_r( $row );
        $tblname = $rowtbl['name'];
    
        //echo "tblname $tblname<br>";
    
        //SELECT name FROM sqlite_master
        //run this query to return all table names
    
        //echo "<div align='center'>";
        //echo "<table border='0' cellpadding='0' cellspacing='2'>\n";
    
        //echo "<tr class='border'>\n";
        //echo "	<td align=\"left\">\n";
        //echo "      <br>";
    
    
        $sql = "PRAGMA TABLE_INFO($tblname)";
        //echo $sql."<br>";
        $result = $db->query($sql);
        $resultcount = count($result);
        /*
        $prepstatement = $db->prepare($sql);
        $prepstatement->execute();
        $result = $prepstatement->fetchAll();
        $resultcount = count($result);
        unset($prepstatement);
        unset($result);
        */
    
    
    
        //echo "<div align='left'>\n";
        //echo "<table border='0' cellpadding='1' cellspacing='1'>\n";
        //echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";
        //        echo "<tr style='".$rowstyle[$c]."'>\n";
        //            echo "<td nowrap valign='top'>cid</td>";
        //            echo "<td nowrap valign='top'>name</td>";
        //            echo "<td nowrap valign='top'>type</td>";
        //            //echo "<td nowrap valign='top'>notnull</td>";
        //            echo "<td nowrap valign='top'>dflt_value</td>";
        //            echo "<td nowrap valign='top'>pk</td>";
        //        echo "</tr>";
        if ($resultcount == 0) { //no results
            //echo "<tr><td>none&nbsp;</td></tr>";
        }
        else { //received results
    
            //echo "<tr>";
            //echo "</tr>";
            //echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";
    
            //$countrows = 1;
            //while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    
              //$countrows++;
            //}
    
    
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            //foreach($result as $row) {
            //print_r( $row );
            //    echo "<tr style='".$rowstyle[$c]."'>\n";
            //        echo "<td nowrap valign='top'>".$row[cid]."</td>";
            //        echo "<td nowrap valign='top'>".$row[name]."</td>";
            //        echo "<td nowrap valign='top'>".$row[type]."</td>";
            //        //echo "<td nowrap valign='top'>".$row[notnull]."</td>";
            //        echo "<td nowrap valign='top'>".$row[dflt_value]."</td>";
            //        echo "<td nowrap valign='top'>".$row[pk]."</td>";
            //    echo "</tr>";
    
              $sql = "insert into tblinfo ";
              $sql .= "(";
              $sql .= "tblname, ";
              $sql .= "cid, ";
              $sql .= "name, ";
              $sql .= "type, ";
              //$sql .= "notnull, ";
              $sql .= "dflt_value, ";
              $sql .= "pk ";
              $sql .= ")";
              $sql .= "values ";
              $sql .= "(";
              $sql .= "'$tblname', ";
              $sql .= "'".$row[cid]."', ";
              $sql .= "'".$row[name]."', ";
              $sql .= "'".$row[type]."', ";
              //$sql .= "'$notnull', ";
              $sql .= "'".$row[dflt_value]."', ";
              $sql .= "'".$row[pk]."' ";
              $sql .= ")";
              $dbmem->exec($sql);
              //echo $sql."<br><br>";
              $lastinsertid = $dbmem->lastInsertId($id);
              unset($sql);
    
                //echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";
                if ($c==0) { $c=1; } else { $c=0; }
            //} //end foreach        unset($sql, $result, $rowcount);
            } //end while
    
            //echo "</table>\n";
            //echo "</div>\n";
    
            //echo "<br>";
    
    
            //echo "  <br><br>";
            //echo "  </td>\n";
            //echo "</tr>\n";
    
        unset($prepstatement);
        unset($result);
    
        } //end if results
    } //end while table names
        //echo "</table>\n";
        //echo "</div>";
}
createtblinfo($db, $dbmem);

function ispk($dbmem, $tblname, $fieldname) {
    $sql = "select pk from tblinfo ";
    $sql .= "where tblname = '$tblname' ";
    $sql .= "and name = '$fieldname' ";
    $sql .= "limit 1 ";
    //echo $sql."<br>";
    $prepstatementpk = $dbmem->prepare($sql);
    $prepstatementpk->execute();
    $row = 0;
    while($row = $prepstatementpk->fetch()) {
        if ($row[pk] == "1") { return true; } else { return false; }
    }
}
////example is pk
//$tblname = 'tblrmr';  $fieldname = 'rmrid';
//if (ispk($dbmem, $tblname, $fieldname)) { echo "tblrmr $fieldname is a pk"; } else { echo "tblrmr $fieldname is not a pk"; }
//echo "<br><br>";


function ispkint($dbmem, $tblname, $fieldname) {
    $sql = "select pk, type from tblinfo ";
    $sql .= "where tblname = '$tblname' ";
    $sql .= "and name = '$fieldname' ";
    $sql .= "limit 1 ";
    //echo $sql."<br>";
    $prepstatementpk = $dbmem->prepare($sql);
    $prepstatementpk->execute();
    $row = 0;
    //echo "hello";
    while($row = $prepstatementpk->fetch()) {
        //echo "pk ". $row[pk]."<br>";
        //echo "type ". $row[type]."<br>";
        if ($row[pk] == "1" && $row[type] == "INTEGER") { return true; } else { return false; }
    }
}

 
//$tblname = "tblblog"; $fieldname="blogid";
//if (!ispkint($dbmem, $tblname, $fieldname)) { 
//      echo "false";
//      //if not a integer and primary key (auto increment value)
//}
//else {
//      echo "true";
//}


//if (!ispkint($dbmem, $tblname, $fieldname)) { //if not a integer and primary key (auto increment value)


function getpkintarray ($dbmem, $tblname) {
    $sql = "select * from tblinfo ";
    $sql .= "where tblname = '$tblname' ";
    $sql .= "and pk = 1 ";
    $sql .= "and type = 'INTEGER' ";
    //echo $sql."<br>";
    $prepstatementpkint = $dbmem->prepare($sql);
    $prepstatementpkint->execute();
    $row = 0;
    $pkintarraytmp = array();
    $x = 0;
    while($row = $prepstatementpkint->fetch()) {
        $pkintarraytmp[$x] = $row[name];
        $row[type];
        $x++;
    }
    return $pkintarraytmp;
}
//$pkintarray = getpkarray ($dbmem, $tblname);

function getpkarray ($dbmem, $tblname) {
    $sql = "select * from tblinfo ";
    $sql .= "where tblname = '$tblname' ";
    $sql .= "and pk = 1 "; //
    //echo $sql."<br>";
    $prepstatementpk = $dbmem->prepare($sql);
    $prepstatementpk->execute();
    $row = 0;
    $pkarraytmp = array();
    $x = 0;
    while($row = $prepstatementpk->fetch()) {
        $pkarraytmp[$x] = $row[name];
        $x++;
    }
    return $pkarraytmp;
}
////example get pk array
//$tblname = 'tblrmr';
//$pkarray = getpkarray ($dbmem, $tblname);
//foreach($pkarray as $fieldname) {
//    echo $fieldname."<br>";
//}


    /*
    //get the primary key info -----------------------------
    $sql = "select * from tblinfo ";
    $sql .= "where pk = 1 "; //
    $sql .= "and tblname = 'tblrmr' ";
    echo $sql."<br>";
    $prepstatementpk = $dbmem->prepare($sql);
    $prepstatementpk->execute();

    echo "<br><br>";
    echo "<div align='center'>";

    echo "<table cellpadding='5' cellspacing='0'>";
    echo "<tr>";
    echo "<td>tblname</td>";
    echo "<td>name</td>";
    echo "<td>type</td>";
    echo "<td>pk</td>";
    echo "</tr>";
    $row = 0;
    while($row = $prepstatementpk->fetch())
    {
        //print_r( $row );
        echo "<tr>";
        echo "<td>".$row[tblname]."</td>";
        echo "<td>".$row[name]."</td>";
        echo "<td>".$row[type]."</td>";
        echo "<td>".$row[pk]."</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<div>";

    echo "<br><br>";
    require_once "admin/footer.php";

    unset ($resultcount);
    unset ($result);
    unset ($key);
    unset ($val);
    unset ($c);
    */

?>
