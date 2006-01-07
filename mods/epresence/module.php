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
define('AT_PRIV_EPRESENCE', $this->getPrivilege());
define('AT_ADMIN_PRIV_EPRESENCE', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
//$this->_stacks['epresence'] = array('title_var'=>'epresence', 'file'=>'mods/epresence/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('epresence', array('title_var' => 'epresence', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_student_tool = 'mods/epresence/index.php';
// ** possible alternative: **
// $this->addTool('./index.php');

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_EPRESENCE, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/epresence/index_admin.php');
	$this->_pages['mods/epresence/index_admin.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['mods/epresence/index_admin.php']['title_var'] = 'epresence';
}
/*******
 * instructor Manage section: **Not needed for now**
 */
$this->_pages['mods/epresence/index_instructor.php']['title_var'] = 'epresence';
$this->_pages['mods/epresence/index_instructor.php']['parent']   = 'tools/index.php';
// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'epresence';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';  

/*******
 * student page.
 */
$this->_pages['mods/epresence/index.php']['title_var'] = 'epresence';
$this->_pages['mods/epresence/index.php']['img']       = 'mods/epresence/epresence_logo.gif';
?>