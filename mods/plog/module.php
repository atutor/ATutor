<?php
/*
This is the main pLog blog module file for ATutor. See the README_ATUTOR_MODULE for
documentation
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
define('AT_PRIV_PLOG',       $this->getPrivilege());
define('AT_ADMIN_PRIV_PLOG', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
$this->_stacks['plog'] = array('title_var'=>'plog', 'file'=>dirname(__FILE__).'/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('hello_world', array('title_var' => 'hello_world', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */

$_student_tool = 'mods/plog/index.php';

// ** possible alternative: **
// $this->addTool('./index.php');

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_PLOG, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/plog/index_admin.php');
	$this->_pages['mods/plog/index_admin.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['mods/plog/index_admin.php']['title_var'] = 'plog';
	$this->_pages['mods/plog/admin_plog.php']['title_var'] = 'plog_admin';
	$this->_pages['mods/plog/index_admin.php']['children'] = array('mods/plog/admin_plog.php');
	$this->_pages['mods/plog/admin_plog.php']['parent']    = 'mods/plog/index_admin.php';

}

/*******
 * instructor Manage section:
 */
$this->_pages['mods/plog/index_instructor.php']['title_var'] = 'plog';
$this->_pages['mods/plog/index_instructor.php']['parent']   = 'tools/index.php';

// Instructor and student plogs are the same, so grant access to sync dabatases only if the user is the course instructor.
/*
if($_SESSION['is_admin']){
	$this->_pages['mods/plog/sync_plog.php']['title_var'] = 'plog_sync_dbs';
	$this->_pages['mods/plog/index_instructor.php']['children'] = array('mods/plog/sync_plog.php');
	$this->_pages['mods/plog/sync_plog.php']['parent']    = 'mods/plog/index_instructor.php';
}*/
// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'hello_world';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages['mods/plog/index.php']['title_var'] = 'plog';
$this->_pages['mods/plog/index.php']['img']       = 'mods/plog/plog_logo.gif';

// If pLog was installed with something other than the default database table prefix "plog_"
// adjust this setting below
define('PLOG_PREFIX', "plog_");



?>