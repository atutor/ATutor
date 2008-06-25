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
define('AT_PRIV_OPENMEETINGS',       $this->getPrivilege());
define('AT_ADMIN_PRIV_OPENMEETINGS', $this->getAdminPrivilege());

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_group_tool = $_student_tool = 'mods/openmeetings/index.php';

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_OPENMEETINGS, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/openmeetings/openmeetings.php');
	$this->_pages['mods/openmeetings/openmeetings.php']['title_var'] = 'openmeetings';
	$this->_pages['mods/openmeetings/openmeetings.php']['parent']    = AT_NAV_ADMIN;
}

/*******
 * instructor Manage section:
 */
$this->_pages['mods/openmeetings/openmeetings_instructor.php']['title_var'] = 'openmeetings_course_meetings';
$this->_pages['mods/openmeetings/openmeetings_instructor.php']['parent']   = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages['mods/openmeetings/index.php']['title_var'] = 'openmeetings';
//$this->_pages['mods/openmeetings/index.php']['img']       = 'mods/openmeetings/openmeetings_logo.jpg';

$this->_pages['mods/openmeetings/view_meetings.php']['title_var'] = 'openmeetings_view_meetings';
$this->_pages['mods/openmeetings/view_meetings.php']['parent'] = 'mods/openmeetings/index.php';
$this->_pages['mods/openmeetings/add_group_meetings.php']['title_var'] = 'openmeetings_grp_meetings';
$this->_pages['mods/openmeetings/add_group_meetings.php']['parent'] = 'mods/openmeetings/index.php';
$this->_pages['mods/openmeetings/openmeetings_delete.php']['title_var'] = 'openmeetings_delete';
$this->_pages['mods/openmeetings/openmeetings_delete.php']['parent'] = 'mods/openmeetings/index.php';
$this->_pages['mods/openmeetings/openmeetings_group.php']['title_var'] = 'openmeetings_grp_meetings';
$this->_pages['mods/openmeetings/openmeetings_group.php']['parent'] = 'mods/openmeetings/index.php';

/*******
 * Group functions
 */
function openmeetings_get_group_url($group_id) {
	return 'mods/openmeetings/openmeetings_group.php?gid='.$group_id;
}
?>