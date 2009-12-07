<?
	/**
	 * function avaialble to the file manager
	 * @author Logan Cai (cailongqun@yahoo.com.cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "config.php");
if (!function_exists("stripos")) 
{
  function stripos($str,$needle,$offset=0)
  {
      return strpos(strtolower($str),strtolower($needle),$offset);
  }
}

/**
 * print out an array
 *
 * @param array $array
 */
function displayArray($array, $comments="")
{
	echo "<pre>";
	echo $comments;
	print_r($array);
	echo $comments;
	echo "</pre>";
}
	/**
	 * check if a file extension is permitted
	 *
	 * @param string $filePath
	 * @param array $validExts
	 * @param array $invalidExts
	 * @return boolean
	 */
	function isValidExt($filePath, $validExts, $invalidExts=array())
	{
		$tem = array();

		if(sizeof($validExts))
		{
			foreach($validExts as $k=>$v)
			{
				$tem[$k] = trim($v);
			}
		}
		$validExts = $tem;

		if(sizeof($validExts) && sizeof($invalidExts))
		{
			foreach($validExts as  $k=>$ext)
			{
				if(array_search($ext, $invalidExts) !== false)
				{
					unset($validExts[$k]);
				}
			}
		}
		if(sizeof($validExts))
		{
			if(array_search(getFileExt($filePath), $validExts) !== false)
			{
				return true;
			}
		}elseif(array_search(getFileExt($filePath), $invalidExts) === false)
		{
			return true;
		}else 
		{
			return false;
		}
	}





/** transform relative path to absolute path
 */
function relToAbs($value) 
{
	return backslashToSlash(preg_replace("/(\\\\)/","\\", realpath($value)));

}

	function getRelativeFileUrl($value, $relativeTo)
	{
		$output = '';
		$wwwroot = removeTrailingSlash(backslashToSlash(getRootPath()));
		$urlprefix = "";
		$urlsuffix = "";
		$value = backslashToSlash(realpath($value));
		$pos = strpos($value, $wwwroot);
		if ($pos !== false && $pos == 0)
		{
			$output  = $urlprefix . substr($value, strlen($wwwroot)) . $urlsuffix;
		}
	}
/**
 * replace slash with backslash
 *
 * @param string $value
 * @return string
 */
function slashToBackslash($value) {
	return str_replace("/", DIRECTORY_SEPARATOR, $value);
}

/**
 * replace backslash with slash
 *
 * @param string $value
 * @return string
 */
function backslashToSlash($value) {
	return str_replace(DIRECTORY_SEPARATOR, "/", $value);
}

/**
 * removes the trailing slash
 *
 * @param string $value
 * @return string
 */
function removeTrailingSlash($value) {
	if(preg_match('@^.+/$@i', $value))
	{
		$value = substr($value, 0, strlen($value)-1);
	}
	return $value;
}

/**
 * append a trailing slash
 *
 * @param string $value 
 * @return string
 */
function addTrailingSlash($value) {
	if(preg_match('@^.*[^/]{1}$@i', $value))
	{
		$value .= '/';
	}
	return $value;
}

/**
 * transform a file path to user friendly
 *
 * @param string $value
 * @return string
 */
function transformFilePath($value) {
	$rootPath = backslashToSlash(addTrailingSlash(realpath(CONFIG_SYS_ROOT_PATH)));
	$value = backslashToSlash(addTrailingSlash(realpath($value)));
	if(!empty($rootPath) && ($i = strpos($value, $rootPath)) !== false)
	{
		$value = ($i == 0?substr($value, strlen($rootPath)):"/");		
	}
	$value = prependSlash($value);
	return $value;
}
/**
 * prepend slash 
 *
 * @param string $value
 * @return string
 */
function prependSlash($value)
{
		if (($value && $value[0] != '/') || !$value )
		{
			$value = "/" . $value;
		}			
		return $value;	
}

	function writeInfo($data, $die = false)
	{
		$fp = @fopen(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'data.php', 'w+');
		@fwrite($fp, $data);
		@fwrite($fp, "\n\n" . date('d/M/Y H:i:s') );
		@fclose($fp);
		if($die)
		{
			die();
		}
		
	}

/**
 * no cachable header
 */
function addNoCacheHeaders() {
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
}
	/**
	 * add extra query stiring to a url
	 * @param string $baseUrl
	 * @param string $extra the query string added to the base url
	 */
	function appendQueryString($baseUrl, $extra)
	{
		$output = $baseUrl;
		if(strpos($baseUrl, "?") !== false)
		{
			$output .= "&" . $extra;
		}else
		{
			$output .= "?" . $extra;
		}
		return $output;
	}
	/**
	 * get parent path from specific path
	 *
	 * @param string $value
	 * @return string
	 */
	function getParentPath($value)
	{
		$value = removeTrailingSlash($value);
		if(false !== ($index = strrpos($value, "/")) )
		{
			return substr($value, 0, $index);
		}

	}

	/**
	 * check if the file/folder is sit under the root
	 *
	 * @param string $value
	 * @return  boolean
	 */
	function isUnderRoot($value)
	{
		$roorPath = strtolower(backslashToSlash(addTrailingSlash(CONFIG_SYS_ROOT_PATH)));
		if(file_exists($value) && strpos(strtolower(backslashToSlash(addTrailingSlash($value))), $roorPath) === 0 )
		{
			return true;
		}
		return false;
	}
	/**
	 * check if a file under the session folder
	 *
	 * @param string $value
	 * @return boolean
	 */
	function isUnderSession($value)
	{
		global $session;
		$sessionPath = strtolower(backslashToSlash(addTrailingSlash($session->getSessionDir())));
		if(file_exists($value) && strpos(strtolower(backslashToSlash(addTrailingSlash($value))), $sessionPath) === 0 )
		{
			return true;
		}
		return false;		
	}
	
	
	/**
	 * get thumbnail width and height
	 *
	 * @param integer $originaleImageWidth
	 * @param integer $originalImageHeight
	 * @param integer $thumbnailWidth
	 * @param integer $thumbnailHeight
	 * @return array()
	 */
	function getThumbWidthHeight( $originaleImageWidth, $originalImageHeight, $thumbnailWidth, $thumbnailHeight)
	{
		$outputs = array( "width"=>0, "height"=>0);
		$thumbnailWidth	= intval($thumbnailWidth);
		$thumbnailHeight = intval($thumbnailHeight);
		if(!empty($originaleImageWidth) && !empty($originalImageHeight))
		{
			//start to get the thumbnail width & height
        	if(($thumbnailWidth < 1 && $thumbnailHeight < 1) || ($thumbnailWidth > $originaleImageWidth && $thumbnailHeight > $originalImageHeight ))
        	{
        		$thumbnailWidth =$originaleImageWidth;
        		$thumbnailHeight = $originalImageHeight;
        	}elseif($thumbnailWidth < 1)
        	{
        		$thumbnailWidth = floor($thumbnailHeight / $originalImageHeight * $originaleImageWidth);

        	}elseif($thumbnailHeight < 1)
        	{
        		$thumbnailHeight = floor($thumbnailWidth / $originaleImageWidth * $originalImageHeight);
        	}else
        	{
        		$scale = min($thumbnailWidth/$originaleImageWidth, $thumbnailHeight/$originalImageHeight);
				$thumbnailWidth = floor($scale*$originaleImageWidth);
				$thumbnailHeight = floor($scale*$originalImageHeight);
        	}
			$outputs['width'] = $thumbnailWidth;
			$outputs['height'] = $thumbnailHeight;
		}
		return $outputs;

	}
/**
 * turn to absolute path from relative path
 *
 * @param string $value
 * @return string
 */
function getAbsPath($value) {
	if (substr($value, 0, 1) == "/")
		return slashToBackslash(DIR_AJAX_ROOT . $value);

	return slashToBackslash(dirname(__FILE__) . "/" . $value);
}

	/**
	 * get file/folder base name
	 *
	 * @param string $value
	 * @return string
	 */
	function getBaseName($value)
	{
		$value = removeTrailingSlash($value);

		if(false !== ($index = strrpos($value, "/")) )
		{
			return substr($value, $index + 1);
		}else
		{
			return $value;
		}
	}
	/**
	 * get file url
	 *
	 * @param string $value
	 * @return string
	 */
	function getFileUrl($value)
	{
		$output = '';
		$wwwroot = removeTrailingSlash(backslashToSlash(getRootPath()));
		$urlprefix = "";
		$urlsuffix = "";
		$value = backslashToSlash(realpath($value));
		$pos = stripos($value, $wwwroot);
		if ($pos !== false && $pos == 0)
		{
			$output  = $urlprefix . substr($value, strlen($wwwroot)) . $urlsuffix;
		}else 
		{
			$output = $value;
		}
		return "http://" .  addTrailingSlash($_SERVER['HTTP_HOST']) . removeBeginingSlash($output);
	}
	
/**
 * 
 *	transfer file size number to human friendly string
 * @param integer $size.
 * @return String
 */
function transformFileSize($size) {

	if ($size > 1048576)
	{
		return round($size / 1048576, 1) . " MB";
	}elseif ($size > 1024)
	{
		return round($size / 1024, 1) . " KB";
	}elseif($size == '')
	{
		return $size;
	}else
	{
		return $size . " b";
	}	
}
	
	/**
	 * remove beginging slash
	 *
	 * @param string $value
	 * @return string
	 */
	function removeBeginingSlash($value)
	{
		if(strpos($value, "/") === 0)
		{
			$value = substr($value, 1);
		}
		return $value;
	}
	
/**
 * get site root path
 *
 * @return String.
 */
function getRootPath() {
		$output = "";
		if (defined('CONFIG_URL_PREVIEW_ROOT') && CONFIG_URL_PREVIEW_ROOT)
		{
			return slashToBackslash(CONFIG_URL_PREVIEW_ROOT);
		}
		if(isset($_SERVER['DOCUMENT_ROOT']) && ($output = relToAbs($_SERVER['DOCUMENT_ROOT'])) != '' )
		{
			return $output;
		}elseif(isset($_SERVER["SCRIPT_NAME"]) && isset($_SERVER["SCRIPT_FILENAME"]) && ($output = str_replace(backslashToSlash($_SERVER["SCRIPT_NAME"]), "", backslashToSlash($_SERVER["SCRIPT_FILENAME"]))) && is_dir($output))
		{
			return slashToBackslash($output);
		}elseif(isset($_SERVER["SCRIPT_NAME"]) && isset($_SERVER["PATH_TRANSLATED"]) && ($output = str_replace(backslashToSlash($_SERVER["SCRIPT_NAME"]), "", str_replace("//", "/", backslashToSlash($_SERVER["PATH_TRANSLATED"])))) && is_dir($output))
		{
			return $output;
		}else 
		{
			return '';
		}	

	return null;
}

	
	/**
	 * add beginging slash
	 *
	 * @param string $value
	 * @return string
	 */	
	function addBeginingSlash($value)
	{
		if(strpos($value, "/") !== 0 && !empty($value))
		{
			$value .= "/" . $value;
		}
		return $value;		
	}


	

	
	/**
	 * get a file extension
	 *
	 * @param string $fileName the path to a file or just the file name
	 */	
	function getFileExt($filePath)
	{
		return @substr(@strrchr($filePath, "."), 1);
	}


?>