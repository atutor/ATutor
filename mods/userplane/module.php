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

define('AT_ADMIN_PRIV_USERPLANE', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
$this->_stacks['userplane'] = array('title_var'=>'userplane', 'file'=>AT_INCLUDE_PATH.'../mods/userplane/side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_student_tool = 'mods/userplane/index.php';

/*******
 * add the admin page so the Userplane ID can be managed
 */

if (admin_authenticate(AT_ADMIN_PRIV_USERPLANE, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/userplane/index_admin.php');
	$this->_pages['mods/userplane/index_admin.php']['title_var'] = 'userplane';
	$this->_pages['mods/userplane/index_admin.php']['parent']    = AT_NAV_ADMIN;
}

/*******
 * student page.
 */
$this->_pages['mods/userplane/index.php']['title_var'] = 'userplane';
$this->_pages['mods/userplane/index.php']['img']       = 'mods/userplane/userplane.jpg';

// You may use the ATutor community ID (e2405db9bcd4f802ffed98a4d1a15ac3) but you should register your own
// at http://www.userplane.com/buy/index.cfm

?>