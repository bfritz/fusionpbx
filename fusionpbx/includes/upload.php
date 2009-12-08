<?php
include "root.php";
require_once "includes/config.php";require_once "includes/checkauth.php";

require_once($virtualroot."includes/header.php");

//The hidden MAX_FILE_SIZE field contains the maximum file size accepted, in bytes.
//This cannot be larger than upload_max_filesize in php.ini (default 2MB).

echo "<table width='600' border='0' cellpadding='0' >";

echo "<tr><td colspan='2' align='center'>\n";
echo "<br>";
echo "<form enctype=\"multipart/form-data\" action=\"upload2.php\" method=\"post\">\n";
echo "    <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"1000000\" />\n";
echo "    Upload File: <input name=\"userfile\" type=\"file\" />\n";
echo "    <input type=\"submit\" value=\"Upload File\" />\n";
echo "</form>\n";

echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";


require_once($virtualroot."includes/footer.php");


?>
