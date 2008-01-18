<?php
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
define('AT_PRIV_PHPDOC',       $this->getPrivilege());
define('AT_ADMIN_PRIV_PHPDOC', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
//$this->_stacks['phpdoc'] = array('title_var'=>'phpdoc', 'file'=>'mods/phpdoc/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('phpdoc', array('title_var' => 'phpdoc', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
//$_group_tool = $_student_tool = 'mods/phpdoc/index.php';

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_PHPDOC, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/phpdoc/index_admin.php');
	$this->_pages['mods/phpdoc/PHPDoc/index.php']['title_var'] = 'phpdoc';
	$this->_pages['mods/phpdoc/index_admin.php']['title_var'] = 'phpdoc';
	$this->_pages['mods/phpdoc/index_admin.php']['parent']    = AT_NAV_ADMIN;
}

/*******
 * instructor Manage section:
 */
//$this->_pages['mods/phpdoc/index_instructor.php']['title_var'] = 'phpdoc';
//$this->_pages['mods/phpdoc/index_instructor.php']['parent']   = 'tools/index.php';
// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'phpdoc';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';

/*******
 * student page.
 */
//$this->_pages['mods/phpdoc/index.php']['title_var'] = 'phpdoc';
//$this->_pages['mods/phpdoc/index.php']['img']       = 'mods/phpdoc/phpdoc.jpg';


/* public pages */
//$this->_pages[AT_NAV_PUBLIC] = array('mods/phpdoc/index_public.php');
//$this->_pages['mods/phpdoc/index_public.php']['title_var'] = 'phpdoc';
//$this->_pages['mods/phpdoc/index_public.php']['parent'] = AT_NAV_PUBLIC;

/* my start page pages */
//$this->_pages[AT_NAV_START]  = array('mods/phpdoc/index_mystart.php');
//$this->_pages['mods/phpdoc/index_mystart.php']['title_var'] = 'phpdoc';
//$this->_pages['mods/phpdoc/index_mystart.php']['parent'] = AT_NAV_START;

//function phpdoc_get_group_url($group_id) {
//	return 'mods/phpdoc/index.php';
//}
?>