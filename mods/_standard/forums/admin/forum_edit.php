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

include(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/forums/admin/forums.php');
	exit;
} else if (isset($_POST['edit_forum'])) {
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

	if (!($msg->containsErrors())) {

		//update forum
		$forum_id = intval($_POST['forum']);
		$_POST['title']  = $addslashes($_POST['title']);
		$_POST['edit'] = intval($_POST['edit']);
		$_POST['description']  = $addslashes($_POST['description']);

		$sql	= "UPDATE %sforums SET title='%s', description='%s', last_post=last_post, mins_to_edit=%d WHERE forum_id=%d";
		$result	= queryDB($sql, array(TABLE_PREFIX, $_POST['title'], $_POST['description'], $_POST['edit'], $forum_id));
		
		global $sqlout;
		write_to_log(AT_ADMIN_LOG_UPDATE, 'forums', $result, $sqlout);

		// unsubscribe all the members who are NOT in $_POST['courses']
		$courses_list = implode(',', $_POST['courses']);

		// list of all the students who are in other courses as well
		$sql     = "SELECT member_id FROM %scourse_enrollment WHERE course_id IN (%s)";
		$rows_enrolled = queryDB($sql, array(TABLE_PREFIX, $courses_list));
		
		foreach($rows_enrolled as $row2){
			$students[] = $row2['member_id'];
		}

		// list of students who must REMAIN subscribed!
		$students_list = implode(',', $students);

		if ($students_list) {
			// remove the subscriptions

			$sql	= "SELECT post_id FROM %sforums_threads WHERE forum_id=%d";
			$rows_threads = queryDB($sql, array(TABLE_PREFIX, $forum_id));
			
			foreach($rows_threads as $row2){

				$sql	 = "DELETE FROM %sforums_accessed WHERE post_id=%d AND member_id NOT IN (%s)";
				$result3 = queryDB($sql, array(TABLE_PREFIX, $row2['post_id'], $students_list));
			}

			$sql	 = "DELETE FROM %sforums_subscriptions WHERE forum_id=%d AND member_id NOT IN (%s)";
			$result3 = queryDB($sql, array(TABLE_PREFIX, $forum_id, $students_list));
		}

		$sql = "DELETE FROM %sforums_courses WHERE forum_id=%d AND course_id NOT IN (%s)";
		$result = queryDB($sql, array(TABLE_PREFIX, $forum_id, $courses_list));
		
		global $sqlout;
		write_to_log(AT_ADMIN_LOG_DELETE, 'forums_courses', $result, $sqlout);

		//update forums_courses
		if (in_array('0', $_POST['courses'])) {
			//general course - used by all.  put one entry in forums_courses w/ course_id=0
			$sql	= "REPLACE INTO %sforums_courses VALUES (%d, 0)";
			$result	= queryDB($sql, array(TABLE_PREFIX, $_POST['forum']));
			
			global $sqlout;
			write_to_log(AT_ADMIN_LOG_REPLACE, 'forums_courses', $result, $sqlout);
		} else {
			foreach ($_POST['courses'] as $course) {

				$sql	= "REPLACE INTO %sforums_courses VALUES (%d,%d)";
				$result	= queryDB($sql, array(TABLE_PREFIX, $_POST['forum'], $course));
				
				global $sqlout;
				write_to_log(AT_ADMIN_LOG_REPLACE, 'forums_courses', $result, $sqlout);
			}
		}
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.AT_BASE_HREF.'mods/_standard/forums/admin/forums.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

if (!($forum = @get_forum($_GET['forum']))) {
	//no such forum
	$msg->addError('FORUM_NOT_FOUND');
	$msg->printAll();
} else {
	$msg->printAll();

	$sql	= "SELECT * FROM %sforums_courses WHERE forum_id=%d";
	$rows_courses	= queryDB($sql, array(TABLE_PREFIX, $forum['forum_id']));
	
	foreach($rows_courses as $row){
		$courses[] = $row['course_id'];		
	}

	$sql = "SELECT course_id, title FROM %scourses ORDER BY title";
	$rows_titles = queryDB($sql, array(TABLE_PREFIX));
?>
	
<?php
}
$savant->assign('titles', $rows_titles);
$savant->assign('courses', $courses);
$savant->assign('forum', $forum);
$savant->display('admin/courses/forum_edit.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php');
?>