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

//check what comment this is for. Album or Photo.
$pid = intval($_POST['pid']);
$aid = intval($_POST['aid']);
$cid = $_POST['cid'];
$comment = $_POST['comment'];

if (isset($_POST['pid']) && $pid>0){
	$isPhoto = true;
} else {
	$isPhoto = false;
}
$cid = intval(str_replace('cid_', '', $cid));

$pa = new PhotoAlbum($aid);
//validates
if ($pa->checkAlbumPriv($_SESSION['member_id']) || $pa->checkCommentPriv($cid, $_SESSION['member_id'], $isPhoto)){
	$result = $pa->editComment($cid, $comment, $isPhoto);
}

if ($result){
	//TODO: AJAX
	header('HTTP/1.0 200 OK');
} else {
	$msg->addError(); //sql or permission
	header('HTTP/1.0 404 Not Found');
}
exit;
?>