<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

// module statuses
// do not confuse with _MOD_ constants!

define('AT_MODULE_STATUS_DISABLED',    1);
define('AT_MODULE_STATUS_ENABLED',     2);
define('AT_MODULE_STATUS_UNINSTALLED', 4); // not in the db

define('AT_MODULE_TYPE_CORE',     1);
define('AT_MODULE_TYPE_STANDARD', 2);
define('AT_MODULE_TYPE_EXTRA',    4);

define('AT_MODULE_DIR_CORE',     '_core');
define('AT_MODULE_DIR_STANDARD', '_standard');

define('AT_MODULE_PATH', realpath(AT_INCLUDE_PATH.'../mods') . DIRECTORY_SEPARATOR);

/**
* ModuleFactory
* 
* @access	public
* @author	Joel Kronenberg
* @package	Module
*/
class ModuleFactory {
	// private
	var $_modules = NULL; // array of module refs

	function ModuleFactory($auto_load = FALSE) {
		global $db;

		$this->_modules = array();

		if ($auto_load == TRUE) {
			// initialise enabled modules
			$sql	= "SELECT dir_name, privilege, admin_privilege, status FROM ". TABLE_PREFIX . "modules WHERE status=".AT_MODULE_STATUS_ENABLED;
			$result = mysql_query($sql, $db);
			while($row = mysql_fetch_assoc($result)) {
				$module =& new ModuleProxy($row);
				$this->_modules[$row['dir_name']] =& $module;
				$module->load();
			}
		}
	}

	// public
	// state := enabled | disabled | uninstalled
	// type  := core | standard | extra
	function & getModules($status, $type =0) {
		global $db;

		$modules     = array();
		$all_modules = array();

		if ($type == 0) {
			$type = AT_MODULE_TYPE_CORE | AT_MODULE_TYPE_STANDARD | AT_MODULE_TYPE_EXTRA;
		}

		$sql	= "SELECT dir_name, privilege, admin_privilege, status FROM ". TABLE_PREFIX . "modules";
		$result = mysql_query($sql, $db);
		while($row = mysql_fetch_assoc($result)) {
			if (!isset($this->_modules[$row['dir_name']])) {
				$module =& new ModuleProxy($row);
			} else {
				$module =& $this->_modules[$row['dir_name']];
			}
			$all_modules[$row['dir_name']] =& $module;
		}

		// small performance addition:
		if (query_bit($status, AT_MODULE_STATUS_UNINSTALLED)) {
			$dir = opendir(AT_MODULE_PATH);
			while (false !== ($dir_name = readdir($dir))) {
				if (($dir_name == '.') 
					|| ($dir_name == '..') 
					|| ($dir_name == '.svn') 
					|| ($dir_name == AT_MODULE_DIR_CORE) 
					|| ($dir_name == AT_MODULE_DIR_STANDARD)) {
					continue;
				}

				if (is_dir(AT_MODULE_PATH . $dir_name) && !isset($all_modules[$dir_name])) {
					$module =& new ModuleProxy($dir_name);
					$all_modules[$dir_name] =& $module;
				}
			}
			closedir($dir);
		}

		$keys = array_keys($all_modules);
		foreach ($keys as $dir_name) {
			$module =& $all_modules[$dir_name];
			if ($module->checkStatus($status) && $module->checkType($type)) {
				$modules[$dir_name] =& $module;
			}
		}

		return $modules;
	}

	// public.
	function & getModule($module_dir) {
		if (!isset($this->_modules[$module_dir])) {
			$module =& new ModuleProxy($module_dir);
			$this->_modules[$module_dir] =& $module;
		}
		return $this->_modules[$module_dir];
	}
}

/**
* ModuleProxy
* 
* @access	public
* @author	Joel Kronenberg
* @package	Module
*/
class ModuleProxy {
	// private
	var $_moduleObj;
	var $_directoryName;
	var $_status; // core|enabled|disabled
	var $_privilege; // priv bit(s) | 0 (in dec form)
	var $_admin_privilege; // priv bit(s) | 0 (in dec form)
	var $_pages;
	var $_type; // core, standard, extra

	function ModuleProxy($row) {
		if (is_array($row)) {
			$this->_directoryName   = $row['dir_name'];
			$this->_status          = $row['status'];
			$this->_privilege       = $row['privilege'];
			$this->_admin_privilege = $row['admin_privilege'];

			if (substr($row['dir_name'], 0, strlen(AT_MODULE_DIR_CORE)) == AT_MODULE_DIR_CORE) {
				$this->_type = AT_MODULE_TYPE_CORE;
			} else if (substr($row['dir_name'], 0, strlen(AT_MODULE_DIR_STANDARD)) == AT_MODULE_DIR_STANDARD) {
				$this->_type = AT_MODULE_TYPE_STANDARD;
			} else {
				$this->_type = AT_MODULE_TYPE_EXTRA;
			}
		} else {
			$this->_directoryName   = $row;
			$this->_status          = AT_MODULE_STATUS_UNINSTALLED;
			$this->_privilege       = 0;
			$this->_admin_privilege = 0;
			$this->_type            = AT_MODULE_TYPE_EXTRA; // standard/core are installed by default
		}
	}

	// statuses
	function checkStatus($status) { return query_bit($status, $this->_status); }
	function isUninstalled()  { return ($this->_status == AT_MODULE_STATUS_UNINSTALLED)  ? true : false; }
	function isEnabled()      { return ($this->_status == AT_MODULE_STATUS_ENABLED)      ? true : false; }
	function isDisabled()     { return ($this->_status == AT_MODULE_STATUS_DISABLED)     ? true : false; }

	// types
	function checkType($type) { return query_bit($type, $this->_type); }
	function isCore()     { return ($this->_type == AT_MODULE_TYPE_CORE)     ? true : false; }
	function isStandard() { return ($this->_type == AT_MODULE_TYPE_STANDARD) ? true : false; }
	function isExtra()    { return ($this->_type == AT_MODULE_TYPE_EXTRA)    ? true : false; }

	// privileges
	function getPrivilege()      { return $this->_privilege;       }
	function getAdminPrivilege() { return $this->_admin_privilege; }

	// private
	function initModuleObj() {
		if (!isset($this->_moduleObj)) {
			$this->_moduleObj =& new Module($this->_directoryName);
		}
	}

	function getProperties($properties_list) {
		// this requires a real module object
		$this->initModuleObj();
		return $this->_moduleObj->getProperties($properties_list);
	}

	function getProperty($property) {
		$this->initModuleObj();
		return $this->_moduleObj->getProperty($property);
	}

	function getVersion() {
		$this->initModuleObj();
		return $this->_moduleObj->getVersion();
	}

	function getName($lang) {
		$this->initModuleObj();
		return $this->_moduleObj->getName($lang);
	}

	function getDescription($lang) {
		$this->initModuleObj();
		return $this->_moduleObj->getDescription($lang);
	}

	function load() {
		if (is_file(AT_MODULE_PATH . $this->_directoryName.'/module.php')) {
			global $_modules, $_pages, $_stacks;

			require(AT_MODULE_PATH . $this->_directoryName.'/module.php');
			if (isset($_module_pages)) {
				$this->_pages =& $_module_pages;

				$_pages = array_merge_recursive($_pages, $this->_pages);
			}

			//side menu items
			if (isset($_module_stacks)) {
				$this->_stacks =& $_module_stacks;
				$_stacks = array_merge($_stacks, $this->_stacks);
			}

			//student tools
			if (isset($_student_tools)) {
				$this->_student_tools =& $_student_tools;
				$_modules = array_merge_recursive($_modules, $this->_student_tools);
			}
		}					
	}

	function getChildPage($page) {
		if (!is_array($this->_pages)) {
			return;
		}
		foreach ($this->_pages as $tmp_page => $item) {
			if ($item['parent'] == $page) {
				return $tmp_page;
			}
		}
	}

	function isBackupable() {
		return is_file(AT_MODULE_PATH . $this->_directoryName.'/module_backup.php');
	}

	function backup($course_id, &$zipfile) {
		if (!isset($this->_moduleObj)) {
			$this->_moduleObj =& new Module($this->_directoryName);
		}
		$this->_moduleObj->backup($course_id, $zipfile);
	}

	function restore($course_id, $version, $import_dir) {
		if (!isset($this->_moduleObj)) {
			$this->_moduleObj =& new Module($this->_directoryName);
		}
		$this->_moduleObj->restore($course_id, $version, $import_dir);
	}

	function delete($course_id) {
		if (is_file(AT_MODULE_PATH . $this->_directoryName.'/module_delete.php')) {
			require(AT_MODULE_PATH . $this->_directoryName.'/module_delete.php');
			if (function_exists($this->_directoryName.'_delete')) {
				$fnctn = $this->_directoryName.'_delete';
				$fnctn($course_id);
			}
		}
	}

	function enable() {
		if (!isset($this->_moduleObj)) { 
			$this->_moduleObj =& new Module($this->_directoryName);
		}
		$this->_moduleObj->enable();

		$this->_status = AT_MOD_ENABLED;
	}

	function disable() {
		if (!isset($this->_moduleObj)) {
			$this->_moduleObj =& new Module($this->_directoryName);
		}
		$this->_moduleObj->disable();

		$this->_status = AT_MOD_DISABLED;
	}

	function install() {
		if (!isset($this->_moduleObj)) { 
			$this->_moduleObj =& new Module($this->_directoryName);
		}
		$this->_moduleObj->install();
	}

	function getStudentTools() {
		if (!isset($this->_student_tools)) {
			return array();
		} 

		return $this->_student_tools;
	}
}

// ----------------- in a diff file. only required when .. required.
require_once(AT_INCLUDE_PATH . 'classes/CSVExport.class.php');
require_once(AT_INCLUDE_PATH . 'classes/CSVImport.class.php');

/**
* Module
* 
* @access	protected
* @author	Joel Kronenberg
* @package	Module
*/
class Module {
	// all private
	var $_directoryName;
	var $_properties; // array from xml

	function Module($dir_name) {
		require_once(dirname(__FILE__) . '/ModuleParser.class.php');
		$moduleParser   =& new ModuleParser();
		$this->_directoryName = $dir_name;
		$moduleParser->parse(@file_get_contents(AT_MODULE_PATH . $dir_name.'/module.xml'));
		if ($moduleParser->rows[0]) {
			$this->_properties = $moduleParser->rows[0];
		} else {
			$this->_properties = array();
		}
	}

	function getVersion() {
		return $this->_properties['version'];
	}

	function getName($lang = 'en') {
		// this may have to connect to the DB to get the name.
		// such that, it returns _AT($this->_directory_name) instead.
		if (!$this->_properties) {
			return;
		}

		return (isset($this->_properties['name'][$lang]) ? $this->_properties['name'][$lang] : current($this->_properties['name']));
	}

	function getDescription($lang = 'en') {
		// this may have to connect to the DB to get the name.
		// such that, it returns _AT($this->_directory_name) instead.
		if (!$this->_properties) {
			return;
		}

		return (isset($this->_properties['description'][$lang]) ? $this->_properties['description'][$lang] : current($this->_properties['description']));
	}

	function getProperties($properties_list) {
		if (!$this->_properties) {
			return;
		}

		$properties_list = array_flip($properties_list);
		foreach ($properties_list as $property => $garbage) {
			$properties_list[$property] = $this->_properties[$property];
		}
		return $properties_list;
	}

	function getProperty($property) {
		if (!$this->_properties) {
			return;
		}

		return $this->_properties[$property];
	}

	function isBackupable() {
		return is_file(AT_MODULE_PATH . $this->_directoryName . '/module_backup.php');
	}

	function backup($course_id, &$zipfile) {
		static $CSVExport;

		if (!isset($CSVExport)) {
			$CSVExport = new CSVExport();
		}
		$now = time();

		if ($this->isBackupable()) {
			require(AT_MODULE_PATH . $this->_directoryName . '/module_backup.php');
			if (isset($sql)) {
				foreach ($sql as $file_name => $table_sql) {
					$content = $CSVExport->export($table_sql, $course_id);
					$zipfile->add_file($content, $file_name . '.csv', $now);
				}
			}

			if (isset($dirs)) {
				foreach ($dirs as $dir => $path) {
					$path = str_replace('?', $course_id, $path);

					$zipfile->add_dir($path , $dir);
				}
			}
		}
	}
	

	function restore($course_id, $version, $import_dir) {
		static $CSVImport;
		if (!file_exists(AT_MODULE_PATH . $this->_directoryName.'/module_backup.php')) {
			return;
		}

		if (!isset($CSVImport)) {
			$CSVImport = new CSVImport();
		}

		require(AT_MODULE_PATH . $this->_directoryName.'/module_backup.php');
		if (isset($sql)) {
			foreach ($sql as $table_name => $table_sql) {
				$CSVImport->import($table_name, $import_dir, $course_id);
			}
		}
		if (isset($dirs)) {
			foreach ($dirs as $src => $dest) {
				$dest = str_replace('?', $course_id, $dest);
				copys($import_dir.$src, $dest);
			}
		}
	}

	function delete($course_id) {

	}

	function enable() {
		global $db;

		$sql = 'UPDATE '. TABLE_PREFIX . 'modules SET status='.AT_MODULE_STATUS_ENABLED.' WHERE dir_name="'.$this->_directoryName.'"';
		$result = mysql_query($sql, $db);
	}

	function disable() {
		global $db;

		$sql = 'UPDATE '. TABLE_PREFIX . 'modules SET status='.AT_MODULE_STATUS_DISABLED.' WHERE dir_name="'.$this->_directoryName.'"';
		$result = mysql_query($sql, $db);
	}

	function install() {
		global $db;

		// get the module details from the XML file.

		// if use_privilege then set $priv to the next available privilege on the system
		$priv = AT_PRIV_ADMIN; //or function: get next avail priv
		$admin_priv = AT_PRIV_ADMIN;;
		// 

		$sql = 'INSERT INTO '. TABLE_PREFIX . 'modules VALUES ("'.$this->_directoryName.'", '.AT_MODULE_STATUS_DISABLED.', '.$priv.', '.$admin_priv.')';
		$result = mysql_query($sql, $db);
	}

}

?>