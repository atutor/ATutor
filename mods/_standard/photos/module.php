<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$

/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/*******
 * add savant variable
 */
global $savant;
require(AT_INCLUDE_PATH.'../mods/_standard/photos/include/constants.inc.php');	//load constant file right away.
//$savant->addPath('template', AT_PA_INCLUDE.'html/');

/******
 * this file must only be included within a Module obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_PHOTO_ALBUM',       $this->getPrivilege());
define('AT_ADMIN_PRIV_PHOTO_ALBUM', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
//$this->_stacks['pa_photo_gallery'] = array('title_var'=>'pa_photo_gallery', 'file'=>AT_PA_BASE.'side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('social', array('title_var' => 'social', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
//$_group_tool = $_student_tool = AT_PA_BASENAME.'index.php';
$_student_tool = AT_PA_BASENAME.'index.php';
$this->_list['pa_photo_gallery'] = array('title_var'=>'pa_photo_gallery','file'=>AT_PA_BASENAME.'sublinks.php');
$this->_pages[AT_PA_BASENAME.'index.php']['icon']      = 'images/home-directory_sm.png';

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_PHOTO_ALBUM, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array(AT_PA_BASENAME.'index_admin.php');
	$this->_pages[AT_PA_BASENAME.'index_admin.php']['title_var'] = 'pa_photo_gallery';
	$this->_pages[AT_PA_BASENAME.'index_admin.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages[AT_PA_BASENAME.'index_admin.php']['children']    = array(AT_PA_BASENAME.'admin/preferences.php');
		$this->_pages[AT_PA_BASENAME.'admin/preferences.php']['title_var'] = 'pa_preferences';
		$this->_pages[AT_PA_BASENAME.'admin/preferences.php']['parent'] = AT_PA_BASENAME.'index_admin.php';

		$this->_pages[AT_PA_BASENAME.'admin/edit_album.php']['title_var'] = 'pa_edit_album';
		$this->_pages[AT_PA_BASENAME.'admin/edit_album.php']['parent'] = AT_PA_BASENAME.'index_admin.php';

		$this->_pages[AT_PA_BASENAME.'admin/edit_photos.php']['title_var'] = 'pa_edit_photos';
		$this->_pages[AT_PA_BASENAME.'admin/edit_photos.php']['parent'] = AT_PA_BASENAME.'index_admin.php';
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
//Temp _pages 
$this->_pages[AT_SOCIAL_BASENAME.'index.php']['children'] = array(AT_PA_BASENAME.'index.php');
//end temp

$this->_pages[AT_PA_BASENAME.'index.php']['title_var'] = _AT('test');
$this->_pages[AT_PA_BASENAME.'index.php']['parent'] = AT_SOCIAL_BASENAME.'index.php';
//$this->_pages[AT_SOCIAL_BASENAME.'index_mystart.php']['children'] = array_push($this->_pages[AT_SOCIAL_BASENAME.'index_mystart.php']['children'], AT_PA_BASENAME.'index.php');


$this->_pages[AT_PA_BASENAME.'index.php']['title_var'] = 'pa_photo_gallery';
$this->_pages[AT_PA_BASENAME.'index.php']['img']       = AT_PA_BASENAME.'images/photo_gallery.png';

if(isset($_SESSION['course_id']) && $_SESSION['course_id'] < 1){
	$this->_pages[AT_PA_BASENAME.'index.php']['children'] = array(AT_PA_BASENAME.'profile_album.php', AT_PA_BASENAME.'create_album.php');
}else{
	$this->_pages[AT_PA_BASENAME.'index.php']['children'] = array(AT_PA_BASENAME.'profile_album.php', AT_PA_BASENAME.'course_albums.php', AT_PA_BASENAME.'shared_albums.php', AT_PA_BASENAME.'create_album.php');
	$this->_pages[AT_PA_BASENAME.'course_albums.php']['title_var'] = 'pa_course_albums';
	$this->_pages[AT_PA_BASENAME.'course_albums.php']['parent'] = AT_PA_BASENAME.'index.php';
	$this->_pages[AT_PA_BASENAME.'course_albums.php']['guide']     = 'general/?p=pa_index.php';
}

$this->_pages[AT_PA_BASENAME.'index.php']['guide']     = 'general/?p=pa_index.php';
$this->_pages[AT_PA_BASENAME.'index.php']['parent'] = AT_SOCIAL_BASENAME.'index_mystart.php';
$this->_pages[AT_SOCIAL_BASENAME.'index_mystart.php']['children'] = array_merge(isset($this->_pages[AT_SOCIAL_BASENAME.'index_mystart.php']['children']) ? $this->_pages[AT_SOCIAL_BASENAME.'index_mystart.php']['children'] : array(), array(AT_PA_BASENAME.'index.php'));

//$this->_pages[AT_PA_BASENAME.'my_albums.php']['title_var'] = 'pa_my_albums';
//$this->_pages[AT_PA_BASENAME.'my_albums.php']['parent'] = AT_PA_BASENAME.'index.php';

$this->_pages[AT_PA_BASENAME.'create_album.php']['title_var'] = 'pa_create_album';
$this->_pages[AT_PA_BASENAME.'create_album.php']['parent'] = AT_PA_BASENAME.'index.php';
$this->_pages[AT_PA_BASENAME.'create_album.php']['guide']     = 'general/?p=pa_index.php';

$this->_pages[AT_PA_BASENAME.'profile_album.php']['title_var'] = 'pa_profile_album';
$this->_pages[AT_PA_BASENAME.'profile_album.php']['parent'] = AT_PA_BASENAME.'index.php';
$this->_pages[AT_PA_BASENAME.'profile_album.php']['guide']     = 'general/?p=pa_albums.php';

$this->_pages[AT_PA_BASENAME.'shared_albums.php']['title_var'] = 'pa_shared_albums';
$this->_pages[AT_PA_BASENAME.'shared_albums.php']['parent'] = AT_PA_BASENAME.'index.php';
$this->_pages[AT_PA_BASENAME.'shared_albums.php']['guide']     = 'general/?p=pa_albums.php';

$this->_pages[AT_PA_BASENAME.'search.php']['title_var'] = 'search';
$this->_pages[AT_PA_BASENAME.'search.php']['parent'] = AT_PA_BASENAME.'index.php';
$this->_pages[AT_PA_BASENAME.'search.php']['guide']     = 'general/?p=pa_albums.php';

$this->_pages[AT_PA_BASENAME.'edit_album.php']['title_var'] = 'pa_edit_album';
$this->_pages[AT_PA_BASENAME.'edit_album.php']['parent'] = AT_PA_BASENAME.'index.php';
$this->_pages[AT_PA_BASENAME.'edit_album.php']['guide']     = 'general/?p=pa_albums.php';

$this->_pages[AT_PA_BASENAME.'delete_album.php']['title_var'] = 'pa_delete_album';
$this->_pages[AT_PA_BASENAME.'delete_album.php']['parent'] = AT_PA_BASENAME.'index.php';

$this->_pages[AT_PA_BASENAME.'albums.php']['title_var'] = 'pa_albums';
$this->_pages[AT_PA_BASENAME.'albums.php']['parent'] = AT_PA_BASENAME.'index.php';
$this->_pages[AT_PA_BASENAME.'albums.php']['guide']     = 'general/?p=pa_albums.php';
//$this->_pages[AT_PA_BASENAME.'albums.php']['children']  = array(AT_PA_BASENAME.'photo.php');

$this->_pages[AT_PA_BASENAME.'photo.php']['title_var'] = 'pa_photo';
$this->_pages[AT_PA_BASENAME.'photo.php']['parent'] = AT_PA_BASENAME.'albums.php';
$this->_pages[AT_PA_BASENAME.'photo.php']['guide']     = 'general/?p=pa_photo.php';
	$this->_pages[AT_PA_BASENAME.'delete_photo.php']['title_var'] = 'pa_delete_photo';
	$this->_pages[AT_PA_BASENAME.'delete_photo.php']['parent'] = AT_PA_BASENAME.'photo.php';

$this->_pages[AT_PA_BASENAME.'edit_photos.php']['title_var'] = 'pa_edit_photos';
$this->_pages[AT_PA_BASENAME.'edit_photos.php']['parent'] = AT_PA_BASENAME.'albums.php';
$this->_pages[AT_PA_BASENAME.'edit_photos.php']['guide']     = 'general/?p=pa_albums.php';

$this->_pages[AT_PA_BASENAME.'delete_comment.php']['title_var'] = 'pa_delete_comment';
$this->_pages[AT_PA_BASENAME.'delete_comment.php']['parent'] = AT_PA_BASENAME.'photo.php';

?>
