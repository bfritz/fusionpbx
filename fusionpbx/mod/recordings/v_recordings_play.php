<?php
/* $Id$ */
/*
	v_recordings_play.php
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
if (ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//require("v_config.inc");

$filename = $_GET['filename'];
$type = $_GET['type']; //moh //rec

?>
<html>
<head>
</head>
<body link="#0000CC" vlink="#0000CC" alink="#0000CC">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
   <tr>
     <td align='center'>
      <b>file: <?php echo$filename?></b>
     </td>
   </tr>
   <tr>
     <td align='center'>
     <?php

      $file_ext = substr($_GET['filename'], -3);
      if ($file_ext == "wav") {
        echo "<embed src=\"v_recordings.php?a=download&type=".$type."&filename=".$filename."\" autostart=true width=200 height=40 name=\"sound".$$filename."\" enablejavascript=\"true\">\n";
      }
      if ($file_ext == "mp3") {
        echo "<object type=\"application/x-shockwave-flash\" width=\"400\" height=\"17\" data=\"slim.swf?autoplay=true&song_title=".urlencode($filename)."&song_url=".urlencode($v_relative_url."/v_recordings.php?a=download&type=".$type."&filename=".$filename)."\">\n";
        echo "<param name=\"movie\" value=\"slim.swf?autoplay=true&song_url=".urlencode($v_relative_url."/v_recordings.php?a=download&type=".$type."&filename=".$filename)."\" />\n";
        echo "<param name=\"quality\" value=\"high\"/>\n";
        echo "<param name=\"bgcolor\" value=\"#E6E6E6\"/>\n";
        echo "</object>\n";
      }

     ?>
     </td>
   </tr>
</table>

</body>
</html>
