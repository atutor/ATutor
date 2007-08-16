<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');


if ($_POST['m']) {
	$m =str_replace('.', '', $_POST['m']);
} else if ($_GET['m']) {
	$m =str_replace('.', '', $_GET['m']);
}

if ($_POST['submit_no']) {
	$msg->addFeedback('CANCELLED');
	Header('Location: index.php');
	exit;
}

if ($_POST['submit_yes']) {
	unlink(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$m.'.html');

	//if its the current tran, unset it
	if (str_replace('.html', '', $admin['tranFile']) == $m) {
		$admin['produceTran'] = 0;
		writeAdminSettings($admin);
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	Header('Location: index.php');
	exit;
}

if (!file_exists(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$m.'.html')) {
	$msg->addError('FILE_NOT_FOUND');

	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$hidden_vars['m'] = $m;

$msg->addConfirm(array('DELETE_TRANSCRIPT', $m), $hidden_vars);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>
