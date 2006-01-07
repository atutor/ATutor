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
define('AT_PRIV_CALENDAR',       $this->getPrivilege());
define('AT_ADMIN_PRIV_CALENDAR', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
$this->_stacks['calendar'] = array('title_var'=>'calendar', 'file'=>dirname(__FILE__).'/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('hello_world', array('title_var' => 'hello_world', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */

$_student_tool = 'mods/calendar/index.php';

// ** possible alternative: **
// $this->addTool('./index.php');

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_CALENDAR, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/calendar/index_admin.php');
	$this->_pages['mods/calendar/index_admin.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['mods/calendar/index_admin.php']['title_var'] = 'webcalendar';
	$this->_pages['mods/calendar/admin_cal.php']['title_var'] = 'admin_cal';
	$this->_pages['mods/calendar/index_admin.php']['children'] = array('mods/calendar/admin_cal.php');
	$this->_pages['mods/calendar/admin_cal.php']['parent']    = 'mods/calendar/index_admin.php';

}

/*******
 * instructor Manage section:
 */
$this->_pages['mods/calendar/index.php']['title_var'] = 'calendar';
$this->_pages['mods/calendar/index.php']['parent']   = 'tools/index.php';

// Instructor and student calendars are the same, so grant access to sync dabatases only if the user is the course instructor.

if($_SESSION['is_admin']){
	$this->_pages['mods/calendar/sync_cal.php']['title_var'] = 'webcalendar_sync_dbs';
	$this->_pages['mods/calendar/index.php']['children'] = array('mods/calendar/sync_cal.php');
	$this->_pages['mods/calendar/sync_cal.php']['parent']    = 'mods/calendar/index.php';
}
// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'hello_world';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages['mods/calendar/index.php']['title_var'] = 'calendar';
$this->_pages['mods/calendar/index.php']['img']       = 'mods/calendar/calendar.gif';

// }

?>