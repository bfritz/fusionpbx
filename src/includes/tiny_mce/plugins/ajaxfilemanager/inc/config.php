<?
	/**
	 * sysem  config setting
	 * @author Logan Cai (cailongqun@yahoo.com.cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
	
	//FILESYSTEM CONFIG	

	session_start();	
	header('Content-Type: text/html; charset=utf-8');
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "config.base.php");
	require_once(DIR_AJAX_LANGS . CONFIG_LANG_DEFAULT . ".php");
	require_once(DIR_AJAX_INC . "function.base.php");	
	require_once(dirname(__FILE__) .  DIRECTORY_SEPARATOR . "class.session.php");
	$session = new Session();
	
	if(CONFIG_ACCESS_CONTROL_MODE == 1)
	{//access control enabled
		if(empty($_SESSION[CONFIG_LOGIN_INDEX]) && strtolower(basename($_SERVER['PHP_SELF']) != strtolower(basename(CONFIG_LOGIN_PAGE))))
		{//
			header('Location: ' . CONFIG_LOGIN_PAGE);
			exit;
		}
	}
	addNoCacheHeaders();


?>