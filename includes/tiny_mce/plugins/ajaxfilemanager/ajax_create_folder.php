<?
	/**
	 * create a folder
	 * @author Logan Cai (cailongqun@yahoo.com.cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");	
	echo "{";
	$error = "";
	$info = "";	
/*	$_POST['new_folder'] = substr(md5(time()), 1, 5);
	$_POST['currentFolderPath'] = "../../uploaded/";*/
	
	if(empty($_POST['new_folder']))
	{
		$error  =  ERR_FOLDER_NAME_EMPTY;
	}elseif(!preg_match("/[a-zA-Z0-9_\- ]+/", $_POST['new_folder']))
	{
		$error  =  ERR_FOLDER_FORMAT;
	}else if(empty($_POST['currentFolderPath']) || !isUnderRoot($_POST['currentFolderPath']))
	{
		$error = ERR_FOLDER_PATH_NOT_ALLOWED;
	}
	elseif(file_exists(addTrailingSlash($_POST['currentFolderPath']) . $_POST['new_folder']))
	{
		$error = ERR_FOLDER_EXISTS;
	}else
	{
	include_once(CLASS_FILE);
		$file = new file();
		if($file->mkdir(addTrailingSlash($_POST['currentFolderPath']) . $_POST['new_folder'], 0777))
		{
					include_once(CLASS_MANAGER);
					$manager = new manager(addTrailingSlash($_POST['currentFolderPath']) . $_POST['new_folder'], false);
					$pathInfo = $manager->getFolderInfo(addTrailingSlash($_POST['currentFolderPath']) . $_POST['new_folder']);
					foreach($pathInfo as $k=>$v)
					{				
						switch ($k)
						{
							case "path";
								$v = transformFilePath($v);
								break;
							case "ctime";								
							case "mtime":
							case "atime":
								$v = date(DATE_TIME_FORMAT, $v);
								break;
						}							
						$info .= sprintf(", %s:'%s'", $k, $v);
					}
					$info .= sprintf(", url:'%s'",  appendQueryString(CONFIG_URL_HOME, "path=" . $pathInfo['path']));
					$info .= sprintf(", tip:'%s'", $k, TIP_FOLDER_GO_DOWN);
					$info .= sprintf(", tipedit:'%s'", $k, TIP_DOC_RENAME);
		}else 
		{
			$error = ERR_FOLDER_CREATION_FAILED;
		}
		//$error = "For security reason, folder creation function has been disabled.";
	}
	echo "error:'" . $error . "'";
	echo $info;
	echo "}";
?>