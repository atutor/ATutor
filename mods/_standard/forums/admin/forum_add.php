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

		$sql	= "INSERT INTO %sforums (title, description, mins_to_edit) VALUES ('%s','%s', %d)";
		$result	= queryDB($sql, array(TABLE_PREFIX, $_POST['title'], $_POST['description'], $_POST['edit']));
		$forum_id = at_insert_id();
		
		global $sqlout;
		write_to_log(AT_ADMIN_LOG_INSERT, 'forums', $result, $sqlout);

		//for each course, add an entry to the forums_courses table
		foreach ($_POST['courses'] as $course) {

			$sql	= "INSERT INTO %sforums_courses VALUES (%d,%d)";
			$result	= queryDB($sql, array(TABLE_PREFIX, $forum_id, $course));
			global $sqlout;
			write_to_log(AT_ADMIN_LOG_INSERT, 'forums_courses', $result, $sqlout);
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

		$sql = "SELECT course_id, title FROM %scourses ORDER BY title";
		$rows_titles = queryDB($sql, array(TABLE_PREFIX));
	
		$savant->assign('titles', $rows_titles);		
		
require(AT_INCLUDE_PATH.'header.inc.php'); 

$savant->assign('system_courses', $system_courses);
$savant->display('admin/courses/forum_add.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>