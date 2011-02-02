<?php
//get the browser version
	$user_agent = http_user_agent();
	$browser_version =  $user_agent['version'];
	$browser_name =  $user_agent['name'];
	$browser_version_array = explode('.', $browser_version);

//set the doctype
	if ($browser_name == "Internet Explorer") {
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title><!--{title}--></title>
<!--{head}-->
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<style type='text/css'>

img {
	/*behavior: url('<!--{project_path}-->/includes/png.htc');*/
	border: none;
}

A {
	color: #004083;
	width: 100%;
}

body {
	margin-top: 0px;
	margin-bottom: 0px;
	margin-right: 0px;
	margin-left: 0px;
	/*background-image: url('<!--{project_path}-->/themes/accessible/background.gif');*/
	background-image: url('<!--{project_path}-->/themes/accessible/menu_background.jpg');
	background-repeat: repeat-x;
	background-color: #FFFFFF;
	background-attachment:fixed;
}

th {
	border-top: 1px solid #999999;
	border-bottom: 1px solid #777777;
	color: #FFFFFF;
	font-size: 12px;
	font-family: arial;
	font-weight: bold;
	background-color: #FFFFFF;
	background-image: url('<!--{project_path}-->/themes/accessible/background_th.png');
	padding-top: 4px;
	padding-bottom: 4px;
	padding-right: 7px;
	padding-left: 7px;
}

th a:link{ color:#FFFFFF; }
th a:visited{ color:#FFFFFF; }
th a:hover{ color:#FFBF00; }
th a:active{ color:#FFBF00; }

td {
	color: #5f5f5f;
	font-size: 12px;
	font-family: arial;
}

INPUT.btn {
	font-family: verdana;
	font-size: 11px;
}

INPUT.button {
	font-family: verdana;
	font-size: 11px;
}

SELECT.txt {
	font-family: arial;
	font-size: 12px;
	width: 98.75%;
	border: solid 1px #CCCCCC;
	color: #666666;
	background-color: #EFEFEF;
	background-repeat:repeat-x;
	height: 19px;
}

TEXTAREA.txt {
	font-family: arial;
	font-size: 12px;
	width: 98.75%;
	border: solid 1px #CCCCCC;
	color: #666666;
	background-color: #EFEFEF;
	background-repeat:repeat-x;
	overflow: auto;
	padding: 4px;

	-moz-border-radius-topleft:5px;
	-webkit-border-top-left-radius:5px;
	border-top-left-radius:5px

	-moz-border-radius-topright:5px;
	-webkit-border-top-right-radius:5px;
	border-top-right-radius:5px

	-moz-border-radius-bottomleft:5px;
	-webkit-border-bottom-left-radius:5px;
	border-bottom-left-radius:5px

	-moz-border-radius-bottomright:5px;
	-webkit-border-bottom-right-radius:5px;
	border-bottom-right-radius:5px
}

INPUT.txt {
	font-family: arial;
	font-size: 12px;
	width: 98.75%;
	border: solid 1px #CCCCCC;
	color: #666666;
	background-color: #EFEFEF;
	background-repeat:repeat-x;
}

.formfld {
	border: solid 1px #CCCCCC;
	color: #666666;
	background-color: #F7F7F7;
	width: 50%;
	text-align: left;
	/*width: 300px;*/
	padding-left: 4px;

	-moz-border-radius-topleft:5px;
	-webkit-border-top-left-radius:5px;
	border-top-left-radius:5px

	-moz-border-radius-topright:5px;
	-webkit-border-top-right-radius:5px;
	border-top-right-radius:5px

	-moz-border-radius-bottomleft:5px;
	-webkit-border-bottom-left-radius:5px;
	border-bottom-left-radius:5px

	-moz-border-radius-bottomright:5px;
	-webkit-border-bottom-right-radius:5px;
	border-bottom-right-radius:5px
}

/*
.th {
	-webkit-border-radius: 10px 10px 0px 0px;
	-moz-border-radius: 10px 10px 0px 0px;
	border-radius: 10px 10px 0px 0px;
}
*/

.vncell {
	background: #EFEFEF;
	color: gray;
	border-bottom: 1px solid #CCCCCC;
	background-image: url('<!--{project_path}-->/themes/accessible/background_cell.gif');
	padding-right: 20px;
	padding-left: 8px;
	text-align: left;
	color: #555555;
	border-bottom: 1px solid #999999;
}

.vncell a:link{ color:#555555; }
.vncell a:visited{ color:#555555; }
.vncell style0 a:hover{ color:#FFBF00; }
.vncell a:active{ color:#555555; }

.vncellreq {
	background-image: url('<!--{project_path}-->/themes/accessible/background_cell.gif');
	border-bottom: 1px solid #999999;
	background-color: #639BC1;
	padding-right: 20px;
	padding-left: 8px;
	text-align: left;
	font-weight: bold;
	color: #555555;
}

.vtable {
	border-bottom: 1px solid #DFDFDF;
}

.listbg {
	border-bottom: 1px solid #999999;
	font-size: 11px;
	background-color: #990000;
	color: #FFFFFF;
	padding-right: 16px;
	padding-left: 6px;
	padding-top: 4px;
	padding-bottom: 4px;*/
}

.rowstyle0 {
	background-image: url('<!--{project_path}-->/themes/accessible/background_cell.gif');
	border-bottom: 1px solid #999999;
	/*background-color: #ECE9D8; */
	background-color: #639BC1;
	color: #555555;
	text-align: left;
	padding-top: 4px;
	padding-bottom: 4px;
	padding-right: 7px;
	padding-left: 7px;
}

.rowstyle0 a:link{ color:#555555; }
.rowstyle0 a:visited{ color:#555555; }
.rowstyle0 a:hover{ color:#FFBF00; }
.rowstyle0 a:active{ color:#FFBF00; }

.rowstyle1 {
	border-bottom: 1px solid #999999;
	background-color: #FFFFFF;
	text-align: left;
	padding-top: 4px;
	padding-bottom: 4px;
	padding-right: 7px;
	padding-left: 7px;
}

.rowstylebg {
	border-bottom: 1px solid #999999;
	background-color: #5F5F5F;
	color: #FFFFFF;
	text-align: left;
	padding-top: 5px;
	padding-bottom: 5px;
	padding-right: 10px;
	padding-left: 10px;
}

.border {
	border-left: 1px solid #FFFFFF;
	border-right: 1px solid #FFFFFF;
	background-color: #FFFFFF;
}

.headermain {
	background-color: #7FAEDE;
}

.frm {
	border: solid 1px #CCCCCC;
	color: #666666;
	background-color: #EFEFEF;

}

.smalltext {
	color: #BBBBBB;
	font-size: 11px;
	font-family: arial;
}

table tr:first-child th:first-child {
	-moz-border-radius-topleft:7px;
	-webkit-border-top-left-radius:7px;
	border-top-left-radius:7px
	}

table tr:first-child th:last-of-type {
	-moz-border-radius-topright:7px;
	-webkit-border-top-right-radius:7px;
	border-top-right-radius:7px
	}

table tr:nth-last-child(-5) td:first-of-type {
	-moz-border-radius-bottomleft:7px;
	-webkit-border-bottom-left-radius:7px;
	border-bottom-left-radius:7px
	}

table tr:nth-last-child(-5) td:first-of-type {
	-moz-border-radius-topleft:7px;
	-webkit-border-top-left-radius:7px;
	border-bottom-top-radius:7px
	}

/*
table tr:last-child td:first-of-type {
	-moz-border-radius-topright:10px;
	-webkit-border-top-right-radius:10px;
	border-top-right-radius:10px
	}

table tr:last-child td:first-child {
	-moz-border-radius-bottomleft:10px;
	-webkit-border-bottom-left-radius:10px;
	border-bottom-left-radius:10px
	}

table tr:last-child td:last-child {
	-moz-border-radius-bottomright:10px;
	-webkit-border-bottom-right-radius:10px;
	border-bottom-right-radius:10px
	}
*/

/* begin the menu css*/
	.menu_bg {
		background-image: url('<!--{project_path}-->/themes/accessible/menu_background.jpg');
		background-repeat: repeat-x;
		background-color: #FFFFFF;
		text-align: left;
		padding-top: 4px;
		padding-bottom: 25px;
		padding-left: 5px;
		padding-right:20px;
	}

	.menu_bg h2 {
		text-align: left;
	}

	#menu ul {
		list-style-type:none;
		padding:0px;
		margin:0px;
	}

	#menu li {
		margin-top: 0;
		border-top-width: 0;
		padding-top: 0;
		margin-bottom: 0;
		border-bottom-width: 0;
		padding-bottom: 0;
		list-style-type:none;
		padding:0px;
		margin:0px;
		padding-left:15px;
	}

	#menu {
		width:100%;
		float:left;
	}

	#menu a, #menu h2 {
		font:bold 11px/16px arial,helvetica,sans-serif;
		display:block;
		/*border-color:#ccc #888 #555 #bbb;*/
		/*border: solid 0.5px #222222;*/
		white-space:nowrap;
		margin:0;
		padding-top:2px;
		padding-bottom:2px;
	}

	#menu h2 {
		align: left;
		/*text-transform:uppercase*/
	}

	#menu a {
		/*background:#333333;*/
		text-decoration:none;
	}

	#menu a:hover {
		list-style-type:disc;
		text-decoration:underline;
		/*list-style-image: url("images/img.gif");*/
	}

/* end the menu css */
</style>

<script type="text/javascript">
<!--
function jsconfirm(title,msg,url) {
	if (confirm(msg)){
		window.location = url;
	}
	else{
	}
}
//-->
</script>

<SCRIPT language="JavaScript">
<!--
function confirmdelete(url) {
	var confirmed = confirm("Are you sure want to delete this.");
	if (confirmed == true) {
		window.location=url;
	}
}
//-->
</SCRIPT>
</head>
<body>

<div align='center'>
<table width='90%' class='border' border='0' cellpadding='0' cellspacing='0'>
<tr>
<td class='headermain' style='background-color:#FFFFFF;' width='100%'>
	<table cellpadding='0' cellspacing='0' border='0' style="background-image: url('<!--{project_path}-->/themes/accessible/background_head.png'); color: #FFFFFF; font-size: 20px;" width='100%'>
	<tr>
	<td align='center' colspan='2' style='' width='100%' height='4'>
	</td>
	</tr>
	<tr>
	<td></td>
	<td align='left' valign='middle' nowrap>
		<img src='<!--{project_path}-->/themes/accessible/logo.png' />
	</td>
	</tr>
	<tr>
	<td align='center' colspan='2' style="background-image: url('<!--{project_path}-->/themes/accessible/background_black.png');" width='100%' height='22'>
		<!--{menu.disabled}-->
	</td>
	</tr>
	</table>

</td>
</tr>
<!--
<tr><td colspan='100%'><img src='<!--{project_path}-->/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>
-->
<tr>
<td valign='top' align='center' width='100%'>

<table width='100%' cellpadding='30' cellspacing='0' border='0'>
<tr>
<td width='140' align='left' valign='top' class='menu_bg'>
<br />

<!--{menu}-->
</td>
<td align='left' valign='top'>
<?php
if ($_SESSION["reload_xml"]) {
		if ($_SERVER["PHP_SELF"] != PROJECT_PATH."/core/status/v_status.php") {
			if(stristr($_SERVER["PHP_SELF"] , "_edit") != FALSE) { 
				//found
			}
			else {
				echo "<div align='center'>\n";
				echo "<table border='0' width='400px'>\n";
				echo "<tr>\n";
				echo "<th align='left'>Message</th>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td class='rowstyle1'>\n";

				echo "	<table width='100%' border='0'>\n";
				echo "	<tr>\n";
				echo "	<td width='90%' align='left'>\n";
				echo "			The configuration has been changed. \n";
				echo "			Apply the changes in order for them to take effect. \n";
				echo "	</td>\n";
				echo "	<td align='right'>\n";
				echo "		<input type='button' class='btn' value='Apply Settings' onclick=\"document.location.href='".PROJECT_PATH."/core/status/v_cmd.php?cmd=api+reloadxml';\" />\n";
				echo "	</td>\n";
				echo "	</tr>\n";
				echo "	</table>\n";

				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";
				echo "</div>\n";
			}
	}
}
?>
<!--{body}-->

</td>
</tr>
</table>


</td>
</tr>
</table>

<span class='smalltext'>
<a class='smalltext' target='_blank' href='http://www.fusionpbx.com'>fusionpbx.com</a>. Copyright 2008 - 2011. All Rights Reserved
</span>

</td>
</tr>
</table>
</div>

</td>
</tr>
</table>

<br>

</body>
</html>
