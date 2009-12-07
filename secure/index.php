<?php

//block directory browsing and send the user to the main index

include "root.php";
header("Location: ".PROJECT_PATH."/index.php");

?>