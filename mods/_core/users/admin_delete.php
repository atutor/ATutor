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
/* linked from admin/users.php                                  */
/* deletes a user from the system.                              */
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/file_storage/file_storage.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

function delete_user($id) {
	global $db, $msg;

	//make sure not instructor of a course

	$sql	= "SELECT course_id FROM %scourses WHERE member_id=%d";
	$row_instructor = queryDB($sql, array(TABLE_PREFIX, $id), TRUE);
	
	// WHAT'S THE PURPOSE OF THIS CONDITION?
	if(count($row_instructor) > 0){
		/*$msg->addError('NODELETE_USER');
		header('Location: '.AT_BASE_HREF.'users.php');
		exit;*/
		return;
	}

	$sql	= "DELETE FROM %scourse_enrollment WHERE member_id=%d";
	$result  = queryDB($sql, array(TABLE_PREFIX, $id));
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_DELETE, 'course_enrollment', $result, $sqlout);

	$sql	= "DELETE FROM %sforums_accessed WHERE member_id=%d";
	$result  = queryDB($sql, array(TABLE_PREFIX, $id));
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_DELETE, 'forums_accessed', $result, $sqlout);

	$sql	= "DELETE FROM %sforums_subscriptions WHERE member_id=%d";
	$result  = queryDB($sql, array(TABLE_PREFIX, $id));
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_DELETE, 'forums_subscriptions', $result, $sqlout);

	/****/
	/* delete forum threads block: */
		/* delete the thread replies: */
		$sql	= "SELECT COUNT(*) AS cnt, parent_id, forum_id FROM %sforums_threads WHERE member_id=%d AND parent_id<>0 GROUP BY parent_id";
		$rows_threads = queryDB($sql, array(TABLE_PREFIX, $id));
		foreach($rows_threads as $row){
			/* update the forum posts counter */
			$sql = "UPDATE %sforums SET num_posts=num_posts - %d, last_post=last_post WHERE forum_id=%d";
			$result = queryDB($sql, array(TABLE_PREFIX, $row['cnt'], $row['forum_id']));
			global $sqlout;
			write_to_log(AT_ADMIN_LOG_UPDATE, 'forums', $result, $sqlout);
						
			/* update the topics reply counter */
			$sql = "UPDATE %sforums_threads SET num_comments=num_comments-%d, last_comment=last_comment, date=date WHERE post_id=%d";
			$result = queryDB($sql, array(TABLE_PREFIX, $row['cnt'], $row['parent_id']));
			global $sqlout;
			write_to_log(AT_ADMIN_LOG_UPDATE, 'forums_threads', $result, $sqlout);

		}

		/* delete threads this member started: */
		$sql	= "SELECT post_id, forum_id, num_comments FROM %sforums_threads WHERE member_id=%d AND parent_id=0";
		$rows_posts = queryDB($sql, array(TABLE_PREFIX, $id));
		
		foreach($rows_posts as $row){
			/* update the forum posts and topics counters */
			$num_posts = $row['num_comments'] + 1;
			$sql = "UPDATE %sforums SET num_topics=num_topics-1, num_posts=num_posts - %d, last_post=last_post WHERE forum_id=%d";
			$result = queryDB($sql, array(TABLE_PREFIX, $num_posts, $row['forum_id']));
			global $sqlout;
			write_to_log(AT_ADMIN_LOG_UPDATE, 'forums', $result, $sqlout);

			/* delete the replies */
			$sql = "DELETE FROM %sforums_threads WHERE parent_id=%d";
			$result = queryDB($sql, array(TABLE_PREFIX, $row['post_id']));
			global $sqlout;
			write_to_log(AT_ADMIN_LOG_DELETE, 'forums_threads', $result, $sqlout);
		}
		/* delete the actual threads */
		$sql	= "DELETE FROM %sforums_threads WHERE member_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $id));
		global $sqlout;
		write_to_log(AT_ADMIN_LOG_DELETE, 'forums_threads', $result, $sqlout);

	/* end delete forum threads block. */
	/****/

	$sql	= "DELETE FROM %sinstructor_approvals WHERE member_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $id));
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_DELETE, 'instructor_approvals', $result, $sqlout);

	$sql	= "DELETE FROM %smessages WHERE from_member_id=%d OR to_member_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $id, $id));
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_DELETE, 'messages', $result, $sqlout);

	$sql	= "DELETE FROM %spolls_members WHERE member_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $id));
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_DELETE, 'polls_members', $result, $sqlout);

	$sql	= "DELETE FROM %stests_answers WHERE member_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $id));
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_DELETE, 'tests_answers', $result, $sqlout);

	$sql	= "DELETE FROM %stests_results WHERE member_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $id));
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_DELETE, 'tests_results', $result, $sqlout);

	$sql	= "DELETE FROM %susers_online WHERE member_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $id));
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_DELETE, 'users_online', $result, $sqlout);

	$sql	= "DELETE FROM %smembers WHERE member_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $id));
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_DELETE, 'members', $result, $sqlout);

	$sql	= "DELETE FROM %smember_track WHERE member_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $id));
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_DELETE, 'member_track', $result, $sqlout);
	
	// delete personal files from file storage
	fs_delete_workspace(WORKSPACE_PERSONAL, $id);


	return;
}

$ids = explode(',', $_REQUEST['id']);

if (isset($_POST['submit_yes'])) {
	
	foreach($ids as $id) {
		delete_user(intval($id));
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	if (isset($_POST['ml']) && $_REQUEST['ml']) {
		header('Location: '.AT_BASE_HREF.'mods/_core/users/master_list.php');
	} else {
		header('Location: '.AT_BASE_HREF.'mods/_core/users/users.php');
	}
	exit;
} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	if (isset($_POST['ml']) && $_REQUEST['ml']) {
		header('Location: '.AT_BASE_HREF.'mods/_core/users/master_list.php');
	} else {
		header('Location: '.AT_BASE_HREF.'mods/_core/users/users.php');
	}
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 
$names = get_login($ids);
$names_html = '<ul>'.html_get_list($names).'</ul>';
$hidden_vars['id'] =  implode(',', array_keys($names));
$hidden_vars['ml'] = intval($_REQUEST['ml']);

$confirm = array('DELETE_USER', $names_html);
$msg->addConfirm($confirm, $hidden_vars);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');

?>