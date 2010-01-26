<?php

	if (!function_exists('check_str')) {
		function check_str($strtemp) {
			////when code in db is urlencoded the ' does not need to be modified
			//$strtemp = str_replace ("\'", "''", $strtemp); //escape the single quote
			$strtemp = str_replace ("'", "''", $strtemp); //escape the single quote
			$strtemp = trim ($strtemp); //remove white space
			return $strtemp;
		}
	}

	if (!function_exists('check_sql')) {
		function check_sql($strtemp) {
			if ($dbtype == "sqlite") {
				//$strtemp = str_replace ("'", "''", $strtemp); //escape the single quote
			}
			if ($dbtype == "pgsql") {
				$strtemp = str_replace ("\\", "\\\\", $strtemp); //escape the backslash
			}
			if ($dbtype == "mysql") {
				$strtemp = str_replace ("\\", "\\\\", $strtemp); //escape the backslash
			}
			$strtemp = trim ($strtemp); //remove white space
			return $strtemp;
		}
	}


	if (!function_exists('recursive_copy')) {
		function recursive_copy($src,$dst) {
			$dir = opendir($src);
			@mkdir($dst);
			while(false !== ( $file = readdir($dir)) ) {
				if (( $file != '.' ) && ( $file != '..' )) {
					if ( is_dir($src . '/' . $file) ) {
						recursive_copy($src . '/' . $file,$dst . '/' . $file);
					}
					else {
						copy($src . '/' . $file,$dst . '/' . $file);
					}
				}
			}
			closedir($dir);
		}
	}


	if (!function_exists('ifgroup')) {
		function ifgroup($group) {
			if (stripos($_SESSION["groups"], "||".$group."||") === false) {
				return false; //group does not exist
			}
			else {
				return true; //group exists
			}
		}
	}


	if (!function_exists('groupmemberlist')) {
		function groupmemberlist($db, $username) {
			global $v_id;
			$sql = "select * from v_group_members ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and username = '".$username."' ";
			//echo $sql;
			$prepstatement = $db->prepare(check_sql($sql));
			$prepstatement->execute();
			$result = $prepstatement->fetchAll();
			$resultcount = count($result);

			$groupmemberlist = "||";
			foreach($result as $field) {
				//get the list of groups
				$groupmemberlist .= $field[groupid]."||";
			}
			unset($sql, $result, $rowcount);
			return $groupmemberlist;
		}
	}


	if (!function_exists('ifgroupmember')) {
		function ifgroupmember($groupmemberlist, $group) {
			if (stripos($groupmemberlist, "||".$group."||") === false) {
				return false; //group does not exist
			}
			else {
				return true; //group exists
			}
		}
	}


	if (!function_exists('superadminlist')) {
		function superadminlist($db) {
			global $v_id;
			$sql = "select * from v_group_members ";
			$sql .= "where groupid = 'superadmin' ";
			//echo $sql;
			$prepstatement = $db->prepare(check_sql($sql));
			$prepstatement->execute();
			$result = $prepstatement->fetchAll();
			$resultcount = count($result);

			$strsuperadminlist = "||";
			foreach($result as $field) {
				//get the list of superadmins
				$strsuperadminlist .= $field[groupid]."||";
			}
			unset($sql, $result, $rowcount);
			return $strsuperadminlist;
		}
	}
	//superadminlist($db);

	if (!function_exists('ifsuperadmin')) {
		function ifsuperadmin($superadminlist, $username) {
			if (stripos($superadminlist, "||".$username."||") === false) {
				return false; //username does not exist
			}
			else {
				return true; //username exists
			}
		}
	}


	if (!function_exists('htmlselectother')) {
		function htmlselectother($db, $tablename, $fieldname, $sqlwhereoptional, $fieldcurrentvalue) {
			//html select other : build a select box from distinct items in db with option for other
			global $v_id;

			$html  = "<table width='50%' border='0' cellpadding='1' cellspacing='0'>\n";
			$html .= "<tr>\n";
			$html .= "<td id=\"cell".$fieldname."1\" width='100%'>\n";
			$html .= "\n";
			$html .= "<select id=\"".$fieldname."\" name=\"".$fieldname."\" class='formfld' style='width: 100%;' onchange=\"if (document.getElementById('".$fieldname."').value == 'Other') { /*enabled*/ document.getElementById('".$fieldname."_other').style.width='95%'; document.getElementById('cell".$fieldname."2').width='70%'; document.getElementById('cell".$fieldname."1').width='30%'; document.getElementById('".$fieldname."_other').disabled = false; document.getElementById('".$fieldname."_other').className='txt'; document.getElementById('".$fieldname."_other').focus(); } else { /*disabled*/ document.getElementById('".$fieldname."_other').value = ''; document.getElementById('cell".$fieldname."1').width='95%'; document.getElementById('cell".$fieldname."2').width='5%'; document.getElementById('".$fieldname."_other').disabled = true; document.getElementById('".$fieldname."_other').className='frmdisabled' } \">\n";
			$html .= "<option value=''></option>\n";

			$sql = "SELECT distinct($fieldname) as $fieldname FROM $tablename $sqlwhereoptional ";
			//echo $sql;
			$prepstatement = $db->prepare(check_sql($sql));
			$prepstatement->execute();
			$result = $prepstatement->fetchAll();
			$resultcount = count($result);
			//echo $resultcount;
			if ($resultcount > 0) { //if user account exists then show login
				//print_r($result);
				foreach($result as $field) {
					if (strlen($field[$fieldname]) > 0) {
						if ($fieldcurrentvalue == $field[$fieldname]) {
							$html .= "<option value=\"".$field[$fieldname]."\" selected>".$field[$fieldname]."</option>\n";
						}
						else {
							$html .= "<option value=\"".$field[$fieldname]."\">".$field[$fieldname]."</option>\n";
						}
					}
				}
			}
			unset($sql, $result, $resultcount);

			$html .= "<option value='Other'>Other</option>\n";
			$html .= "</select>\n";
			$html .= "</td>\n";
			$html .= "<td id=\"cell".$fieldname."2\" width='5'>\n";
			$html .= "<input id=\"".$fieldname."_other\" name=\"".$fieldname."_other\" value='' style='width: 5%;' disabled onload='document.getElementById('".$fieldname."_other').disabled = true;' type='text' class='frmdisabled'>\n";
			$html .= "</td>\n";
			$html .= "</tr>\n";
			$html .= "</table>";

		return $html;
		}
	}


	if (!function_exists('htmlselect')) {
		function htmlselect($db, $tablename, $fieldname, $sqlwhereoptional, $fieldcurrentvalue, $fieldvalue = '') {
			//html select other : build a select box from distinct items in db with option for other
			global $v_id;

			if (strlen($fieldvalue) > 0) {
			$html .= "<select id=\"".$fieldvalue."\" name=\"".$fieldvalue."\" class='formfld'>\n";
			$html .= "<option value=\"\"></option>\n";
				$sql = "SELECT distinct($fieldname) as $fieldname, $fieldvalue FROM $tablename $sqlwhereoptional ";
			}
			else {
				$html .= "<select id=\"".$fieldname."\" name=\"".$fieldname."\" class='formfld'>\n";
				$html .= "<option value=\"\"></option>\n";
				$sql = "SELECT distinct($fieldname) as $fieldname FROM $tablename $sqlwhereoptional ";
			}

			$prepstatement = $db->prepare(check_sql($sql));
			$prepstatement->execute();
			$result = $prepstatement->fetchAll();
			$resultcount = count($result);
			//echo $resultcount;
			if ($resultcount > 0) { //if user account exists then show login
				//print_r($result);
				foreach($result as $field) {
					if (strlen($field[$fieldname]) > 0) {
						if ($fieldcurrentvalue == $field[$fieldname]) {
							if (strlen($fieldvalue) > 0) {
								$html .= "<option value=\"".$field[$fieldvalue]."\" selected>".$field[$fieldname]."</option>\n";
							}
							else {
								$html .= "<option value=\"".$field[$fieldname]."\" selected>".$field[$fieldname]."</option>\n";
							}
						}
						else {
							if (strlen($fieldvalue) > 0) {
								$html .= "<option value=\"".$field[$fieldvalue]."\">".$field[$fieldname]."</option>\n";
							}
							else {
								$html .= "<option value=\"".$field[$fieldname]."\">".$field[$fieldname]."</option>\n";
							}
						}
					}
				}
			}
			unset($sql, $result, $resultcount);
			$html .= "</select>\n";

		return $html;
		}
	}
	//$tablename = 'v_templates'; $fieldname = 'templatename'; $sqlwhereoptional = "where v_id = '$v_id' "; $fieldcurrentvalue = '';
	//echo htmlselect($db, $tablename, $fieldname, $sqlwhereoptional, $fieldcurrentvalue);


	if (!function_exists('htmlselectonchange')) {
		function htmlselectonchange($db, $tablename, $fieldname, $sqlwhereoptional, $fieldcurrentvalue, $onchange, $fieldvalue = '') {
			//html select other : build a select box from distinct items in db with option for other
			global $v_id;

			$html .= "<select id=\"".$fieldname."\" name=\"".$fieldname."\" class='formfld' onchange=\"".$onchange."\">\n";
			$html .= "<option value=''></option>\n";

			$sql = "SELECT distinct($fieldname) as $fieldname FROM $tablename $sqlwhereoptional ";
			//echo $sql;
			$prepstatement = $db->prepare(check_sql($sql));
			$prepstatement->execute();
			$result = $prepstatement->fetchAll();
			$resultcount = count($result);
			//echo $resultcount;
			if ($resultcount > 0) { //if user account exists then show login
				//print_r($result);
				foreach($result as $field) {
					if (strlen($field[$fieldname]) > 0) {
						if ($fieldcurrentvalue == $field[$fieldname]) {
								if (strlen($fieldvalue) > 0) {
									$html .= "<option value=\"".$field[$fieldvalue]."\" selected>".$field[$fieldname]."</option>\n";
								}
								else {
									$html .= "<option value=\"".$field[$fieldname]."\" selected>".$field[$fieldname]."</option>\n";
								}
						}
						else {
								if (strlen($fieldvalue) > 0) {
									$html .= "<option value=\"".$field[$fieldvalue]."\">".$field[$fieldname]."</option>\n";
								}
								else {
									$html .= "<option value=\"".$field[$fieldname]."\">".$field[$fieldname]."</option>\n";
								}
						}
					}
				}

			}
			unset($sql, $result, $resultcount);
			$html .= "</select>\n";

		return $html;
		}
	}

	if (!function_exists('thorderby')) {
		//html table header order by
		function thorderby($fieldname, $columntitle, $orderby, $order) {

			$html .= "<th nowrap>&nbsp; &nbsp; ";
			if (strlen($orderby)==0) {
				$html .= "<a href='?orderby=$fieldname&order=desc' title='ascending'>$columntitle</a>";
			}
			else {
				if ($order=="asc") {
					$html .= "<a href='?orderby=$fieldname&order=desc' title='ascending'>$columntitle</a>";
				}
				else {
					$html .= "<a href='?orderby=$fieldname&order=asc' title='descending'>$columntitle</a>";
				}
			}
			$html .= "&nbsp; &nbsp; </th>";

			return $html;
		}
	}
	////example usage
		//$tablename = 'tblcontacts'; $fieldname = 'contactcategory'; $sqlwhereoptional = "", $fieldcurrentvalue ='';
		//echo htmlselectother($db, $tablename, $fieldname, $sqlwhereoptional, $fieldcurrentvalue);
	////  On the page that recieves the POST
		//if (check_str($_POST["contactcategory"]) == "Other") { //echo "found: ".$contactcategory;
		//  $contactcategory = check_str($_POST["contactcategoryother"]);
		//}

	if (!function_exists('logadd')) {
		function logadd($db, $logtype, $logstatus, $logdesc, $logadduser, $logadduserip) {
		//--- Begin: Log entry -----------------------------------------------------
			return; //this disables the function
			global $v_id;

			$sql = "insert into tbllogs ";
			$sql .= "(";
			$sql .= "logtype, ";
			$sql .= "logstatus, ";
			$sql .= "logdesc, ";
			$sql .= "logadduser, ";
			$sql .= "logadduserip, ";
			$sql .= "logadddate ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$logtype', ";
			$sql .= "'$logstatus', ";
			$sql .= "'$logdesc', ";
			$sql .= "'$logadduser', ";
			$sql .= "'$logadduserip', ";
			$sql .= "now() ";
			$sql .= ")";
			//echo $sql;
			$db->exec(check_sql($sql));
			$lastinsertid = $db->lastInsertId($id);
			unset($sql);
		//--- End: Log entry -------------------------------------------------------
		}
	}
	//$logtype = ''; $logstatus=''; $logadduser=''; $logdesc='';
	//logadd($db, $logtype, $logstatus, $logdesc, $logadduser, $_SERVER["REMOTE_ADDR"]);


	if (!function_exists('get_ext')) {
		function get_ext($filename) {
			preg_match('/[^?]*/', $filename, $matches);
			$string = $matches[0];

			$pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);

			// check if there is any extension
			if(count($pattern) == 1){
				//echo 'No File Extension Present';
				return '';
			}
	 
			if(count($pattern) > 1) {
				$filenamepart = $pattern[count($pattern)-1][0];
				preg_match('/[^?]*/', $filenamepart, $matches);
				return $matches[0];
			}
		}
		//echo "ext: ".get_ext('test.txt');
	}


	if (!function_exists('fileupload')) {
			function fileupload($field = '', $file_type = '', $dest_dir = '') {

					$uploadtempdir = $_ENV["TEMP"]."\\";
					ini_set('upload_tmp_dir', $uploadtempdir);

					$tmp_name = $_FILES[$field]["tmp_name"];
					$file_name = $_FILES[$field]["name"];
					$file_type = $_FILES[$field]["type"];
					$file_size = $_FILES[$field]["size"];
					$file_ext = get_ext($file_name);
					$file_name_orig = $file_name;
					$file_name_base = substr($file_name, 0, (strlen($file_name) - (strlen($file_ext)+1)));
					//$dest_dir = '/tmp';

					if ($file_size ==  0){
						 return;
					}

					if (!is_dir($dest_dir)) {
					   echo "dest_dir not found<br />\n";
						 return;
					}

					//check if allowed file type
					if ($file_type == "img") {
							switch (strtolower($file_ext)) {
								case "jpg":
									break;
								case "png":
									break;
								case "gif":
									break;
								case "bmp":
									break;
								case "psd":
									break;
								case "tif":
									break;
								default:
									return false;
							}
					}

						if ($file_type == "file") {
							switch (strtolower($file_ext)) {
								case "doc":
									break;
								case "pdf":
									break;
								case "ppt":
									break;
								case "xls":
									break;
								case "zip":
									break;
								case "exe":
									break;
								default:
									return false;
								}
						}


					//find unique filename: check if file exists if it does then increment the filename
						$i = 1;
						while( file_exists($dest_dir.'/'.$file_name)) {
							if (strlen($file_ext)> 0) {
								$file_name = $file_name_base . $i .'.'. $file_ext;
							}
							else {
								$file_name = $file_name_orig . $i;
							}
							$i++;
						}

					//echo "file_type: ".$file_type."<br />\n";
					//echo "tmp_name: ".$tmp_name."<br />\n";
					//echo "file_name: ".$file_name."<br />\n";
					//echo "file_ext: ".$file_ext."<br />\n";
					//echo "file_name_orig: ".$file_name_orig."<br />\n";
					//echo "file_name_base: ".$file_name_base."<br />\n";
					//echo "dest_dir: ".$dest_dir."<br />\n";

					//move the file to upload directory  
					//bool move_uploaded_file  ( string $filename, string $destination  )

						if (move_uploaded_file($tmp_name, $dest_dir.'/'.$file_name)){
							//print "<pre>";
							//print_r($_FILES);
							//print "</pre>";
							 return $file_name;
						}
						else {
							echo "File upload failed!  Here's some debugging info:\n";
							//print "<pre>";
							//print_r($_FILES);
							//print "</pre>";
							return false;
						}
						exit;
						
			} //end function
	}

	if ( !function_exists('sys_get_temp_dir')) {
		function sys_get_temp_dir() {
			if( $temp=getenv('TMP') )        return $temp;
			if( $temp=getenv('TEMP') )        return $temp;
			if( $temp=getenv('TMPDIR') )    return $temp;
			$temp=tempnam(__FILE__,'');
			if (file_exists($temp)) {
				unlink($temp);
				return dirname($temp);
			}
			return null;
		}
	}
	//echo realpath(sys_get_temp_dir());

	if (!function_exists('user_exists')) {
		function user_exists($username) {
			global $db, $v_id;
			$user_exists = false;
			$sql = "select * from v_users ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and username = '".$username."' ";
			//echo $sql;
			$prepstatement = $db->prepare(check_sql($sql));
			$prepstatement->execute();
			$result = $prepstatement->fetchAll();
			$resultcount = count($result);
			if ($resultcount > 0) {
				return true;
			}
			else {
				return false;
			}
		}
	}

	if (!function_exists('user_add')) {
		function user_add($username, $password, $userfirstname='', $userlastname='', $useremail='') {
			if (strlen($username) == 0) { return false; }
			if (strlen($password) == 0) { return false; }
			if (!user_exists($username)) {
				global $db, $v_id;
				//add the user account
					$usertype = 'Individual';
					$usercategory = 'user';
					$sql = "insert into v_users ";
					$sql .= "(";
					$sql .= "v_id, ";
					$sql .= "username, ";
					$sql .= "password, ";
					$sql .= "usertype, ";
					$sql .= "usercategory, ";
					if (strlen($userfirstname) > 0) { $sql .= "userfirstname, "; }
					if (strlen($userlastname) > 0) { $sql .= "userlastname, "; }
					if (strlen($useremail) > 0) { $sql .= "useremail, "; }
					$sql .= "useradddate, ";
					$sql .= "useradduser ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$v_id', ";
					$sql .= "'$username', ";
					$sql .= "'".md5('e3.7d.12'.$password)."', ";
					$sql .= "'$usertype', ";
					$sql .= "'$usercategory', ";
					if (strlen($userfirstname) > 0) { $sql .= "'$userfirstname', "; }
					if (strlen($userlastname) > 0) { $sql .= "'$userlastname', "; }
					if (strlen($useremail) > 0) { $sql .= "'$useremail', "; }
					$sql .= "now(), ";
					$sql .= "'".$_SESSION["username"]."' ";
					$sql .= ")";
					//echo $sql;
					//exit;
					$db->exec(check_sql($sql));
					$lastinsertid = $db->lastInsertId($id);
					unset($sql);

				//add the user to the member group
					$groupid = 'member';
					$sql = "insert into v_group_members ";
					$sql .= "(";
					$sql .= "v_id, ";
					$sql .= "groupid, ";
					$sql .= "username ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$v_id', ";
					$sql .= "'$groupid', ";
					$sql .= "'$username' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					$lastinsertid = $db->lastInsertId($id);
					unset($sql);
			} //end if !user_exists
		} //end function definition
	} //end function_exists
?>
