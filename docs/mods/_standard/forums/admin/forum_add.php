<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_FORUMS);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/forums/admin/forums.php');
	exit;
} else if (isset($_POST['add_forum'])) {
	$missing_fields = array();

	if (empty($_POST['title'])) {
		$missing_fields[] = _AT('title');
	} 

	if (empty($_POST['courses'])) {
		$missing_fields[] = _AT('courses');
	} 

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	$_POST['edit'] = intval($_POST['edit']);

	if (!($msg->containsErrors())) {
		//add forum
		$sql	= "INSERT INTO ".TABLE_PREFIX."forums (title, description, mins_to_edit) VALUES ('" . $_POST['title'] . "','" . $_POST['description'] ."', $_POST[edit])";
		$result	= mysql_query($sql, $db);
		$forum_id = mysql_insert_id($db);
		write_to_log(AT_ADMIN_LOG_INSERT, 'forums', mysql_affected_rows($db), $sql);

		//for each course, add an entry to the forums_courses table
		foreach ($_POST['courses'] as $course) {
			$sql	= "INSERT INTO ".TABLE_PREFIX."forums_courses VALUES (" . $forum_id . "," . $course . ")";
			$result	= mysql_query($sql, $db);
			write_to_log(AT_ADMIN_LOG_INSERT, 'forums_courses', mysql_affected_rows($db), $sql);
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		if($course =="0"){
			$msg->addFeedback('FORUM_POSTING');
		}
		header('Location: '.AT_BASE_HREF.'mods/_standard/forums/admin/forums.php');
		exit;
	}
}

$onload = 'document.form.title.focus();';

		$sql = "SELECT course_id, title FROM ".TABLE_PREFIX."courses ORDER BY title";
		$result = mysql_query($sql, $db);
		$savant->assign('result', $result);		

require(AT_INCLUDE_PATH.'header.inc.php'); 

$savant->assign('system_courses', $system_courses);
$savant->display('admin/courses/forum_add.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>