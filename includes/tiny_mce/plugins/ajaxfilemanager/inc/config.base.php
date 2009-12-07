<?php
if(!isset($_SERVER["DOCUMENT_ROOT"])) { $_SERVER["DOCUMENT_ROOT"]=substr($_SERVER['SCRIPT_FILENAME'] , 0 , -strlen($_SERVER['PHP_SELF'])+1 );}
require_once $_SERVER["DOCUMENT_ROOT"]."/includes/config.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/includes/checkauth.php";

if (!ifgroup("admin")) {
    header("Location: /index.php");
    return;
}

	/**
	 * sysem base config setting
	 * @author Logan Cai (cailongqun@yahoo.com.cn)
	 * @link www.phpletter.com
	 * @since 21/April/2007
	 *
	 */


error_reporting(E_ALL);
//error_reporting(E_ALL ^ E_NOTICE);


	//Directories Declarations

	define('DIR_AJAX_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR) ; // the path to ajax file manager
	define('DIR_AJAX_INC', DIR_AJAX_ROOT . "inc" . DIRECTORY_SEPARATOR);
	define('DIR_AJAX_CLASSES', DIR_AJAX_ROOT .  "classes" . DIRECTORY_SEPARATOR);
	define("DIR_AJAX_LANGS", DIR_AJAX_ROOT . "langs" . DIRECTORY_SEPARATOR);

	//Class Declarations
	define('CLASS_FILE', DIR_AJAX_INC .'class.file.php');
	define("CLASS_UPLOAD", DIR_AJAX_INC .  'class.upload.php');
	define('CLASS_MANAGER', DIR_AJAX_INC . 'class.manager.php');
	define('CLASS_IMAGE', DIR_AJAX_INC . "class.image.php");
	define('CLASS_HISTORY', DIR_AJAX_INC . "class.history.php");
	define('CLASS_SESSION_ACTION', DIR_AJAX_INC . "class.sessionaction.php");
	//SCRIPT FILES declarations
	define('SPT_FUNCTION_BASE', DIR_AJAX_INC . 'function.base.php');
	//Access Control Setting
	/**
	 * turn off => 0
	 * by session => 1
	 */
	define('CONFIG_ACCESS_CONTROL_MODE', 0);
	define('CONFIG_LOGIN_INDEX', 'site_user'); //must set this when you turn the access control on
	define("CONFIG_LOGIN_USERNAME", 'ajax');
	define('CONFIG_LOGIN_PASSWORD', '123456');
	define('CONFIG_LOGIN_PAGE', 'ajax_login.php'); //the url to the login page

	//SYSTEM MODE CONFIG
		/**
		 * turn it on when you have this system for demo purpose
		*  that means changes made to each image is not physically applied to it.
		*/
	define('CONFIG_SYS_DEMO_ENABLE', 0);

	//FILESYSTEM CONFIG
		/*
		* CONFIG_SYS_DEFAULT_PATH is the default folder where the files would be uploaded to
			and it must be a folder under the CONFIG_SYS_ROOT_PATH or the same folder
		*/

//--- Begin: customization -------------------------------------------------------
$server_name = $_SERVER["SERVER_NAME"];
$server_name = str_replace ("www.", "", $server_name);
$server_name = str_replace (".", "_", $server_name);

//check and see if folder exists //if not create the folder
$ajaxfilemanagerdir = $_SERVER["DOCUMENT_ROOT"].'\\files\\'.$server_name.'\\';
//echo "ajaxfilemanagerdir $ajaxfilemanagerdir";
if (is_dir($ajaxfilemanagerdir)) {
  //the directory exists do nothing
}
else {
  //the directory does not exist //make the directory
  mkdir($ajaxfilemanagerdir);
}
//--- End: customization -------------------------------------------------------

    if(!isset($_SERVER["DOCUMENT_ROOT"])) { $_SERVER["DOCUMENT_ROOT"]=substr($_SERVER['SCRIPT_FILENAME'] , 0 , -strlen($_SERVER['PHP_SELF'])+1 );}
	//echo $_SERVER["DOCUMENT_ROOT"]
	define('CONFIG_SYS_DEFAULT_PATH', '../../../../files/'.$server_name.'/');
	define('CONFIG_SYS_ROOT_PATH', '../../../../files/'.$server_name.'/');	//the root folder where the files would be uploaded to

	//define('CONFIG_SYS_DEFAULT_PATH', '../../../../uploaded/');
	//define('CONFIG_SYS_ROOT_PATH', '../../../../uploaded/');	//the root folder where the files would be uploaded to
	//define("CONFIG_SYS_DIR_SESSION_PATH", 'session/');

    define("CONFIG_SYS_DIR_SESSION_PATH", $_SERVER["DOCUMENT_ROOT"].'\\files\\sessions');

	define('CONFIG_SYS_INC_DIR_PATTERN', ''); //leave empty if you want to include all foldler
	define('CONFIG_SYS_EXC_DIR_PATTERN', ''); //leave empty if you want to include all folder
	define('CONFIG_SYS_INC_FILE_PATTERN', '');
	define('CONFIG_SYS_EXC_FILE_PATTERN', '');
	define('CONFIG_SYS_DELETE_RECURSIVE', 0); //delete all contents within a specific folder if set to be 1

	//UPLOAD OPTIONS CONFIG
	define('CONFIG_UPLOAD_MAXSIZE', 10 * 1024 * 1024); //by bytes
	//define('CONFIG_UPLOAD_MAXSIZE', 2048); //by bytes
	//define('CONFIG_UPLOAD_VALID_EXTS', 'txt');//
	define('CONFIG_UPLOAD_VALID_EXTS', 'gif,jpg,png,bmp,tif,zip,sit,rar,gz,tar,htm,html,mov,mpg,avi,asf,mpeg,wmv,aif,aiff,wav,mp3,swf,ppt,rtf,doc,pdf,xls,txt,xml,xsl,dtd');//
	//define('CONFIG_UPLOAD_VALID_EXTS', 'gif,jpg,png,txt'); //
	define('CONFIG_UPLOAD_INVALID_EXTS', '');
	//define('CONFIG_UPLOAD_OVERRIDE_ALLOWED', 0);

	//URL Declartions
		/**
		 * Normally, you don't need to bother with CONFIG_URL_PREVIEW_ROOT
		 * Howerver, some Web Hosts do not have standard php.ini setting
		 * which you will find the file manager can not locate your files correctly
		 * if you do have such issue, please change it to fit your system.
		 * this is tricky part, please pay patient to figure it out
		 * so how it works?
		 * 	  an example given here for reference only, remember each host would be different
		 * 		wrong file path found http://www.yourdomain.name/www/htdocs/uploaded/phpletter.gif
		 * 	  set define('CONFIG_URL_PREVIEW_ROOT', 'www/htdocs/'); should fix your issue.
		 *
		 *
		 */
	define('CONFIG_URL_PREVIEW_ROOT', '');
	define('CONFIG_URL_CREATE_FOLDER', 'ajax_create_folder.php');
	define('CONFIG_URL_DELETE', 'ajax_delete_file.php');
	define('CONFIG_URL_HOME', 'ajaxfilemanager.php');
	define("CONFIG_URL_UPLOAD", 'ajax_file_upload.php');
	define('CONFIG_URL_PREVIEW', 'ajax_preview.php');
	define('CONFIG_URL_SAVE_NAME', 'ajax_save_name.php');
	define('CONFIG_URL_IMAGE_EDITOR', 'ajax_image_editor.php');
	define('CONFIG_URL_IMAGE_SAVE', 'ajax_image_save.php');
	define('CONFIG_URL_IMAGE_RESET', 'ajax_editor_reset.php');
	define('CONFIG_URL_IMAGE_UNDO', 'ajax_image_undo.php');
	define('CONFIG_URL_CUT', 'ajax_file_cut.php');
	define('CONFIG_URL_COPY', 'ajax_file_copy.php');
	define('CONFIG_URL_PASTE', 'ajax_get_file_list.php');



	//theme related setting
			/*
			*	options avaialbe for CONFIG_THEME_MODE are:
					stand_alone
					tinymce
					fckeditor
			*/

	define('CONFIG_THEME_MODE', 'tinymce');  //set tinymce you want use it for tinymce editor or stand-alone
	define('CONFIG_THEME_NAME', 'default');  //change the theme to your custom theme rather than default


	//General Option Declarations
	//define('CONFIG_GENERAL_FRIENDLY_PATH', true);
	//LANGAUGAE DECLARATIONNS
	define('CONFIG_LANG_INDEX', 'language'); //the index in the session
	define('CONFIG_LANG_DEFAULT', 'en'); //change it to be your language file base name, such en
?>
