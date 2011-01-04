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
// $Id: delete_course.php 10142 2010-08-17 19:17:26Z hwong $

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_COURSES);

$course = intval($_REQUEST['course']);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: ../../courses/admin/courses.php');
	exit;
} else if (isset($_POST['step']) && ($_POST['step'] == 2) && isset($_POST['submit_yes'])) {
	require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');
	require(AT_INCLUDE_PATH.'../mods/_core/properties/lib/delete_course.inc.php');

	delete_course($course, $entire_course = true, $rel_path = '../'); // delete the course
	cache_purge('system_courses','system_courses'); // purge the system_courses cache (if successful)
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: ../../courses/admin/courses.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

if (!isset($_POST['step'])) {
	$hidden_vars['step']   = 1;
	$hidden_vars['course'] = $course;
	$msg->addConfirm(array('DELETE_COURSE_1', $system_courses[$course]['title']), $hidden_vars);
	$msg->printConfirm();
} else if ($_POST['step'] == 1) {
	$hidden_vars['step']   = 2;
	$hidden_vars['course'] = $course;
	$msg->addConfirm(array('DELETE_COURSE_2', $system_courses[$course]['title']), $hidden_vars);
	$msg->printConfirm();
}

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>