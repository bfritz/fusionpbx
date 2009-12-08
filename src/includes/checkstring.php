<?php
function checkstring($str) {
    $str = str_replace ("\'", "''", $str); //escape the single quote
    return $str;
}
?>
