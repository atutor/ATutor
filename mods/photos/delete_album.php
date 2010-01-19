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
include (AT_INCLUDE_PATH.'lib/filemanager.inc.php');	//clr_dir()
include (AT_PA_INCLUDE.'lib.inc.php');	//album_filepath
include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');

//validates if this is me/have the privilege to delete.
$id = intval($_REQUEST['id']);
$pa = new PhotoAlbum($id);

if (!$pa->checkAlbumPriv($_SESSION['member_id'])){
	$msg->addError('');	//TODO: nice try
	header('Location: index.php');
	exit;
}

if ($_POST['submit_no']) {
	$msg->addFeedback('CANCELLED');
	Header('Location: index.php');
	exit;
}

if ($_POST['submit_yes']) {
	//delete
	$pa->deleteAlbum();

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	Header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$hidden_vars['id'] = $id;

$msg->addConfirm(array('DELETE_ALBUM', $id), $hidden_vars);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>
