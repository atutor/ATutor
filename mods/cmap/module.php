<?php
/*
This file defines privileges, and where the modules will be linked into ATutor, as tabs, tool icons, or side menu blocks.

*/
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a Module obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_CMAP',       $this->getPrivilege());
define('AT_ADMIN_PRIV_CMAP', $this->getAdminPrivilege());
define('AT_CMAP_WSDL', 'http://greg-pc.atrc.utoronto.ca::8080/services/CmapWebService');

/*******
 * create a side menu box/stack.
 */
//$this->_stacks['cmap'] = array('title_var'=>'cmap', 'file'=>'mods/cmap/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('cmap', array('title_var' => 'cmap', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_student_tool = 'mods/cmap/index.php';
// ** possible alternative: **
// $this->addTool('./index.php');

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_CMAP, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/cmap/index_admin.php');
	$this->_pages['mods/cmap/index_admin.php']['title_var'] = 'cmap';
	$this->_pages['mods/cmap/index_admin.php']['parent']    = AT_NAV_ADMIN;
}

/*******
 * instructor Manage section:
 */
$this->_pages['mods/cmap/index_instructor.php']['title_var'] = 'cmap';
$this->_pages['mods/cmap/index_instructor.php']['parent']   = 'tools/index.php';
// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'cmap';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages['mods/cmap/index.php']['title_var'] = 'cmap';
$this->_pages['mods/cmap/index.php']['img']       = 'mods/cmap/cmap_logo.jpg';


/* public pages */
// $this->_pages[AT_NAV_PUBLIC] = array('mods/cmap/index_public.php');
// $this->_pages['mods/cmap/index_public.php']['title_var'] = 'cmap';
// $this->_pages['mods/cmap/index_public.php']['parent'] = 'login.php';
// $this->_pages['login.php']['children'] = array('mods/cmap/index_public.php');

/* my start page pages */
// $this->_pages[AT_NAV_START]  = array('mods/cmap/index_mystart.php');
// $this->_pages['mods/cmap/index_mystart.php']['title_var'] = 'cmap';
// $this->_pages['mods/cmap/index_mystart.php']['parent'] = 'users/index.php';
// $this->_pages['users/index.php']['children'] = array('mods/cmap/index_mystart.php');
?>