<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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
define('AT_MODULE_DISABLED',	1);
define('AT_MODULE_ENABLED',	    2);
define('AT_MODULE_CORE',		4);
define('AT_MODULE_UNINSTALLED',	8); // not in the db

// all is (DIS | EN | UN)

/**
* ModuleFactory
* 
* @access	public
* @author	Joel Kronenberg
* @package	Module
*/
class ModuleFactory {
	// private
	var $_enabled_modules     = NULL; // make status the key to the array of modules $_modules[STATUS]
	var $_core_modules        = NULL;
	var $_disabled_modules    = NULL;
	var $_installed_modules   = NULL;
	var $_uninstalled_modules = NULL;
	var $_all_modules         = NULL;

	var $_db;

	function ModuleFactory($auto_load = FALSE) {
		global $db;

		$this->db =& $db;

		$this->_enabled_modules = array();
		// initialise enabled modules
		$sql	= "SELECT dir_name, privilege, status FROM ". TABLE_PREFIX . "modules WHERE status<>".AT_MOD_DISABLED;
		$result = mysql_query($sql, $this->db);
		while($row = mysql_fetch_assoc($result)) {
			$module =& new ModuleProxy($row['dir_name'], $row['status'], $row['privilege']);
			if ($row['status'] == AT_MOD_ENABLED) {
				$this->_enabled_modules[$row['dir_name']] =& $module;
			} else if ($row['status'] == AT_MOD_CORE) {
				$this->_core_modules[$row['dir_name']] =& $module;
			}
			$this->_all_modules[$row['dir_name']]     =& $module;

			if ($auto_load == TRUE) {
				$module->load();
			}
		}
	}

	// public
	// state is a bit wise combination of enabled, disabled, and uninstalled.
	// more specifically AT_MODULE_DISABLED | AT_MODULE_CORE | AT_MODULE_ENABLED | AT_MODULE_UNINSTALLED
	function & getModules($state) {
		$modules = array();
		if (query_bit($state, AT_MODULE_ENABLED)) {
			$modules =& $this->_enabled_modules;
		}

		if (query_bit($state, AT_MODULE_DISABLED)) {
			$this->initDisabledModules();
			$modules = array_merge($modules, $this->_disabled_modules);
		}

		if (query_bit($state, AT_MODULE_CORE)) {
			$modules = array_merge($modules, $this->_core_modules);
		}

		if (query_bit($state, AT_MODULE_UNINSTALLED)) {
			$this->initDisabledModules();
			$this->initUninstalledModules();
			$modules = array_merge($modules, $this->_uninstalled_modules);
		}
		return $modules;
	}

	// public.
	function & getModule($module_dir) {
		if (!isset($this->_all_modules[$module_dir])) {
			$module =& new ModuleProxy($module_dir);
			if ($module->isEnabled()) {
				$this->_enabled_modules[$module_dir]   =& $module;
				$this->_installed_modules[$module_dir] =& $module;
			}
			$this->_all_modules[$module_dir] =& $module;
		}
		return $this->_all_modules[$module_dir];
	}

	// private
	function initUnInstalledModules() {
		$this->initInstalledModules();

		// has to scan the dir
		$dir = opendir(AT_INCLUDE_PATH.'../mods/');
		while (false !== ($dir_name = readdir($dir))) {
			if (($dir_name == '.') || ($dir_name == '..') || ($dir_name == '.svn')) {
				continue;
			}

			if (is_dir(AT_INCLUDE_PATH.'../mods/' . $dir_name) && !isset($this->_installed_modules[$dir_name])) {
				$module =& new ModuleProxy($dir_name, FALSE);
				$this->_uninstalled_modules[$dir_name] =& $module;
				$this->_all_modules[$dir_name]         =& $module;
			}
		}
		closedir($dir);
	}

	// private
	function initDisabledModules() {
		static $initialised;
		if ($initialised) {
			return;
		}
		$initialised = TRUE;
		$sql	= "SELECT dir_name, privilege FROM ". TABLE_PREFIX . "modules WHERE status=".AT_MOD_DISABLED;
		$result = mysql_query($sql, $this->db);
		while($row = mysql_fetch_assoc($result)) {
			$module =& new ModuleProxy($row['dir_name'], FALSE, $row['privilege']);
			$this->_disabled_modules[$row['dir_name']] =& $module;
			$this->_all_modules[$row['dir_name']]      =& $module;
		}
	}

	// private
	function initInstalledModules() {
		// installed modules are Enabled (always given) + Disabled
		if (!isset($this->_installed_modules)) {
			$this->initDisabledModules();
			$this->_installed_modules = array_merge($this->_enabled_modules, $this->_core_modules, $this->_disabled_modules);
		}
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
	var $_pages;

	function ModuleProxy($dir, $status = AT_MOD_DISABLED, $privilege = 0) {
		$this->_directoryName = $dir;
		$this->_status        = $status;
		$this->_privilege     = $privilege;
	}

	function isEnabled() {
		return ($this->_status == AT_MOD_ENABLED) ? true : false;
	}

	function isCore() {
		return ($this->_status == AT_MOD_CORE) ? true : false;
	}

	function getPrivilege() {
		return $this->_privilege;
	}

	function getProperties($properties_list) {
		// this requires a real module object
		if (!isset($this->_moduleObj)) {
			$this->_moduleObj =& new Module($this->_directoryName);
		}
		return $this->_moduleObj->getProperties($properties_list);
	}

	function getProperty($property) {
		// this requires a real module object
		if (!isset($this->_moduleObj)) {
			$this->_moduleObj =& new Module($this->_directoryName);
		}
		return $this->_moduleObj->getProperty($property);
	}

	function getVersion() {
		// this requires a real module object
		if (!isset($this->_moduleObj)) {
			$this->_moduleObj =& new Module($this->_directoryName);
		}
		return $this->_moduleObj->getVersion();
	}


	function getName($lang) {
		// this requires a real module object
		if (!isset($this->_moduleObj)) {
			$this->_moduleObj =& new Module($this->_directoryName);
		}
		return $this->_moduleObj->getName($lang);
	}

	function getDescription($lang) {
		// this requires a real module object
		if (!isset($this->_moduleObj)) {
			$this->_moduleObj =& new Module($this->_directoryName);
		}
		return $this->_moduleObj->getDescription($lang);
	}

	function load() {
		if (is_file(AT_INCLUDE_PATH.'../mods/'.$this->_directoryName.'/module.php')) {
			global $_modules, $_pages;

			require(AT_INCLUDE_PATH.'../mods/'.$this->_directoryName.'/module.php');
			if (isset($_module_pages)) {
				$this->_pages =& $_module_pages;
				$_pages = array_merge($_pages, $this->_pages);
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

	function backup($course_id) {

	}

	function restore($course_id) {

	}

	function delete($course_id) {

	}

	function enable() {

	}

	function disable() {

	}

	function install() {

	}
}

// ----------------- in a diff file. only required when .. required.
/**
* Module
* 
* @access	protected
* @author	Joel Kronenberg
* @package	Module
*/
class Module {
	// all private
	var $_directory_name;
	var $_properties; // array from xml

	function Module($dir_name) {
		require_once(dirname(__FILE__) . '/ModuleParser.class.php');
		$moduleParser   =& new ModuleParser();
		
		$moduleParser->parse(@file_get_contents(AT_INCLUDE_PATH . '../mods/'.$dir_name.'/module.xml'));
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

	function backup($course_id) {

	}

	function restore($course_id) {

	}

	function delete($course_id) {

	}

	function enable() {

	}

	function disable() {

	}

	function install() {

	}
}

?>