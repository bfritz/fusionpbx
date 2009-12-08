<?php

function persistformvar($formarray) {
	// Remember Form Input Values
	if(is_array($formarray)) {
	  $content .= "<form method='post' action='".$_SERVER["HTTP_REFERER"]."' target='_self'>\n";
	 foreach($formarray as $key => $val) {
	   if($key == "XID" || $key == "ACT" || $key == "RET") continue;
       if ($key != "persistform") { //clears the persistform value
            $content .= "<input type='hidden' name='$key' value='$val' />\n";
       }
	 }
    	$content .= "<input type='hidden' name='persistformvar' value='true' />\n"; //sets persistform to yes
	 $content .= "<input class='btn' type='submit' value='Back' />\n";
	 $content .= "</form>\n";
	}
	echo $content;
	//return $content;
}
//persistformvar($_POST);
//persistformvar($_GET);

?>
