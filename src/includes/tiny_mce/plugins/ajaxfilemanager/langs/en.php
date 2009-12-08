<?
	/**
	 * language pack
	 * @author Logan Cai (cailongqun@yahoo.com.cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
	define('DATE_TIME_FORMAT', 'd/M/Y H:i:s');
	//Label
		//Top Action
		define('LBL_ACTION_REFRESH', 'Refresh');
		define("LBL_ACTION_DELETE", 'Delete');
		define('LBL_ACTION_CUT', 'Cut');
		define('LBL_ACTION_COPY', 'Copy');
		define('LBL_ACTION_PASTE', 'Paste');
		//File Listing
	define('LBL_NAME', 'Name');
	define('LBL_SIZE', 'Size');
	define('LBL_MODIFIED', 'Modified At');
		//File Information
	define('LBL_FILE_INFO', 'File Information:');
	define('LBL_FILE_NAME', 'Name:');	
	define('LBL_FILE_CREATED', 'Created At:');
	define("LBL_FILE_MODIFIED", 'Modified At:');
	define("LBL_FILE_SIZE", 'File Size:');
	define('LBL_FILE_TYPE', 'File Type:');
	define("LBL_FILE_WRITABLE", 'Writable?');
	define("LBL_FILE_READABLE", 'Readable?');
		//Folder Information
	define('LBL_FOLDER_INFO', 'Folder Information');
	define("LBL_FOLDER_PATH", 'Path:');
	define("LBL_FOLDER_CREATED", 'Created At:');
	define("LBL_FOLDER_MODIFIED", 'Modified At:');
	define('LBL_FOLDER_SUDDIR', 'Subfolders:');
	define("LBL_FOLDER_FIELS", 'Files:');
	define("LBL_FOLDER_WRITABLE", 'Writable?');
	define("LBL_FOLDER_READABLE", 'Readable?');
		//Preview
	define("LBL_PREVIEW", 'Preview');
	//Buttons
	define('LBL_BTN_SELECT', 'Select');
	define('LBL_BTN_CANCEL', 'Cancel');
	define("LBL_BTN_UPLOAD", 'Upload');
	define('LBL_BTN_CREATE', 'Create');
	define('LBL_BTN_CLOSE', 'Close');
	define("LBL_BTN_NEW_FOLDER", 'New Folder');
	define('LBL_BTN_EDIT_IMAGE', 'Edit');
	//Cut
	define('ERR_NOT_DOC_SELECTED_FOR_CUT', 'No document(s) selected for cut.');
	//Copy
	define('ERR_NOT_DOC_SELECTED_FOR_COPY', 'No document(s) selected for copy.');
	//Paste
	define('ERR_NOT_DOC_SELECTED_FOR_PASTE', 'No document(s) selected for paste.');
	define('WARNING_CUT_PASTE', 'Are you sure to move selected documents to current folder?');
	define('WARNING_COPY_PASTE', 'Are you sure to copy selected documents to current folder?');
	
	//ERROR MESSAGES
		//deletion
	define('ERR_NOT_FILE_SELECTED', 'Please select a file.');
	define('ERR_NOT_DOC_SELECTED', 'No document(s) selected for deletion.');
	define('ERR_DELTED_FAILED', 'Unable to delete selected document(s).');
	define('ERR_FOLDER_PATH_NOT_ALLOWED', 'The folder path is not allowed.');
		//class manager
	define("ERR_FOLDER_NOT_FOUND", 'Unable to locate the specific folder: ');
		//rename
	define('ERR_RENAME_FORMAT', 'Please give it a name which only contain letters, digits, space, hyphen and underscore.');
	define('ERR_RENAME_EXISTS', 'Please give it a name which is unique under the folder.');
	define('ERR_RENAME_FILE_NOT_EXISTS', 'The file/folder does not exist.');
	define('ERR_RENAME_FAILED', 'Unable to rename it, please try again.');
	define('ERR_RENAME_EMPTY', 'Please give it a name.');
	define("ERR_NO_CHANGES_MADE", 'No changes has been made.');
	define('ERR_RENAME_FILE_TYPE_NOT_PERMITED', 'You are not permitted to change the file to such extension.');
		//folder creation
	define('ERR_FOLDER_FORMAT', 'Please give it a name which only contain letters, digits, space, hyphen and underscore.');
	define('ERR_FOLDER_EXISTS', 'Please give it a name which is unique under the folder.');
	define('ERR_FOLDER_CREATION_FAILED', 'Unable to create a folder, please try again.');
	define('ERR_FOLDER_NAME_EMPTY', 'Please give it  a name.');
	
		//file upload
	define("ERR_FILE_NAME_FORMAT", 'Please give it a name which only contain letters, digits, space, hyphen and underscore.');
	define('ERR_FILE_NOT_UPLOADED', 'No file has been selected for uploading.');
	define('ERR_FILE_TYPE_NOT_ALLOWED', 'You are not allowed to upload such file type.');
	define('ERR_FILE_MOVE_FAILED', 'Failed to move the file.');
	define('ERR_FILE_NOT_AVAILABLE', 'The file is unavailable.');
	define('ERROR_FILE_TOO_BID', 'File too large. (max: %s)');
	

	//Tips
	define('TIP_FOLDER_GO_DOWN', 'Single Click to get to this folder...');
	define("TIP_DOC_RENAME", 'Double Click to edit...');
	define('TIP_FOLDER_GO_UP', 'Single Click to get to the parent folder...');
	define("TIP_SELECT_ALL", 'Select All');
	define("TIP_UNSELECT_ALL", 'Unselect All');
	//WARNING
	define('WARNING_DELETE', 'Are you sure to delete selected files.');
	define('WARNING_IMAGE_EDIT', 'Please select an image for edit.');
	define('WARING_WINDOW_CLOSE', 'Are you sure to close the window?');
	//Preview
	define('PREVIEW_NOT_PREVIEW', 'No preview available.');
	define('PREVIEW_OPEN_FAILED', 'Unable to open the file.');
	define('PREVIEW_IMAGE_LOAD_FAILED', 'Unable to load the image');

	//Login
	define('LOGIN_PAGE_TITLE', 'Ajax File Manager Login Form');
	define('LOGIN_FORM_TITLE', 'Login Form');
	define('LOGIN_USERNAME', 'Username:');
	define('LOGIN_PASSWORD', 'Password:');
	define('LOGIN_FAILED', 'Invalid username/password.');
	
	
	//88888888888   Below for Image Editor   888888888888888888888
		//Warning 
		define('IMG_WARNING_NO_CHANGE_BEFORE_SAVE', "You have not made any changes to the images.");
		
		//General
		define('IMG_GEN_IMG_NOT_EXISTS', 'Image does not exist');
		define('IMG_WARNING_LOST_CHANAGES', 'All unsaved changes made to the image will lost, are you sure you wish to continue?');
		define('IMG_WARNING_REST', 'All unsaved changes made to the image will be lost, are you sure to reset?');
		define('IMG_WARNING_EMPTY_RESET', 'No changes has been made to the image so far');
		define('IMG_WARING_WIN_CLOSE', 'Are you sure to close the window?');
		define('IMG_WARNING_UNDO', 'Are you sure to restore the image to previous state?');
		define('IMG_WARING_FLIP_H', 'Are you sure to flip the image horizotally?');
		define('IMG_WARING_FLIP_V', 'Are you sure to flip the image vertically');
		define('IMG_INFO', 'Image Information');
		
		//Mode
			define('IMG_MODE_RESIZE', 'Resize:');
			define('IMG_MODE_CROP', 'Crop:');
			define('IMG_MODE_ROTATE', 'Rotate:');
			define('IMG_MODE_FLIP', 'Flip:');		
		//Button
		
			define('IMG_BTN_ROTATE_LEFT', '90&deg;CCW');
			define('IMG_BTN_ROTATE_RIGHT', '90&deg;CW');
			define('IMG_BTN_FLIP_H', 'Flip Horizontal');
			define('IMG_BTN_FLIP_V', 'Flip Vertical');
			define('IMG_BTN_RESET', 'Reset');
			define('IMG_BTN_UNDO', 'Undo');
			define('IMG_BTN_SAVE', 'Save');
			define('IMG_BTN_CLOSE', 'Close');
		//Checkbox
			define('IMG_CHECKBOX_CONSTRAINT', 'Constraint?');
		//Label
			define('IMG_LBL_WIDTH', 'Width:');
			define('IMG_LBL_HEIGHT', 'Height:');
			define('IMG_LBL_X', 'X:');
			define('IMG_LBL_Y', 'Y:');
			define('IMG_LBL_RATIO', 'Ratio:');
			define('IMG_LBL_ANGLE', 'Angle:');
		//Editor

			
		//Save
		define('IMG_SAVE_EMPTY_PATH', 'Empty image path.');
		define('IMG_SAVE_NOT_EXISTS', 'Image does not exist.');
		define('IMG_SAVE_PATH_DISALLOWED', 'You are not allowed to access this file.');
		define('IMG_SAVE_UNKNOWN_MODE', 'Unexpected Image Operation Mode');
		define('IMG_SAVE_RESIZE_FAILED', 'Failed to resize the image.');
		define('IMG_SAVE_CROP_FAILED', 'Failed to crop the image.');
		define('IMG_SAVE_FAILED', 'Failed to save the image.');
		define('IMG_SAVE_BACKUP_FAILED', 'Unable to backup the original image.');
		define('IMG_SAVE_ROTATE_FAILED', 'Unable to rotate the image.');
		define('IMG_SAVE_FLIP_FAILED', 'Unable to flip the image.');
		define('IMG_SAVE_SESSION_IMG_OPEN_FAILED', 'Unable to open image from session.');
		define('IMG_SAVE_IMG_OPEN_FAILED', 'Unable to open image');
		
		//UNDO
		define('IMG_UNDO_NO_HISTORY_AVAIALBE', 'No history avaiable for undo.');
		define('IMG_UNDO_COPY_FAILED', 'Unable to restore the image.');
		define('IMG_UNDO_DEL_FAILED', 'Unable to delete the session image');
	
	//88888888888   Above for Image Editor   888888888888888888888
	
	//88888888888   Session   888888888888888888888
		define("SESSION_PERSONAL_DIR_NOT_FOUND", 'Unable to find the dedicated folder which should have been created under session folder');
		define("SESSION_COUNTER_FILE_CREATE_FAILED", 'Unable to open the session counter file.');
		define('SESSION_COUNTER_FILE_WRITE_FAILED', 'Unable to write the session counter file.');
	//88888888888   Session   888888888888888888888
	
	
?>