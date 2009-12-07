<?php

	//icons
		$v_icon_edit = PROJECT_PATH."/images/icon_e.gif";
		$v_icon_add = PROJECT_PATH."/images/icon_plus.gif";
		$v_icon_delete = PROJECT_PATH."/images/icon_x.gif";
		$v_icon_view = PROJECT_PATH."/images/icon_view.gif";
		$v_icon_cal = PROJECT_PATH."/images/icon_cal.gif";
		$v_icon_up = PROJECT_PATH."/images/icon_up.gif";

	//start the session
		session_start();

	//error_reporting(E_ALL ^ E_NOTICE); //hide notices
		error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ); //hide notices and warnings
		//error_reporting(E_ALL);

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