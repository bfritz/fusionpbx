<?php

include "root.php";
$_SESSION["username"] = "";
$_SESSION["permissions"] = "";
session_start();
session_destroy();
header("Location: ".PROJECT_PATH."/login.php");
return;

?>
