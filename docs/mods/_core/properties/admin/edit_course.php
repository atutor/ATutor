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
admin_authenticate(AT_ADMIN_PRIV_COURSES);

require(AT_INCLUDE_PATH.'../mods/_core/backups/classes/Backup.class.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: ../../courses/admin/courses.php');
	exit;
} else if (isset($_POST['submit'])) {
	require(AT_INCLUDE_PATH.'../mods/_core/courses/lib/course.inc.php');
	$errors = add_update_course($_POST, TRUE);

	if (is_numeric($errors)) {
		$msg->addFeedback('COURSE_PROPERTIES');
		header('Location: '.AT_BASE_HREF.'mods/_core/courses/admin/courses.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$msg->printAll();

$course = intval($_REQUEST['course']);
$isadmin   = TRUE;

if ($isadmin){
	$sql = "SELECT member_id, login FROM ".TABLE_PREFIX."members WHERE status=".AT_STATUS_INSTRUCTOR;
	$result = mysql_query($sql, $db);
	//$savant->assign('result', $result);
}
if (!$course){
	$Backup = new Backup($db);

			if ($this->isadmin) {
				$sql	= "SELECT course_id, title FROM ".TABLE_PREFIX."courses ORDER BY title";
			} else {
				$sql	= "SELECT course_id, title FROM ".TABLE_PREFIX."courses WHERE member_id=$_SESSION[member_id] ORDER BY title";
			}

			$result2 = mysql_query($sql, $db);
}

$savant->assign('isadmin', $isadmin);
$savant->assign('course', $course);
$savant->assign('result', $result);
require(AT_INCLUDE_PATH.'../mods/_core/courses/html/course_properties.inc.php');

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>