<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_MODULE_STATUS_DISABLED',    1);
define('AT_MODULE_STATUS_ENABLED',     2);
define('AT_MODULE_STATUS_MISSING',     4);
define('AT_MODULE_STATUS_UNINSTALLED', 8); // not in the db

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

		/* snippit to use when extending Module classes:
		$sql	= "SELECT dir_name, privilege, admin_privilege, status FROM ". TABLE_PREFIX . "modules WHERE status=".AT_MODULE_STATUS_ENABLED;
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);
		require(AT_MODULE_PATH . $row['dir_name'].'/module.php');
		$module =& new PropertiesModule($row);
		***/

		$this->_modules = array();

		if ($auto_load == TRUE) {
			// initialise enabled modules
			$sql	= "SELECT dir_name, privilege, admin_privilege, status FROM ". TABLE_PREFIX . "modules WHERE status=".AT_MODULE_STATUS_ENABLED;
			$result = mysql_query($sql, $db);
			while($row = mysql_fetch_assoc($result)) {
				$module =& new Module($row);
				$this->_modules[$row['dir_name']] =& $module;
				$module->load();
			}
		}
	}

	// public
	// status := enabled | disabled | uninstalled | missing
	// type  := core | standard | extra
	// sort  := true | false (by name only)
	// the results of this method are not cached. call sparingly.
	function & getModules($status, $type = 0, $sort = FALSE) {
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
				$module =& new Module($row);
			} else {
				$module =& $this->_modules[$row['dir_name']];
			}
			$all_modules[$row['dir_name']] =& $module;
		}

		// small performance addition:
		if ($status & AT_MODULE_STATUS_UNINSTALLED) {
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
					$module =& new Module($dir_name);
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

		if ($sort) {
			uasort($modules, array($this, 'compare'));
		}
		return $modules;
	}

	// public.
	function & getModule($module_dir) {
		if (!isset($this->_modules[$module_dir])) {
			global $db;
			$sql	= "SELECT dir_name, privilege, admin_privilege, status FROM ". TABLE_PREFIX . "modules WHERE dir_name='$module_dir'";
			$result = mysql_query($sql, $db);
			if ($row = mysql_fetch_assoc($result)) {
				$module =& new Module($row);
			} else {
				$module =& new Module($module_dir);
			}
			$this->_modules[$module_dir] =& $module;
		}
		return $this->_modules[$module_dir];
	}

	// private
	// used for sorting modules
	function compare($a, $b) {
		return strnatcmp($a->getName(), $b->getName());
	}
}

/**
* Module
* 
* @access	public
* @author	Joel Kronenberg
* @package	Module
*/
class Module {
	// private
	var $_moduleObj;
	var $_directoryName;
	var $_status; // core|enabled|disabled
	var $_privilege; // priv bit(s) | 0 (in dec form)
	var $_admin_privilege; // priv bit(s) | 0 (in dec form)
	var $_display_defaults; // bit(s)
	var $_pages;
	var $_type; // core, standard, extra
	var $_properties; // array from xml

	// constructor
	function Module($row) {
		if (is_array($row)) {
			$this->_directoryName   = $row['dir_name'];
			$this->_status          = $row['status'];
			$this->_privilege       = $row['privilege'];
			$this->_admin_privilege = $row['admin_privilege'];
			$this->_display_defaults= $row['display_defaults'];

			if (strpos($row['dir_name'], AT_MODULE_DIR_CORE) === 0) {
				$this->_type = AT_MODULE_TYPE_CORE;
			} else if (strpos($row['dir_name'], AT_MODULE_DIR_STANDARD) === 0) {
				$this->_type = AT_MODULE_TYPE_STANDARD;
			} else {
				$this->_type = AT_MODULE_TYPE_EXTRA;
			}
		} else {
			$this->_directoryName   = $row;
			$this->_status          = AT_MODULE_STATUS_UNINSTALLED;
			$this->_privilege       = 0;
			$this->_admin_privilege = 0;
			$this->_display_defaults= 0;
			$this->_type            = AT_MODULE_TYPE_EXTRA; // standard/core are installed by default
		}
	}

	// statuses
	function checkStatus($status) { return (bool) ($status & $this->_status); }
	function isUninstalled()  { return ($this->_status == AT_MODULE_STATUS_UNINSTALLED) ? true : false; }
	function isEnabled()      { return ($this->_status == AT_MODULE_STATUS_ENABLED)     ? true : false; }
	function isDisabled()     { return ($this->_status == AT_MODULE_STATUS_DISABLED)    ? true : false; }
	function isMissing()      { return ($this->_status == AT_MODULE_STATUS_MISSING)     ? true : false; }

	// types
	function checkType($type) { return (bool) ($type & $this->_type); }
	function isCore()     { return ($this->_type == AT_MODULE_TYPE_CORE)     ? true : false; }
	function isStandard() { return ($this->_type == AT_MODULE_TYPE_STANDARD) ? true : false; }
	function isExtra()    { return ($this->_type == AT_MODULE_TYPE_EXTRA)    ? true : false; }

	// privileges
	function getPrivilege()      { return $this->_privilege;       }
	function getAdminPrivilege() { return $this->_admin_privilege; }

	function load() {
		if (is_file(AT_MODULE_PATH . $this->_directoryName.'/module.php')) {
			global $_modules, $_pages, $_stacks;

			require(AT_MODULE_PATH . $this->_directoryName.'/module.php');

			if (isset($this->_pages)) {
				$_pages = array_merge_recursive((array)$_pages, $this->_pages);
			}

			//side menu items
			if (isset($this->_stacks)) {
				$count = 0;
				$_stacks = array_merge((array)$_stacks, $this->_stacks);
			}

			//student tools
			if (isset($_student_tool)) {
				$this->_student_tool =& $_student_tool;
				$_modules[] = $this->_student_tool;
			}

			//group tools
			if (isset($_group_tool)) {
				$this->_group_tool =& $_group_tool;
			}
		}					
	}

	// private
	function _initModuleProperties() {
		if (!isset($this->_properties)) {
			require_once(dirname(__FILE__) . '/ModuleParser.class.php');
			$moduleParser   =& new ModuleParser();
			$moduleParser->parse(@file_get_contents(AT_MODULE_PATH . $this->_directoryName.'/module.xml'));
			if ($moduleParser->rows[0]) {
				$this->_properties = $moduleParser->rows[0];
			} else {
				$this->_properties = array();
				$this->setIsMissing(); // the xml file may not be found -> the dir may be missing.
			}
		}
	}

	/**
	* Get the properties of this module as found in the module.xml file
	* @access  public
	* @param   array $properties_list	list of property names
	* @return  array associative array of property/value pairs
	* @author  Joel Kronenberg
	*/
	function getProperties($properties_list) {
		$this->_initModuleProperties();

		if (!$this->_properties) {
			return;
		}
		$properties_list = array_flip($properties_list);
		foreach ($properties_list as $property => $garbage) {
			$properties_list[$property] = $this->_properties[$property];
		}
		return $properties_list;
	}
	/**
	* Get a single property as found in the module.xml file
	* @access  public
	* @param   string $property	name of the property to return
	* @return  string the value of the property 
	* @author  Joel Kronenberg
	*/
	function getProperty($property) {
		$this->_initModuleProperties();

		if (!$this->_properties) {
			return;
		}

		return $this->_properties[$property];
	}

	function getName() {
		if ($this->isUninstalled()) {
			return current($this->getProperty('name'));
		}
		return _AT(basename($this->_directoryName));
	}

	function getDescription($lang = 'en') {
		$this->_initModuleProperties();

		if (!$this->_properties) {
			return;
		}

		return (isset($this->_properties['description'][$lang]) ? $this->_properties['description'][$lang] : current($this->_properties['description']));
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

	/**
	* Checks whether or not this module can be backed-up
	* @access  public
	* @return  boolean true if this module can be backed-up, false otherwise
	* @author  Joel Kronenberg
	*/
	function isBackupable() {
		return is_file(AT_MODULE_PATH . $this->_directoryName.'/module_backup.php');
	}

	function createGroup($group_id) {
		if (is_file(AT_MODULE_PATH . $this->_directoryName.'/module_groups.php')) {
			require_once(AT_MODULE_PATH . $this->_directoryName.'/module_groups.php');
			$fn_name = basename($this->_directoryName) .'_create_group';
			$fn_name($group_id);
		}
	}

	function deleteGroup($group_id) {
		if (is_file(AT_MODULE_PATH . $this->_directoryName.'/module_groups.php')) {
			require_once(AT_MODULE_PATH . $this->_directoryName.'/module_groups.php');
			$fn_name = basename($this->_directoryName) .'_delete_group';
			$fn_name($group_id);
		}
	}

	function getGroupTool() {
		if (!isset($this->_group_tool)) {
			return;
		} 

		return $this->_group_tool;
	}

	function isGroupable() {
		return is_file(AT_MODULE_PATH . $this->_directoryName.'/module_groups.php');
	}

	/**
	* Backup this module for a given course
	* @access  public
	* @param   int		$course_id	ID of the course to backup
	* @param   object	$zipfile	a reference to a zipfile object
	* @author  Joel Kronenberg
	*/
	function backup($course_id, &$zipfile) {
		static $CSVExport;

		if (!isset($CSVExport)) {
			require_once(AT_INCLUDE_PATH . 'classes/CSVExport.class.php');
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

	/**
	* Restores this module into the given course
	* @access  public
	* @param   int		$course_id	ID of the course to restore into
	* @param   string	$version	version number of the ATutor installation used to make this backup
	* @param   string	$import_dir	the path to the import directory
	* @author  Joel Kronenberg
	*/
	function restore($course_id, $version, $import_dir) {
		static $CSVImport;
		if (!file_exists(AT_MODULE_PATH . $this->_directoryName.'/module_backup.php')) {
			return;
		}

		if (!isset($CSVImport)) {
			require_once(AT_INCLUDE_PATH . 'classes/CSVImport.class.php');
			$CSVImport = new CSVImport();
		}

		require(AT_MODULE_PATH . $this->_directoryName.'/module_backup.php');
		if (isset($sql)) {
			foreach ($sql as $table_name => $table_sql) {
				$CSVImport->import($table_name, $import_dir, $course_id, $version);
			}
		}
		if (isset($dirs)) {
			foreach ($dirs as $src => $dest) {
				$dest = str_replace('?', $course_id, $dest);
				copys($import_dir.$src, $dest);
			}
		}
	}

	/**
	* Delete this module's course content
	* @access  public
	* @param   int $course_id	ID of the course to delete
	* @author  Joel Kronenberg
	*/
	function delete($course_id) {
		if (is_file(AT_MODULE_PATH . $this->_directoryName.'/module_delete.php')) {
			require(AT_MODULE_PATH . $this->_directoryName.'/module_delete.php');
			if (function_exists(basename($this->_directoryName).'_delete')) {
				$fnctn = basename($this->_directoryName).'_delete';
				$fnctn($course_id);
			}
		}
	}

	/**
	* Enables the installed module
	* @access  public
	* @author  Joel Kronenberg
	*/
	function enable() {
		global $db;

		$sql = 'UPDATE '. TABLE_PREFIX . 'modules SET status='.AT_MODULE_STATUS_ENABLED.' WHERE dir_name="'.$this->_directoryName.'"';
		$result = mysql_query($sql, $db);
	}

	/**
	* Sets the status to missing if the module dir doesn't exist.
	* @access  public
	* @author  Joel Kronenberg
	*/
	function setIsMissing() {
		global $db;
		// if the directory doesn't exist then set the status to MISSING
		if (!is_dir(AT_MODULE_PATH . $this->_directoryName)) {
			$sql = 'UPDATE '. TABLE_PREFIX . 'modules SET status='.AT_MODULE_STATUS_MISSING.' WHERE dir_name="'.$this->_directoryName.'"';
			$result = mysql_query($sql, $db);
		}
	}

	/**
	* Disables the installed module
	* @access  public
	* @author  Joel Kronenberg
	*/
	function disable() {
		global $db;

		$sql = 'UPDATE '. TABLE_PREFIX . 'modules SET status='.AT_MODULE_STATUS_DISABLED.' WHERE dir_name="'.$this->_directoryName.'"';
		$result = mysql_query($sql, $db);

		if (function_exists(basename($this->_directoryName).'_disable')) {
			$fn_name = basename($this->_directoryName).'_disable';
			$fn_name();
		}
	}

	/**
	* Installs the module
	* @access  public
	* @author  Joel Kronenberg
	*/
	function install() {
		global $msg;

		// should check if this module is already installed...

		if (file_exists(AT_MODULE_PATH . $this->_directoryName . '/module_install.php')) {
			require(AT_MODULE_PATH . $this->_directoryName . '/module_install.php');
		}

		if (!$msg->containsErrors()) {
			global $db;

			$sql = "SELECT MAX(`privilege`) AS `privilege`, MAX(admin_privilege) AS admin_privilege FROM ".TABLE_PREFIX."modules";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);

			if (($_course_privilege === TRUE) || ((string) $_course_privilege == 'new')) {
				$priv = $row['privilege'] * 2;
			} else if ($_course_privilege == AT_PRIV_ADMIN) {
				$priv = AT_PRIV_ADMIN;
			} else {
				$priv = 0;
			}

			if (($_admin_privilege === TRUE) || ((string) $_admin_privilege == 'new')) {
				$admin_priv = $row['admin_privilege'] * 2;
			} else {
				$admin_priv = AT_ADMIN_PRIV_ADMIN;
			}

			$sql = 'INSERT INTO '. TABLE_PREFIX . 'modules VALUES ("'.$this->_directoryName.'", '.AT_MODULE_STATUS_DISABLED.', '.$priv.', '.$admin_priv.')';
			$result = mysql_query($sql, $db);
		}
	}

	function getStudentTools() {
		if (!isset($this->_student_tool)) {
			return FALSE;
		} 

		return $this->_student_tool;
	}

}

?>