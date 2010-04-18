<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2010
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
include "includes/config.php";
session_start();

$_SESSION["menu"] = ''; //force the menu to generate on every page load
if (strlen($_SESSION["menu"])==0) { //build menu it session menu has no length

	$menuwidth = '110';

	//echo "    <!-- http://www.seoconsultants.com/css/menus/horizontal/ -->\n";
	//echo "    <!-- http://www.tanfa.co.uk/css/examples/css-dropdown-menus.asp -->";

	$strmenu = "";
	$strmenu .= "    <!--[if IE]>\n";
	$strmenu .= "    <style type=\"text/css\" media=\"screen\">\n";
	$strmenu .= "    #menu{float:none;} /* This is required for IE to avoid positioning bug when placing content first in source. */\n";
	$strmenu .= "    /* IE Menu CSS */\n";
	$strmenu .= "    /* csshover.htc file version: V1.21.041022 - Available for download from: http://www.xs4all.nl/~peterned/csshover.html */\n";
	$strmenu .= "    body{behavior:url(/includes/csshover.htc);\n";
	$strmenu .= "    font-size:100%; /* to enable text resizing in IE */\n";
	$strmenu .= "    }\n";
	$strmenu .= "    #menu ul li{float:left;width:100%;}\n";
	$strmenu .= "    #menu h2, #menu a{height:1%;font:bold arial,helvetica,sans-serif;}\n";
	$strmenu .= "    </style>\n";
	$strmenu .= "    <![endif]-->\n";

	//$strmenu .= "    <style type=\"text/css\">@import url(\"/includes/menuh.css\");</style>\n";
	$strmenu .= "\n";

	$strmenu .= "\n";
	$strmenu .= "    <!-- End Grab This -->";

	$strmenu .= "<!-- Begin CSS Horizontal Popout Menu -->\n";
	$strmenu .= "<div id=\"menu\" style=\"position: relative; z-index:199; width:100%;\" align='left'>\n";
	$strmenu .= "\n";


	//---- Begin DB Menu --------------------
	function builddbmenu($db, $sql, $menulevel) {

		global $v_id;
		$dbmenufull = '';

		if (strlen($sql)==0) { //default sql for base of the menu
			$sql = "select * from v_menu ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and menuparentid = '' ";
			$sql .= "order by menuorder asc ";
		}
		//echo "base: ".$sql;
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();

		$result = $prepstatement->fetchAll();
		//$count = count($result);
		foreach($result as $field) {
			$menuid = $field[menuid];
			$menutitle = $field[menutitle];
			$menustr = $field[menustr];
			$menucategory = $field[menucategory];
			$menugroup = $field[menugroup];
			$menudesc = $field[menudesc];
			$menuparentid = $field[menuparentid];
			$menuorder = $field[menuorder];
			$menulanguage = $field[menulanguage];

			$menuatags = '';
			switch ($menucategory) {
				case "internal":
					$menutags = "href='".PROJECT_PATH."$menustr'";
					break;
				case "external":
					$menustr = str_replace ("<!--{project_path}-->", PROJECT_PATH, $menustr);
					$menutags = "href='$menustr' target='_blank'";
					break;
				case "email":
					$menutags = "href='mailto:$menustr'";
					break;
			}

			if ($menulevel == "main") {
				$dbmenu  = "<ul>\n";
				$dbmenu .= "<li>\n";
				//$dbmenu .= "      <table border='0' width='$menuwidth'>\n";
				//$dbmenu .= "      <tr>\n";
				//$dbmenu .= "          <td valign='top' width='50%'>&nbsp;</td>\n";
				//$dbmenu .= "          <td valign='top'>\n";

				//$dbmenu .= "<table cellpadding='0' cellspacing='0'><tr><td nowrap>\n";
				if (strlen($_SESSION["username"]) == 0) {
					$dbmenu .= "<a $menutags style='padding: 0px 0px; border-style: none; background: none;'><h2 align='center' style=''>$menutitle</h2></a>\n";
				}
				else {
					if ($menustr == "/login.php" || $menustr == "/users/signup.php") {
						//hide login and sign-up when the user is logged in
					}
					else {
						$dbmenu .= "<a $menutags style='padding: 0px 0px; border-style: none; background: none;'><h2 align='center' style=''>$menutitle</h2></a>\n";
					}
				}
				//$dbmenu .= "</td></tr></table>\n";
			}

			//$dbmenusub = builddbchildmenu($db, $menulevel, $menuid);
			//$dbmenu .= $dbmenusub;
			$menulevel = 0;
			$dbmenu .= builddbchildmenu($db, $menulevel, $menuid);
			//unset($dbmenusub);

			if ($menulevel == "main") {
				//$dbmenu .= "          </td>\n";
				//$dbmenu .= "          <td valign='top' width='50%' align='right'><img src=\"/images/menu_main_div.gif\" width=\"1\" height=\"55\" border=\"0\"></td>\n";
				//$dbmenu .= "      </tr>\n";
				//$dbmenu .= "      </table>\n";

				$dbmenu .= "</li>\n";
				$dbmenu .= "</ul>\n\n";
			}


			if (strlen($menugroup)==0) { //public
				$dbmenufull .= $dbmenu;
			}
			else {
				if (ifgroup($menugroup)) { //viewable only to designated group
					$dbmenufull .= $dbmenu;
				}
				else {
					//not authorized do not add to menu
				}
			}

		} //end for each

		unset($menutitle);
		unset($menustrv);
		unset($menucategory);
		unset($menugroup);
		unset($menuparentid);
		unset($prepstatement, $sql, $result);

		return $dbmenufull;
	}


	function builddbchildmenu($db, $menulevel, $menuid) {

			global $v_id;
			$menulevel = $menulevel+1;

				//--- Begin check for children -----------------------------------------
					$sql = "select * from v_menu ";
					$sql .= "where v_id = '$v_id' ";
					$sql .= "and menuparentid = '$menuid' ";
					$sql .= "order by menuorder asc ";
					//echo "sqlchild: ".$sql."<br>\n";
					$prepstatement2 = $db->prepare($sql);
					$prepstatement2->execute();
					$result2 = $prepstatement2->fetchAll();

					//echo "resultcount: --".count($result2)."--<br><br>";

					if (count($result2) > 0) {
							//echo "resultcount: --".count($result2)."--<br>";
							//child menu found

							$dbmenusub .= "<ul>\n";
							//$dbmenusub .= "<li><a href=\"#\" title=\"\" class='blank'><img src='/images/blank.gif' height='4' border='0'/></a></li>\n";

							foreach($result2 as $row) {
								$menuid = $row[menuid];
								$menutitle = $row[menutitle];
								$menustr = $row[menustr];
								$menucategory = $row[menucategory];
								$menugroup = $row[menugroup];
								$menuparentid = $row[menuparentid];

								$menuatags = '';
								switch ($menucategory) {
									case "internal":
										$menutags = "href='".PROJECT_PATH."$menustr'";
										break;
									case "external":
										$menustr = str_replace ("<!--{project_path}-->", PROJECT_PATH, $menustr);
										$menutags = "href='$menustr' target='_blank'";
										break;
									case "email":
										$menutags = "href='mailto:$menustr'";
										break;
								}

								if (strlen($menugroup)==0) { //public
										$dbmenusub .= "<li>";
										$strchildmenu = builddbchildmenu($db, $menulevel, $menuid);   //get sub menu for children
										if (strlen($strchildmenu) > 1) {
											//$dbmenusub .= "<table cellpadding='0' cellspacing='0' width='100%'><tr><td>";
											$dbmenusub .= "<a $menutags>$menutitle</a>";
											//$dbmenusub .= "</td><td align='right'><a $menutags> >> </a></td></tr></table>";
											$dbmenusub .= $strchildmenu;
											unset($strchildmenu);
										}
										else {
											$dbmenusub .= "<a $menutags>$menutitle</a>";
										}
										$dbmenusub .= "</li>\n";
								}
								else {
									if (ifgroup($menugroup)) { //viewable only to designated group
										$dbmenusub .= "<li>";
										$strchildmenu = builddbchildmenu($db, $menulevel, $menuid);   //get sub menu for children
										if (strlen($strchildmenu) > 1) {
											//$dbmenusub .= "<table cellpadding='0' cellspacing='0' width='100%'><tr><td>";
											$dbmenusub .= "<a $menutags>$menutitle</a>";
											//$dbmenusub .= "</td><td align='right'><a $menutags> >> </a></td></tr></table>";
											$dbmenusub .= $strchildmenu;
											unset($strchildmenu);
										}
										else {
											$dbmenusub .= "<a $menutags>$menutitle</a>";
										}
										$dbmenusub .= "</li>\n";
									}
									else {
										//not authorized do not add to menu
									}
								}

								//echo "menuid ".$menuid."<br>\n";


							}
							unset($sql, $result2);

							$dbmenusub .="</ul>\n";
							//echo "--".$dbmenusub."--";
							return $dbmenusub;

					}
					unset($prepstatement2, $sql);
				//--- End check for children -----------------------------------------

	}

	$strmenu .= builddbmenu($db, "", "main"); //display the menu
	//---- End DB Menu --------------------

	$strmenu .= "</div>\n";
	//echo "<!-- End CSS Horizontal Popout Menu -->";

	$_SESSION["menu"] = $strmenu;
	unset ($strmenu);

} //end if //if (strlen($_SESSION["menu"])==0) {
else {
	//echo "from session";
}
//echo $_SESSION["menu"];


?>
