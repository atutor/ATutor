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
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

$_section[0][0] = _AT('my_courses');
$_section[0][1] = 'users/index.php';
$_section[1][0] = _AT('delete_course');

$_SESSION['course_id'] = 0;

require(AT_INCLUDE_PATH.'lib/delete_course.inc.php');

/* make sure we own this course */
$course = intval($_GET['course']);
$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=$course AND member_id=$_SESSION[member_id]";
$result	= mysql_query($sql, $db);
if (mysql_num_rows($result) != 1) {
	echo _AT('not_your_course');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if ($_GET['d'] == 2){
	$msg->deleteFeedback('CANCELLED');
		
	/* delete this course */
	delete_course($course, $entire_course = TRUE, $rel_path = '../');

	// purge the system_courses cache! (if successful)
	cache_purge('system_courses','system_courses');
	
	//Update RSS feeds if they exist
	if(file_exists("../pub/feeds/0/browse_courses_feedRSS2.0.xml")||
		file_exists("../pub/feeds/0/browse_courses_feedRSS1.0.xml")){
		require_once('../tools/feeds/browse_courses_feed.php');
	}
	$msg->addFeedback('COURSE_DELETED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

if (!$_GET['d']) {
	$warnings = array('SURE_DELETE_COURSE1', $system_courses[$course]['title']);
	$msg->printWarnings($warnings);
	
	
	$msg->addFeedback('CANCELLED');
	echo '<p align="center"><a href="'.$_SERVER['PHP_SELF'].'?course='.$course.SEP.'d=1'.'">'._AT('yes_delete').'</a> | <a href="users/index.php">'._AT('no_cancel').'</a></p>';

} else {
	$msg->deleteFeedback('CANCELLED');
	
	$warnings = array('SURE_DELETE_COURSE2', $system_courses[$course]['title']);
	$msg->printWarnings($warnings);
	
	$msg->addFeedback('CANCELLED');
	echo '<p align="center"><a href="'.$_SERVER['PHP_SELF'].'?course='.$course.SEP.'d=2'.'">'._AT('yes_delete').'</a> | <a href="users/index.php">'._AT('no_cancel').'</a></p>';
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>