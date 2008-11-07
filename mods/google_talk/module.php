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
define('AT_PRIV_GOOGLE_TALK',       $this->getPrivilege());
define('AT_ADMIN_PRIV_GOOGLE_TALK', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
#$this->_stacks['google_talk'] = array('title_var'=>'google_talk', 'file'=>'mods/google_talk/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('google_talk', array('title_var' => 'google_talk', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_group_tool = $_student_tool = 'mods/google_talk/index.php';

/*******
 * add the admin pages when needed.
 */
#if (admin_authenticate(AT_ADMIN_PRIV_google_talk, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
#	$this->_pages[AT_NAV_ADMIN] = array('mods/google_talk/index_admin.php');
#	$this->_pages['mods/google_talk/index_admin.php']['title_var'] = 'google_talk';
#	$this->_pages['mods/google_talk/index_admin.php']['parent']    = AT_NAV_ADMIN;
#}

/*******
 * instructor Manage section:
 */
$this->_pages['mods/google_talk/index_instructor.php']['title_var'] = 'google_talk';
$this->_pages['mods/google_talk/index_instructor.php']['parent']   = 'tools/index.php';
// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'google_talk';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages['mods/google_talk/index.php']['title_var'] = 'google_talk';
$this->_pages['mods/google_talk/index.php']['img']       = 'mods/google_talk/google_talk.png';


/* public pages */
#$this->_pages[AT_NAV_PUBLIC] = array('mods/google_talk/index_public.php');
#$this->_pages['mods/google_talk/index_public.php']['title_var'] = 'google_talk';
#$this->_pages['mods/google_talk/index_public.php']['parent'] = AT_NAV_PUBLIC;

/* my start page pages */
#$this->_pages[AT_NAV_START]  = array('mods/google_talk/index_mystart.php');
#$this->_pages['mods/google_talk/index_mystart.php']['title_var'] = 'google_talk';
#$this->_pages['mods/google_talk/index_mystart.php']['parent'] = AT_NAV_START;

function google_talk_get_group_url($group_id) {
	return 'mods/google_talk/index.php';
}
?>