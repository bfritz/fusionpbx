<?php

function persistform($formarray) {
	// Remember Form Input Values
	if(is_array($formarray)) {
	  $content .= "<form method='post' action='".$_SERVER["HTTP_REFERER"]."' target='_self'>\n";
	 foreach($formarray as $key => $val) {
	   if($key == "XID" || $key == "ACT" || $key == "RET") continue;
       if ($key != "persistform") { //clears the persistform value
            $content .= "<input type='hidden' name='$key' value='$val' />\n";
       }
	 }
     $content .= "<input type='hidden' name='persistform' value='1' />\n"; //sets persistform to yes
	 $content .= "<input class='btn' type='submit' value='Back' />\n";
	 $content .= "</form>\n";
	}
	return $content;
}
//persistform($_POST);
//persistform($_GET);

?>
