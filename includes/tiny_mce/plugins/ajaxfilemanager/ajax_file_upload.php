<?
	/**
	 * processing the uploaded files
	 * @author Logan Cai (cailongqun@yahoo.com.cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */	
	
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");	
	@ob_start();
	echo "{";
	$error = "";
	$info = "";
	include_once(CLASS_UPLOAD);
	$upload = new Upload();
								
	$upload->setInvalidFileExt(explode(",", CONFIG_UPLOAD_INVALID_EXTS));
	if(empty($_POST['currentFolderPath']) || !isUnderRoot($_POST['currentFolderPath']))
	{
		$error = ERR_FOLDER_PATH_NOT_ALLOWED;
	}else	if(!$upload->isFileUploaded('new_file'))
	{
		$error = ERR_FILE_NOT_UPLOADED;
	}else if(!$upload->moveUploadedFile($_POST['currentFolderPath']))
	{
		$error = ERR_FILE_MOVE_FAILED;
	}	
	elseif(!$upload->isPermittedFileExt(explode(",", CONFIG_UPLOAD_VALID_EXTS)))
	{		
		$error = ERR_FILE_TYPE_NOT_ALLOWED;
	}elseif(defined('CONFIG_UPLOAD_MAXSIZE') && CONFIG_UPLOAD_MAXSIZE && $upload->isSizeTooBig(CONFIG_UPLOAD_MAXSIZE))
	{		
		 $error = sprintf(ERROR_FILE_TOO_BID, transformFileSize(CONFIG_UPLOAD_MAXSIZE));
	}else
	{
							include_once(CLASS_FILE);
							$path = $upload->getFilePath();
							$obj = new file($path);
							$tem = $obj->getFileInfo();							
							if(sizeof($tem))
							{	
								include_once(CLASS_MANAGER);
							
								$manager = new manager($upload->getFilePath(), false);			
															
								$fileType = $manager->getFileType($upload->getFileName());

								foreach($fileType as $k=>$v)
								{
									$tem[$k] = $v;
								}
								
								$tem['path'] = backslashToSlash($path);		
								$tem['type'] = "file";
								$tem['size'] = transformFileSize($tem['size']);
								$tem['ctime'] = date(DATE_TIME_FORMAT, $tem['ctime']);
								$tem['mtime'] = date(DATE_TIME_FORMAT, $tem['mtime']);
								$obj->close();
								foreach($tem as $k=>$v)
								{
										$info .= sprintf(", %s:'%s'", $k, $v);									
								}

								$info .= sprintf(", url:'%s'",  getFileUrl($path));
								$info .= sprintf(", tipedit:'%s'",  TIP_DOC_RENAME);		

																				
							}else 
							{
								$error = ERR_FILE_NOT_AVAILABLE;
							}


	}
	echo "error:'" . $error . "'";
	echo $info;
	echo "}";
	
?>