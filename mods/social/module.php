<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

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
$this->_stacks['social'] = array('title_var'=>'social', 'file'=>AT_INCLUDE_PATH.'../mods/social/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('social', array('title_var' => 'social', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_group_tool = $_student_tool = 'mods/social/index.php';

/*******
 * add the admin pages when needed.
 */

if (admin_authenticate(AT_ADMIN_PRIV_SOCIAL, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/social/index_admin.php');
	$this->_pages['mods/social/index_admin.php']['title_var'] = 'social';
	$this->_pages['mods/social/index_admin.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['mods/social/index_admin.php']['children']    = array('mods/social/admin/delete_applications.php');

		$this->_pages['mods/social/admin/delete_applications.php']['title_var'] = 'delete_applications';
		$this->_pages['mods/social/admin/delete_applications.php']['parent'] = 'mods/social/index_admin.php';
}


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

$this->_pages['mods/social/profile_picture.php']['title_var'] = 'picture';
$this->_pages['mods/social/profile_picture.php']['parent'] = 'mods/social/edit_profile.php';

$this->_pages['mods/social/basic_profile.php']['title_var'] = 'profile';
$this->_pages['mods/social/basic_profile.php']['parent'] = 'mods/social/edit_profile.php';

$this->_pages['mods/social/applications.php']['title_var'] = 'gadgets';
$this->_pages['mods/social/applications.php']['parent'] = 'mods/social/index.php';

$this->_pages['mods/social/connections.php']['title_var'] = 'connections';
$this->_pages['mods/social/connections.php']['parent'] = 'mods/social/index.php';
//	$this->_pages['mods/social/add_friends.php']['title_var'] = 'add_friends';
//	$this->_pages['mods/social/add_friends.php']['parent'] = 'mods/social/connections.php';

$this->_pages['mods/social/activities.php']['title_var'] = 'activities';
$this->_pages['mods/social/activities.php']['parent'] = 'mods/social/index.php';

$this->_pages['mods/social/settings.php']['title_var'] = 'settings';
$this->_pages['mods/social/settings.php']['parent'] = 'mods/social/index.php';

$this->_pages['mods/social/groups/index.php']['title_var'] = 'social_groups';
$this->_pages['mods/social/groups/index.php']['parent'] = 'mods/social/index.php';

$this->_pages['mods/social/groups/create.php']['title_var'] = 'create_groups';
$this->_pages['mods/social/groups/create.php']['parent'] = 'mods/social/groups/index.php';

$this->_pages['mods/social/groups/view.php']['title_var'] = 'view_groups';
$this->_pages['mods/social/groups/view.php']['parent'] = 'mods/social/groups/index.php';

$this->_pages['mods/social/groups/invite.php']['title_var'] = 'invite_groups';
$this->_pages['mods/social/groups/invite.php']['parent'] = 'mods/social/groups/index.php';

$this->_pages['mods/social/groups/edit.php']['title_var'] = 'edit_group';
$this->_pages['mods/social/groups/edit.php']['parent'] = 'mods/social/groups/index.php';

$this->_pages['mods/social/groups/search.php']['title_var'] = 'search';
$this->_pages['mods/social/groups/search.php']['parent'] = 'mods/social/groups/index.php';

$this->_pages['mods/social/groups/list.php']['title_var'] = 'group_members';
$this->_pages['mods/social/groups/list.php']['parent'] = 'mods/social/groups/index.php';

/* public pages */
$this->_pages[AT_NAV_PUBLIC] = array('mods/social/index_public.php');
$this->_pages['mods/social/index_public.php']['title_var'] = 'social';
$this->_pages['mods/social/index_public.php']['parent'] = AT_NAV_PUBLIC;


/* my start page pages */
if ($_SESSION['valid_user']==1){
//$this->_pages[AT_NAV_START]  = array('mods/social/index_mystart.php');
$this->_pages[AT_NAV_START]  = array('mods/social/index.php');
$this->_pages['mods/social/index.php']['title_var'] = 'social';
$this->_pages['mods/social/index.php']['parent'] = AT_NAV_START;
}

function social_get_group_url($group_id) {
	return 'mods/social/index.php';
}
?>