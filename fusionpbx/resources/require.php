<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/

//ensure that $_SERVER["DOCUMENT_ROOT"] is defined
	include "root.php";

//find and include the config.php file
	if (file_exists("/etc/fusionpbx/config.php")) {
		include "/etc/fusionpbx/config.php";
	}
	elseif (file_exists("/usr/local/etc/fusionpbx/config.php")) {
		include "/usr/local/etc/fusionpbx/config.php";
	}
	elseif (file_exists("resources/config.php")) {
		include "resources/config.php";
	}
	else {
		include "resources/config.php";
	}

//class auto loader
	class auto_loader {
		public function __construct() {
			spl_autoload_register(array($this, 'loader'));
		}
		private function loader($class_name) {
			//use glob to check "/resources/classes", "/{core,app}/*/resources/classes";
				$results = glob($_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . "{/*/*,}/resources/classes/".$class_name.".php", GLOB_BRACE);
			//include the class
				foreach ($results as &$class_file) {
					if (!class_exists($class_name)) {
						include $class_file;
					}
				}
		}
	}
	$autoload = new auto_loader();

//additional includes
	require_once "resources/php.php";
	require "resources/pdo.php";
	require_once "resources/functions.php";
	require_once "resources/switch.php";
?>