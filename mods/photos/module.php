<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institution  */
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
require(AT_INCLUDE_PATH.'../mods/photo_album/include/constants.inc.php');	//load constant file right away.
$savant->addPath('template', AT_PA_INCLUDE.'html/');

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
$this->_stacks['photo_album'] = array('title_var'=>'photo_album', 'file'=>AT_INCLUDE_PATH.'../mods/photo_album/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('social', array('title_var' => 'social', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_group_tool = $_student_tool = AT_PA_BASENAME.'index.php';

$this->_list['photo_album'] = array('title_var'=>'photo_album','file'=>AT_PA_BASE.'sublinks.php');
$this->_pages[AT_PA_BASENAME.'index.php']['icon']      = 'images/home-directory_sm.png';

/*******
 * add the admin pages when needed.
 */
/*
if (admin_authenticate(AT_ADMIN_PRIV_SOCIAL, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/_standard/social/index_admin.php');
	$this->_pages[AT_SOCIAL_BASENAME.'index_admin.php']['title_var'] = 'social';
	$this->_pages[AT_SOCIAL_BASENAME.'index_admin.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages[AT_SOCIAL_BASENAME.'index_admin.php']['children']    = array(AT_SOCIAL_BASENAME.'admin/delete_applications.php');

		$this->_pages[AT_SOCIAL_BASENAME.'admin/delete_applications.php']['title_var'] = 'delete_applications';
		$this->_pages[AT_SOCIAL_BASENAME.'admin/delete_applications.php']['parent'] = AT_SOCIAL_BASENAME.'index_admin.php';
}
*/

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
$this->_pages[AT_PA_BASENAME.'index.php']['title_var'] = 'photo_album';
$this->_pages[AT_PA_BASENAME.'index.php']['img']       = AT_PA_BASENAME.'images/photo_album.gif';

$this->_pages[AT_PA_BASENAME.'create_album.php']['title_var'] = 'create_album';
$this->_pages[AT_PA_BASENAME.'create_album.php']['parent'] = AT_PA_BASENAME.'index.php';
$this->_pages[AT_PA_BASENAME.'create_album.php']['guide']     = 'general/?p=photo_album.php';

$this->_pages[AT_PA_BASENAME.'edit_album.php']['title_var'] = 'edit_album';
$this->_pages[AT_PA_BASENAME.'edit_album.php']['parent'] = AT_PA_BASENAME.'index.php';
$this->_pages[AT_PA_BASENAME.'edit_album.php']['guide']     = 'general/?p=photo_album.php';

$this->_pages[AT_PA_BASENAME.'delete_album.php']['title_var'] = 'delete_album';
$this->_pages[AT_PA_BASENAME.'delete_album.php']['parent'] = AT_PA_BASENAME.'index.php';
$this->_pages[AT_PA_BASENAME.'delete_album.php']['guide']     = 'general/?p=photo_album.php';

$this->_pages[AT_PA_BASENAME.'albums.php']['title_var'] = 'albums';
$this->_pages[AT_PA_BASENAME.'albums.php']['parent'] = AT_PA_BASENAME.'index.php';
$this->_pages[AT_PA_BASENAME.'albums.php']['guide']     = 'general/?p=photo_album.php';
//$this->_pages[AT_PA_BASENAME.'albums.php']['children']  = array(AT_PA_BASENAME.'photo.php');

$this->_pages[AT_PA_BASENAME.'photo.php']['title_var'] = 'photo';
$this->_pages[AT_PA_BASENAME.'photo.php']['parent'] = AT_PA_BASENAME.'albums.php';
$this->_pages[AT_PA_BASENAME.'photo.php']['guide']     = 'general/?p=photo_album.php';
	$this->_pages[AT_PA_BASENAME.'delete_photo.php']['title_var'] = 'delete_photo';
	$this->_pages[AT_PA_BASENAME.'delete_photo.php']['parent'] = AT_PA_BASENAME.'photo.php';
	$this->_pages[AT_PA_BASENAME.'delete_photo.php']['guide']     = 'general/?p=photo_album.php';


$this->_pages[AT_PA_BASENAME.'edit_photos.php']['title_var'] = 'edit_photos';
$this->_pages[AT_PA_BASENAME.'edit_photos.php']['parent'] = AT_PA_BASENAME.'albums.php';
$this->_pages[AT_PA_BASENAME.'edit_photos.php']['guide']     = 'general/?p=edit_photos.php';

$this->_pages[AT_PA_BASENAME.'delete_comment.php']['title_var'] = 'delete_comment';
$this->_pages[AT_PA_BASENAME.'delete_comment.php']['parent'] = AT_PA_BASENAME.'photo.php';
$this->_pages[AT_PA_BASENAME.'delete_comment.php']['guide']  = 'general/?p=photo_album.php';

?>
