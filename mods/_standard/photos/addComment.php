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
// $Id: addComment.php 10424 2010-11-30 00:42:01Z hwong $
$_user_location = 'public';
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');

//quit if this is not a member
if(!(isset($_SESSION['member_id']) && $_SESSION['member_id'] > 0)){
	$msg->addError('ACCESS_DENIED');
	header('Location: index.php');
	exit;
}

//check what comment this is for. Album or Photo.
$pid = intval($_POST['pid']);
$aid = intval($_POST['aid']);

if (isset($_POST['pid']) && $pid>0){
	$isPhoto = true;
	$id = $pid;
} else {
	$isPhoto = false;
	$id = $aid;
}


//Error checking
if (trim($_POST['comment']) == ''){
	//if comment is empty
	$msg->addError('PA_EMPTY_COMMENT'); //sql
} else {
	$pa = new PhotoAlbum();
	$result = $pa->addComment($id, $_POST['comment'], $_SESSION['member_id'], $isPhoto);

	if ($result){
		//TODO: AJAX
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	} else {
		$msg->addError('PA_ADD_COMMENT_FAILED'); //sql
	}
}

if ($isPhoto){
	header('Location: photo.php?pid='.$pid.SEP.'aid='.$aid);
} else {
	header('Location: albums.php?id='.$aid);
}
exit;
?>
