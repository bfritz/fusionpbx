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
	//error_reporting(E_ALL ^ E_NOTICE); //hide notices
		error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ); //hide notices and warnings
		//error_reporting(E_ALL);

	//start the session
		session_start();

	//icons
		$v_icon_edit = PROJECT_PATH."/images/icon_e.gif";
		$v_icon_add = PROJECT_PATH."/images/icon_plus.gif";
		$v_icon_delete = PROJECT_PATH."/images/icon_x.gif";
		$v_icon_view = PROJECT_PATH."/images/icon_view.gif";
		$v_icon_cal = PROJECT_PATH."/images/icon_cal.gif";
		$v_icon_up = PROJECT_PATH."/images/icon_up.gif";

	//determine whether to use php http compression
		if (strlen($_SESSION['php_http_compression']) == 0) {
			$sql = "";
			$sql .= "select * from v_vars ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and var_name = 'php_http_compression' ";
			$prepstatement = $db->prepare(check_sql($sql));
			$prepstatement->execute();
			$result = $prepstatement->fetchAll();
			foreach ($result as &$row) {
				$_SESSION['php_http_compression'] = $row["var_value"];
				break; //limit to 1 row
			}
		}
	//set http compression
		if ($_SESSION['http_compression'] != "false") {
				if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
					ob_start("ob_gzhandler");
				}
				else{
					ob_start();
				}
		}

	//get the document_root parent directory
		$document_root_parent = join(array_slice(explode("\\",realpath($_SERVER["DOCUMENT_ROOT"])),0,-1), '/');

	//detect the v_secure directory
		if (strlen($dbfilepath) == 0) {
			$tmp_path = $document_root_parent."/secure";
			if (file_exists($tmp_path)) { $v_secure = $tmp_path; }

			$tmp_path = realpath($_SERVER["DOCUMENT_ROOT"]).PROJECT_PATH."/secure";
			if (file_exists($tmp_path)) { $v_secure = $tmp_path; }
		}
		else {
			$v_secure = $dbfilepath;
		}
		$v_secure = str_replace("\\", "/", $v_secure);
		$v_secure = realpath($v_secure);


	//generate a random password with upper, lowercase and symbols
		function generate_password($length = 10, $strength = 4) {
			$password = '';
			$charset = '';
			if ($strength >= 1) { $charset .= "0123456789"; }
			if ($strength >= 2) { $charset .= "abcdefghijkmnopqrstuvwxyz";	}
			if ($strength >= 3) { $charset .= "!!!!!@^$#%*?....."; }
			if ($strength >= 4) { $charset .= "ABCDEFGHIJKLMNPQRSTUVWXYZ";	}
			srand((double)microtime() * rand(1000000, 9999999));
			while ($length > 0) {
					$password.= $charset[rand(0, strlen($charset)-1)];
					$length--;
			}
			return $password;
		}
		//echo generate_password(4, 4);

	//if magic quotes is enabled remove the slashes
		if (get_magic_quotes_gpc()) {
			$in = array(&$_GET, &$_POST, &$_COOKIE);
			while (list($k,$v) = each($in)) {
					foreach ($v as $key => $val) {
							if (!is_array($val)) {
									$in[$k][$key] = stripslashes($val);
									continue;
							}
							$in[] =& $in[$k][$key];
					}
			}
			unset($in);
		}

	//tail php function for non posix systems
		function tail($file, $num_to_get=10) {
				$fp = fopen($file, 'r');
				$position = filesize($file);
				$chunklen = 4096;
				if($position-$chunklen<=0) { 
					fseek($fp,0); 
				}
				else { 
					fseek($fp, $position-$chunklen);
				}
				$data="";$ret="";$lc=0;
				while($chunklen > 0)
				{
						$data = fread($fp, $chunklen);
						$dl=strlen($data);
						for($i=$dl-1;$i>=0;$i--){
								if($data[$i]=="\n"){
										if($lc==0 && $ret!="")$lc++;
										$lc++;
										if($lc>$num_to_get)return $ret;
								}
								$ret=$data[$i].$ret;
						}
						if($position-$chunklen<=0){
								fseek($fp,0);
								$chunklen=$chunklen-abs($position-$chunklen);
						}else   fseek($fp, $position-$chunklen);
						$position = $position - $chunklen;
				}
				fclose($fp);
				return $ret;
		}

?>