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
define('AT_PRIV_ELLUMINATE',       $this->getPrivilege());
define('AT_ADMIN_PRIV_ELLUMINATE', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
$this->_stacks['elluminate'] = array('title_var'=>'elluminate', 'file'=>AT_INCLUDE_PATH.'../mods/elluminate/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('elluminate', array('title_var' => 'elluminate', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_student_tool = 'mods/elluminate/index.php';
// ** possible alternative: **
// $this->addTool('./index.php');

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_ELLUMINATE, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/elluminate/elluminate.php');
	$this->_pages['mods/elluminate/elluminate.php']['title_var'] = 'elluminate';
	$this->_pages['mods/elluminate/elluminate.php']['parent']    = AT_NAV_ADMIN;
}

/*******
 * student page.
 */
$this->_pages['mods/elluminate/index.php']['title_var'] = 'elluminate';
$this->_pages['mods/elluminate/index.php']['img']       = 'mods/elluminate/elluminate_logo.gif';


?>