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
	background-image: url('<!--{project_path}-->/themes/horizontal/background.jpg');
	background-repeat: repeat-x;
	background-attachment: fixed;
	background-color: #FFFFFF;
}

th {
	border-top: 1px solid #444444;
	border-bottom: 1px solid #444444;
	text-align: left;
	color: #FFFFFF;
	font-size: 12px;
	font-family: arial;
	font-weight: bold;
	/*background-color: #506eab;*/
	background-image: url('<!--{project_path}-->/themes/horizontal/background_th.png');
	padding-top: 4px;
	padding-bottom: 4px;
	padding-right: 7px;
	padding-left: 7px;
}

th a:link{ color:#FFFFFF; }
th a:visited{ color:#FFFFFF; }
th a:hover{ color:#FFFFFF; }
th a:active{ color:#FFFFFF; }

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
}

/*
.th {
	-webkit-border-radius: 10px 10px 0px 0px;
	-moz-border-radius: 10px 10px 0px 0px;
	border-radius: 10px 10px 0px 0px;
}
*/

.vncell {
	border-bottom: 1px solid #999999;
	/*background-color: #639BC1;*/
	background-image: url('<!--{project_path}-->/themes/horizontal/background_cell.gif');
	padding-right: 20px;
	padding-left: 8px;
	text-align: left;
	color: #444444;
}
/*
.vncell a:link{ color:#444444; }
.vncell a:visited{ color:#444444; }
.vncell style0 a:hover{ color:#444444; }
.vncell a:active{ color:#444444; }
*/

.vncellreq {
	background-image: url('<!--{project_path}-->/themes/horizontal/background_cell.gif');
	border-bottom: 1px solid #999999;
	background-color: #639BC1;
	padding-right: 20px;
	padding-left: 8px;
	text-align: left;
	font-weight: bold;
	color: #444444;
}

.vtable {
	border-bottom: 1px solid #DFDFDF;
}

.listbg {
	border-bottom: 1px solid #999999;
	font-size: 11px;
	background-color: #990000;
	color: #444444;	
	padding-right: 16px;
	padding-left: 6px;
	padding-top: 4px;
	padding-bottom: 4px;*/
}

.rowstyle0 {
	background-image: url('<!--{project_path}-->/themes/horizontal/background_cell.gif');
	border-bottom: 1px solid #999999;
	color: #444444;
	text-align: left;
	padding-top: 4px;
	padding-bottom: 4px;
	padding-right: 7px;
	padding-left: 7px;
}

.rowstyle0 a:link{ color:#444444; }
.rowstyle0 a:visited{ color:#444444; }
.rowstyle0 a:hover{ color:#444444; }
.rowstyle0 a:active{ color:#444444; }

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
	border-bottom: 1px solid #888888;
	background-color: #5F5F5F;
	color: #FFFFFF;
	text-align: left;
	padding-top: 5px;
	padding-bottom: 5px;
	padding-right: 10px;
	padding-left: 10px;
}

.border {
	border: solid 1px #999999;
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

/* begin the menu css*/

	/* CSS Menus - Horizontal CSS Menu with Dropdown and Popout Menus - 20050131 */

	#menu{
	width:100%;
	float:left;
	}

	#menu a, #menu h2{
	font:bold 11px/16px arial,helvetica,sans-serif;
	display:block;
	/*border-color:#ccc #888 #555 #bbb;*/
	border: solid 0.5px #222222;
	white-space:nowrap;
	margin:0;
	padding:3px 3px 3px 3px;
	}

	#menu h2{
	/*background:#222222 url(<!--{project_path}-->/css/images/expand3.gif) no-repeat 100% 100%;*/
	/*text-transform:uppercase*/
	border: solid 0.5px #222222;
	}

	#menu h2 h2{
	background:#4e4b56 url(<!--{project_path}-->/css/images/expand3.gif) no-repeat 100% 100%;
	/*text-transform:uppercase*/
	border: solid 0.5px #222222;
	padding:3px 3px 3px 3px;
	}

	#menu a{
	background:#333333;
	text-decoration:none;
	}

	/* drop down text color */
	#menu a, #menu a:visited{
	color:#cccccc;
	}

	#menu a:hover{
	color:#fd9c03;
	background:#1F1F1F;
	}

	#menu a:active{
	color:#fd9c03;
	background:#1F1F1F;
	}

	#menu ul{
	list-style:none;
	margin:0;
	padding:0;
	float:left;
	width:9em;
	}

	#menu li{
	position:relative;
	}

	#menu ul ul{
	position:absolute;
	z-index:500;
	top:auto;
	display:none;
	}

	#menu ul ul ul{
	top:0;
	left:100%;
	}


	/* Begin non-anchor hover selectors */

	/* Enter the more specific element (div) selector
	on non-anchor hovers for IE5.x to comply with the
	older version of csshover.htc - V1.21.041022. It
	improves IE's performance speed to use the older
	file and this method */

	div#menu h2:hover{
	background:#1F1F1F url(<!--{project_path}-->/css/images/expand3.gif) no-repeat -999px -9999px;
	}

	div#menu li:hover{
	cursor:pointer;
	z-index:100;
	}

	div#menu li:hover ul ul,
	div#menu li li:hover ul ul,
	div#menu li li li:hover ul ul,
	div#menu li li li li:hover ul ul
	{display:none;}

	div#menu li:hover ul,
	div#menu li li:hover ul,
	div#menu li li li:hover ul,
	div#menu li li li li:hover ul
	{display:block;}

	/* End of non-anchor hover selectors */

	/* Styling for Expand */

	#menu a.x, #menu a.x:visited{
	font-weight:bold;
	color:#000;
	background:#999999 url(<!--{project_path}-->/css/images/expand3.gif) no-repeat 100% 100%;
	}

	#menu a.x:hover{
	color:#fff;
	background:#000;
	}

	#menu a.x:active{
	color:#060;
	background:#ccc;
	}

/* end the menu css*/
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
<table width='90%' class='border.disabled' style='background-color:#FFFFFF;' border='0' cellpadding='0' cellspacing='0'>
<tr>
<td class='headermain' style='background-color:#FFFFFF;' width='100%'>
	<table cellpadding='0' cellspacing='0' border='0' style="background-image: url('<!--{project_path}-->/themes/horizontal/background_head.png'); color: #FFFFFF; font-size: 20px;'" width='100%'>
	<tr>
	<td align='center' colspan='2' style='' width='100%' height='4'>
	</td>
	</tr>
	<tr>
	<td></td>
	<td align='left' valign='middle' height='70px;' nowrap>
		<img src='<!--{project_path}-->/themes/horizontal/logo.png' height='70px' />
	</td>
	</tr>
	<tr>
	<td align='center' colspan='2' style="background-image: url('<!--{project_path}-->/themes/default/background_black.png');" width='100%' height='22'>
	<!--{menu}-->
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


<table width='100%' cellpadding='25' cellspacing='0' border='0'>
<td width='100%' align='left' valign='top'>
<!--{body}-->
<br />
<br />
</td>
</tr>
</table>


</td>
</tr>
</table>

<span class='smalltext'>
<a class='smalltext' target='_blank' href='http://www.fusionpbx.com'>fusionpbx.com</a>. Copyright 2008 - 2010. All Rights Reserved
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