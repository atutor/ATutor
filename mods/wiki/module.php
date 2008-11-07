<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a ModuleProxy obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_EWIKI',       $this->getPrivilege());
define('AT_ADMIN_PRIV_EWIKI', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
//$_module_stacks['ewiki'] = array('title_var'=>'wiki', 'file'=>'mods/wiki/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('hello_world', array('title_var' => 'hello_world', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_student_tool = 'mods/wiki/index.php';
// ** possible alternative: **
// $this->addTool('./index.php');

/*******
 * add the admin pages when needed.
 */

/*
if (admin_authenticate(AT_ADMIN_PRIV_HELLO_WORLD, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$_module_pages[AT_NAV_ADMIN] = array('mods/wiki/index_admin.php');
	$_module_pages['mods/wiki/index_admin.php']['parent']    = AT_NAV_ADMIN;
	$_module_pages['mods/wiki/index_admin.php']['title_var'] = 'wiki';
}

*/

/*******
 * instructor Manage section:
 */
$this->_pages['mods/wiki/index.php']['title_var'] = 'wiki';
$this->_pages['mods/wiki/index.php']['parent']   = 'tools/index.php';
$this->_pages['mods/wiki/index.php']['img']       = 'mods/wiki/tlogo.png';


// if($_SESSION['status'] == 2){
// //instructor
// $this->_pages['mods/wiki/index.php']['title_var'] = 'wiki';
// $this->_pages['mods/wiki/index.php']['parent']   = 'tools/index.php';
// $this->_pages['mods/wiki/index.php']['img']       = 'mods/wiki/tlogo.png';
// }else{
// //student
// $this->_pages['mods/wiki/index.php']['title_var'] = 'wiki';
// $this->_pages['mods/wiki/index.php']['img']       = 'mods/wiki/tlogo.png';
// //$_module_pages['mods/wiki/index.php']['parent']   = 'tools/index.php';
// }
//$_module_pages['mods/wiki/index.php']['title_var'] = 'wiki';
//$_module_pages['mods/wiki/index.php']['parent']   = 'tools/index.php';
// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'hello_world';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';

/*******
 * student page.
 */
//$_module_pages['mods/wiki/index.php']['title_var'] = 'wiki';
//$_module_pages['mods/wiki/index.php']['img']       = 'mods/wiki/tlogo.png';

// $_module_pages['mods/wiki/index.php']['title_var'] = 'packages';
// $_module_pages['mods/wiki/index.php']['parent']    = 'mods/wiki/index.php';
// $_module_pages['mods/wiki/index.php']['children']  = array('tools/packages/import.php', 'tools/packages/delete.php', 'tools/packages/settings.php');

//$_module_pages['mods/wiki/page.php']['title'] = 'Edit This Page';
//$_module_pages['mods/wiki/page.php']['parent'] ='mods/wiki/index.php?page=';

	//$_module_pages['mods/wiki/index.php?page='.$_GET['page'].'']['parent']    = 'mods/wiki/index.php';
	//$_module_pages['mods/wiki/index.php']['children']  = array('./tools/wiki/index.php');

?>