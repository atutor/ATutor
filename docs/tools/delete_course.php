<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

//$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

require(AT_INCLUDE_PATH.'lib/delete_course.inc.php');

/* make sure we own this course */
$course = intval($_GET['course']);
$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id] AND member_id=$_SESSION[member_id]";
$result	= mysql_query($sql, $db);
if (!($row = mysql_num_rows($result))) {
	echo _AT('not_your_course');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$course = $_SESSION['course_id'];

if ($_GET['d'] == 2){
		
	/* delete this course */
	delete_course($course, $entire_course = TRUE, $rel_path = '../');

	// purge the system_courses cache! (if successful)
	cache_purge('system_courses','system_courses');
	
	$msg->deleteFeedback('CANCELLED');

	$msg->addFeedback('COURSE_DELETED');
	header('Location: '.$_base_href.'bounce.php?course=0');
	exit;
}

$msg->deleteFeedback('CANCELLED');
require(AT_INCLUDE_PATH.'header.inc.php');

if (!$_GET['d']) {
	$warnings = array('SURE_DELETE_COURSE1', $system_courses[$course]['title']);
	$msg->printWarnings($warnings);
	
	
	$msg->addFeedback('CANCELLED');
	echo '<p align="center"><a href="'.$_SERVER['PHP_SELF'].'?course='.$course.SEP.'d=1'.'">'._AT('yes_delete').'</a> | <a href="tools/course_properties.php">'._AT('no_cancel').'</a></p>';

} else {
	$warnings = array('SURE_DELETE_COURSE2', $system_courses[$course]['title']);
	$msg->printWarnings($warnings);
	
	$msg->addFeedback('CANCELLED');
	echo '<p align="center"><a href="'.$_SERVER['PHP_SELF'].'?course='.$course.SEP.'d=2'.'">'._AT('yes_delete').'</a> | <a href="tools/course_properties.php">'._AT('no_cancel').'</a></p>';
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>