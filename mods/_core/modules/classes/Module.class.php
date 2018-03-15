<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_MODULE_STATUS_DISABLED',    1);
define('AT_MODULE_STATUS_ENABLED',     2);
define('AT_MODULE_STATUS_MISSING',     4);
define('AT_MODULE_STATUS_UNINSTALLED', 8); // not in the db
define('AT_MODULE_STATUS_PARTIALLY_UNINSTALLED', 16); // not in the db

define('AT_MODULE_TYPE_CORE',     1);
define('AT_MODULE_TYPE_STANDARD', 2);
define('AT_MODULE_TYPE_EXTRA',    4);

define('AT_MODULE_DIR_CORE',     '_core');
define('AT_MODULE_DIR_STANDARD', '_standard');

define('AT_SYSTEM_MODULE_PATH', realpath(AT_INCLUDE_PATH.'../mods') . DIRECTORY_SEPARATOR);

// The commented line points to the subsite mods directory where the subsite owned
// modules are planned to be installed/loaded from there. The feture is not applicable
// for the time being untill the module layer is extended to accept the modules from
// different directories.
//define('AT_SUBSITE_MODULE_PATH', realpath(AT_SITE_PATH.'mods') . DIRECTORY_SEPARATOR);
define('AT_SUBSITE_MODULE_PATH', AT_SYSTEM_MODULE_PATH);

/**
* ModuleFactory //
* 
* @access	public
* @author	Joel Kronenberg
* @package	Module
*/
class ModuleFactory {
	// private
	var $_modules = NULL; // array of module refs

	function __construct($auto_load = FALSE) {
		$this->_modules = array();

		if ($auto_load == TRUE) {
			// initialise enabled modules
			$sql	= "SELECT dir_name, privilege, admin_privilege, status, cron_interval, cron_last_run FROM %smodules WHERE status=%d";
			$rows_modules = queryDB($sql, array(TABLE_PREFIX, AT_MODULE_STATUS_ENABLED));	
				
			foreach($rows_modules as $row){	
				$module = new Module($row);
				$this->_modules[$row['dir_name']] = $module;
				$module->load();
			}
		}
	}

	// public
	// status := enabled | disabled | uninstalled | missing
	// type  := core | standard | extra
	// sort  := true | false (by name only)
	// the results of this method are not cached. call sparingly.
	function getModules($status, $type = 0, $sort = FALSE) {

		$modules     = array();
		$all_modules = array();

		if ($type == 0) {
			$type = AT_MODULE_TYPE_CORE | AT_MODULE_TYPE_STANDARD | AT_MODULE_TYPE_EXTRA;
		}

		$sql	= "SELECT dir_name, privilege, admin_privilege, status, cron_interval, cron_last_run FROM %smodules";
		$rows_modules = queryDB($sql, array(TABLE_PREFIX));		
		
		foreach($rows_modules as $row){
			if (!isset($this->_modules[$row['dir_name']])) {
				$module = new Module($row);
			} else {
				$module = $this->_modules[$row['dir_name']];
			}
			$all_modules[$row['dir_name']] = $module;
		}

		// small performance addition:
		if ($status && AT_MODULE_STATUS_UNINSTALLED) {
			$dir = opendir(AT_SUBSITE_MODULE_PATH);
			
			//Hack to show only extra mods
			if(preg_match('/install_modules\.php/', $_SERVER['SCRIPT_FILENAME'])){
			    $installed_module = $all_modules;
			    if(defined('IS_SUBSITE')){
			        unset($all_modules);
			    }
			}
			while (false !== ($dir_name = readdir($dir))) {
				if (($dir_name == '.') 
					|| ($dir_name == '..') 
					|| ($dir_name == '.git') 
					|| preg_match('/\./', $dir_name) 
					|| ($dir_name == '.svn') 
					|| ($dir_name == AT_MODULE_DIR_CORE) 
					|| ($dir_name == AT_MODULE_DIR_STANDARD)) {
					continue;
				}
				if (is_dir(AT_SUBSITE_MODULE_PATH . $dir_name) && !isset($all_modules[$dir_name])) {
					$module = new Module($dir_name);
					$all_modules[$dir_name] = $module;
				}
			}
			closedir($dir);
		}

		$keys = array_keys($all_modules);
		foreach ($keys as $dir_name) {
			$module = $all_modules[$dir_name];
			if ($module->checkStatus($status) && $module->checkType($type) && $module->checkPrivacy()) {
				$modules[$dir_name] = $module;
			} else if (!$module->checkStatus($status) && $module->checkPrivacy() && is_array($installed_module)){
			    if(!in_array($dir_name, (array_keys($installed_module)))){
			    $modules[$dir_name] = $module;
			    }
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
			$sql	= "SELECT dir_name, privilege, admin_privilege, status FROM %smodules WHERE dir_name='%s'";
			$row = queryDB($sql, array(TABLE_PREFIX, $module_dir), TRUE);
			
			if(count($row) != 0){	
				$module = new Module($row);
			} else {
				$module = new Module($module_dir);
			}
			$this->_modules[$module_dir] =& $module;
		}
		return $this->_modules[$module_dir];
	}

	// private
	// used for sorting modules
	function compare($a, $b) {
		return strnatcasecmp($a->getName(), $b->getName());
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
	var $_pages_i; // instructor course admin tools
	var $_type; // core, standard, extra
	var $_module_path; // module path is different for core/standard module and extra module
	var $_properties; // array from xml
	var $_cron_interval; // cron interval
	var $_cron_last_run; // cron last run date stamp
	var $_content_tools; // content tool icons on "edit content" page

	// constructor
	function __construct($row) {
		global $_content_tools;
		
		if (is_array($row)) {
			$this->_directoryName   = $row['dir_name'];
			$this->_status          = $row['status'];
			$this->_privilege       = $row['privilege'];
			$this->_admin_privilege = $row['admin_privilege'];
			$this->_display_defaults= isset($row['display_defaults']) ? $row['display_defaults'] : 0;
			$this->_cron_interval   = $row['cron_interval'];
			$this->_cron_last_run   = $row['cron_last_run'];

			if (strpos($row['dir_name'], AT_MODULE_DIR_CORE) === 0) {
				$this->_type = AT_MODULE_TYPE_CORE;
			} else if (strpos($row['dir_name'], AT_MODULE_DIR_STANDARD) === 0) {
				$this->_type = AT_MODULE_TYPE_STANDARD;
			} else {
				$this->_type = AT_MODULE_TYPE_EXTRA;
			}
			
			if ($this->_type == AT_MODULE_TYPE_EXTRA) {
				$this->_module_path = AT_SUBSITE_MODULE_PATH;
			} else {
				$this->_module_path = AT_SYSTEM_MODULE_PATH;
			}
			
		} else {
			$this->_directoryName   = $row;
			$this->_status          = AT_MODULE_STATUS_UNINSTALLED;
			$this->_privilege       = 0;
			$this->_admin_privilege = 0;
			$this->_display_defaults= 0;
			$this->_type            = AT_MODULE_TYPE_EXTRA; // standard/core are installed by default
			$this->_module_path = AT_SUBSITE_MODULE_PATH;
		}
		$this->_content_tools   = array();
	}

	// statuses
	function checkStatus($status) { return (bool) ($status & $this->_status); }
	function isPartiallyUninstalled()  { return ($this->_status == AT_MODULE_STATUS_PARTIALLY_UNINSTALLED) ? true : false; }
	function isUninstalled()  { return ($this->_status == AT_MODULE_STATUS_UNINSTALLED) ? true : false; }
	function isEnabled()      { return ($this->_status == AT_MODULE_STATUS_ENABLED)     ? true : false; }
	function isDisabled()     { return ($this->_status == AT_MODULE_STATUS_DISABLED)    ? true : false; }
	function isMissing()      { return ($this->_status == AT_MODULE_STATUS_MISSING)     ? true : false; }

	// types
	function checkType($type) { return (bool) ($type & $this->_type); }
	function isCore()     { return ($this->_type == AT_MODULE_TYPE_CORE)     ? true : false; }
	function isStandard() { return ($this->_type == AT_MODULE_TYPE_STANDARD) ? true : false; }
	function isExtra()    { return ($this->_type == AT_MODULE_TYPE_EXTRA)    ? true : false; }
	
	// privacy
	// public function
	// return true if
	// 1. the request is from an ATutor standalone site or the main site of a multisite installation;
	// 2. this is a public module;
	// 3. this is a private module of the site that the request is sent from
	// otherwise, return false
	function checkPrivacy() {
		// main site can see all the modules including the subsite owned modules
		if (!defined('IS_SUBSITE')) return true;
		
		$properties = $this->getProperties(array('subsite'));
		if (count($properties['subsite']) == 0) return true;  // a public module
		
		foreach ($properties['subsite'] as $subsite) {
			if ($subsite == $_SERVER['HTTP_HOST']) return true;
		}
		return false;
	}
	
	// module path
	function getModulePath() { return $this->_module_path; }

	// privileges
	function getPrivilege()      { return $this->_privilege;       }
	function getAdminPrivilege() { return $this->_admin_privilege; }

	function load() {
		if (is_file($this->_module_path . $this->_directoryName.'/module.php')) {
			global $_modules, $_pages, $_pages_i, $_stacks, $_list, $_tool, $_content_tools, $_callbacks;  // $_list is for sublinks on "detail view"

			require($this->_module_path . $this->_directoryName.'/module.php');

			if (isset($this->_pages)) {
				$_pages = array_merge_recursive((array) $_pages, $this->_pages);
			}
			if (isset($this->_pages_i)) {
				$_pages_i = array_merge_recursive((array) $_pages_i, $this->_pages_i);
			}
			//side menu items
			if (isset($this->_stacks)) {
				$count = 0;
				$_stacks = array_merge((array)$_stacks, $this->_stacks);
			}

			// sublinks on "detail view"
			if(isset($this->_list)) {
				$_list = array_merge((array)$_list, $this->_list);			
			}

			if(isset($this->_content_tools)) {
				$_content_tools = array_merge((array)$_content_tools, $this->_content_tools);			
			}
			
			if(isset($this->_callbacks)) {
				$_callbacks = array_merge((array)$_callbacks, $this->_callbacks);			
			}
			
			//TODO***********BOLOGNA***********REMOVE ME***********/
			//tool manager (content editing)
			if(isset($this->_tool)) {
				$_tool = array_merge((array)$_tool, $this->_tool);
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
			$moduleParser = new ModuleParser();
			$moduleParser->parse(@file_get_contents($this->_module_path . $this->_directoryName.'/module.xml'));
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

	function getCronInterval() {
		return $this->_cron_interval;

	}

	function getName() {
		if ($this->isUninstalled()) {
			$name = $this->getProperty('name');
			if($name) return current($name);
		}
		return _AT(basename($this->_directoryName));
	}

	function getDescription($lang = 'en') {
		$this->_initModuleProperties();

		if (!$this->_properties) {
			return;
		}

		if (isset($this->_properties['description'][$lang])) {
			return $this->_properties['description'][$lang];
		}
		$description = current($this->_properties['description']);
		return $description;
	}

	function getChildPage($page) {
		if (!is_array($this->_pages)) {
			return;
		}
		foreach ($this->_pages as $tmp_page => $item) {
			if (!empty($item['parent']) && $item['parent'] == $page) {
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
		return is_file($this->_module_path . $this->_directoryName.'/module_backup.php');
	}

	function createGroup($group_id) {
		if (is_file($this->_module_path . $this->_directoryName.'/module_groups.php')) {
			require_once($this->_module_path . $this->_directoryName.'/module_groups.php');
			$fn_name = basename($this->_directoryName) .'_create_group';
			$fn_name($group_id);
		}
	}

	function deleteGroup($group_id) {
		$fn_name = basename($this->_directoryName) .'_delete_group';

		if (!function_exists($fn_name) && is_file($this->_module_path . $this->_directoryName.'/module_groups.php')) {
			require_once($this->_module_path . $this->_directoryName.'/module_groups.php');
		} 
		if (function_exists($fn_name)) {
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
		return is_file($this->_module_path . $this->_directoryName.'/module_groups.php');
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
			require($this->_module_path . $this->_directoryName . '/module_backup.php');
			if (isset($sql)) {
				foreach ($sql as $file_name => $table_sql) {
					$content = $CSVExport->export($table_sql, $course_id);
					if ($content) {
						$zipfile->add_file($content, $file_name . '.csv', $now);
					}
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
		if (!file_exists($this->_module_path . $this->_directoryName.'/module_backup.php')) {
			return;
		}

		if (!isset($CSVImport)) {
			require_once(AT_INCLUDE_PATH . 'classes/CSVImport.class.php');
			$CSVImport = new CSVImport();
		}

		require($this->_module_path . $this->_directoryName.'/module_backup.php');

		if (isset($sql)) {
			foreach ($sql as $table_name => $table_sql) {
				$CSVImport->import($table_name, $import_dir, $course_id, $version);
			}
		}
		if ($this->_directoryName == '_core/content')
		{
			if (version_compare($version, '1.6.4', '<')) {
				$this->convertContent164($course_id);
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
	* Delete this module's course content. If $groups is specified then it will
	* delete all content for the groups specified.
	* @access  public
	* @param   int   $course_id	ID of the course to delete
	* @param   array $groups    Array of groups to delete
	* @author  Joel Kronenberg
	*/
	function delete($course_id, $groups) {
		if (is_file($this->_module_path . $this->_directoryName.'/module_delete.php')) {
			require($this->_module_path . $this->_directoryName.'/module_delete.php');
			if (function_exists(basename($this->_directoryName).'_delete')) {
				$fnctn = basename($this->_directoryName).'_delete';
				$fnctn($course_id);
			}
		}
		if ($groups) {
			foreach ($groups as $group_id) {
				$this->deleteGroup($group_id);
			}
		}
	}

	/**
	* Enables the installed module
	* @access  public
	* @author  Joel Kronenberg
	*/
	function enable() {
		
		$sql = "UPDATE %smodules SET status=%d WHERE dir_name='%s'";
		$result = queryDB($sql, array(TABLE_PREFIX, AT_MODULE_STATUS_ENABLED, $this->_directoryName));
	}

	/**
	* Sets the status to missing if the module dir doesn't exist.
	* @access  public
	* @param   boolean $force whether or not to force the module to be missing (used for bundled extra modules upon upgrade)
	* @author  Joel Kronenberg
	*/
	function setIsMissing($force = false) {
		// if the directory doesn't exist then set the status to MISSING
		if ($force || !is_dir($this->_module_path . $this->_directoryName)) {
			$sql = 'UPDATE %smodules SET status=%d WHERE dir_name="%s"';
			$result = queryDB($sql, array(TABLE_PREFIX, AT_MODULE_STATUS_MISSING, $this->_directoryName));
		}
	}

	/**
	* Disables the installed module
	* @access  public
	* @author  Joel Kronenberg
	*/
	function disable() {

		// remove any privileges admins, students
		if ($this->_privilege > 1) {
			$sql = 'UPDATE %scourse_enrollment SET `privileges`=`privileges`-%d WHERE `privileges` > 1 AND (`privileges` & %d)<>0';
			$result = queryDB($sql, array(TABLE_PREFIX, $this->_privilege, $this->_privilege));			
		}

		if ($this->_admin_privilege > 1) {
			$sql = 'UPDATE %sadmins SET `privileges`=`privileges`-%d WHERE `privileges` > 1 AND (`privileges` & %d)<>0';
			$result = queryDB($sql, array(TABLE_PREFIX, $this->_admin_privilege, $this->_admin_privilege));			
		}

		$sql = 'UPDATE %smodules SET status=%d WHERE dir_name="%s"';
		$result = queryDB($sql, array(TABLE_PREFIX, AT_MODULE_STATUS_DISABLED, $this->_directoryName));
		
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
		if (file_exists($this->_module_path . $this->_directoryName . '/module_install.php')) {
			require($this->_module_path . $this->_directoryName . '/module_install.php');
		}

		if (!$msg->containsErrors()) {
			$sql = "SELECT MAX(`privilege`) AS `privilege`, MAX(admin_privilege) AS admin_privilege FROM %smodules";
			$row = queryDB($sql, array(TABLE_PREFIX), TRUE);

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

			if (isset($_cron_interval)) {
				$_cron_interval = abs($_cron_interval);
			} else {
				$_cron_interval = 0;
			}

			$sql = 'INSERT INTO %smodules VALUES ("%s", %d, %d, %d, %d, 0)';
			$result = queryDB($sql, array(TABLE_PREFIX, $this->_directoryName, AT_MODULE_STATUS_DISABLED, $priv, $admin_priv, $_cron_interval));			
		
		    if($result != 1){
				// in case this module has to be re-installed (because it was Missing)
				$sql = 'UPDATE %smodules SET status=%d WHERE dir_name="%s"';
				queryDB($sql, array(TABLE_PREFIX, AT_MODULE_STATUS_DISABLED, $this->_directoryName));				
			}
		}
	}

	/**
	* Uninstalls the module
	* @access  public
	* @author  Cindy Qi Li
	*/
	function uninstall($del_data='') {
		global $msg;

		if (file_exists($this->_module_path . $this->_directoryName . '/module_uninstall.php') && $del_data == 1) 
		{
			require($this->_module_path . $this->_directoryName . '/module_uninstall.php');
		}

		if (!$msg->containsErrors()) 
		{
			require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');
						
			if (!clr_dir($this->_module_path . $this->_directoryName))
				$msg->addError(array('MODULE_UNINSTALL', '<li>'.$this->_module_path . $this->_directoryName.' can not be removed. Please manually remove it.</li>'));
		}
		
		if (!$msg->containsErrors()) 
		{
			$sql = "DELETE FROM %smodules WHERE dir_name = '%s'";
			queryDB($sql, array(TABLE_PREFIX, $this->_directoryName));
		}

		if ($msg->containsErrors()) 
		{
			$sql = "UPDATE %smodules SET status=%d WHERE dir_name='%s'";
			queryDB($sql, array(TABLE_PREFIX, AT_MODULE_STATUS_PARTIALLY_UNINSTALLED, $this->_directoryName));			
		}
	}

	function getStudentTools() {
		if (!isset($this->_student_tool)) {
			return FALSE;
		} 

		return $this->_student_tool;
	}


	function runCron() {
		if ( ($this->_cron_last_run + ($this->_cron_interval * 60)) < time()) {
			if (is_file($this->_module_path . $this->_directoryName.'/module_cron.php')) {
				require($this->_module_path . $this->_directoryName.'/module_cron.php');
				if (function_exists(basename($this->_directoryName).'_cron')) {
					$fnctn = basename($this->_directoryName).'_cron';
					$fnctn();
				}
			}
			$this->updateCronLastRun();
		}
	}

	// i'm private! update the last time the cron was run
	function updateCronLastRun() {
		$sql = "UPDATE %smodules SET cron_last_run=".time()." WHERE dir_name='%s'";
		queryDB($sql, array(TABLE_PREFIX, $this->_directoryName));
	}

	/**
	 * Get the latest news from the Module. 
	 * @access	public
	 * @author	Harris Wong 
	 * @date	Feb 25, 2010
	 */
	function getNews(){
		global $msg, $enrolled_courses;

		if (!isset($enrolled_courses)){
			$sql = "SELECT E.approved, E.last_cid, C.* FROM %scourse_enrollment E, %scourses C WHERE E.member_id=%d AND E.course_id=C.course_id ORDER BY C.title";
			$rows_enrolled = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['member_id']));

			if(count($rows_enrolled) != 0){
			    foreach($rows_enrolled as $row){
					$enrolled_courses = $enrolled_courses . $row['course_id'] . ', ';
				}
				$enrolled_courses = substr($enrolled_courses, 0, -2); 

				if ($enrolled_courses != ''){
					$enrolled_courses = '(' . $enrolled_courses . ')';
				} 
			}
		}

		if (file_exists($this->_module_path . $this->_directoryName . '/module_news.php')) {
			require($this->_module_path . $this->_directoryName . '/module_news.php');
			if (function_exists(basename($this->_directoryName).'_news')) {
				$fnctn = basename($this->_directoryName).'_news';
                /// WHAT DOES $course_id DO?
                ///return $fnctn($course_id);
				return $fnctn();
			}
		}
	}

    /**
     * Function which allows the dates to be accessed 
     * @access public
     * @author Anurup Raveendran
     * @date Jul 8, 2011
     */
   function extend_date($member_id, $course_id){
        if (file_exists(AT_SYSTEM_MODULE_PATH . $this->_directoryName . '/module_extend_date.php')) {
            require_once(AT_SYSTEM_MODULE_PATH . $this->_directoryName . '/module_extend_date.php');

            if (function_exists(basename($this->_directoryName).'_extend_date')) {
                $fnctn = basename($this->_directoryName).'_extend_date';
                return $fnctn($member_id, $course_id);
            }        
        }
    }


	/**
	 * Get the output that this module wants to add onto content page. 
	 * @access	public
	 * @author	Cindy Li 
	 * @date	Dec 7, 2010
	 */
	function getContent($cid){
		if (file_exists($this->_module_path . $this->_directoryName.'/ModuleCallbacks.class.php') &&
		    isset($this->_callbacks[basename($this->_directoryName)])) 
		{
			require($this->_module_path . $this->_directoryName.'/ModuleCallbacks.class.php');
			if (method_exists($this->_callbacks[basename($this->_directoryName)], "appendContent")) {
				eval('$output = '.$this->_callbacks[basename($this->_directoryName)]."::appendContent($cid);");
				return $output;
			}
		}
		return NULL;
	}
	
	private function convertContent164($course_id) {
		
		/* convert all content nodes to the IMS standard. (adds null nodes for all top pages) */
		/* 1. Convert db to a tree */
		$sql = 'SELECT * FROM %scontent where course_id=%d';
		$rows_content = queryDB($sql, array(TABLE_PREFIX, $course_id));
		
		$content_array = array(); 
        foreach($rows_content as $row){
			$content_array[$row['content_parent_id']][$row['ordering']] = $row['content_id'];
		}
		$tree = $this->buildTree($content_array[0], $content_array);

		/* 2. Restructure the tree */
		$tree = $this->rebuild($tree);

		/* 3. Update the Db based on this new tree */
		$this->reconstruct($tree, '', 0, TABLE_PREFIX);
	}

	/** 
	 * Construct a tree based on table entries
	 * @param	array	current node, (current parent)
	 * @param	mixed	a set of parents, where each parents is in the format of [parent]=>children
	 *					should remain the same throughout the recursion.
	 * @return	A tree structure representation of the content entries.
	 * @author	Harris Wong
	 */
	private function buildTree($current, $content_array){
		$folder = array();
		foreach($current as $order=>$content_id){
			//if has children
			if (isset($content_array[$content_id])){
				$wrapper[$content_id] = $this->buildTree($content_array[$content_id], $content_array);
			}
	
			//no children.
			if ($wrapper){
				$folder['order_'.$order] = $wrapper;
				unset($wrapper);
			} else {
				$folder['order_'.$order] = $content_id;
			}
		}	
		return $folder;
	}
	
	
	/**
	 * Transverse the content tree structure, and reconstruct it with the IMS spec.  
	 * This tree has the structure of [order=>array(id)], so the first layer is its order, second is the id
	 * if param merge is true, if node!=null, merge it to top layer, and + offset to all others
	 * @param	mixed	Tree from the buildTree() function, or sub-tree
	 * @param	mixed	the current tree.
	 * @return	A new content tree that meets the IMS specification.
	 * @author	Harris Wong 
	 */
	private function rebuild($tree, $node=''){
	    $order_offset = 0;
	    $folder = array();
	    if (!is_array($tree)){
	        return $tree;
	    }
	    if ($node!=''){
	        $tree['order_0'] = $node;
	        $order_offset += 1;
	    }
	    //go through the tree
	    foreach($tree as $k=>$v){
	        if (preg_match('/order\_([\d]+)/', $k, $match)==1){
	            //if this is the order layer
	            $folder['order_'.($match[1]+$order_offset)] = $this->rebuild($v);
	        } else {
	            //if this is the content layer
	            if(is_array($v)){
	                $folder[$k] = $this->rebuild($v, $k);
	            }
	        }
	    }
	    return $folder;
	}
	
	/**
	 * Transverse the tree and update/insert entries based on the updated structure.
	 * @param	array	The tree from rebuild(), and the subtree from the recursion.
	 * @param	int		the ordering of this subtree respect to its parent.
	 * @param	int		parent content id
	 * @return	null (nothing to return, it updates the db only)
	 */
	private function reconstruct($tree, $order, $content_parent_id, $table_prefix){
	
		//a content page.
		if (!is_array($tree)){
			$sql = "UPDATE %scontent SET ordering=%d, content_parent_id=%d WHERE content_id=%d";
			$result= queryDB($sql, array($table_prefix, $order, $content_parent_id, $tree));

			return $result;
		}
		foreach ($tree as $k=>$v){
	        if (preg_match('/order\_([\d]+)/', $k, $match)==1){
				//order layer
				$this->reconstruct($v, $match[1], $content_parent_id, $table_prefix);	//inherit the previous layer id
			} else {
				//content folder layer
				$sql = "SELECT * FROM %scontent WHERE content_id=%d";
				$old_content_row = queryDB($sql, array($table_prefix, $k), TRUE);				
				
				$sql = 'INSERT INTO %scontent (course_id, content_parent_id, ordering, last_modified, revision, formatting, release_date, keywords, content_path, title, use_customized_head, allow_test_export, content_type) VALUES ('
					.$old_content_row['course_id'] . ', '
					.$content_parent_id . ', '
					.$order . ', '
					.'\''. $old_content_row['last_modified'] . '\', '
					.$old_content_row['revision'] . ', '
					.$old_content_row['formatting'] . ', '
					.'\''. $old_content_row['release_date'] . '\', '
					.'\''. $old_content_row['keywords'] . '\', '
					.'\''. $old_content_row['content_path'] . '\', '
					.'\''. $old_content_row['title'] . '\', '
					.$old_content_row['use_customized_head'] . ', '
					.$old_content_row['allow_test_export'] . ', '
					. '1)';
				$result = queryDB($sql, array($table_prefix));
				if($result > 0){
					$folder_id = at_insert_id();					
					$this->reconstruct($v, '', $folder_id, $table_prefix);
				} else {
					//throw error
					echo at_db_error();
				}
			}
		}
	}
}
?>