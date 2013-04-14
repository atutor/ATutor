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

		$sql	= "UPDATE ".TABLE_PREFIX."forums SET title='" . $_POST['title'] . "', description='" . $_POST['description'] . "', last_post=last_post, mins_to_edit=$_POST[edit] WHERE forum_id=".$forum_id;
		$result	= mysql_query($sql, $db);
		write_to_log(AT_ADMIN_LOG_UPDATE, 'forums', mysql_affected_rows($db), $sql);

		// unsubscribe all the members who are NOT in $_POST['courses']
		$courses_list = implode(',', $_POST['courses']);

		// list of all the students who are in other courses as well
		$sql     = "SELECT member_id FROM ".TABLE_PREFIX."course_enrollment WHERE course_id IN ($courses_list)";
		$result2 = mysql_query($sql, $db);
		while ($row2 = mysql_fetch_assoc($result2)) {
			$students[] = $row2['member_id'];
		}

		// list of students who must REMAIN subscribed!
		$students_list = implode(',', $students);

		if ($students_list) {
			// remove the subscriptions
			$sql	= "SELECT post_id FROM ".TABLE_PREFIX."forums_threads WHERE forum_id=$forum_id";
			$result2 = mysql_query($sql, $db);
			while ($row2 = mysql_fetch_assoc($result2)) {
				$sql	 = "DELETE FROM ".TABLE_PREFIX."forums_accessed WHERE post_id=$row2[post_id] AND member_id NOT IN ($students_list)";
				$result3 = mysql_query($sql, $db);
			}

			$sql	 = "DELETE FROM ".TABLE_PREFIX."forums_subscriptions WHERE forum_id=$forum_id AND member_id NOT IN ($students_list)";
			$result3 = mysql_query($sql, $db);
		}

		$sql = "DELETE FROM ".TABLE_PREFIX."forums_courses WHERE forum_id=$forum_id AND course_id NOT IN ($courses_list)";
		$result = mysql_query($sql, $db);
		write_to_log(AT_ADMIN_LOG_DELETE, 'forums_courses', mysql_affected_rows($db), $sql);

		//update forums_courses
		if (in_array('0', $_POST['courses'])) {
			//general course - used by all.  put one entry in forums_courses w/ course_id=0
			$sql	= "REPLACE INTO ".TABLE_PREFIX."forums_courses VALUES (" . $_POST['forum'] . ", 0)";
			$result	= mysql_query($sql, $db);
			write_to_log(AT_ADMIN_LOG_REPLACE, 'forums_courses', mysql_affected_rows($db), $sql);
		} else {
			foreach ($_POST['courses'] as $course) {
				$sql	= "REPLACE INTO ".TABLE_PREFIX."forums_courses VALUES (" . $_POST['forum'] . "," . $course . ")";
				$result	= mysql_query($sql, $db);
				write_to_log(AT_ADMIN_LOG_REPLACE, 'forums_courses', mysql_affected_rows($db), $sql);
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

	$sql	= "SELECT * FROM ".TABLE_PREFIX."forums_courses WHERE forum_id=$forum[forum_id]";
	$result	= mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$courses[] = $row['course_id'];		
	}
$sql = "SELECT course_id, title FROM ".TABLE_PREFIX."courses ORDER BY title";
	$result = mysql_query($sql, $db);
?>
	
<?php
}
$savant->assign('courses', $courses);
$savant->assign('result', $result);
$savant->assign('forum', $forum);
$savant->display('admin/courses/forum_edit.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php');
?>