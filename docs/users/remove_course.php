<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$course = intval($_POST['course']);
	if ($system_courses[$course]['member_id'] != $_SESSION['member_id']) {
		$sql	= "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$course";
		$result = mysql_query($sql, $db) or die(mysql_error());

		// Unsubscribe from forums and threads of the course
		$sql	= "DELETE FROM ".TABLE_PREFIX."forums_subscriptions 
		         WHERE forum_id IN (SELECT forum_id FROM ".TABLE_PREFIX."forums_courses WHERE course_id=$course)
		           AND member_id=".$_SESSION[member_id];
		$result = mysql_query($sql, $db) or die(mysql_error());

		$sql	= "UPDATE ".TABLE_PREFIX."forums_accessed 
		           SET subscribe = 0
		         WHERE post_id IN (SELECT distinct t.post_id FROM ".TABLE_PREFIX."forums_courses c, ".TABLE_PREFIX."forums_threads t WHERE c.course_id=$course)
		           AND member_id=".$_SESSION[member_id];
		$result = mysql_query($sql, $db) or die(mysql_error());

		$msg->addFeedback('COURSE_REMOVED');
	}
	header("Location: ".AT_BASE_HREF."users/index.php");
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

unset($hidden_vars);
$hidden_vars['course'] = $_GET['course'];
$msg->addConfirm(array('UNENROLL', $system_courses[$_GET['course']]['title']), $hidden_vars);

$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>