<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Inclusive Design Institute                                   */
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
require(AT_INCLUDE_PATH.'../mods/_standard/social/lib/constants.inc.php');	//load constant file right away.
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
$this->_stacks['social'] = array('title_var'=>'social', 'file'=>AT_INCLUDE_PATH.'../mods/_standard/social/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('social', array('title_var' => 'social', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_group_tool = $_student_tool = AT_SOCIAL_BASENAME.AT_SOCIAL_INDEX;

$this->_list['social'] = array('title_var'=>'social','file'=>'mods/_standard/social/sublinks.php');
$this->_pages[AT_SOCIAL_BASENAME.'index.php']['icon']      = 'images/home-directory_sm.png';

/*******
 * add the admin pages when needed.
 */

if (admin_authenticate(AT_ADMIN_PRIV_SOCIAL, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	//$this->_pages[AT_NAV_ADMIN] = array('mods/_standard/social/index_admin.php');
	$this->_pages[AT_SOCIAL_BASENAME.'index_admin.php']['title_var'] = 'social';
	$this->_pages[AT_SOCIAL_BASENAME.'index_admin.php']['parent']    = 'admin/config_edit.php';
	$this->_pages[AT_SOCIAL_BASENAME.'index_admin.php']['children']    = array(AT_SOCIAL_BASENAME.'admin/delete_applications.php');

		$this->_pages[AT_SOCIAL_BASENAME.'admin/delete_applications.php']['title_var'] = 'delete_applications';
		$this->_pages[AT_SOCIAL_BASENAME.'admin/delete_applications.php']['parent'] = AT_SOCIAL_BASENAME.'index_admin.php';
}


/*******
 * instructor Manage section:
 */
//$this->_pages[AT_SOCIAL_BASENAME.'index_instructor.php']['title_var'] = 'social';
//$this->_pages[AT_SOCIAL_BASENAME.'index_instructor.php']['parent']   = 'tools/index.php';

// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'social';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages[AT_SOCIAL_BASENAME.AT_SOCIAL_INDEX]['title_var'] = 'social';
$this->_pages[AT_SOCIAL_BASENAME.AT_SOCIAL_INDEX]['img']       = AT_SOCIAL_BASENAME.'images/social.gif';
$this->_pages[AT_SOCIAL_BASENAME.AT_SOCIAL_INDEX]['children'] = array_merge(
array(AT_SOCIAL_BASENAME.'connections.php',
AT_SOCIAL_BASENAME.'sprofile.php', 
AT_SOCIAL_BASENAME.'applications.php', 
AT_SOCIAL_BASENAME.'groups/index.php',
AT_SOCIAL_BASENAME.'settings.php'),
isset($_pages[AT_SOCIAL_BASENAME.AT_SOCIAL_INDEX]['children']) ? $_pages[AT_SOCIAL_BASENAME.AT_SOCIAL_INDEX]['children'] : array());

$this->_pages[AT_SOCIAL_BASENAME.'sprofile.php']['title_var'] = 'social_profile';
$this->_pages[AT_SOCIAL_BASENAME.'sprofile.php']['parent'] = AT_SOCIAL_BASENAME.AT_SOCIAL_INDEX;

$this->_pages[AT_SOCIAL_BASENAME.'edit_profile.php']['title_var'] = 'edit_profile';
$this->_pages[AT_SOCIAL_BASENAME.'edit_profile.php']['parent'] = AT_SOCIAL_BASENAME.'sprofile.php';
$this->_pages[AT_SOCIAL_BASENAME.'sprofile.php']['guide']     = 'general/?p=my_profile.php';

$this->_pages[AT_SOCIAL_BASENAME.'profile_picture.php']['title_var'] = 'picture';
$this->_pages[AT_SOCIAL_BASENAME.'profile_picture.php']['parent'] = AT_SOCIAL_BASENAME.'edit_profile.php';

$this->_pages[AT_SOCIAL_BASENAME.'basic_profile.php']['title_var'] = 'profile';
$this->_pages[AT_SOCIAL_BASENAME.'basic_profile.php']['parent'] = AT_SOCIAL_BASENAME.'edit_profile.php';

$this->_pages[AT_SOCIAL_BASENAME.'applications.php']['title_var'] = 'applications';
$this->_pages[AT_SOCIAL_BASENAME.'applications.php']['parent'] = AT_SOCIAL_BASENAME.AT_SOCIAL_INDEX;
$this->_pages[AT_SOCIAL_BASENAME.'applications.php']['guide']     = 'general/?p=my_gadgets.php';

$this->_pages[AT_SOCIAL_BASENAME.'connections.php']['title_var'] = 'connections';
$this->_pages[AT_SOCIAL_BASENAME.'connections.php']['parent'] = AT_SOCIAL_BASENAME.AT_SOCIAL_INDEX;
$this->_pages[AT_SOCIAL_BASENAME.'connections.php']['guide']     = 'general/?p=my_contacts.php';
//	$this->_pages['mods/social/add_friends.php']['title_var'] = 'add_friends';
//	$this->_pages['mods/social/add_friends.php']['parent'] = 'mods/social/connections.php';

$this->_pages[AT_SOCIAL_BASENAME.'activities.php']['title_var'] = 'activities';
$this->_pages[AT_SOCIAL_BASENAME.'activities.php']['parent'] = AT_SOCIAL_BASENAME.AT_SOCIAL_INDEX;

$this->_pages[AT_SOCIAL_BASENAME.'settings.php']['title_var'] = 'settings';
$this->_pages[AT_SOCIAL_BASENAME.'settings.php']['parent'] = AT_SOCIAL_BASENAME.AT_SOCIAL_INDEX;
$this->_pages[AT_SOCIAL_BASENAME.'settings.php']['guide']     = 'general/?p=my_settings.php';

$this->_pages[AT_SOCIAL_BASENAME.'groups/index.php']['title_var'] = 'social_groups';
$this->_pages[AT_SOCIAL_BASENAME.'groups/index.php']['parent'] = AT_SOCIAL_BASENAME.AT_SOCIAL_INDEX;
$this->_pages[AT_SOCIAL_BASENAME.'groups/index.php']['guide']     = 'general/?p=my_groups.php';

$this->_pages[AT_SOCIAL_BASENAME.'groups/create.php']['title_var'] = 'create_groups';
$this->_pages[AT_SOCIAL_BASENAME.'groups/create.php']['parent'] = AT_SOCIAL_BASENAME.'groups/index.php';

$this->_pages[AT_SOCIAL_BASENAME.'groups/view.php']['title_var'] = 'view_groups';
$this->_pages[AT_SOCIAL_BASENAME.'groups/view.php']['parent'] = AT_SOCIAL_BASENAME.'groups/index.php';
	$this->_pages[AT_SOCIAL_BASENAME.'groups/delete_message.php']['title_var'] = 'delete_message';
	$this->_pages[AT_SOCIAL_BASENAME.'groups/delete_message.php']['parent'] = AT_SOCIAL_BASENAME.'groups/view.php';

$this->_pages[AT_SOCIAL_BASENAME.'groups/invite.php']['title_var'] = 'invite_groups';
$this->_pages[AT_SOCIAL_BASENAME.'groups/invite.php']['parent'] = AT_SOCIAL_BASENAME.'groups/index.php';

$this->_pages[AT_SOCIAL_BASENAME.'groups/edit.php']['title_var'] = 'edit_group';
$this->_pages[AT_SOCIAL_BASENAME.'groups/edit.php']['parent'] = AT_SOCIAL_BASENAME.'groups/index.php';

$this->_pages[AT_SOCIAL_BASENAME.'groups/search.php']['title_var'] = 'search';
$this->_pages[AT_SOCIAL_BASENAME.'groups/search.php']['parent'] = AT_SOCIAL_BASENAME.'groups/index.php';

$this->_pages[AT_SOCIAL_BASENAME.'groups/list.php']['title_var'] = 'group_members';
$this->_pages[AT_SOCIAL_BASENAME.'groups/list.php']['parent'] = AT_SOCIAL_BASENAME.'groups/index.php';

/* public pages */
$this->_pages[AT_NAV_PUBLIC] = array(AT_SOCIAL_BASENAME.'index_public.php');
$this->_pages[AT_SOCIAL_BASENAME.'index_public.php']['title_var'] = 'social';
$this->_pages[AT_SOCIAL_BASENAME.'index_public.php']['parent'] = AT_NAV_PUBLIC;

/* my start page pages */
if ($_SESSION['valid_user']==1){
$this->_pages[AT_NAV_START]  = array('mods/social/index_mystart.php');
$this->_pages[AT_NAV_START]  = array(AT_SOCIAL_BASENAME.'index_mystart.php');
$this->_pages[AT_SOCIAL_BASENAME.'index_mystart.php']['title_var'] = 'social';
$this->_pages[AT_SOCIAL_BASENAME.'index_mystart.php']['guide']     = 'general/?p=my_network.php';
$this->_pages[AT_SOCIAL_BASENAME.'index_mystart.php']['parent'] = AT_NAV_START;

$this->_pages[AT_SOCIAL_BASENAME.'index_mystart.php']['children'] = array_merge(
array(AT_SOCIAL_BASENAME.'connections.php',
AT_SOCIAL_BASENAME.'sprofile.php', 
AT_SOCIAL_BASENAME.'applications.php', 
AT_SOCIAL_BASENAME.'groups/index.php',
AT_SOCIAL_BASENAME.'settings.php'),
isset($_pages[AT_SOCIAL_BASENAME.'index_mystart.php']['children']) ? $_pages[AT_SOCIAL_BASENAME.'index_mystart.php']['children'] : array());

$this->_pages[AT_SOCIAL_BASENAME.'index.php']['title_var'] = 'social';
$this->_pages[AT_SOCIAL_BASENAME.'index.php']['guide']     = 'general/?p=my_network.php';
}

function social_get_group_url($group_id) {
	return AT_SOCIAL_BASENAME.AT_SOCIAL_INDEX;
}
?>