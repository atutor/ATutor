<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FORUMS);

require (AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/forums/index.php');
	exit;
} else if (isset($_POST['edit_forum'])) {
	$_POST['fid'] = intval($_POST['fid']);

	if ($_POST['title'] == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('title')));
	} else {
		$_POST['title'] = validate_length($_POST['title'], 60);
	}

	if (!$msg->containsErrors()) {
		if (!is_shared_forum($_POST['fid'])) {
			edit_forum($_POST);
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		} else {
			$msg->addError('FORUM_NO_EDIT_SHARE');
		}
		
		header('Location: '.AT_BASE_HREF.'mods/_standard/forums/index.php');
		exit;
	}
}

$onload = 'document.form.title.focus();';
require(AT_INCLUDE_PATH.'header.inc.php');

$fid = intval($_REQUEST['fid']);

if (!isset($_POST['submit'])) {
	$row = get_forum($fid, $_SESSION['course_id']);
	if (!is_array($row)) {
		$msg->addError('FORUM_NOT_FOUND');
		$msg->printALL();
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
} else {
	$row['description'] = $_POST['body'];
	$row['mins_to_edit'] = $_POST['edit'];
}

$msg->printErrors();
$savant->assign('row', $row);
$savant->assign('fid', $fid);
$savant->display('instructor/forums/edit_forum.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>