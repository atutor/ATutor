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
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');

//validates if this is me/have the privilege to delete.
$pid = intval($_REQUEST['pid']);
$aid = intval($_REQUEST['aid']);
$comment_id = intval($_REQUEST['comment_id']);

//_pages
$_pages[AT_PA_BASENAME.'albums.php?id='.$aid]['title']    = _AT('pa_albums');
$_pages[AT_PA_BASENAME.'albums.php?id='.$aid]['parent']   = AT_PA_BASENAME.'index.php';
//$_pages[AT_PA_BASENAME.'albums.php?id='.$aid]['children'] = array(AT_PA_BASENAME.'photo.php');
$_pages[AT_PA_BASENAME.'photo.php?pid='.$pid.SEP.'aid='.$aid]['title']    = _AT('pa_photo');
$_pages[AT_PA_BASENAME.'photo.php?pid='.$pid.SEP.'aid='.$aid]['parent']    = AT_PA_BASENAME.'albums.php?id='.$aid;
$_pages[AT_PA_BASENAME.'delete_comment.php']['parent']    = AT_PA_BASENAME.'photo.php?pid='.$pid.SEP.'aid='.$aid;

//init
$pa = new PhotoAlbum($aid);

if ($pid==0){
	//not a photo
	$isPhoto = false;
} else {
	$isPhoto = true;
}

//Check permission
//owner of comments and album owner can delete comments.
if (!$pa->checkCommentPriv($comment_id, $_SESSION['member_id'], $isPhoto) || 
	!$pa->checkAlbumPriv($_SESSION['member_id'])){
	$msg->addError('');	//TODO: nice try
	header('Location: index.php');
	exit;
}

if ($_POST['submit_no']) {
	$msg->addFeedback('CANCELLED');
	if ($isPhoto){
		header('Location: photo.php?pid='.$pid.SEP.'aid='.$aid);
	} else {
		header('Location: albums.php?id='.$aid);
	}
	exit;
}

if ($_POST['submit_yes']) {
	//delete
	if ($pid==0){
		//not a photo
		$pa->deleteComment($comment_id, false);
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		Header('Location: albums.php?id='.$aid);
		exit;
	} else {
		$pa->deleteComment($comment_id, true);
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		Header('Location: photo.php?pid='.$pid.SEP.'aid='.$aid);
		exit;
	}	
}

require(AT_INCLUDE_PATH.'header.inc.php');

$hidden_vars['comment_id'] = $comment_id;
$hidden_vars['aid'] = $aid;
$hidden_vars['pid'] = $pid;


$msg->addConfirm(array('PA_DELETE_COMMENT'), $hidden_vars);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>
