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

if (ifgroup("superadmin")) {
  //access granted
}
else {
  echo "access denied";
  return;
}

$temprecordid = '';


$sql = $_POST["frmsql"];

//echo "sql $sql<br>";
$resulttype = $_POST["frmresulttype"];
$tblname = urldecode($_POST["frmtblname"]);
if (substr($tblname, 0, 3) == "tbl") {
    $filename = substr($tblname, 3, strlen($tblname)-3);
}
else {
    $filename = $tblname;
}
if (strlen($sql) == 0) {
    $sql = 'select * from '.$tblname;
}


$msg = '';
$errormsg = '';

if(stristr($sql, 'insert') === FALSE) {
    //insert not found
    
    //---Begin SQLite Table Details -------------------------------------
    if (strlen($tblname) > 0) {

        $sql2 = '';
        $sql2 .= "SELECT sql FROM sqlite_master ";
        $sql2 .= "where name = '$tblname' ";
        $prepstatement = $db->prepare($sql2);
        $prepstatement->execute();
        $result = $prepstatement->fetchAll();
        foreach ($result as &$row) {
            $tblsql = $row[sql];
        }
        //echo $tblsql."<br><br>";

        //field list
        $tblfields = str_replace ("CREATE TABLE $tblname (", "", $tblsql);
        $tblfields = str_replace (")", "", $tblfields);
        $tblfieldarray = explode(",",$tblfields);
        foreach($tblfieldarray as $key => $val) {
            $val = str_replace ("INTEGER PRIMARY KEY", "INTEGERPRIMARYKEY", $val);
            $nameandtypearray = explode(" ",trim($val));
            $fieldname = $nameandtypearray[0];
            $fieldtype = $nameandtypearray[1];
            $fieldtype = str_replace ("INTEGERPRIMARYKEY", "INTEGER PRIMARY KEY", $fieldtype);
            //echo $fieldname."|".$fieldtype."<br>";
        	//echo "key $key | val $val<br>";
        }
        //echo "<br>";
        //echo "tblfields: ".$tblfields."<br>";

        $tblcolumnscount = count($tblfieldarray);
        //echo "column count: ".$tblcolumnscount."<br><br>\n\n";

        //blank insert
        $tblinsertsql = "INSERT INTO $tblname (";
        foreach($tblfieldarray as $key => $val) {
            $val = str_replace ("INTEGER PRIMARY KEY", "INTEGERPRIMARYKEY", $val);
            $nameandtypearray = explode(" ",trim($val));
            $fieldname = $nameandtypearray[0];
            $fieldtype = $nameandtypearray[1];
            if ($fieldtype != "INTEGERPRIMARYKEY") {
                $tblinsertsql .= "$fieldname, ";
                $tblinsertsqlval .= "'', ";
            }
            else {
                //this is used with the temporay id
                $temprecordprimarykey = $fieldname;
            }
        }
        $tblinsertsql .= ") VALUES (";
        $tblinsertsql .= $tblinsertsqlval;
        $tblinsertsql .= ")";
        $tblinsertsql = str_replace (", )", ")", $tblinsertsql);
        //echo "tblinsertsql: ".$tblinsertsql."<br>\n\n";

        unset($prepstatement, $row, $sql2);
    }

    //---End SQLite Details -------------------------------------


    try {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $prepstatement = $db->prepare(check_sql($sql));
        $prepstatement->execute();
        $result = $prepstatement->fetchall(PDO::FETCH_ASSOC);
        $resultcount = count($result);

        if ($resultcount == 0) { //no results found

            //clean up previous query
            unset($prepstatement, $result, $resultcount);

            //create a temporary record this will provide ability to get the field names
            $db->exec($tblinsertsql);
            $temprecordid = $db->lastInsertId($id);
            //echo "temprecordid $temprecordid<br>";

            $prepstatement = $db->prepare(check_sql($sql));
            $prepstatement->execute();
            $result = $prepstatement->fetchall(PDO::FETCH_ASSOC);
            $resultcount = count($result);

        }

        $msg = 'Results: '.$resultcount.'<br>';
        //echo $msg."</br>";

      //$db->exec("INSERT INTO testuser (ID, NAME, ADDRESS, COMPANY) VALUES ('BOGUS_PK', 'a', 'b', 'c')");
      //echo 'the script should not echo this line';
    }
    catch(PDOException $e) {
        $errormsg = 'Error:<br> <b>'.$e->getMessage().'</b>';
    }
    
    
}
else {
    echo "found insert";

    if(stristr($sql, ';') === FALSE) {

        //no semi-colon ; character found in the sql string
            try {
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $prepstatement = $db->prepare(check_sql($sql));
                $prepstatement->execute();
                $result = $prepstatement->fetchall(PDO::FETCH_ASSOC);
                $resultcount = count($result);

                if ($resultcount == 0) { //no results found

                    //clean up previous query
                    unset($prepstatement, $result, $resultcount);

                }


                $msg = 'Results: '.$resultcount.'<br>';
                //echo $msg."</br>";

              //$db->exec("INSERT INTO testuser (ID, NAME, ADDRESS, COMPANY) VALUES ('BOGUS_PK', 'a', 'b', 'c')");
              //echo 'the script should not echo this line';
            }
            catch(PDOException $e) {
                $errormsg = 'Error:<br> <b>'.$e->getMessage().'</b>';
            }
    }
    else {

        echo "; found";

        $sqlarray = explode (";", $sql);
        foreach($sqlarray as $sql) {

            try {
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $prepstatement = $db->prepare(check_sql($sql));
                $prepstatement->execute();
                $result = $prepstatement->fetchall(PDO::FETCH_ASSOC);
                $resultcount = count($result);

                if ($resultcount == 0) { //no results found

                    //clean up previous query
                    unset($prepstatement, $result, $resultcount);

                }


                $msg = 'Results: '.$resultcount.'<br>';
                //echo $msg."</br>";

              //$db->exec("INSERT INTO testuser (ID, NAME, ADDRESS, COMPANY) VALUES ('BOGUS_PK', 'a', 'b', 'c')");
              //echo 'the script should not echo this line';
            }
            catch(PDOException $e) {
                $errormsg = 'Error:<br> <b>'.$e->getMessage().'</b>';
            }

        }




    } //end if semi colon is found";

} //if insert is found






//echo 'Test Warning:';
//$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
//$db->exec("INSERT INTO testuser (ID, NAME, ADDRESS, COMPANY) VALUES ('BOGUS_PK', 'a', 'b', 'c')");

//echo 'Test Silent:'; $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
//$db->exec("INSERT INTO testuser (ID, NAME, ADDRESS, COMPANY) VALUES ('BOGUS_PK', 'a', 'b', 'c')");
//var_dump($db->errorInfo());


if ($resulttype == "html") {

    require_once "header.php";
    echo "<div align='center'>\n";
    echo "<table border='0' cellpadding='0' cellspacing='2'>\n";

    echo "<tr class='main'>\n";
    echo "	<td align=\"left\">\n";
    echo "      SQL Query:<br><b>".$sql."</b><br><br>\n";
    echo "      <br>\n";

    echo $msg;
    echo $errormsg;

    $c = 0;
    $rowstyle["0"] = "background-color: #F5F5DC;";
    $rowstyle["1"] = "background-color: #FFFFFF;";

    echo "<div align='left'>\n";
    echo "<table width='100%' border='0' cellpadding='1' cellspacing='1'>\n";
    echo "<tr><td colspan='999'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";

    if ($resultcount == 0) { //no results
        echo "<tr><td>&nbsp;</td></tr>";
    }
    else { //received results

        echo "<tr>";
        foreach($result[0] as $key => $val) {
          echo "<th nowrap>&nbsp; ".$key." &nbsp;</th>";
        }
        echo "</tr>";
        echo "<tr><td colspan='999'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";

        $x=0;
        while($row = $result) {
            echo "<tr style='".$rowstyle[$c]."'>\n";
            foreach($result[0] as $key => $val) {
              echo "<td valign='top'>&nbsp;".$result[$x][$key]."&nbsp;</td>\n";
            }
            echo "</tr>";

            ++$x;
            if ($x > ($resultcount-1)) {
                break;
            }
            echo "<tr><td colspan='999'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";
            if ($c==0) { $c=1; } else { $c=0; }
            //$row++;
        }

        echo "<tr><td colspan='999'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";


    } //end if results

    echo "</table>\n";
    echo "</div>\n";


    echo "  <br><br>";
    echo "  </td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</div>";

    echo "<br><br>";
    require_once("footer.php");

    unset ($resultcount);
    unset ($resulttype);
    unset ($result);
    unset ($key);
    unset ($val);
    unset ($msg);
    unset ($errormsg);
    unset ($sql);
    unset ($x);
    unset ($c);

} //end if html


if ($resulttype == "csv") {


    if ($resultcount > 0) { //received results

        header('Content-type: application/octet-binary');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');

        $z = 0;
        foreach($result[0] as $key => $val) {
            if ($z == 0) {
                echo '"'.$key.'"';
            }
            else {
                echo ',"'.$key.'"';
            }
            $z++;
        }
        echo "\n";


        $x=0;
        while(true) {

            $z = 0;
            foreach($result[0] as $key => $val) {
                if ($z == 0) {
                    echo '"'.$result[$x][$key].'"';
                }
                else {
                    echo ',"'.$result[$x][$key].'"';
                }
                $z++;
            }
            echo "\n";

            ++$x;
            if ($x > ($resultcount-1)) {
                break;
            }
            //$row++;
        }

        unset ($resultcount);
        unset ($resulttype);
        unset ($result);
        unset ($key);
        unset ($val);
        unset ($msg);
        unset ($errormsg);
        unset ($sql);
        unset ($x);
        unset ($z);

    }
    else { //no results


        echo "if(!isset(\$_SERVER[\"DOCUMENT_ROOT\"])) { \$_SERVER[\"DOCUMENT_ROOT\"]=substr(\$_SERVER['SCRIPT_FILENAME'] , 0 , -strlen(\$_SERVER['PHP_SELF'])+1 );}\n";
        echo "require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/config.php\";\n";

        echo "<div align='center'>";
        echo "<table border='0' cellpadding='0' cellspacing='2'>\n";

        echo "<tr class='border'>\n";
        echo "	<td align=\"left\">\n";
        echo "      SQL Query:<br><b>".$sql."</b><br><br>";
        echo "      <br>";

        echo $msg;
        echo $errormsg;

        echo "<div align='left'>\n";
        echo "<table border='0' cellpadding='1' cellspacing='1'>\n";
        echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";

        echo "<tr><td>&nbsp;</td></tr>";

        echo "</table>\n";
        echo "</div>";

        echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";
        echo "</div>";


        echo "<br><br>";
        require_once("footer.php");

        unset ($msg);
        unset ($errormsg);
        unset ($sql);

    } //end if no results

} //end if csv



if ($resulttype == "phpcsv") {




    if ($resultcount > 0) { //received results
        header('Content-type: application/octet-binary');
        header('Content-Disposition: attachment; filename='.$filename.'csv.php');

        echo "<?php\n";
        echo "if(!isset(\$_SERVER[\"DOCUMENT_ROOT\"])) { \$_SERVER[\"DOCUMENT_ROOT\"]=substr(\$_SERVER['SCRIPT_FILENAME'] , 0 , -strlen(\$_SERVER['PHP_SELF'])+1 );}\n";
        echo "require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/config.php\";\n";
        echo "\n";
        echo "\n";
        echo "header('Content-type: application/octet-binary');\n";
        echo "header('Content-Disposition: attachment; filename=".$filename.".csv');\n";
        echo "\n";
        echo "\n";

        //echo "$sql\n\n";
        echo "\$sql = \"\";\n";
        $sql = str_replace ("\r\n", "\n", $sql);
        $sqlarray = explode ("\n", $sql);
        foreach($sqlarray as $value) {
            //$value = str_replace ("\n", "", $value);
            if (strlen($value)>0) {
                echo "\$sql .= \"$value \";\n";
            }
        }
        echo "if (strlen(\$orderby)> 0) { \$sql .= \"order by \$orderby \$order \"; }";
        echo "\n";
        echo "\n";


        echo "\$prepstatement = \$db->prepare(\$sql);\n";
        echo "\$prepstatement->execute();\n";
        echo "\$result = \$prepstatement->fetchAll();\n";
        echo "\$resultcount = count(\$result);\n";


        echo "\n";
        echo "\n";
        echo "\$z = 0;\n";
        echo "foreach(\$result[0] as \$key => \$val) {\n";
        echo "    if (\$z == 0) {\n";
        echo "        echo '\"'.\$key.'\"';\n";
        echo "    }\n";
        echo "    else {\n";
        echo "        echo ',\"'.\$key.'\"';\n";
        echo "    }\n";
        echo "    \$z++;\n";
        echo "}\n";
        echo "echo \"\\n\";\n";
        echo "\n";
        echo "\n";
        echo "\$x=0;\n";
        echo "while(true) {\n";
        echo "\n";
        echo "    \$z = 0;\n";
        echo "    foreach(\$result[0] as \$key => \$val) {\n";
        echo "        if (\$z == 0) {\n";
        echo "            echo '\"'.\$result[\$x][\$key].'\"';\n";
        echo "        }\n";
        echo "        else {\n";
        echo "            echo ',\"'.\$result[\$x][\$key].'\"';\n";
        echo "        }\n";
        echo "        \$z++;\n";
        echo "    }\n";
        echo "    echo \"\\n\";\n";
        echo "\n";
        echo "    ++\$x;\n";
        echo "    if (\$x > (\$resultcount-1)) {\n";
        echo "        break;\n";
        echo "    }\n";
        echo "    //\$row++;\n";
        echo "}\n";
        echo "\n";
        echo "unset (\$resultcount);\n";
        echo "unset (\$resulttype);\n";
        echo "unset (\$result);\n";
        echo "unset (\$key);\n";
        echo "unset (\$val);\n";
        echo "unset (\$msg);\n";
        echo "unset (\$errormsg);\n";
        echo "unset (\$sql);\n";
        echo "unset (\$x);\n";
        echo "unset (\$z);";
        echo "\n";
        echo "?>\n";


    }
    else { //no results


        echo "if(!isset(\$_SERVER[\"DOCUMENT_ROOT\"])) { \$_SERVER[\"DOCUMENT_ROOT\"]=substr(\$_SERVER['SCRIPT_FILENAME'] , 0 , -strlen(\$_SERVER['PHP_SELF'])+1 );}\n";
        echo "require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/config.php\";\n";

        echo "<div align='center'>";
        echo "<table border='0' cellpadding='0' cellspacing='2'>\n";

        echo "<tr class='border'>\n";
        echo "	<td align=\"left\">\n";
        echo "      SQL Query:<br><b>".$sql."</b><br><br>";
        echo "      <br>";

        echo $msg;
        echo $errormsg;

        echo "<div align='left'>\n";
        echo "<table border='0' cellpadding='1' cellspacing='1'>\n";
        echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";

        echo "<tr><td>&nbsp;</td></tr>";

        echo "</table>\n";
        echo "</div>";

        echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";
        echo "</div>";


        echo "<br><br>";
        require_once("footer.php");

        unset ($msg);
        unset ($errormsg);
        unset ($sql);

    } //end if no results

} //end if csv


if ($resulttype == "phplist") {

    //require_once "sql/sqlitetblinfo.php";
    require_once "sqlitetblinfo.php";


    header('Content-type: application/octet-binary');
    header('Content-Disposition: attachment; filename='.$filename.'list.php');

    echo "<?php\n";
    echo "if(!isset(\$_SERVER[\"DOCUMENT_ROOT\"])) { \$_SERVER[\"DOCUMENT_ROOT\"]=substr(\$_SERVER['SCRIPT_FILENAME'] , 0 , -strlen(\$_SERVER['PHP_SELF'])+1 );}\n";
    echo "require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/config.php\";\n";
    echo "\n";

    echo "if (!function_exists('thorderby')) {\n";
    echo "    //html table header order by\n";
    echo "    function thorderby(\$fieldname, \$columntitle, \$orderby, \$order) {\n";
    echo "\n";
    echo "        \$html .= \"<th nowrap>&nbsp; &nbsp; \";\n";
    echo "        if (strlen(\$orderby)==0) {\n";
    echo "          \$html .= \"<a href='?orderby=\$fieldname&order=desc' title='ascending'>\$columntitle</a>\";\n";
    echo "        }\n";
    echo "        else {\n";
    echo "          if (\$order==\"asc\") {\n";
    echo "              \$html .= \"<a href='?orderby=\$fieldname&order=desc' title='ascending'>\$columntitle</a>\";\n";
    echo "          }\n";
    echo "          else {\n";
    echo "              \$html .= \"<a href='?orderby=\$fieldname&order=asc' title='descending'>\$columntitle</a>\";\n";
    echo "          }\n";
    echo "        }\n";
    echo "        \$html .= \"&nbsp; &nbsp; </th>\";\n";
    echo "\n";
    echo "        return \$html;\n";
    echo "    }\n";
    echo "}\n";
    echo "//example use\n";
    echo "//echo thorderby('id', 'Id', \$orderby, \$order);";

    echo "\n";

    echo "require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/header.php\";\n";
    echo "\n";
    echo "\$orderby = \$_GET[\"orderby\"];\n";
    echo "\$order = \$_GET[\"order\"];\n";
    echo "\n";
    echo "    echo \"<div align='center'>\";\n";

    echo "    echo \"<table border='0' cellpadding='0' cellspacing='2'>\\n\";\n";
    echo "\n";
    echo "    echo \"<tr class='border'>\\n\";\n";
    echo "    echo \"	<td align=\\\"left\\\">\\n\";\n";
    //echo "    echo \"      SQL Query:<br><b>\".\$sql.\"</b><br><br>\";\n";
    echo "    echo \"      <br>\";\n";
    echo "\n";
    echo "\n";

    echo "    echo \"<table width='100%' border='0'><tr>\";\n";
    echo "    echo \"<td width='50%' nowrap><b>".ucwords($filename)." List</b></td>\";\n";
    echo "    echo \"<td width='50%' align='right'><input type='button' class='btn' name='' onclick=\\\"window.location='".$filename."add.php'\\\" value='Add'></td>\\n\";\n";
    echo "    echo \"</tr></table>\";";

    echo "\n";
    echo "\n";

    //echo "    echo \$msg;\n";
    //echo "    echo \$errormsg;\n";

    //echo "$sql\n\n";
    echo "    \$sql = \"\";\n";
    $sql = str_replace ("\r\n", "\n", $sql);
    $sqlarray = explode ("\n", $sql);
    foreach($sqlarray as $value) {
        //$value = str_replace ("\n", "", $value);
        if (strlen($value)>0) {
            echo "    \$sql .= \"$value \";\n";
        }
    }
    echo "    if (strlen(\$orderby)> 0) { \$sql .= \"order by \$orderby \$order \"; }";
    echo "\n";
    echo "\n";


    echo "    \$prepstatement = \$db->prepare(\$sql);\n";
    echo "    \$prepstatement->execute();\n";
    echo "    \$result = \$prepstatement->fetchAll();\n";
    echo "    \$resultcount = count(\$result);";


    echo "\n";
    echo "\n";
    echo "    \$c = 0;\n";
    echo "    \$rowstyle[\"0\"] = \"background-color: #F5F5DC;\";\n";
    echo "    \$rowstyle[\"1\"] = \"background-color: #FFFFFF;\";\n";
    echo "\n";
    echo "    echo \"<div align='left'>\\n\";\n";
    echo "    echo \"<table border='0' cellpadding='1' cellspacing='1'>\\n\";\n";
    echo "    echo \"<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\";\n";
    echo "\n";
    echo "    if (\$resultcount == 0) { //no results\n";
    echo "        echo \"<tr><td>&nbsp;</td></tr>\";\n";
    echo "    }\n";
    echo "    else { //received results\n";
    echo "\n";
    echo "        echo \"<tr>\";\n";
    foreach($result[0] as $key => $val) {
            //echo thorderby('contactcategory', 'Category', $orderby, $order);
            echo "        echo thorderby('".$key."', '".ucwords($key)."', \$orderby, \$order);\n";
    }
    echo "        echo \"</tr>\";\n";
    echo "        echo \"<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\\n\";\n";
    echo "\n";


    echo "        foreach(\$result as \$row) {\n";
    echo "        //print_r( \$row );\n";

        //$x=0;
        //while($row = $result) {
            echo "            echo \"<tr style='\".\$rowstyle[\$c].\"'>\\n\";\n";
                foreach($result[0] as $key => $val) {
                    //echo "    echo \"<td>\".\$row[var1].\"</td>\";\n";
                    $urlget = '?';
                    $pkarray = getpkarray ($dbmem, $tblname);
                    foreach($pkarray as $fieldname) {
                        $urlget .= "&".$fieldname."=\".\$row[$key].\"";
                    }
                    $urlget = str_replace ("?&", "?", $urlget);
                    if (ispk($dbmem, $tblname, $key)) { //if not a integer and primary key (auto increment value)
                        echo "                echo \"<td valign='top'><a href='".$filename."update.php".$urlget."'>\".\$row[".$key."].\"</a></td>\";\n";
                    }
                    else {
                        echo "                echo \"<td valign='top'>\".\$row[".$key."].\"</td>\";\n";
                    }


                    //echo "              echo \"<td valign='top'>&nbsp;\".\$result[\$x][\$key].\"&nbsp;</td>\\n\";\n";
                } //foreach
            echo "            echo \"</tr>\";\n";
            echo "\n";
            //++$x;
            //if ($x > ($resultcount-1)) {
            //    break;
            //}
            echo "            echo \"<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\\n\";\n";
            echo "            if (\$c==0) { \$c=1; } else { \$c=0; }\n";
            //$row++;
        //}

    echo "        } //end foreach";
    echo "        unset(\$sql, \$result, \$rowcount);";

    echo "\n";
    echo "\n";
    //echo "        echo \"<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\";\n";

    echo "\n";
    echo "    } //end if results\n";
    echo "\n";


    echo "        echo \"</table>\\n\";\n";
    echo "        echo \"</div>\\n\";\n";
    echo "\n";
    echo "        echo \"  <br><br>\";\n";
    echo "        echo \"  </td>\\n\";\n";
    echo "        echo \"</tr>\\n\";\n";
    echo "    echo \"</table>\\n\";\n";
    echo "    echo \"<input type='button' class='btn' name='' onclick=\\\"window.location='".$filename."search.php'\\\" value='Search'>&nbsp; &nbsp;\\n\";\n";
    echo "    echo \"<input type='button' class='btn' name='' onclick=\\\"window.location='".$filename."add.php'\\\" value='Add'>&nbsp; &nbsp;\\n\";\n";
    echo "    echo \"</div>\";\n";
    echo "\n";
    echo "    echo \"<br><br>\";\n";
    echo "    require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/footer.php\";\n";
    echo "\n";
    echo "    unset (\$resultcount);\n";
    echo "    unset (\$result);\n";
    echo "    unset (\$key);\n";
    echo "    unset (\$val);\n";
    echo "    unset (\$c);";

    echo "\n\n";
    echo "?>\n";

} //phplist




if ($resulttype == "phpadd") {
    //require_once "sql/sqlitetblinfo.php";
    require_once "sqlitetblinfo.php";
    
    header('Content-type: application/octet-binary');
    header('Content-Disposition: attachment; filename='.$filename.'add.php');

    echo "<?php\n";
    echo "if(!isset(\$_SERVER[\"DOCUMENT_ROOT\"])) { \$_SERVER[\"DOCUMENT_ROOT\"]=substr(\$_SERVER['SCRIPT_FILENAME'] , 0 , -strlen(\$_SERVER['PHP_SELF'])+1 );}\n";
    echo "require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/config.php\";\n";
    echo "\n";
    echo "if (count(\$_POST)>0) {\n";
    echo "\n";

    foreach($result[0] as $key => $val) {
        if (!ispkint($dbmem, $tblname, $key)) { //if not a integer and primary key (auto increment value)
            echo "    \$$key = \$_POST[\"$key\"];\n";
        }
    }
    echo "\n";

    echo "    \$sql = \"insert into $tblname \";\n";
    //echo "sql $sql<br>";
    //echo "tblname $tblname<br>";
    echo "    \$sql .= \"(\";\n";
    //echo "    \$sql .= \"username, \";";
    //echo "    //count: ".count($result[0]);
    $countpkautoint = count(getpkintarray ($dbmem, $tblname));
    $count = count($result[0])-$countpkautoint;
    $row = 1;
    foreach($result[0] as $key => $val) {
        if (!ispkint($dbmem, $tblname, $key)) { //if not a integer and primary key (auto increment value)
            if ($row < $count) { echo "    \$sql .= \"$key, \";\n"; }
            if ($row == $count) { echo "    \$sql .= \"$key \";\n"; }
            $row++;
        }
    }
    unset($count, $row);
    echo "    \$sql .= \")\";\n";
    echo "    \$sql .= \"values \";\n";
    echo "    \$sql .= \"(\";\n";
    //echo "    \$sql .= \"'johndedoe', \";";
    $countpkautoint = count(getpkintarray ($dbmem, $tblname));
    $count = count($result[0])-$countpkautoint;
    $row = 1;
    foreach($result[0] as $key => $val) {
        if (!ispkint($dbmem, $tblname, $key)) { //if not a integer and primary key (auto increment value)
            if ($row < $count) { echo "    \$sql .= \"'\$$key', \";\n"; }
            if ($row == $count) { echo "    \$sql .= \"'\$$key' \";\n"; }
            $row++;
        }
    }
    unset($count, $row);
    echo "    \$sql .= \")\";\n";
    //echo "    \$sql = str_replace (\", )\", \")\", \$sql);\n";

    echo "    \$db->exec(\$sql);\n";
    echo "    \$lastinsertid = \$db->lastInsertId(\$id);\n";
    echo "    unset(\$sql,\$db);\n\n";

    //after insert move to meta redirect
    echo "    require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/header.php\";\n";
    echo "    echo \"<meta http-equiv=\\\"refresh\\\" content=\\\"2;url=".$filename."list.php\\\">\\n\";\n";
    echo "    echo \"<div align='center'>\\n\";\n";
    echo "    echo \"Add Complete\\n\";\n";
    echo "    echo \"</div>\\n\";\n";
    echo "    require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/footer.php\";\n";
    echo "    return;";
    echo "\n";
    echo "}\n"; //end add

    echo "\n";

    echo "    require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/header.php\";\n";
    echo "    echo \"<div align='center'>\";\n";
    echo "    echo \"<table width='300%' border='0' cellpadding='0' cellspacing='2'>\\n\";\n";
    echo "\n";
    echo "    echo \"<tr class='border'>\\n\";\n";
    echo "    echo \"	<td align=\\\"left\\\">\\n\";\n";
    echo "    echo \"      <br>\";\n";
    echo "\n";
    echo "\n";

    echo "    echo \"<table width='100%' border='0'><tr>\";\n";
    echo "    echo \"<td width='50%' nowrap><b>".ucwords($filename)." Add</b></td>\";\n";
    echo "    echo \"<td width='50%' align='right'><input type='button' class='btn' name='' onclick=\\\"window.location='".$filename."list.php'\\\" value='Back'></td>\\n\";\n";
    echo "    echo \"</tr></table>\";";

    echo "\n";
    echo "\n";
    echo "    echo \"<form method='post' action=''>\";\n";
    echo "    echo \"<table>\";\n";

    foreach($result[0] as $key => $val) {
        if (!ispkint($dbmem, $tblname, $key)) { //if not a integer and primary key (auto increment value)
            echo "      echo \"	<tr>\";\n";
            echo "      echo \"		<td nowrap>".ucwords($key).":</td>\";\n";
            echo "      echo \"		<td width='100%'><input type='text' class='txt' name='".$key."'></td>\";\n";
            echo "      echo \"	</tr>\";\n";
        }
    }
    echo "\n";
    echo "    //echo \"	<tr>\";\n";
    echo "    //echo \"	<td>example:</td>\";\n";
    echo "    //echo \"	<td><textarea name='example'></textarea></td>\";\n";
    echo "    //echo \"	</tr>\";\n";
    echo "\n";
    echo "    echo \"	<tr>\\n\";\n";
    echo "    echo \"		<td colspan='2' align='right'>\\n\";\n";
    echo "    echo \"		    <input type='submit' name='submit' class='btn' value='Add'>\\n\";\n";
    echo "    echo \"		</td>\\n\";\n";
    echo "    echo \"	</tr>\";\n";
    echo "    echo \"</table>\";\n";
    echo "    echo \"</form>\";\n";
    echo "\n";
    echo "\n";
    echo "    echo \"	</td>\";\n";
    echo "    echo \"	</tr>\";\n";
    echo "    echo \"</table>\";\n";

    echo "    echo \"</div>\";\n";

    echo "\n\n";
    echo "require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/footer.php\";\n";
    echo "?>\n";

} //end phpadd

if ($resulttype == "phpupdate") {

    //require_once "sql/sqlitetblinfo.php";
    require_once "sqlitetblinfo.php";
    header('Content-type: application/octet-binary');
    header('Content-Disposition: attachment; filename='.$filename.'update.php');

    echo "<?php\n";
    echo "if(!isset(\$_SERVER[\"DOCUMENT_ROOT\"])) { \$_SERVER[\"DOCUMENT_ROOT\"]=substr(\$_SERVER['SCRIPT_FILENAME'] , 0 , -strlen(\$_SERVER['PHP_SELF'])+1 );}\n";
    echo "require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/config.php\";\n";
    echo "\n";

    echo "if (count(\$_POST)>0) {\n";
    echo "\n";
    //echo "if (strlen(\$_POST[\"submit\"]) > 0) {\n";
    foreach($result[0] as $key => $val) {
        //if (!ispkint($dbmem, $tblname, $key)) { //if not a integer and primary key (auto increment value)
            echo "    \$$key = \$_POST[\"$key\"];\n";
        //}
    }
    echo "\n";



    echo "    //sql update\n";
    echo "    \$sql  = \"update $tblname set \";\n";
    //echo "\$sql .= \"password = '456' \";";
    $countpkautoint = count(getpkintarray ($dbmem, $tblname));
    $count = count($result[0])-$countpkautoint;
    $row = 0;
    foreach($result[0] as $key => $val) {
        if (!ispkint($dbmem, $tblname, $key)) { //if not a integer and primary key (auto increment value)
            if ($row < $count) {
                echo "    \$sql .= \"$key = '\$$key', \";\n";
            }
            if ($row == $count) {
                echo "    \$sql .= \"$key = '\$$key' \";\n";
            }
        }
        $row++;
    }

    unset($count, $row);
    $pkarray = getpkarray ($dbmem, $tblname);
    $x = 0;
    foreach($pkarray as $fieldname) {
        if ($x == 0) {
              echo "    \$sql .= \"where $fieldname = '\$$fieldname' \";\n";
        }
        else {
              echo "    \$sql .= \"and $fieldname = '\$$fieldname' \";\n";
        }
        $x++;
    }
    echo "    \$count = \$db->exec(\$sql);\n";
    echo "    //echo \"Affected Rows: \".\$count;\n\n";


    //after update move to meta redirect
    echo "    //edit: make sure the meta redirect url is correct \n";
    echo "    require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/header.php\";\n";
    echo "    echo \"<meta http-equiv=\\\"refresh\\\" content=\\\"2;url=".$filename."list.php\\\">\\n\";\n";
    echo "    echo \"<div align='center'>\\n\";\n";
    echo "    echo \"Update Complete\\n\";\n";
    echo "    echo \"</div>\\n\";\n";
    echo "    require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/footer.php\";\n";

    echo "    return;";
    echo "\n";
    echo "}\n"; //end update

    echo "else {\n";

    echo "  //get data from the db\n";
    //echo "  \$id = \$_GET[\"id\"];";
    $pkarray = getpkarray ($dbmem, $tblname);
    //print_r( $pkarray );
    foreach($pkarray as $fieldname) {
      //$urlget .= "&".$fieldname."=\".\$row[$key].\"";
      echo "    \$$fieldname = \$_GET[\"$fieldname\"];\n";
    }
    
      //print_r($result[0]);
      //echo "\n";
      echo "\n";

      echo "    \$sql = \"\";\n";
      $sql = str_replace ("\r\n", "\n", $sql);
      $sqlarray = explode ("\n", $sql);
      foreach($sqlarray as $value) {
          //$value = str_replace ("\n", "", $value);
          if (strlen(trim($value)) > 0) {
            echo "    \$sql .= \"$value \";\n";
          }
      }

      $pkarray = getpkarray ($dbmem, $tblname);
      $x = 0;
      foreach($pkarray as $fieldname) {
          if ($x == 0) {
                echo "    \$sql .= \"where $fieldname = '\$$fieldname' \";\n";
          }
          else {
                echo "    \$sql .= \"and $fieldname = '\$$fieldname' \";\n";
          }
          $x++;
      }
      echo "    \$prepstatement = \$db->prepare(\$sql);\n";
      echo "    \$prepstatement->execute();\n";
      echo "\n";
      echo "    \$result = \$prepstatement->fetchAll();\n";
      echo "    foreach (\$result as &\$row) {\n";
          foreach($result[0] as $key => $val) {
              if (!ispkint($dbmem, $tblname, $key)) { //if not a integer and primary key (auto increment value)
                  echo "        \$$key = \$row[\"$key\"];\n";
              }
          }

      echo "          break; //limit to 1 row\n";
      echo "    }\n"; //end while



    echo "}\n"; //else
    echo "\n";


    echo "    require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/header.php\";\n";

    echo "    echo \"<div align='center'>\";\n";
    echo "    echo \"<table border='0' cellpadding='0' cellspacing='2'>\\n\";\n";
    echo "\n";
    echo "    echo \"<tr class='border'>\\n\";\n";
    echo "    echo \"	<td align=\\\"left\\\">\\n\";\n";

    echo "    echo \"      <br>\";\n";

    echo "\n";
    echo "\n";

    echo "    echo \"<table width='100%' border='0'><tr>\";\n";
    echo "    echo \"<td width='50%' nowrap><b>".ucwords($filename)." Update</b></td>\";\n";
    echo "    echo \"<td width='50%' align='right'><input type='button' class='btn' name='' onclick=\\\"window.location='".$filename."list.php'\\\" value='Back'></td>\\n\";\n";
    echo "    echo \"</tr></table>\";";

    echo "\n";
    echo "\n";

    echo "    echo \"<form method='post' action=''>\";\n";
    echo "    echo \"<table>\";\n";

    foreach($result[0] as $key => $val) {
        if (!ispkint($dbmem, $tblname, $key)) { //if not a integer and primary key (auto increment value)
            echo "      echo \"	<tr>\";\n";
            echo "      echo \"		<td>".ucwords($key).":</td>\";\n";
            echo "      echo \"		<td><input type='text' name='".$key."' class='txt' value='\$$key'></td>\";\n";
            echo "      echo \"	</tr>\";\n";
        }
    }
    
    
//    foreach($pkarray as $fieldname) {
//      //$urlget .= "&".$fieldname."=\".\$row[$key].\"";
//      echo "  \$$fieldname = \$_GET[\"$fieldname\"];\n";
//    }
    echo "      echo \"	<tr>\\n\";\n";
    echo "      echo \"	<td colspan='2' align='right'>\\n\";\n";
    foreach($pkarray as $fieldname) {
        //$urlget .= "&".$fieldname."=\".\$row[$key].\"";
        //echo "  \$$fieldname = \$_GET[\"$fieldname\"];\n";
        echo "      echo \"           <input type='hidden' name='$fieldname' value='\$$fieldname'>\";\n";
    }
    echo "      echo \"           <input type='submit' name='submit' class='btn' value='Update'>\\n\";\n";
    echo "      echo \"		</td>\";\n";
    echo "      echo \"	</tr>\";\n";
    echo "    echo \"</table>\";\n";
    echo "    echo \"</form>\";\n";
    echo "\n";
    echo "\n";
    echo "    echo \"	</td>\";\n";
    echo "    echo \"	</tr>\";\n";
    echo "    echo \"</table>\";\n";

    echo "    echo \"</div>\";\n";

    echo "\n\n";
    echo "  require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/footer.php\";\n";
    echo "?>\n";

} //end phpedit


if ($resulttype == "phpsearch") {

    //require_once "sql/sqlitetblinfo.php";
    require_once "sqlitetblinfo.php";

    header('Content-type: application/octet-binary');
    header('Content-Disposition: attachment; filename='.$filename.'search.php');

    echo "<?php\n";
    echo "if(!isset(\$_SERVER[\"DOCUMENT_ROOT\"])) { \$_SERVER[\"DOCUMENT_ROOT\"]=substr(\$_SERVER['SCRIPT_FILENAME'] , 0 , -strlen(\$_SERVER['PHP_SELF'])+1 );}\n";
    echo "require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/config.php\";\n";
    echo "\n";

    echo "if (count(\$_POST)>0) {\n";
    //--Begin Search Result List ---------------------


        foreach($result[0] as $key => $val) {
            echo "    \$$key = \$_POST[\"$key\"];\n";
        }
        echo "\n\n";


        echo "    require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/header.php\";\n";

        echo "    echo \"<div align='center'>\";\n";
        echo "    echo \"<table border='0' cellpadding='0' cellspacing='2'>\\n\";\n";
        echo "\n";
        echo "    echo \"<tr class='border'>\\n\";\n";
        echo "    echo \"	<td align=\\\"left\\\">\\n\";\n";
        //echo "    echo \"      SQL Query:<br><b>\".\$sql.\"</b><br><br>\";\n";
        echo "    echo \"      <br>\";\n";
        echo "\n";
        echo "\n";
        //echo "    echo \$msg;\n";
        //echo "    echo \$errormsg;\n";

        //echo "$sql\n\n";
        echo "    \$sql = \"\";\n";
        $sql = str_replace ("\r\n", "\n", $sql);
        $sqlarray = explode ("\n", $sql);
        foreach($sqlarray as $value) {
            //$value = str_replace ("\n", "", $value);
            echo "    \$sql .= \"$value \";\n";
        }
        echo "    \$sql .= \"where \";\n";
        //echo "if (strlen(\$_POST[\"submit\"]) > 0) {\n";
        foreach($result[0] as $key => $val) {
            echo "    if (strlen(\$$key) > 0) { \$sql .= \"and $key like '%\$$key%' \"; }\n";
            //echo "    \$$key = \$_POST[\"$key\"];\n";
        }
        echo "\n\n";
        echo "    \$sql = trim(\$sql);\n";
        echo "    if (substr(\$sql, -5) == \"where\"){ \$sql = substr(\$sql, 0, (strlen(\$sql)-5)); }\n";
        echo "    \$sql = str_replace (\"where and\", \"where\", \$sql);\n";

        echo "    \$prepstatement = \$db->prepare(\$sql);\n";
        echo "    \$prepstatement->execute();\n";
        echo "    \$result = \$prepstatement->fetchAll();\n";
        echo "    \$resultcount = count(\$result);";


        echo "\n";
        echo "\n";
        echo "    \$c = 0;\n";
        echo "    \$rowstyle[\"0\"] = \"background-color: #F5F5DC;\";\n";
        echo "    \$rowstyle[\"1\"] = \"background-color: #FFFFFF;\";\n";
        echo "\n";
        echo "    echo \"<div align='left'>\\n\";\n";
        echo "    echo \"<table border='0' cellpadding='1' cellspacing='1'>\\n\";\n";
        echo "    echo \"<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\";\n";
        echo "\n";
        echo "    if (\$resultcount == 0) { //no results\n";
        echo "        echo \"<tr><td>&nbsp;</td></tr>\";\n";
        echo "    }\n";
        echo "    else { //received results\n";
        echo "\n";
        echo "        echo \"<tr>\";\n";
        foreach($result[0] as $key => $val) {
                echo "          echo \"<th nowrap>&nbsp; &nbsp; ".ucwords($key)."&nbsp; &nbsp; </th>\";\n";
        }
        echo "        echo \"</tr>\";\n";
        echo "        echo \"<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\\n\";\n";
        echo "\n";


        echo "        foreach(\$result as \$row) {\n";
        echo "        //print_r( \$row );\n";

            //$x=0;
            //while($row = $result) {
                echo "            echo \"<tr style='\".\$rowstyle[\$c].\"'>\\n\";\n";
                  
                    foreach($result[0] as $key => $val) {
                    
                        $urlget = '?';
                        $pkarray = getpkarray ($dbmem, $tblname);
                        foreach($pkarray as $fieldname) {
                            $urlget .= "&".$fieldname."=\".\$row[$key].\"";
                        }
                        $urlget = str_replace ("?&", "?", $urlget);
                        if (ispk($dbmem, $tblname, $key)) { //if not a integer and primary key (auto increment value)
                            echo "                echo \"<td valign='top'><a href='".$filename."update.php".$urlget."'>\".\$row[".$key."].\"</a></td>\";\n";
                        }
                        else {
                            echo "                echo \"<td valign='top'>\".\$row[".$key."].\"</td>\";\n";
                        }
                    
                        //echo "    echo \"<td>\".\$row[var1].\"</td>\";\n";
                        //echo "                echo \"<td valign='top'>\".\$row[".$key."].\"</td>\";\n";
                        //echo "              echo \"<td valign='top'>&nbsp;\".\$result[\$x][\$key].\"&nbsp;</td>\\n\";\n";
                    } //foreach
                echo "            echo \"</tr>\";\n";
                echo "\n";
                //++$x;
                //if ($x > ($resultcount-1)) {
                //    break;
                //}
                echo "            echo \"<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\\n\";\n";
                echo "            if (\$c==0) { \$c=1; } else { \$c=0; }\n";
                //$row++;
            //}

        echo "        } //end foreach";
        echo "        unset(\$sql, \$result, \$rowcount);";

        echo "\n";
        echo "\n";
        //echo "        echo \"<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\";\n";
        echo "\n";
        echo "    } //end if results\n";
        echo "\n";
        echo "    echo \"</table>\\n\";\n";
        echo "    echo \"</div>\\n\";\n";
        echo "\n";
        echo "    echo \"  <br><br>\";\n";
        echo "    echo \"  </td>\\n\";\n";
        echo "    echo \"</tr>\\n\";\n";
        echo "    echo \"</table>\\n\";\n";
        echo "    echo \"</div>\";\n";
        echo "\n";
        echo "    echo \"<br><br>\";\n";
        echo "    require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/footer.php\";\n";
        echo "\n";
        echo "    unset (\$resultcount);\n";
        echo "    unset (\$result);\n";
        echo "    unset (\$key);\n";
        echo "    unset (\$val);\n";
        echo "    unset (\$c);";

        echo "\n\n";


    //-- End Search Result List ----------------------
    echo "    }\n";
    echo "    else {\n";
    echo "\n";
    echo "        echo \"\\n\";";


        echo "    require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/header.php\";\n";
        echo "    echo \"<div align='center'>\";\n";
        echo "    echo \"<table border='0' cellpadding='0' cellspacing='2'>\\n\";\n";
        echo "\n";
        echo "    echo \"<tr class='border'>\\n\";\n";
        echo "    echo \"	<td align=\\\"left\\\">\\n\";\n";

        echo "    echo \"      <br>\";\n";
        echo "\n";
        echo "\n";

        echo "    echo \"<form method='post' action=''>\";\n";
        echo "    echo \"<table>\";\n";

        //echo "    //edit: remove the auto increment id from the form \n";
        foreach($result[0] as $key => $val) {
            echo "      echo \"	<tr>\";\n";
            echo "      echo \"		<td>".ucwords($key).":</td>\";\n";
            echo "      echo \"		<td><input type='text' class='txt' name='".$key."'></td>\";\n";
            echo "      echo \"	</tr>\";\n";
        }


        echo "    echo \"	<tr>\";\n";
        echo "    echo \"		<td colspan='2' align='right'><input type='submit' name='submit' value='Search'></td>\";\n";
        echo "    echo \"	</tr>\";\n";
        echo "    echo \"</table>\";\n";
        echo "    echo \"</form>\";\n";
        echo "\n";
        echo "\n";
        echo "    echo \"	</td>\";\n";
        echo "    echo \"	</tr>\";\n";
        echo "    echo \"</table>\";\n";

        echo "    echo \"</div>\";\n";

        echo "\n\n";
        echo "require_once \$_SERVER[\"DOCUMENT_ROOT\"].\"/includes/footer.php\";\n";
        echo "\n";
    echo "} //end if not post";
    echo "\n";
    echo "?>\n";

} //end phpsearch




if ($resulttype == "sqlinsert") {

    $sql = '';
    $sql .= "insert into $tblname\n";
    $sql .= "(\n";
    foreach($result[0] as $key => $val) {
      $sql .= "".$key.", \n";
    }
    $sql .= ")\n";
    $sql .= "values ";
    $sql .= "(\n";
    foreach($result[0] as $key => $val) {
      $sql .= "'', \n";
    }
    $sql .= ")\n";
    $sql = str_replace (", \n)", " \n)", $sql);

    //echo "<textarea>";
    echo $sql;
    //echo "</textarea>";


    unset ($resultcount);
    unset ($resulttype);
    unset ($result);
    unset ($key);
    unset ($val);
    unset ($msg);
    unset ($errormsg);
    unset ($sql);
    unset ($x);
    unset ($c);

} //end if sqlinsert

//------------------------------------------------------------------------------



if ($resulttype == "sqlcreatetbl") {
    //dependent on sqlite

    $tblsql = str_replace (", ", ", \r\n", $tblsql);
    echo "<pre>";
    echo $tblsql;
    echo "</pre>";

} //end if sqlcreatetbl





//------------------------------------------------------------------------------















if (strlen($tblname) > 0) {
    if ($temprecordid > 0) {
        if (strlen($temprecordprimarykey) > 0) {
            //remove the temporary record that was used to provide access to the field names
            $sql = "delete from $tblname where $temprecordprimarykey = $temprecordid";
            //echo $sql;
            $db->exec(check_sql($sql));
        }
    }

}
?>
