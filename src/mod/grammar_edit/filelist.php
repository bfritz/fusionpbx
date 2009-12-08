<?php
/* $Id$ */
/*
	filelist.php
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
if (ifgroup("admin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "config.php";
require_once "header.php";

function isfile($filename) {
	//if (@filesize($filename) > 0) { return true; } else { return false; }
}

function space($count) {
	$r=''; $i=0;
	while($i < $count) {
		$r .= '     ';
		$i++;
	}
	return $r;
}


function recur_dir($dir) {
	clearstatcache();
	$htmldirlist = '';
	$htmlfilelist = '';
	$dirlist = opendir($dir);
	while ($file = readdir ($dirlist)) {
		if ($file != '.' && $file != '..') {
			$newpath = $dir.'/'.$file;
			$level = explode('/',$newpath);

			if (is_dir($newpath)) {
				/*$mod_array[] = array(
						'level'=>count($level)-1,
						'path'=>$newpath,
						'name'=>end($level),
						'type'=>'dir',
						'mod_time'=>filemtime($newpath),
						'size'=>'');
						$mod_array[] = recur_dir($newpath);
				*/
				$dirname = end($level);
				$htmldirlist .= space(count($level))."<TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD nowrap WIDTH=12></TD><TD nowrap><a onClick=\"Toggle(this)\"><IMG SRC=\"images/plus.gif\"> <IMG SRC=\"images/folder.gif\" border='0'> $dirname </a><DIV style='display:none'>\n";
				//$htmldirlist .= space(count($level))."   <TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD nowrap WIDTH=12></TD><TD nowrap><A onClick=\"Toggle(this)\"><IMG SRC=\"images/plus.gif\"> <IMG SRC=\"images/gear.png\"> Tools </A><DIV style='display:none'>\n";
				//$htmldirlist .= space(count($level))."       <TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD nowrap WIDTH=12></TD><TD nowrap align='bottom'><IMG SRC=\"images/file.png\"><a href='foldernew.php?folder=".urlencode($newpath)."' title=''>New Folder </a><DIV style='display:none'>\n"; //parent.document.getElementById('file').value='".urlencode($newpath)."'
				//$htmldirlist .= space(count($level))."       </DIV></TD></TR></TABLE>\n";
				//$htmldirlist .= space(count($level))."       <TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD nowrap WIDTH=12></TD><TD nowrap align='bottom'><IMG SRC=\"images/file.png\"><a href='filenew.php?folder=".urlencode($newpath)."' title=''>New File </a><DIV style='display:none'>\n"; //parent.document.getElementById('file').value='".urlencode($newpath)."'
				//$htmldirlist .= space(count($level))."       </DIV></TD></TR></TABLE>\n";
				//$htmldirlist .= space(count($level))."   </DIV></TD></TR></TABLE>\n";
				//$htmldirlist .= space(count($level))."       <TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD nowrap WIDTH=12></TD><TD nowrap align='bottom'><IMG SRC=\"images/gear.png\"><a href='fileoptions.php?folder=".urlencode($newpath)."' title=''>Options </a><DIV style='display:none'>\n"; //parent.document.getElementById('file').value='".urlencode($newpath)."'
				//$htmldirlist .= space(count($level))."       </DIV></TD></TR></TABLE>\n";
				$htmldirlist .= recur_dir($newpath);
				$htmldirlist .= space(count($level))."</DIV></TD></TR></TABLE>\n";

			}
			else {
				/*$mod_array[] = array(
					'level'=>count($level)-1,
					'path'=>$newpath,
					'name'=>end($level),
					'type'=>'file',
					'mod_time'=>filemtime($newpath),
					'size'=>filesize($newpath));
				*/
				$filename = end($level);
				$filesize = round(filesize($newpath)/1024, 2);
				$htmlfilelist .= space(count($level))."<TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD nowrap WIDTH=12></TD><TD nowrap align='bottom'><a href='javascript:void(0);' onclick=\"parent.document.title='".$newpath."';parent.document.getElementById('file').value='".urlencode($newpath)."'; parent.window.frames['frame_'+'edit1'].editArea.previous= new Array(); parent.window.frames['frame_'+'edit1'].editArea.switchClassSticky(document.getElementById('undo'), 'editAreaButtonDisabled', true); makeRequest('fileread.php','file=".urlencode($newpath)."'); window.setTimeout('parent.my_setSelectionRange(\'edit1\')','100');\" title='$filesize KB'><IMG SRC=\"images/file.png\" border='none'> $filename </a><DIV style='display:none'>\n";
				$htmlfilelist .=  space(count($level))."</DIV></TD></TR></TABLE>\n";
			}
		}
	}

	closedir($dirlist);
	return $htmldirlist ."\n". $htmlfilelist;
}


	echo "<script type=\"text/javascript\" language=\"javascript\">\n";
	echo "    function makeRequest(url, strpost) {\n";
	//echo "        alert(url); \n";
	echo "        var http_request = false;\n";
	echo "\n";
	echo "        if (window.XMLHttpRequest) { // Mozilla, Safari, ...\n";
	echo "            http_request = new XMLHttpRequest();\n";
	echo "            if (http_request.overrideMimeType) {\n";
	echo "                http_request.overrideMimeType('text/xml');\n";
	echo "                // See note below about this line\n";
	echo "            }\n";
	echo "        } else if (window.ActiveXObject) { // IE\n";
	echo "            try {\n";
	echo "                http_request = new ActiveXObject(\"Msxml2.XMLHTTP\");\n";
	echo "            } catch (e) {\n";
	echo "                try {\n";
	echo "                    http_request = new ActiveXObject(\"Microsoft.XMLHTTP\");\n";
	echo "                } catch (e) {}\n";
	echo "            }\n";
	echo "        }\n";
	echo "\n";
	echo "        if (!http_request) {\n";
	echo "            alert('Giving up :( Cannot create an XMLHTTP instance');\n";
	echo "            return false;\n";
	echo "        }\n";
	echo "        http_request.onreadystatechange = function() { returnContent(http_request); };\n";
	echo "        if (http_request.overrideMimeType) {\n";
	echo "              http_request.overrideMimeType('text/html');\n";
	echo "        }\n";
	echo "        http_request.open('POST', url, true);\n";
	echo "\n";
	echo "\n";
	echo "        if (strpost.length == 0) {\n";
	//echo "            alert('none');\n";
	echo "            //http_request.send(null);\n";
	echo "            http_request.send('name=value&foo=bar');\n";
	echo "        }\n";
	echo "        else {\n";
	//echo "            alert(strpost);\n";
	echo "            http_request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');\n";
	//echo "            http_request.send('name=value&foo=bar');\n";
	echo "            http_request.send(strpost);\n";
	echo "        }\n";
	echo "\n";
	echo "    }\n";
	echo "\n";
	echo "    function returnContent(http_request) {\n";
	echo "\n";
	echo "        if (http_request.readyState == 4) {\n";
	echo "            if (http_request.status == 200) {\n";

	echo "                  parent.editAreaLoader.setValue('edit1', http_request.responseText); \n";
	//echo "                alert(http_request.responseText);\n";
	echo "\n";
	//echo "                //var xmldoc = http_request.responseXML;\n";
	//echo "                //var root_node = xmldoc.getElementsByTagName('doc').item(0);\n";
	//echo "                //alert(xmldoc.getElementByID('fr1').value);\n";
	//echo "                //alert(root_node.firstChild.data);\n";
	//echo "\n";
	echo "            }\n";
	echo "            else {\n";
	echo "                alert('There was a problem with the request.');\n";
	echo "            }\n";
	echo "        }\n";
	echo "\n";
	echo "    }\n";
	echo "</script>";




	echo "<SCRIPT LANGUAGE=\"JavaScript\">\n";
	//echo "// ---------------------------------------------\n";
	//echo "// --- http://www.codeproject.com/jscript/dhtml_treeview.asp\n";
	//echo "// --- Name:    Easy DHTML Treeview           --\n";
	//echo "// --- Author:  D.D. de Kerf                  --\n";
	//echo "// --- Version: 0.2          Date: 13-6-2001  --\n";
	//echo "// ---------------------------------------------\n";
	echo "function Toggle(node) {\n";
	echo "	// Unfold the branch if it isn't visible\n";
	echo "	if (node.nextSibling.style.display == 'none')	{\n";
	echo "  		// Change the image (if there is an image)\n";
	echo "  		if (node.childNodes.length > 0)	{\n";
	echo "    			if (node.childNodes.item(0).nodeName == \"IMG\") {\n";
	echo "    				node.childNodes.item(0).src = \"images/minus.gif\";\n";
	echo "    			}\n";
	echo "  		}\n";
	echo "  \n";
	echo "  		node.nextSibling.style.display = 'block';\n";
	echo "	}\n";
	echo "	// Collapse the branch if it IS visible\n";
	echo "	else	{\n";
	echo "  		// Change the image (if there is an image)\n";
	echo "  		if (node.childNodes.length > 0)	{\n";
	echo "    			if (node.childNodes.item(0).nodeName == \"IMG\") {\n";
	echo "    				node.childNodes.item(0).src = \"images/plus.gif\";\n";
	echo "    			}\n";
	echo "  		}\n";
	echo "  		node.nextSibling.style.display = 'none';\n";
	echo "	}\n";
	echo "\n";
	echo "}\n";
	echo "</SCRIPT>";


	echo "<div align='center' valign='1'>";
	echo "<table  width='100%' height='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\" valign='top' nowrap>\n";
	//echo "      <br>";



	echo "\n";
	echo "      <TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD><a href='javascript:void(0);' onclick=\"if (typeof(clipwin)!='undefined') { clipwin.close(); } clipwin = window.open('fileoptions.php?folder=".urlencode($_SERVER["DOCUMENT_ROOT"])."','null','left=20,top=20,width=310,height=300,toolbar=0,resizable=0');\" style='text-decoration:none;' title=''><IMG SRC=\"images/folder.gif\" border='0'> Files </a><DIV style=''>\n"; //display:none
	//echo "      <TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD><A onClick=\"Toggle(this)\"><IMG SRC=\"images/plus.gif\"> <IMG SRC=\"images/folder.gif\"> Files </A><DIV style=''>\n"; //display:none

	//echo "<TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD nowrap WIDTH=12></TD><TD nowrap><A onClick=\"Toggle(this)\"><IMG SRC=\"images/plus.gif\"> <IMG SRC=\"images/gear.png\"> Tools </A><DIV style='display:none'>\n";
	//echo "<TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD nowrap WIDTH=12></TD><TD nowrap align='bottom'><IMG SRC=\"images/file.png\"><a href='/edit/foldernew.php?folder=".urlencode($_SERVER["DOCUMENT_ROOT"])."' title=''>New Folder </a><DIV style='display:none'>\n"; //parent.document.getElementById('file').value='".urlencode($newpath)."'
	//echo "</DIV></TD></TR></TABLE>\n";
	//echo "<TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD nowrap WIDTH=12></TD><TD nowrap align='bottom'><IMG SRC=\"images/file.png\"><a href='/edit/filenew.php?folder=".urlencode($_SERVER["DOCUMENT_ROOT"])."' title=''>New File </a><DIV style='display:none'>\n"; //parent.document.getElementById('file').value='".urlencode($newpath)."'
	//echo "</DIV></TD></TR></TABLE>\n";
	//echo "</DIV></TD></TR></TABLE>\n";


	echo recur_dir($v_dir.'/grammar');

	echo "</DIV></TD></TR></TABLE>\n";

	/*
	echo "      <TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD><A onClick=\"Toggle(this)\"><IMG SRC=\"/images/plus.gif\"> <IMG SRC=\"/images/folder.gif\"> Files </A><DIV style='display:none'>\n";
	echo "\n";
	echo "         <TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD WIDTH=10></TD><TD><A onClick=\"Toggle(this)\"><IMG SRC=\"/images/plus.gif\"> <IMG SRC=\"/images/folder.gif\"> Folder 1</A><DIV style='display:none'>\n";
	echo "\n";
	echo "         <TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD WIDTH=10></TD><TD align='bottom'><IMG SRC=\"/images/file.png\"> demo1.php <DIV style='display:none'>\n";
	echo "         </DIV></TD></TR></TABLE>\n";
	echo "\n";
	echo "         <TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD WIDTH=10></TD><TD align='bottom'><IMG SRC=\"/images/file.png\"> demo2.php <DIV style='display:none'>\n";
	echo "         </DIV></TD></TR></TABLE>\n";
	echo "\n";
	echo "         </DIV></TD></TR></TABLE>\n";
	echo "\n";
	echo "        <TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD WIDTH=10></TD><TD><A onClick=\"Toggle(this)\"><IMG SRC=\"/images/plus.gif\"> <IMG SRC=\"/images/folder.gif\"> Folder 2</A><DIV style='display:none'>\n";
	echo "\n";
	echo "            <TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD WIDTH=10></TD><TD align='bottom'><IMG SRC=\"/images/file.png\"> demo3.php <DIV style='display:none'>\n";
	echo "            </DIV></TD></TR></TABLE>\n";
	echo "\n";
	echo "            <TABLE BORDER=0 cellpadding='0' cellspacing='0'><TR><TD WIDTH=10></TD><TD align='bottom'><IMG SRC=\"/images/file.png\"> demo4.php <DIV style='display:none'>\n";
	echo "            </DIV></TD></TR></TABLE>\n";
	echo "\n";
	echo "         </DIV></TD></TR></TABLE>\n";
	echo "\n";
	echo "      </DIV></TD></TR></TABLE>\n";
	*/


	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>";

	echo "<br><br>";
	require_once "footer.php";

	unset ($resultcount);
	unset ($result);
	unset ($key);
	unset ($val);
	unset ($c);

	echo "</body>";
	echo "</html>";

?>
