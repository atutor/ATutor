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
define('AT_PRIV_GOOGLE_CALENDAR',       $this->getPrivilege());

/*******
 * create a side menu box/stack.
 */
$menu_path =AT_INCLUDE_PATH.'../mods/google_calendar/side_menu.inc.php';

$this->_stacks['google_calendar'] = array('title_var'=>'google_calendar', 'file'=>''.$menu_path.'');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_group_tool = $_student_tool = 'mods/google_calendar/index.php';


/*******
 * instructor Manage section:
 */
$this->_pages['mods/google_calendar/index_instructor.php']['title_var'] = 'google_calendar';
$this->_pages['mods/google_calendar/index_instructor.php']['parent']   = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages['mods/google_calendar/index.php']['title_var'] = 'google_calendar';
$this->_pages['mods/google_calendar/index.php']['img']       = 'mods/google_calendar/google_cal.gif';

/* my start page pages */
$this->_pages[AT_NAV_START]  = array('mods/google_calendar/index_mystart.php');
$this->_pages['mods/google_calendar/index_mystart.php']['title_var'] = 'google_calendar';
$this->_pages['mods/google_calendar/index_mystart.php']['parent'] = AT_NAV_START;

function google_calendar_get_group_url($group_id) {
	return 'mods/google_calendar/index.php';
}
?>