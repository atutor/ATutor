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
define('AT_PRIV_ASSIGNMENTS', $this->getPrivilege());

/*******
 * create a side menu box/stack.
 */
//$this->_stacks['hello_world'] = array('title_var'=>'hello_world', 'file'=>'hello_world/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('hello_world', array('title_var' => 'hello_world', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_student_tool = 'assignments/index.php';
// ** possible alternative: **
// $this->addTool('./index.php');

/*******
 * add the admin pages when needed.
 */
/*
if (admin_authenticate(AT_ADMIN_PRIV_HELLO_WORLD, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('hello_world/index_admin.php');
	$this->_pages['hello_world/index_admin.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['hello_world/index_admin.php']['title_var'] = 'hello_world';
}
*/
/*******
 * instructor Manage section:
 */
$this->_pages['assignments/index_instructor.php']['title_var'] = 'assignments';
$this->_pages['assignments/index_instructor.php']['parent']   = 'tools/index.php';
$this->_pages['assignments/index_instructor.php']['children'] = array('assignments/add_assignment.php');

	$this->_pages['assignments/add_assignment.php']['title_var'] = 'add_assignment';
	$this->_pages['assignments/add_assignment.php']['parent']    = 'assignments/index_instructor.php';

	$this->_pages['assignments/edit_assignment.php']['title_var'] = 'edit_assignment';
	$this->_pages['assignments/edit_assignment.php']['parent']    = 'assignments/index_instructor.php';

	$this->_pages['assignments/delete_assignment.php']['title_var'] = 'delete_assignment';
	$this->_pages['assignments/delete_assignment.php']['parent']    = 'assignments/index_instructor.php';

/*******
 * student page.
 */
 /*
$this->_pages['assignments/index.php']['title_var'] = 'assignments';
$this->_pages['assignments/index.php']['img']       = 'assignments/assignments.gif';

$this->_pages['assignments/index.php']['children'] = array('assignments/assignment_details.php');

	$this->_pages['assignments/assignment_details.php']['title_var'] = 'am_display_assignments';
	$this->_pages['assignments/assignment_details.php']['parent']    = 'assignments/index.php';
*/
?>