<?
	/**
	 * file manager platform
	 * @author Logan Cai (cailongqun@yahoo.com.cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
	$session->gc();
	require_once(CLASS_MANAGER);
	define('URL_AJAX_FILE_MANAGER', CONFIG_URL_HOME);
	require_once(CLASS_SESSION_ACTION);
	$sessionAction = new SessionAction();
	//displayArray($sessionAction->get());
	require_once(DIR_AJAX_INC . "class.manager.php");

	$manager = new manager();
	$manager->setSessionAction($sessionAction);
	
	$fileList = $manager->getFileList();
	$folderInfo = $manager->getFolderInfo();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="Logan Cai" />
<meta name="website" content="http://www.phpletter.com" />
<script type="text/javascript" src="jscripts/jquery.js"></script>
<script type="text/javascript" src="jscripts/jeditable.js"></script>
<script type="text/javascript" src="jscripts/form.js"></script>
<script type="text/javascript" src="jscripts/select.js"></script>
<script type="text/javascript" src="jscripts/file_manager_general.js"></script>
<script type="text/javascript" src="jscripts/for_<?=CONFIG_THEME_MODE; ?>.js"></script>

<script type="text/javascript">
function enableEditable()
{
			 $("#fileList tr[@id^=row] td.left").editable("<?=CONFIG_URL_SAVE_NAME; ?>",
		 {
					 submit    : 'Save',
					 width	   : '150',
					 height    : '14',
					 loadtype  : 'POST',
					 event	   :  'dblclick',
					 indicator : "<img src='theme/<?=CONFIG_THEME_NAME; ?>/images/loading.gif'>",
					 tooltip   : '<?=TIP_DOC_RENAME; ?>'
		 }
		 
		 );	 	
}
	var urlPreview = '<?=CONFIG_URL_PREVIEW; ?>';
	var msgNotPreview = '<?=PREVIEW_NOT_PREVIEW; ?>';
	var urlCut = '<?=CONFIG_URL_CUT; ?>';
	var urlCopy = '<?=CONFIG_URL_COPY; ?>';
	var urlPaste = '<?=CONFIG_URL_PASTE; ?>';
	var warningCutPaste = '<?=WARNING_CUT_PASTE; ?>';
	var warningCopyPaste = '<?=WARNING_COPY_PASTE; ?>';
	var urlDelete = '<?=CONFIG_URL_DELETE; ?>';
	var action = '<?=$sessionAction->getAction(); ?>';
	var numFiles = <?=$sessionAction->count(); ?>;
	var warningCloseWindow = '<?=WARING_WINDOW_CLOSE; ?>';
$(document).ready(
	function()
	{
		
		//tableRuler('#tableList tbody tr');
		$('#edit').hide();	
		enableEditable();
		initAction();
	} );

	
</script>
<link rel="stylesheet" type="text/css" href="theme/<?=CONFIG_THEME_NAME; ?>/css/<?=CONFIG_THEME_MODE; ?>.css" />
<title>Ajax File Manager</title>
</head>
<body>
	<div id="container">
		<div id="leftCol">
			<div id="header">
				<ul id="actionHeader">
					<li><a href="#" id="actionRefresh" onclick="windowRefresh();"><span><?=LBL_ACTION_REFRESH; ?></span></a></li>
					<li><a href="#" id="actionDelete" onclick="deleteDocuments('<?=ERR_NOT_DOC_SELECTED; ?>', '<?=ERR_DELTED_FAILED; ?>', '<?=WARNING_DELETE; ?>');"><span><?=LBL_ACTION_DELETE; ?></span></a></li>
					<li><a href="#" id="actionCut" onclick="cutDocuments('<?=ERR_NOT_DOC_SELECTED_FOR_CUT; ?>');"><span><?=LBL_ACTION_CUT; ?></span></a><li>					
					<li><a href="#" id="actionCopy" onclick="copyDocuments('<?=ERR_NOT_DOC_SELECTED_FOR_COPY; ?>');""><span><?=LBL_ACTION_COPY; ?></span></a><li>
					<li><a href="#" id="actionPaste" onclick="pasteDocuments('<?=ERR_NOT_DOC_SELECTED_FOR_PASTE; ?>');""><span><?=LBL_ACTION_PASTE; ?></span></a><li>
					<li ><a href="#" id="actionInfo" target="_blank" title="Visit www.phpletter.com for more information"><span>Info</span></a><li>
					<!-- thest functions will be added in the near future
 					<li ><a href="#" id="actionZip"><span>Zip</span></a><li>
					<li ><a href="#" id="actionUnzip"><span>Unzip</span></a><li>-->
				</ul>
				<img src="theme/<?=CONFIG_THEME_NAME; ?>/images/loading.gif" id="loading" width="32" height="32" style="display:none;" />
				
			</div>
			<form action="<?=CONFIG_URL_DELETE ?>" method="POST" name="formAction" id="formAction"><select name="selectedDoc[]" id="selectedDoc" style="display:none;" multiple="multiple"></select><input type="hidden" name="action_value" value="" id="action_value" /><input type="hidden" name="currentFolderPath"  value="<?=$folderInfo['path']; ?>" /></form>
			<div id="body"><?
						include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ajax_get_file_list.php');
					?></div>
			<div id="footer">
					<form name="hiddenForm" id="hiddenForm" action="" method="POST">
					   <input type="hidden" name="selectedFileRowNum" id="selectedFileRowNum" value="" />
					</form>
					<div id="divNewFolder">
    					<form id="formNewFolder" name="formNewFolder" action="<?=CONFIG_URL_CREATE_FOLDER; ?>" method="POST">
    						<p><input type="hidden" name="currentFolderPath" value="<?=$folderInfo['path']; ?>" />
    						<input class="input" type="text" name="new_folder" id="new_folder"  value="<?=LBL_BTN_NEW_FOLDER; ?>" size="44"/>
    						<button class="button" id="create" onclick="return createFolder( '<?=ERR_FOLDER_FORMAT; ?>');"><?=LBL_BTN_CREATE; ?></button></p>
    					</form>
					</div>
					<div id="divFormFile">
						<form name="formFile" action="<?=CONFIG_URL_UPLOAD; ?>" method="post" id="formFile" enctype="multipart/form-data">
    						<p><input type="hidden" name="currentFolderPath"  value="<?=$folderInfo['path']; ?>" />
    						<input class="inputFile" type="file" name="new_file" id="new_file" size="34"/>
    						<button class="button" id="upload" onclick="return uploadFile('<?=ERR_FILE_NAME_FORMAT; ?>', '<?=ERR_FILE_NOT_UPLOADED; ?>');"><?=LBL_BTN_UPLOAD; ?></button></p>
						</form>
					</div>
					<div class="clear"></div>
			</div>
		</div>
		<div id="rightCol">
			<fieldset id="fileFieldSet" style="display:none" >
				<legend><?=LBL_FILE_INFO; ?></legend>
				<table cellpadding="0" cellspacing="0" class="tableSummary" id="fileInfo">
					<tbody>
						<tr>
							<th><?=LBL_FILE_NAME; ?></th>
							<td colspan="3" id="fileName"></td>
						</tr>
						<tr>
							<th><?=LBL_FILE_CREATED; ?></th>
							<td colspan="3" id="fileCtime"></td>

						</tr>
						<tr>
							<th><?=LBL_FILE_MODIFIED; ?></th>
							<td colspan="3" id="fileMtime"></td>
						</tr>
						<tr>
							<th><?=LBL_FILE_SIZE; ?></th>
							<td id="fileSize"></td>
							<th><?=LBL_FILE_TYPE; ?></th>
							<td id="fileType"></td>
						</tr>
						<tr>
							<th><?=LBL_FILE_WRITABLE; ?></th>
							<td id="fileWritable"><span class="flagYes">&nbsp;</span></td>
							<th><?=LBL_FILE_READABLE; ?></th>
							<td id="fileReadable"><span class="flagNo">&nbsp;</span></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			<fieldset id="folderFieldSet" >
				<legend><?=LBL_FOLDER_INFO; ?></legend>
				<table cellpadding="0" cellspacing="0" class="tableSummary" id="folderInfo">
					<tbody>
						<tr>
							<th><?=LBL_FOLDER_PATH; ?></th>
							<td colspan="3" id="folderPath"><?=transformFilePath($folderInfo['path']); ?></td>
						</tr>
						<tr>
							<th><?=LBL_FOLDER_CREATED; ?></th>
							<td colspan="3" id="folderCtime"><?=date(DATE_TIME_FORMAT,$folderInfo['ctime']); ?></td>

						</tr>
						<tr>
							<th><?=LBL_FOLDER_MODIFIED; ?></th>
							<td colspan="3" id="folderMtime"><?=date(DATE_TIME_FORMAT,$folderInfo['mtime']); ?></td>
						</tr>
						<tr>
							<th><?=LBL_FOLDER_SUDDIR; ?></th>
							<td id="folderSubdir"><?=$folderInfo['subdir']; ?></td>
							<th><?=LBL_FOLDER_FIELS; ?></th>
							<td id="folderFile"><?=$folderInfo['file']; ?></td>
						</tr>
						<tr>
							<th><?=LBL_FOLDER_WRITABLE; ?></th>
							<td id="folderWritable"><span class="<?=($folderInfo['is_readable']?'flagYes':'flagNo'); ?>">&nbsp;</span></td>
							<th><?=LBL_FOLDER_READABLE; ?></th>
							<td id="folderReadable"><span class="<?=($folderInfo['is_writable']?'flagYes':'flagNo'); ?>">&nbsp;</span></td>
						</tr>


					</tbody>
				</table>
			</fieldset>
			<fieldset>
				<legend><?=LBL_PREVIEW; ?></legend>
				<div id="preview">
				<?=PREVIEW_NOT_PREVIEW; ?>				
				</div>
				<img id="previewLoading" src="theme/<?=CONFIG_THEME_NAME; ?>/images/loading.gif" style="display:none" width="32" height="32" />
			</fieldset>
			<div id="previewFooter">
				<p>
				<?
					if(CONFIG_THEME_MODE != 'stand_alone')
					{//select button is not needed for stand alone mode
						?>
						<button class="button" id="select" onclick="selectFile('<?=ERR_NOT_FILE_SELECTED; ?>');"><?=LBL_BTN_SELECT; ?></button> 
						<?
					}
				?>				
				<button class="button" id="edit" onclick="selectImageForEdit('<?=WARNING_IMAGE_EDIT; ?>', '<?=CONFIG_URL_IMAGE_EDITOR; ?>');"><?=LBL_BTN_EDIT_IMAGE; ?></button>
				<?
					if(CONFIG_THEME_MODE != 'stand_alone')
					{//cacel button is not needed for stand alone mode			
						?>
						<button class="button" id="cancel" onclick="cancelSelectFile();"><?=LBL_BTN_CANCEL; ?></button></p>
						<?
					}else 
					{
						?>
						<button class="button" id="close" onclick="return closeWindow();"><?=LBL_BTN_CLOSE; ?></button></p>
						<?
					}
				?>
				
			</div>
		</div>
	</div>
	<div class="clear"></div>
</body>
</html>
