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
$_user_location = 'public';
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
include (AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');	//clr_dir()
//include (AT_INCLUDE_PATH.'lib/filemanager.inc.php');	//clr_dir()
include (AT_PA_INCLUDE.'lib.inc.php');	//album_filepath
include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');

//validates if this is me/have the privilege to delete.
$pid = intval($_REQUEST['pid']);
$aid = intval($_REQUEST['aid']);

//_pages
$_pages[AT_PA_BASENAME.'albums.php?id='.$aid]['title']    = _AT('pa_albums');
$_pages[AT_PA_BASENAME.'albums.php?id='.$aid]['parent']   = AT_PA_BASENAME.'index.php';
//$_pages[AT_PA_BASENAME.'albums.php?id='.$aid]['children'] = array(AT_PA_BASENAME.'photo.php');
$_pages[AT_PA_BASENAME.'photo.php?pid='.$pid.SEP.'aid='.$aid]['title']    = _AT('pa_photo');
$_pages[AT_PA_BASENAME.'photo.php?pid='.$pid.SEP.'aid='.$aid]['parent']    = AT_PA_BASENAME.'albums.php?id='.$aid;
$_pages[AT_PA_BASENAME.'delete_photo.php']['parent']    = AT_PA_BASENAME.'photo.php?pid='.$pid.SEP.'aid='.$aid;


//init
$pa = new PhotoAlbum($aid);

if ($pid<1 || $aid <1){
	$msg->addError('PA_PHOTO_NOT_FOUND');	//no such picture
	header('Location: index.php');
	exit;
} elseif (!$pa->checkPhotoPriv($pid, $_SESSION['member_id']) && !$pa->checkAlbumPriv($_SESSION['member_id'])){
	$msg->addError('ACCESS_DENIED');
	header('Location: albums.php?id='.$aid);
	exit;
} 

if ($_POST['submit_no']) {
	$msg->addFeedback('CANCELLED');
	Header('Location: photo.php?aid='.$aid.SEP.'pid='.$pid);
	exit;
}

if ($_POST['submit_yes']) {
	//delete
	$pa->deletePhoto($pid);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: albums.php?id='.$aid);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$hidden_vars['pid'] = $pid;
$hidden_vars['aid'] = $aid;

$msg->addConfirm(array('PA_DELETE_PHOTO'), $hidden_vars);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>
