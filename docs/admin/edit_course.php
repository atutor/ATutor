<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_COURSES);

require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: courses.php');
	exit;
} else if (isset($_POST['course']) && !isset($_POST['setvisual'])) {
	require(AT_INCLUDE_PATH.'lib/course.inc.php');
	$errors = add_update_course($_POST, TRUE);

	if (is_numeric($errors)) {
		header('Location: '.$_base_href.'admin/courses.php');
		exit;
	}

}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$msg->printAll();

$course = intval($_REQUEST['course']);
$isadmin   = TRUE;


require(AT_INCLUDE_PATH.'html/course_properties.inc.php');

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>