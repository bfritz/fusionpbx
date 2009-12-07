<?php
/* $Id$ */
/*
	header.php
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

echo "<html>";
echo "<head>";
echo "<title></title>";
//echo "<link href='style.css' rel='stylesheet' type='text/css'>";
echo "<style type='text/css'>";
echo "<!--\n";

//echo "body {\n";
//echo "	margin-top: 40px;\n";
//echo "	margin-bottom: 40px;\n";
//echo "	margin-right: 40px;\n";
//echo "	margin-left: 40px;\n";
//echo "    background-color: #EEEEEE;\n";
//echo "}\n";

echo "th {\n";
echo "	color: #5f5f5f;\n";
echo "	font-size: 12px;\n";
echo "	font-family: arial;\n";
echo "	font-weight: bold;\n";
echo "	background-color: #EFEFEF;\n";
echo "}\n";

echo "BODY {\n";
//echo "	color: #5f5f5f;\n";
echo "	font-size: 11px;\n";
echo "	font-family: arial;\n";
//echo "	background-color: #FFFFFF;\n";
echo "}\n";

echo "TD {\n";
//echo "	color: #5f5f5f;\n";
echo "	font-size: 11px;\n";
echo "	font-family: arial;\n";
//echo "	background-color: #FFFFFF;\n";
echo "}\n";


echo "INPUT, SELECT, TEXTAREA {\n";
//echo "    color: #666666;\n";
//echo "	font-family: verdana;\n";
echo "	font-size: 11px;\n";
echo "    }\n";

//echo ".border {\n";
//echo "    border: solid 1px #CCCCCC;\n";
//echo "}\n";

echo ".btn {\n";
echo "    width: 100%;\n";
echo "}\n";


echo ".txt {\n";
echo "    width: 100%;\n";
echo "}\n";

//echo ".frm {\n";
//echo "    color: #666666;\n";
//echo "    background-color: #EFEFEF;\n";
//echo "    width: 100%;\n";
//echo "}\n";

//echo ".smalltext {\n";
//echo "	color: #666666;\n";
//echo "	font-size: 11px;\n";
//echo "	font-family: arial;\n";
//echo "}";
echo "//-->\n";
echo "</style>";


echo "<SCRIPT language=\"JavaScript\">\n";
echo "<!--\n";
echo "function confirmdelete(url)\n";
echo "{\n";
echo " var confirmed = confirm(\"Are you sure want to delete this.\");\n";
echo " if (confirmed == true) {\n";
echo "      window.location=url;\n";
echo " }\n";
echo "}\n";
echo "//-->\n";
echo "</SCRIPT>";
echo "</head>";
echo "<body>";
echo "<div align='center'>";


?>
