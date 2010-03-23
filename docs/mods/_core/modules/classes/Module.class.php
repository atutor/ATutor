<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: Module.class.php 9081 2010-01-13 20:26:03Z cindy $

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

define('AT_MODULE_PATH', realpath(AT_INCLUDE_PATH.'../mods') . DIRECTORY_SEPARATOR);

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

	function ModuleFactory($auto_load = FALSE) {
		global $db;

		/* snippit to use when extending Module classes:
		$sql	= "SELECT dir_name, privilege, admin_privilege, status FROM ". TABLE_PREFIX . "modules WHERE status=".AT_MODULE_STATUS_ENABLED;
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);
		require(AT_MODULE_PATH . $row['dir_name'].'/module.php');
		$module = new PropertiesModule($row);
		***/

		$this->_modules = array();

		if ($auto_load == TRUE) {
			// initialise enabled modules
			$sql	= "SELECT dir_name, privilege, admin_privilege, status, cron_interval, cron_last_run FROM ". TABLE_PREFIX . "modules WHERE status=".AT_MODULE_STATUS_ENABLED;
			$result = mysql_query($sql, $db);
			while($row = mysql_fetch_assoc($result)) {
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
		global $db;

		$modules     = array();
		$all_modules = array();

		if ($type == 0) {
			$type = AT_MODULE_TYPE_CORE | AT_MODULE_TYPE_STANDARD | AT_MODULE_TYPE_EXTRA;
		}

		$sql	= "SELECT dir_name, privilege, admin_privilege, status, cron_interval, cron_last_run FROM ". TABLE_PREFIX . "modules";
		$result = mysql_query($sql, $db);
		
		while($row = mysql_fetch_assoc($result)) {
			if (!isset($this->_modules[$row['dir_name']])) {
				$module = new Module($row);
			} else {
				$module = $this->_modules[$row['dir_name']];
			}
			$all_modules[$row['dir_name']] = $module;
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
					$module = new Module($dir_name);
					$all_modules[$dir_name] = $module;
				}
			}
			closedir($dir);
		}

		$keys = array_keys($all_modules);
		foreach ($keys as $dir_name) {
			$module =$all_modules[$dir_name];
			if ($module->checkStatus($status) && $module->checkType($type)) {
				$modules[$dir_name] = $module;
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
	var $_type; // core, standard, extra
	var $_properties; // array from xml
	var $_cron_interval; // cron interval
	var $_cron_last_run; // cron last run date stamp

	// constructor
	function Module($row) {
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

	// privileges
	function getPrivilege()      { return $this->_privilege;       }
	function getAdminPrivilege() { return $this->_admin_privilege; }

	function load() {
		if (is_file(AT_MODULE_PATH . $this->_directoryName.'/module.php')) {
			global $_modules, $_pages, $_stacks, $_list, $_tool;  // $_list is for sublinks on "detail view"

			require(AT_MODULE_PATH . $this->_directoryName.'/module.php');

			if (isset($this->_pages)) {
				$_pages = array_merge_recursive((array) $_pages, $this->_pages);
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

	function getCronInterval() {
		return $this->_cron_interval;

	}

	function getName() {
		if ($this->isUninstalled()) {
			$name = $this->getProperty('name');
			return current($name);
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
		$fn_name = basename($this->_directoryName) .'_delete_group';

		if (!function_exists($fn_name) && is_file(AT_MODULE_PATH . $this->_directoryName.'/module_groups.php')) {
			require_once(AT_MODULE_PATH . $this->_directoryName.'/module_groups.php');
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
		if (is_file(AT_MODULE_PATH . $this->_directoryName.'/module_delete.php')) {
			require(AT_MODULE_PATH . $this->_directoryName.'/module_delete.php');
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
		global $db;

		$sql = 'UPDATE '. TABLE_PREFIX . 'modules SET status='.AT_MODULE_STATUS_ENABLED.' WHERE dir_name="'.$this->_directoryName.'"';
		$result = mysql_query($sql, $db);
	}

	/**
	* Sets the status to missing if the module dir doesn't exist.
	* @access  public
	* @param   boolean $force whether or not to force the module to be missing (used for bundled extra modules upon upgrade)
	* @author  Joel Kronenberg
	*/
	function setIsMissing($force = false) {
		global $db;
		// if the directory doesn't exist then set the status to MISSING
		if ($force || !is_dir(AT_MODULE_PATH . $this->_directoryName)) {
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

		// remove any privileges admins, students
		if ($this->_privilege > 1) {
			$sql = 'UPDATE '. TABLE_PREFIX . 'course_enrollment SET `privileges`=`privileges`-'.$this->_privilege.' WHERE `privileges` > 1 AND (`privileges` & '.$this->_privilege.')<>0';
			$result = mysql_query($sql, $db);
		}

		if ($this->_admin_privilege > 1) {
			$sql = 'UPDATE '. TABLE_PREFIX . 'admins SET `privileges`=`privileges`-'.$this->_admin_privilege.' WHERE `privileges` > 1 AND (`privileges` & '.$this->_admin_privilege.')<>0';
			$result = mysql_query($sql, $db);
		}

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

			if (isset($_cron_interval)) {
				$_cron_interval = abs($_cron_interval);
			} else {
				$_cron_interval = 0;
			}

			$sql = 'INSERT INTO '. TABLE_PREFIX . 'modules VALUES ("'.$this->_directoryName.'", '.AT_MODULE_STATUS_DISABLED.', '.$priv.', '.$admin_priv.', '.$_cron_interval.', 0)';
			mysql_query($sql, $db);
			if (mysql_affected_rows($db) != 1) {
				// in case this module has to be re-installed (because it was Missing)
				$sql = 'UPDATE '. TABLE_PREFIX . 'modules SET status='.AT_MODULE_STATUS_DISABLED.' WHERE dir_name="'.$this->_directoryName.'"';
				mysql_query($sql, $db);
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

		if (file_exists(AT_MODULE_PATH . $this->_directoryName . '/module_uninstall.php') && $del_data == 1) 
		{
			require(AT_MODULE_PATH . $this->_directoryName . '/module_uninstall.php');
		}

		if (!$msg->containsErrors()) 
		{
			require(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');
						
			if (!clr_dir(AT_MODULE_PATH . $this->_directoryName))
				$msg->addError(array('MODULE_UNINSTALL', '<li>'.AT_MODULE_PATH . $this->_directoryName.' can not be removed. Please manually remove it.</li>'));
		}
		
		if (!$msg->containsErrors()) 
		{
			global $db;

			$sql = "DELETE FROM ". TABLE_PREFIX . "modules WHERE dir_name = '".$this->_directoryName."'";
			mysql_query($sql, $db);
		}

		if ($msg->containsErrors()) 
		{
			global $db;

			$sql = "UPDATE ". TABLE_PREFIX . "modules SET status=".AT_MODULE_STATUS_PARTIALLY_UNINSTALLED." WHERE dir_name='".$this->_directoryName."'";
			mysql_query($sql, $db);
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
			if (is_file(AT_MODULE_PATH . $this->_directoryName.'/module_cron.php')) {
				require(AT_MODULE_PATH . $this->_directoryName.'/module_cron.php');
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
		global $db;

		$sql = "UPDATE ".TABLE_PREFIX."modules SET cron_last_run=".time()." WHERE dir_name='$this->_directoryName'";
		mysql_query($sql, $db);

	}


	/**
	 * Get the latest news from the Module. 
	 * @access	public
	 * @author	Harris Wong 
	 * @date	Feb 25, 2010
	 */
	function getNews(){
		global $msg, $enrolled_courses, $db;

		if (!isset($enrolled_courses)){
			$sql = 'SELECT E.approved, E.last_cid, C.* FROM AT_course_enrollment E, AT_courses C WHERE E.member_id='.$_SESSION['member_id'].' AND E.course_id=C.course_id ORDER BY C.title';
			$result = mysql_query($sql, $db);
			if ($result) {
				while($row = mysql_fetch_assoc($result)){
					$enrolled_courses = $enrolled_courses . $row['course_id'] . ', ';
				}
				$enrolled_courses = substr($enrolled_courses, 0, -2); 

				if ($enrolled_courses != ''){
					$enrolled_courses = '(' . $enrolled_courses . ')';
				} 
			}
		}

		if (file_exists(AT_MODULE_PATH . $this->_directoryName . '/module_news.php')) {
			require(AT_MODULE_PATH . $this->_directoryName . '/module_news.php');
			if (function_exists(basename($this->_directoryName).'_news')) {
				$fnctn = basename($this->_directoryName).'_news';
				return $fnctn($course_id);
			}
		}
	}
	
	private function convertContent164($course_id) {
		global $db;
		
		/* convert all content nodes to the IMS standard. (adds null nodes for all top pages) */
		/* 1. Convert db to a tree */
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'content where course_id='.$course_id;
		
		$result = mysql_query($sql, $db);
		$content_array = array(); 

		while ($row = mysql_fetch_assoc($result)){
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
		global $db;
	
		//a content page.
		if (!is_array($tree)){
			$sql = 'UPDATE '.$table_prefix."content SET ordering=$order, content_parent_id=$content_parent_id WHERE content_id=$tree";
			if (!mysql_query($sql, $db)){
				//throw error
				echo mysql_error();
			}
			return;
		}
		foreach ($tree as $k=>$v){
	        if (preg_match('/order\_([\d]+)/', $k, $match)==1){
				//order layer
				$this->reconstruct($v, $match[1], $content_parent_id, $table_prefix);	//inherit the previous layer id
			} else {
				//content folder layer
				$sql = 'SELECT * FROM '.$table_prefix."content WHERE content_id=$k";
				$result = mysql_query($sql, $db);
				$old_content_row = mysql_fetch_assoc($result);
				$sql = 'INSERT INTO '.$table_prefix.'content (course_id, content_parent_id, ordering, last_modified, revision, formatting, release_date, keywords, content_path, title, use_customized_head, allow_test_export, content_type) VALUES ('
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
				
				if (mysql_query($sql, $db)){
					$folder_id = mysql_insert_id();
					$this->reconstruct($v, '', $folder_id, $table_prefix);
				} else {
					//throw error
					echo mysql_error();
				}
			}
		}
	}
}
?>