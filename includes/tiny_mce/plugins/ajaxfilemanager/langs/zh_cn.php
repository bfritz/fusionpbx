<?
	/**
	 * language pack
	 * @author Logan Cai (cailongqun@yahoo.com.cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	define('DATE_TIME_FORMAT', 'Y/m/d H:i:s');
	//Label
		//Top Action
		define('LBL_ACTION_REFRESH', '刷新');
		define("LBL_ACTION_DELETE", '删除');
		define('LBL_ACTION_CUT', '剪切');
		define('LBL_ACTION_COPY', '复制');
		define('LBL_ACTION_PASTE', '粘贴');
		//File Listing
	define('LBL_NAME', '文件名称');
	define('LBL_SIZE', '大小');
	define('LBL_MODIFIED', '更改于');
		//File Information
	define('LBL_FILE_INFO', '文件信息:');
	define('LBL_FILE_NAME', '名称:');	
	define('LBL_FILE_CREATED', '创建于:');
	define("LBL_FILE_MODIFIED", '更改于:');
	define("LBL_FILE_SIZE", '大小:');
	define('LBL_FILE_TYPE', '种类:');
	define("LBL_FILE_WRITABLE", '可写?');
	define("LBL_FILE_READABLE", '可读?');
		//Folder Information
	define('LBL_FOLDER_INFO', '目录信息');
	define("LBL_FOLDER_PATH", '路径:');
	define("LBL_FOLDER_CREATED", '创建于:');
	define("LBL_FOLDER_MODIFIED", '更改于:');
	define('LBL_FOLDER_SUDDIR', '子目录:');
	define("LBL_FOLDER_FIELS", '文件:');
	define("LBL_FOLDER_WRITABLE", '可写?');
	define("LBL_FOLDER_READABLE", '可读?');
		//Preview
	define("LBL_PREVIEW", '预览');
	//Buttons
	define('LBL_BTN_SELECT', '选择');
	define('LBL_BTN_CANCEL', '取消');
	define("LBL_BTN_UPLOAD", '上传');
	define('LBL_BTN_CREATE', '创建');
	define('LBL_BTN_CLOSE', '关闭');
	define("LBL_BTN_NEW_FOLDER", '新目录');
	define('LBL_BTN_EDIT_IMAGE', '修改');
	//Cut
	define('ERR_NOT_DOC_SELECTED_FOR_CUT', '请选择文件');
	//Copy
	define('ERR_NOT_DOC_SELECTED_FOR_COPY', '请选择文件');
	//Paste
	define('ERR_NOT_DOC_SELECTED_FOR_PASTE', '请选择文件');
	define('WARNING_CUT_PASTE', '确认要移动所选择文件到当前目录?');
	define('WARNING_COPY_PASTE', '确认要复制所选择文件到当前目录?');
	
	//ERROR MESSAGES
		//deletion
	define('ERR_NOT_FILE_SELECTED', '请选择文件.');
	define('ERR_NOT_DOC_SELECTED', '请选择需要删除的文件或者目录.');
	define('ERR_DELTED_FAILED', '无法删除所选择的文件或者目录.');
	define('ERR_FOLDER_PATH_NOT_ALLOWED', '无法访问此目录.');
		//class manager
	define("ERR_FOLDER_NOT_FOUND", '无法找到指定的目录.');
		//rename
	define('ERR_RENAME_FORMAT','文件名称只允许包含字母，数字，空格，连字号与下划线');
	define('ERR_RENAME_EXISTS', '相同名称的目录或者文件已存在');
	define('ERR_RENAME_FILE_NOT_EXISTS', '文件或者目录不存在.');
	define('ERR_RENAME_FAILED', '重命名失败，请重试.');
	define('ERR_RENAME_EMPTY', '请输入名称.');
	define("ERR_NO_CHANGES_MADE", '未有更新.');
	define('ERR_RENAME_FILE_TYPE_NOT_PERMITED', '无权限更改文件成此类扩展名.');
		//folder creation
	define('ERR_FOLDER_FORMAT', '目录名称只允许包含字母，数字，空格，连字号与下划线');
	define('ERR_FOLDER_EXISTS', '相同名称的目录已存在');
	define('ERR_FOLDER_CREATION_FAILED', '目录创建失败，请重试');
	define('ERR_FOLDER_NAME_EMPTY', '请输入目录名称.');
	
		//file upload
	define("ERR_FILE_NAME_FORMAT", '文件名称只允许包含字母，数字，空格，连字号与下划线');
	define('ERR_FILE_NOT_UPLOADED', '请选择所要上传的文件');
	define('ERR_FILE_TYPE_NOT_ALLOWED', '此类文件不允许上传.');
	define('ERR_FILE_MOVE_FAILED', '无法移动已上传的文件.');
	define('ERR_FILE_NOT_AVAILABLE', '文件不存在.');
	define('ERROR_FILE_TOO_BID', '文件太大. (最大允许: %s)');
	

	//Tips
	define('TIP_FOLDER_GO_DOWN', '单击进入此目录...');
	define("TIP_DOC_RENAME", '双击重命名...');
	define('TIP_FOLDER_GO_UP', '单击返回上级目录...');
	define("TIP_SELECT_ALL", '全选择');
	define("TIP_UNSELECT_ALL", '全取消');
	//WARNING
	define('WARNING_DELETE', '确认要删除所选择的文件?');
	define('WARNING_IMAGE_EDIT', '请选择要修改的图像');
	define('WARING_WINDOW_CLOSE', '确认要关闭当前窗口?');
	//Preview
	define('PREVIEW_NOT_PREVIEW', '无预览.');
	define('PREVIEW_OPEN_FAILED', '无法打开文件.');
	define('PREVIEW_IMAGE_LOAD_FAILED', '无法载入图像');

	//Login
	define('LOGIN_PAGE_TITLE', 'Ajax File Manager 登录窗口');
	define('LOGIN_FORM_TITLE', '登录窗口');
	define('LOGIN_USERNAME', '用户名:');
	define('LOGIN_PASSWORD', '密码:');
	define('LOGIN_FAILED', '无效用户名或者密码.');	
	
	
	//88888888888   Below for Image Editor   888888888888888888888
		//Warning 
		define('IMG_WARNING_NO_CHANGE_BEFORE_SAVE', "图像还未做任何更改");
		
		//General
		define('IMG_GEN_IMG_NOT_EXISTS', '图像文件不存在');
		define('IMG_WARNING_LOST_CHANAGES', '所有未保存的图像更改将丢失，确认继续?');
		define('IMG_WARNING_REST', '所有未保存的图像更改将丢失，确认重置?');
		define('IMG_WARNING_EMPTY_RESET', '图像还未做任何更改');
		define('IMG_WARING_WIN_CLOSE', '关闭当前窗口?');
		define('IMG_WARNING_UNDO', '恢复到上一次保存的图像?');
		define('IMG_WARING_FLIP_H', '确认要水平翻转此图像?');
		define('IMG_WARING_FLIP_V', '确认要垂直翻转此图像?');
		define('IMG_INFO', '图像信息');
		
		//Mode
			define('IMG_MODE_RESIZE', '大小:');
			define('IMG_MODE_CROP', '切图:');
			define('IMG_MODE_ROTATE', '旋转:');
			define('IMG_MODE_FLIP', '翻转:');		
		//Button
		
			define('IMG_BTN_ROTATE_LEFT', '90&deg;反');
			define('IMG_BTN_ROTATE_RIGHT', '90&deg;正');
			define('IMG_BTN_FLIP_H', '水平翻转');
			define('IMG_BTN_FLIP_V', '垂直翻转');
			define('IMG_BTN_RESET', '重置');
			define('IMG_BTN_UNDO', '上一步');
			define('IMG_BTN_SAVE', '保存');
			define('IMG_BTN_CLOSE', '关闭');
		//Checkbox
			define('IMG_CHECKBOX_CONSTRAINT', '约束比例?');
		//Label
			define('IMG_LBL_WIDTH', '宽:');
			define('IMG_LBL_HEIGHT', '高:');
			define('IMG_LBL_X', 'X:');
			define('IMG_LBL_Y', 'Y:');
			define('IMG_LBL_RATIO', '比例:');
			define('IMG_LBL_ANGLE', '角度:');
		//Editor

			
		//Save
		define('IMG_SAVE_EMPTY_PATH', '图像地址不能为空');
		define('IMG_SAVE_NOT_EXISTS', '图像不存在');
		define('IMG_SAVE_PATH_DISALLOWED', '未权限访问此图像.');
		define('IMG_SAVE_UNKNOWN_MODE', '无法预知的操作模式');
		define('IMG_SAVE_RESIZE_FAILED', '更改图像大小失败.');
		define('IMG_SAVE_CROP_FAILED', '切图失败.');
		define('IMG_SAVE_FAILED', '保存图像失败.');
		define('IMG_SAVE_BACKUP_FAILED', '无法备份原始图像.');
		define('IMG_SAVE_ROTATE_FAILED', '旋转图像失败');
		define('IMG_SAVE_FLIP_FAILED', '翻转图像失败.');
		define('IMG_SAVE_SESSION_IMG_OPEN_FAILED', '无法从进程中打开图像.');
		define('IMG_SAVE_IMG_OPEN_FAILED', '无法打开图像');
		
		//UNDO
		define('IMG_UNDO_NO_HISTORY_AVAIALBE', '尚无任何图像操作记录.');
		define('IMG_UNDO_COPY_FAILED', '无法恢复图像.');
		define('IMG_UNDO_DEL_FAILED', '无法删除进程中的图像');
	
	//88888888888   Above for Image Editor   888888888888888888888
	
	//88888888888   Session   888888888888888888888
		define("SESSION_PERSONAL_DIR_NOT_FOUND", '无法找到进程目录,此目录应存在于session目录之下');
		define("SESSION_COUNTER_FILE_CREATE_FAILED", '无法打开进程的计数文件');
		define('SESSION_COUNTER_FILE_WRITE_FAILED', '无法写入进程的计数文件.');
	//88888888888   Session   888888888888888888888
	
	
?>