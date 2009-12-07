<?
		/**
	 * Ajax image editor platform
	 * @author Logan Cai (cailongqun@yahoo.com.cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
		$session->gc();
		$_GET['path'] = empty($_GET['path'])?CONFIG_SYS_ROOT_PATH . "ajax_image_editor_demo.jpg":$_GET['path'];
		if(!empty($_GET['path']) && file_exists($_GET['path']) && is_file($_GET['path']) && isUnderRoot($_GET['path']))
		{
				$path = $_GET['path'];
		}else 
		{
			die(IMG_GEN_IMG_NOT_EXISTS);
		}
		require_once(CLASS_HISTORY);
		$history = new History($path, $session);
		if(CONFIG_SYS_DEMO_ENABLE)
		{
			$sessionImageInfo = $history->getLastestRestorable();
			$originalSessionImageInfo = $history->getOriginalImage();
			if(sizeof($originalSessionImageInfo))
			{
				$path = backslashToSlash($session->getSessionDir() . $originalSessionImageInfo['info']['name']);
			}
		}
		require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "class.image.php");
		$image = new Image();
		$imageInfo = $image->getImageInfo($path);

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="Logan Cai" />
<meta name="website" content="http://www.phpletter.com" />
<script type="text/javascript" src="jscripts/jquery.js"></script>
<script type="text/javascript" src="jscripts/form.js"></script>
<script type="text/javascript" src="jscripts/rotate.js"></script>
<script type="text/javascript" src="jscripts/interface.js"></script>
<script type="text/javascript" src="jscripts/image_editor_general.js"></script>
<script type="text/javascript">
	var imageHistory = false;
	var warningLostChanges = '<?=IMG_WARNING_LOST_CHANAGES; ?>';
	var warningReset = '<?=IMG_WARNING_REST; ?>';
	var warningResetEmpty = '<?=IMG_WARNING_EMPTY_RESET; ?>';
	var warningEditorClose = '<?=IMG_WARING_WIN_CLOSE; ?>';
	var warningUndoImage = '<?=IMG_WARNING_UNDO; ?>';
	var warningFlipHorizotal = '<?=IMG_WARING_FLIP_H; ?>';
	var warningFlipVertical = '<?=IMG_WARING_FLIP_V; ?>';
	var numSessionHistory = <?=$history->getNumRestorable(); ?>;
	var noChangeMadeBeforeSave = '<?=IMG_WARNING_NO_CHANGE_BEFORE_SAVE; ?>';
	$(document).ready(
		function()
		{
			$('#image_mode').val('');
			$('#angle').val(0);
			$(getImageElement()).clone().appendTo("#hiddenImage");
			changeMode();
			initDisabledButtons(true);
		}
	);
	
</script>
<link href="theme/<?=CONFIG_THEME_NAME; ?>/css/image_editor_general.css" type="text/css" rel="stylesheet" />
<title>Ajax Image Editor</title>
</head>
<body>
<?
	//displayArray($_SESSION);
	 
?>
<div id="controls">
	<fieldset id="modes">
		<legend>Modes</legend>
		<form name="formAction" id="formAction" method="post" action="<?=CONFIG_URL_IMAGE_UNDO; ?>">
			<input type="hidden" name="file_path" id="file_path" value="<?=$_GET['path']; ?>" />
			
			<p><label><?=IMG_MODE_RESIZE; ?></label> <input type="radio" name="mode" value="resize" class="input" checked="checked"  onclick="return changeMode();"/>
			<label><?=IMG_MODE_CROP; ?></label> <input type="radio" name="mode" value="crop" class="input" onclick="return changeMode();" />
			<label><?=IMG_MODE_ROTATE; ?></label> <input type="radio" name="mode" value="rotate" class="input" onclick="return changeMode();" />
			<label><?=IMG_MODE_FLIP; ?></label> <input type="radio" name="mode" value="flip" class="input" onclick="return changeMode();" />
			<label><?=IMG_CHECKBOX_CONSTRAINT; ?></label> <input type="checkbox" name="constraint" id="constraint" value="1" class="input" onclick="return toggleConstraint();" />
			<!--			<label>Watermark:</label> <input type="radio" name="mode" value="watermark" class="input" onclick="return false;" />-->
			
			<button id="actionRotateLeft" class="disabledButton" onclick="return leftRotate();" disabled><?=IMG_BTN_ROTATE_LEFT; ?></button>
			<button id="actionRotateRight" class="disabledButton" onclick="return rightRotate();" disabled><?=IMG_BTN_ROTATE_RIGHT; ?></button>
			<button id="actionFlipH" class="disabledButton" onclick="return flipHorizontal();" disabled><?=IMG_BTN_FLIP_H; ?></button>
			<button id="actionFlipV" class="disabledButton" onclick="return flipVertical();" disabled><?=IMG_BTN_FLIP_V; ?></button>			
			<button id="actionReset" class="button" onclick="return resetEditor();"><?=IMG_BTN_RESET; ?></button>
			<button id="actionUndo" class="button" onclick="return undoImage();"><?=IMG_BTN_UNDO; ?></button>
			<button id="actionSave" class="button" onclick="return saveImage();"><?=IMG_BTN_SAVE; ?></button>
			<button id="actionClose" class="button" onclick="return editorClose();"><?=IMG_BTN_CLOSE; ?></button></p>
		</form>
	</fieldset>
	<fieldset id="imageInfo">
		<legend id="imageInfoLegend"><?=IMG_INFO; ?></legend>
		<form name="formImageInfo" action="<?=CONFIG_URL_IMAGE_SAVE; ?>" method="post" id="formImageInfo">
			<p><input type="hidden" name="mode" id="image_mode" value="" />
			<input type="hidden" name="path" id="path" value="<?=$_GET['path']; ?>"  />
			<input type="hidden" name="flip_angle" id="flip_angle" value="" />
			<label><?=IMG_LBL_WIDTH; ?></label> <input type="text" name="width" id="width" value="" class="input imageInput"  />
			<label><?=IMG_LBL_HEIGHT; ?></label> <input type="text" name="height" id="height" value="" class="input imageInput" />
			<label><?=IMG_LBL_X; ?></label> <input type="text" name="x" id="x" value="" class="input imageInput"/>
			<label><?=IMG_LBL_Y; ?></label> <input type="text" name="y" id="y" value="" class="input imageInput"/>
<!--			<b>Percentage:</b> <input type="text" name="percentage" id="percentage" value="" class="input imageInput"/>-->
			<label><?=IMG_LBL_RATIO; ?></label> <input type="text" name="ratio" id="ratio" value="" class="input imageInput"/>
			<label><?=IMG_LBL_ANGLE; ?></label> <input type="text" name="angle" id="angle" value="" class="input imageInput" />
			
			</p>
		</form>
	</fieldset>
</div>
<div id="imageArea">
    <div id="imageContainer">
    	<img src="<?=$path; ?>" name="<?=basename($path); ?>" width="<?=$imageInfo['width']; ?>" height="<?=$imageInfo['height']; ?>" />
    </div>
    <div id="resizeMe">
    	<div id="resizeSE"></div>
    	<div id="resizeE"></div>
    	<div id="resizeNE"></div>
    	<div id="resizeN"></div>
    	<div id="resizeNW"></div>
    	<div id="resizeW"></div>
    	<div id="resizeSW"></div>
    	<div id="resizeS"></div>
		<img id="loading" style="display:none;" src="theme/<?=CONFIG_THEME_NAME; ?>/images/loading.gif" />
    </div>
</div>
    <div id="hiddenImage">
    </div>

</body>
</html>
