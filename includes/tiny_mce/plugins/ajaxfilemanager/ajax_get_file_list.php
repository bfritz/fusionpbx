<?
	/**
	 * the php script used to get the list of file or folders under a specific folder
	 * @author Logan Cai (cailongqun @yahoo.com.cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	
	if(!isset($manager))
	{
		/**
		 *  this is part of  script for processing image paste 
		 */
		$_GET = $_POST;
		include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
		include_once(CLASS_MANAGER);
		define('URL_AJAX_FILE_MANAGER', CONFIG_URL_HOME);
		include_once(CLASS_SESSION_ACTION);
		$sessionAction = new SessionAction();
		include_once(DIR_AJAX_INC . "class.manager.php");
	
		$manager = new manager();
		$manager->setSessionAction($sessionAction);
		$selectedDocuments = $sessionAction->get();
		if(sizeof($selectedDocuments))
		{
			include_once(CLASS_FILE);
			$file = new file();
			foreach($selectedDocuments as $doc)
			{
				$sourcePath = $sessionAction->getFolder() . $doc;
				if($file->copyTo($sourcePath, $manager->getCurrentFolderPath()) && $sessionAction->getAction() == "cut")
				{//remove the souce files or folder
					
					$file->delete($sourcePath);
				}	
			}
			$sessionAction->set(array());
		}		
		$fileList = $manager->getFileList();
		$folderInfo = $manager->getFolderInfo();
			
	}

?><table class="tableList" id="tableList" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th width="5%"><a href="#" class="check_all" id="tickAll" title="<?=TIP_SELECT_ALL; ?>" onclick="checkAll('<?=TIP_SELECT_ALL; ?>', '<?=TIP_UNSELECT_ALL; ?>');">&nbsp;</a></th>
							<th width="6%" class="center">&nbsp;</th>
							<th width="48%" class="left"><?=LBL_NAME; ?></th>
							<th width="10%" class="center"><?=LBL_SIZE; ?></th>
							<th width="31%" class="center"><?=LBL_MODIFIED; ?></th>
						</tr>
					</thead>
					<tbody id="fileList">
<tr class="even" id="topRow" onclick="setDocInfo('folder', '0');">
							<td><input type="checkbox" name="check[]" id="check0" disabled="disabled"  />
								<input type="hidden" name="folderPath0" value="<?=transformFilePath($folderInfo['path']); ?>" id="folderPath0" />
								<input type="hidden" name="folderFile0" value="<?=$folderInfo['file']; ?>" id="folderFile0" />
								<input type="hidden" name="folderSubdir0" id="folderSubdir0" value="<?=$folderInfo['subdir']; ?>" />
								<input type="hidden" name="folderCtime0" id="folderCtime0" value="<?=date(DATE_TIME_FORMAT,$folderInfo['ctime']); ?>" />
								<input type="hidden" name="folderMtime0" id="folderMtime0" value="<?=date(DATE_TIME_FORMAT,$folderInfo['mtime']); ?>" />
								<input type="hidden" name="fileReadable0" id="folderReadable0" value="<?=$folderInfo['is_readable']; ?>" />
								<input type="hidden" name="folderWritable0" id="folderWritable0" value="<?=$folderInfo['is_writable']; ?>" />
								<input type="hidden" name="itemType0" id="itemType0" value="folder" />
							</td>
							<td>
							<?
								if(strtolower($folderInfo['path']) ==  strtolower(CONFIG_SYS_ROOT_PATH))
								{//this is root folder
									?>
									<span class="folderParent">&nbsp;</span>
									<?
								}else
								{
									?>

									<a href="<?=appendQueryString(URL_AJAX_FILE_MANAGER, "path=" . getParentPath($folderInfo['path'])); ?>" title="<?=TIP_FOLDER_GO_UP; ?>"><span class="folderParent">&nbsp;</span></a>
									<?
								}
						?>
							</td>
							<td class="left" id="<?=$folderInfo['path']; ?>">
							<?
							if($folderInfo['path'] ==  CONFIG_SYS_ROOT_PATH)
							{
								echo "&nbsp;";
							}else
							{
							?>
									<a href="<?=appendQueryString(URL_AJAX_FILE_MANAGER, "path=" . getParentPath($folderInfo['path'])); ?>" title="<?=TIP_FOLDER_GO_UP; ?>">...</a>
							<?
							}
						?>
							</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<?
							$count = 1;
							$css = "";
							//list all documents (files and folders) under this current folder, 
							foreach($fileList as $file)
							{
								$css = ($css == "" || $css == "even"?"odd":"even");
								$strDisabled = ($file['is_writable']?"":" disabled");
								$strClass = ($file['is_writable']?"left":" leftDisabled");
								if($file['type'] == 'file')
								{

								?>
								<tr class="<?=$css; ?>" id="row<?=$count; ?>"  >
									<td onclick="setDocInfo('<?=$file['type']; ?>', '<?=$count; ?>');"><input type="checkbox" name="check[]" id="check<?=$count; ?>" value="<?=$file['name']; ?>" <?=$strDisabled; ?> />
										<input type="hidden" name="fileName<?=$count; ?>" value="<?=$file['name']; ?>" id="fileName<?=$count; ?>" />
										<input type="hidden" name="fileSize<?=$count; ?>" value="<?=transformFileSize($file['size']); ?>" id="fileSize<?=$count; ?>" />
										<input type="hidden" name="fileType<?=$count; ?>" value="<?=$file['fileType']; ?>" id="fileType<?=$count; ?>" />
										<input type="hidden" name="fileCtime<?=$count; ?>" id="fileCtime<?=$count; ?>" value="<?=date(DATE_TIME_FORMAT,$file['ctime']); ?>" />
										<input type="hidden" name="fileMtime<?=$count; ?>" id="fileMtime<?=$count; ?>" value="<?=date(DATE_TIME_FORMAT,$file['mtime']); ?>" />
										<input type="hidden" name="fileReadable<?=$count; ?>" id="fileReadable<?=$count; ?>" value="<?=$file['is_readable']; ?>" />
										<input type="hidden" name="fileWritable<?=$count; ?>" id="fileWritable<?=$count; ?>" value="<?=$file['is_writable']; ?>" />
										<input type="hidden" name="filePreview<?=$count; ?>" id="filePreview<?=$count; ?>" value="<?=$file['preview']; ?>" />
										<input type="hidden" name="filePath<?=$count; ?>" id="filePath<?=$count; ?>" value="<?=$file['path']; ?>" />
										<input type="hidden" name="fileUrl<?=$count; ?>" id="fileUrl<?=$count; ?>" value="<?=getFileUrl($file['path']); ?>" />
										<input type="hidden" name="itemType<?=$count; ?>" id="itemType<?=$count; ?>" value="file" />
										</td>
									<td><a href="<?=$file['path']; ?>" target="_blank"><span class="<?=$file['cssClass']; ?>"><span id="flag<?=$count; ?>" class="<?=$file['flag']; ?>">&nbsp;</span></span></a></td>
									<td class="<?=$strClass; ?>"  id="<?=$file['path']; ?>"><?=$file['name']; ?></td>
									<td><?=transformFileSize($file['size']); ?></td>
									<td><?=date(DATE_TIME_FORMAT,$file['mtime']); ?></td>
								</tr>
								<?
								}else
								{
									?>
									<tr class="<?=$css; ?>" id="row<?=$count; ?>" >
										<td onclick="setDocInfo('folder', '<?=$count; ?>');"><input type="checkbox" name="check[]" id="check<?=$count; ?>" value="<?=$file['name']; ?>" <?=$strDisabled; ?>/>
											<input type="hidden" name="folderName<?=$count; ?>" id="folderName<?=$count; ?>" value="<?=$file['name']; ?>" />
											<input type="hidden" name="folderPath<?=$count; ?>" value="<?=transformFilePath($file['path']); ?>" id="folderPath<?=$count; ?>" />
											<input type="hidden" name="folderFile<?=$count; ?>" value="<?=$file['file']; ?>" id="folderFile<?=$count; ?>" />
											<input type="hidden" name="folderSubdir<?=$count; ?>" id="folderSubdir<?=$count; ?>" value="<?=$file['subdir']; ?>" />
											<input type="hidden" name="folderCtime<?=$count; ?>" id="folderCtime<?=$count; ?>" value="<?=date(DATE_TIME_FORMAT,$file['ctime']); ?>" />
											<input type="hidden" name="folderMtime<?=$count; ?>" id="folderMtime<?=$count; ?>" value="<?=date(DATE_TIME_FORMAT,$file['mtime']); ?>" />
											<input type="hidden" name="fileReadable<?=$count; ?>" id="folderReadable<?=$count; ?>" value="<?=$file['is_readable']; ?>" />
											<input type="hidden" name="folderWritable<?=$count; ?>" id="folderWritable<?=$count; ?>" value="<?=$file['is_writable']; ?>" />
											<input type="hidden" name="itemType<?=$count; ?>" id="itemType<?=$count; ?>" value="folder" />
										</td>
										<td><a href="<?=appendQueryString(URL_AJAX_FILE_MANAGER, "path=" . $file['path']); ?>" title="<?=TIP_FOLDER_GO_DOWN; ?>"><span class="<?=($file['file']||$file['subdir']?$file['cssClass']:"folderEmpty"); ?>"><span id="flag<?=$count; ?>" class="<?=$file['flag']; ?>">&nbsp;</span></span></a></td>
										<td class="<?=$strClass; ?>" id="<?=$file['path']; ?>"><?=$file['name']; ?></td>
										<td>&nbsp;</td>
										<td><?=date(DATE_TIME_FORMAT,$file['mtime']); ?></td>
									</tr>
									<?
								}
								$count++;
							}
						?>	
					</tbody>
				</table>