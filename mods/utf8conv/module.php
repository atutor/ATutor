<?php
// test
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
define('AT_PRIV_UTF8CONV',       $this->getPrivilege());
define('AT_ADMIN_PRIV_UTF8CONV', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
//$this->_stacks['utf8conv'] = array('title_var'=>'utf8conv', 'file'=>'mods/utf8conv/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('utf8conv', array('title_var' => 'utf8conv', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
//$_group_tool = $_student_tool = 'mods/utf8conv/index.php';
$_student_tool = 'mods/utf8conv/index.php';

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_UTF8CONV, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/utf8conv/index_admin.php');
	$this->_pages['mods/utf8conv/index_admin.php']['title_var'] = 'utf8conv';
	$this->_pages['mods/utf8conv/index_admin.php']['parent']    = AT_NAV_ADMIN;
}

/*******
 * instructor Manage section:
 */
$this->_pages['mods/utf8conv/index_instructor.php']['title_var'] = 'utf8conv';
$this->_pages['mods/utf8conv/index_instructor.php']['parent']   = 'tools/index.php';
// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'utf8conv';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages['mods/utf8conv/index.php']['title_var'] = 'utf8conv';
$this->_pages['mods/utf8conv/index.php']['img']       = 'mods/utf8conv/utf8conv.jpg';


/* public pages */
//$this->_pages[AT_NAV_PUBLIC] = array('mods/utf8conv/index_public.php');
//$this->_pages['mods/utf8conv/index_public.php']['title_var'] = 'utf8conv';
//$this->_pages['mods/utf8conv/index_public.php']['parent'] = AT_NAV_PUBLIC;

/* my start page pages */
//$this->_pages[AT_NAV_START]  = array('mods/utf8conv/index_mystart.php');
//$this->_pages['mods/utf8conv/index_mystart.php']['title_var'] = 'utf8conv';
//$this->_pages['mods/utf8conv/index_mystart.php']['parent'] = AT_NAV_START;

function utf8conv_get_group_url($group_id) {
	return 'mods/utf8conv/index.php';
}
?>