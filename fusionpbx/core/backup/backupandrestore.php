<?php
/* $Id$ */
/*
	backupandrestore.php
	Copyright (C) 2008, 2009 Mark J Crane
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

	echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
	echo "<tr>\n";
	echo "	<th colspan='2' align='left'>Backup</th>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
	echo "	<a href='".PROJECT_PATH."/core/backup/backup.php'>download</a>	\n";
	echo "	</td>\n";
	echo "	<td class=\"vtable\">\n";
	echo "To backup your application click on the download link and then choose  \n";
	echo "a safe location on your computer to save the file. You may want to \n";
	echo "save the backup to more than one computer to prevent the backup from being lost. \n";
	echo "	</td>\n";
	echo "</tr>\n";

	echo "</table>\n";
	echo "\n";

	echo "<span  class=\"\" ><strong></strong></span><br>\n";
	echo "<br>";




	echo "<br><br>";

	/*
	echo "<span  class=\"\" >Restore Application</span><br>\n";
	echo "<div class='borderlight' style='padding:10px;'>\n";
	//Browse to  Backup File
	echo "Click on 'Browse' then locate and select the application backup file named '.bak'.  \n";
	echo "Then click on 'Restore.' \n";
	echo "<br><br>";

	echo "<div align='center'>";
	echo "<form name='frmrestore' method='post' action='restore2.php'>";
	echo "	<table border='0' cellpadding='0' cellspacing='0'>";
	echo "	<tr>\n";
	echo "		<td class='' colspan='2' nowrap align='left'>\n";
	echo "          <table width='200'><tr>";
	echo "			<td><input type='file' class='frm' onChange='frmrestore.fileandpath.value = frmrestore.filename.value;' style='font-family: verdana; font-size: 11px;' name='filename'></td>";
	echo "          <td>";
	echo "			<input type='hidden' name='fileandpath' value=''>\n";
	echo "			<input type='submit' class='btn' value='Restore'>\n";
	echo "          </td>";
	echo "          </tr></table>";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "</form>\n";
	echo "</div>";

	echo "</div>";
	*/

 }


?>
