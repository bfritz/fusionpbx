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
			if ($strength >= 3) { $charset .= "!!!!!@^$%*?....."; }
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

	//browser detection without browscap.ini dependency
		function http_user_agent() { 
			$u_agent = $_SERVER['HTTP_USER_AGENT']; 
			$bname = 'Unknown';
			$platform = 'Unknown';
			$version= "";

			//get the platform?
				if (preg_match('/linux/i', $u_agent)) {
					$platform = 'linux';
				}
				elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
					$platform = 'mac';
				}
				elseif (preg_match('/windows|win32/i', $u_agent)) {
					$platform = 'windows';
				}

			//get the name of the useragent yes seperately and for good reason
				if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
				{ 
					$bname = 'Internet Explorer'; 
					$ub = "MSIE"; 
				} 
				elseif(preg_match('/Firefox/i',$u_agent)) 
				{ 
					$bname = 'Mozilla Firefox'; 
					$ub = "Firefox"; 
				} 
				elseif(preg_match('/Chrome/i',$u_agent)) 
				{ 
					$bname = 'Google Chrome'; 
					$ub = "Chrome"; 
				} 
				elseif(preg_match('/Safari/i',$u_agent)) 
				{ 
					$bname = 'Apple Safari'; 
					$ub = "Safari"; 
				} 
				elseif(preg_match('/Opera/i',$u_agent)) 
				{ 
					$bname = 'Opera'; 
					$ub = "Opera"; 
				} 
				elseif(preg_match('/Netscape/i',$u_agent)) 
				{ 
					$bname = 'Netscape'; 
					$ub = "Netscape"; 
				} 

			//finally get the correct version number
				$known = array('Version', $ub, 'other');
				$pattern = '#(?<browser>' . join('|', $known) .
				')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
				if (!preg_match_all($pattern, $u_agent, $matches)) {
					// we have no matching number just continue
				}

			// see how many we have
				$i = count($matches['browser']);
				if ($i != 1) {
					//we will have two since we are not using 'other' argument yet
					//see if version is before or after the name
					if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
						$version= $matches['version'][0];
					}
					else {
						$version= $matches['version'][1];
					}
				}
				else {
					$version= $matches['version'][0];
				}

			// check if we have a number
				if ($version==null || $version=="") {$version="?";}

			return array(
				'userAgent' => $u_agent,
				'name'      => $bname,
				'version'   => $version,
				'platform'  => $platform,
				'pattern'    => $pattern
			);
		} 

?>
