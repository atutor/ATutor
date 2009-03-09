<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/*******
 * add savant variable
 */
global $savant;
define('AT_SOCIAL_BASE',		AT_INCLUDE_PATH.'../mods/social/');
define('AT_SOCIAL_INCLUDE',		AT_SOCIAL_BASE.'lib/');
$savant->addPath('template',	AT_SOCIAL_BASE.'html/');

/******
 * this file must only be included within a Module obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_SOCIAL',       $this->getPrivilege());
define('AT_ADMIN_PRIV_SOCIAL', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
$this->_stacks['social'] = array('title_var'=>'social', 'file'=>'mods/social/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('social', array('title_var' => 'social', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_group_tool = $_student_tool = 'mods/social/index.php';

/*******
 * add the admin pages when needed.
 */
/*
if (admin_authenticate(AT_ADMIN_PRIV_social, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/social/index_admin.php');
	$this->_pages['mods/social/index_admin.php']['title_var'] = 'social';
	$this->_pages['mods/social/index_admin.php']['parent']    = AT_NAV_ADMIN;
}
*/

/*******
 * instructor Manage section:
 */
$this->_pages['mods/social/index_instructor.php']['title_var'] = 'social';
$this->_pages['mods/social/index_instructor.php']['parent']   = 'tools/index.php';

// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'social';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages['mods/social/index.php']['title_var'] = 'social';
$this->_pages['mods/social/index.php']['img']       = 'mods/social/images/social.jpg';

$this->_pages['mods/social/sprofile.php']['title_var'] = 'social_profile';
$this->_pages['mods/social/sprofile.php']['parent'] = 'mods/social/index.php';

$this->_pages['mods/social/edit_profile.php']['title_var'] = 'edit_profile';
$this->_pages['mods/social/edit_profile.php']['parent'] = 'mods/social/sprofile.php';

$this->_pages['mods/social/applications.php']['title_var'] = 'social_profile';
$this->_pages['mods/social/applications.php']['parent'] = 'mods/social/index.php';

$this->_pages['mods/social/add_friends.php']['title_var'] = 'add_friends';
$this->_pages['mods/social/add_friends.php']['parent'] = 'mods/social/index.php';

$this->_pages['mods/social/connections.php']['title_var'] = 'connections';
$this->_pages['mods/social/connections.php']['parent'] = 'mods/social/index.php';

$this->_pages['mods/social/privacy_settings.php']['title_var'] = 'privacy_settings';
$this->_pages['mods/social/privacy_settings.php']['parent'] = 'mods/social/index.php';


/* public pages */
$this->_pages[AT_NAV_PUBLIC] = array('mods/social/index_public.php');
$this->_pages['mods/social/index_public.php']['title_var'] = 'social';
$this->_pages['mods/social/index_public.php']['parent'] = AT_NAV_PUBLIC;


/* my start page pages */
$this->_pages[AT_NAV_START]  = array('mods/social/index_mystart.php');
$this->_pages['mods/social/index_mystart.php']['title_var'] = 'social';
$this->_pages['mods/social/index_mystart.php']['parent'] = AT_NAV_START;


function social_get_group_url($group_id) {
	return 'mods/social/index.php';
}
?>