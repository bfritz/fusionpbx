<?php
return; //disable

include "root.php";
//require_once "includes/config.php";
//require_once "includes/checkauth.php";
require_once "config.php";
session_start();

require_once "includes/header.php";
echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"\" href=\"rss.php\" />\n";

$_GET["c"] = "html";
require_once "rss.php";
require_once "includes/footer.php";

return;
?>
