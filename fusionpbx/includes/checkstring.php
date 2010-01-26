<?php
function check_string($str) {
    $str = str_replace ("\'", "''", $str); //escape the single quote
    return $str;
}
?>
