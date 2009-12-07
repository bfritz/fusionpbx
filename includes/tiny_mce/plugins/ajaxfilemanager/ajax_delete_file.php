<?
	/**
	 * delete selected files
	 * @author Logan Cai (cailongqun@yahoo.com.cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
	$error = "";
	if(!isset($_POST['selectedDoc']) || !is_array($_POST['selectedDoc']) || sizeof($_POST['selectedDoc']) < 1)
	{
		$error = ERR_NOT_FILE_SELECTED;
	}
	elseif(empty($_POST['currentFolderPath']) || !isUnderRoot($_POST['currentFolderPath']))
	{
		$error = ERR_FOLDER_PATH_NOT_ALLOWED;
	}else 
	{
		include_once(CLASS_FILE);
		$file = new file();
		
		foreach($_POST['selectedDoc'] as $doc)
		{
			$file->delete(addTrailingSlash($_POST['currentFolderPath']) . $doc);
		}
	}
	echo "{error:'" . $error . "'}";
?>