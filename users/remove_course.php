<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
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
	
		$sql	= "DELETE FROM %scourse_enrollment WHERE member_id=%d AND course_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $course));
		
		// Unsubscribe from forums and threads of the course
		
		$sql	= "DELETE FROM %sforums_subscriptions 
		         WHERE forum_id IN (SELECT forum_id FROM %sforums_courses WHERE course_id=%d)
		           AND member_id=%d";

		$result = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $course, $_SESSION['member_id'] ));

		$sql	= "UPDATE %sforums_accessed 
		           SET subscribe = 0
		         WHERE post_id IN (SELECT distinct t.post_id FROM %sforums_courses c, %sforums_threads t WHERE c.course_id=$course)
		           AND member_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, TABLE_PREFIX, $_SESSION['member_id']));
        
    // Unsubscribe from group forums and threads of the group forums
    $group_list = implode(',', $_SESSION['groups']);
    if(!empty($group_list)) {
        $sql	= "DELETE FROM %sforums_subscriptions 
		     WHERE forum_id IN (SELECT forum_id FROM %sforums_groups WHERE group_id IN (%s))
		       AND member_id=%d";

        $result = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $group_list, $_SESSION['member_id'] ));
        
        $sql	= "UPDATE %sforums_accessed 
		       SET subscribe = 0
		     WHERE post_id IN (SELECT distinct t.post_id FROM %sforums_groups g, %sforums_threads t WHERE g.group_id IN (%s))
		       AND member_id=%d";
        $result = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, TABLE_PREFIX, $group_list, $_SESSION['member_id']));
    }
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