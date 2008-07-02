<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: enroll_edit.php 6662 2006-11-20 15:52:49Z joel $

if (!defined('AT_INCLUDE_PATH')) { exit; }

/**
* Generates the list of login ids of the selected user
* @access  private
* @param   string $member_ids	the list of members to be checked
* @return  string				The list of login IDs
* @author  Shozub Qureshi
*/
function get_usernames ($member_ids) {
	global $db;

	$sql    = "SELECT login FROM ".TABLE_PREFIX."members WHERE `member_id` IN ($member_ids)";

	$result = mysql_query($sql, $db);

	while ($row = mysql_fetch_assoc($result)) {
		$str .= '<li>' . $row['login'] . '</li>';
	}
	return $str;
}

/**
* Checks if any of the selected users have non-zero roles or privileges
* @access  private
* @param   string $member_ids	the list of members to be checked
* @return  int					whether the role/priv is empty or not (0 = if empty, 1 = if ok)
* @author  Shozub Qureshi
*/
function check_roles ($member_ids) {
	global $db;

	$sql    = "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE `member_id` IN ($member_ids)";
	$result = mysql_query($sql, $db);

	while ($row = mysql_fetch_assoc($result)) {
		if ($row['role'] != 'Student' || $row['privileges'] != 0) {
			return 1;
		}
	}
	return 0;
}

/**
* Removes students from course enrollement
* @access  private
* @param   array $list			the IDs of the members to be removed
* @author  Shozub Qureshi
*/
/*
// no longer used. Unenroll does this job AND removes groups too.
function remove ($list) {
	global $db;

	$members = '(member_id='.$list[0].')';
	for ($i=1; $i < count($list); $i++) {
		$members .= ' OR (member_id='.$list[$i].')';
	}
	$sql	= "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE course_id = $_SESSION[course_id] AND ($members)";	
	$result = mysql_query($sql, $db);
}*/

/**
* Unenrolls students from course enrollement
* @access  private
* @param   array $list			the IDs of the members to be removed
* @author  Shozub Qureshi
*/
function unenroll ($list) {
	global $db, $system_courses, $course_id;
	$members = implode(',', $list);

	if ($members) {
		$members = addslashes($members);

		$sql    = "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$course_id AND member_id IN ($members)";
		$result = mysql_query($sql, $db);

		$sql    = "DELETE FROM ".TABLE_PREFIX."groups_members WHERE member_id IN ($members)";
		$result = mysql_query($sql, $db);
		// $groupModule->unenroll(course_id, user_id);
		// $forumModule->unenroll(course_id, user_id);
	}
}

/**
* Enrolls students into course enrollement
* @access  private
* @param   array $list			the IDs of the members to be added
* @author  Shozub Qureshi
*/
function enroll ($list) {
	global $db, $msg, $_config, $course_id, $owner;
	require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

	$num_list = count($list);
	$members = '(member_id='.$list[0].')';
	for ($i=0; $i < $num_list; $i++)	{
		$id = intval($list[$i]);
		$members .= ' OR (member_id='.$id.')';
		$sql = "INSERT INTO ".TABLE_PREFIX."course_enrollment VALUES ($id, $course_id, 'y', 0, '', 0)";
		$result = mysql_query($sql, $db);
		if (mysql_affected_rows($db) != 1) {
			$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET approved='y' WHERE course_id=$course_id AND member_id=$id";
			$result = mysql_query($sql, $db);
		}
	}

	//get First_name, Last_name of course Instructor
	$sql_from    = "SELECT first_name, last_name, email FROM ".TABLE_PREFIX."members WHERE member_id = $owner";
	$result_from = mysql_query($sql_from, $db);
	$row_from    = mysql_fetch_assoc($result_from);

	$email_from_name  = $row_from['first_name'] . ' ' . $row_from['last_name'];
	$email_from = $row_from['email'];

	//get email addresses of users:
	$sql_to    = "SELECT email FROM ".TABLE_PREFIX."members WHERE ($members)";
	$result_to = mysql_query($sql_to, $db);

	while ($row_to = mysql_fetch_assoc($result_to)) {
		// send email here.
		$login_link = AT_BASE_HREF . 'login.php?course=' . $course_id;
		$subject = SITE_NAME.': '._AT('enrol_message_subject');
		$body = SITE_NAME.': '._AT('enrol_message_approved', $_SESSION['course_title'], $login_link)."\n\n";

		$mail = new ATutorMailer;
		$mail->From     = $_config['contact_email'];
		$mail->FromName = $_config['site_name'];
		$mail->AddAddress($row_to['email']);
		$mail->Subject  = $subject;
		$mail->Body     = $body;
			
		if (!$mail->Send()) {
			$msg->addError('SENDING_ERROR');
		}

		unset($mail);
	}
}


function group ($list, $gid) {
	global $db,$msg;
	$sql = "REPLACE INTO ".TABLE_PREFIX."groups_members VALUES ";
	$gid=intval($gid);
	for ($i=0; $i < count($list); $i++)	{
		$student_id = intval($list[$i]);
		$sql .= "($gid, $student_id),";
	}
	$sql = substr($sql, 0, -1);
	mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php');
	exit;
}

function group_remove ($ids, $gid) {
	global $db,$msg;
	$gid=intval($gid);

	$ids=implode(',', $ids);

	if ($ids) {
		$sql = "DELETE FROM ".TABLE_PREFIX."groups_members WHERE group_id=$gid AND member_id IN ($ids)";
		mysql_query($sql, $db);
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}

	header('Location: index.php');
	exit;
}

/**
* Marks a student as an alumni of the course (not enrolled, but can view course material and participate in forums)
* @access  private
* @param   array $list			the IDs of the members to be alumni
* @author  Heidi Hazelton
*/
function alumni ($list) {
	global $db, $course_id;
	$members = '(member_id='.$list[0].')';
	for ($i=1; $i < count($list); $i++)	{
		$members .= ' OR (member_id='.$list[$i].')';
	}
	
	$sql    = "UPDATE ".TABLE_PREFIX."course_enrollment SET approved = 'a' WHERE course_id=$course_id AND ($members)";
	$result = mysql_query($sql, $db);
}


//course_owner
$owner = $system_courses[$course_id]['member_id'];

if (isset($_POST['submit_no'])) {
	//if user decides to forgo option
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?current_tab='.$_POST['curr_tab'].SEP.'course_id='.$course_id);
	exit;
} /*
// No longer used. Unenroll does the same job and removes from groups too.
else if (isset($_POST['submit_yes']) && $_POST['func'] =='remove' ) {
	//Remove student from list (unenrolls automatically)

	//you cannot remove anyone unless you are the course owner
	authenticate(AT_PRIV_ADMIN);

	//echo 'atleast this worked';
	remove($_POST['id']);

	$msg->addFeedback('MEMBERS_REMOVED');
	header('Location: index.php?current_tab=4');
	exit;
}*/
else if (isset($_POST['submit_yes']) && $_POST['func'] =='unenroll' ) {
	//Unenroll student from course
	unenroll($_POST['id']);

//	$msg->addFeedback('MEMBERS_UNENROLLED');
	$msg->addFeedback('MEMBERS_REMOVED');
	header('Location: index.php?current_tab=4'.SEP.'course_id='.$course_id);
	exit;
} else if (isset($_POST['submit_yes']) && $_POST['func'] =='enroll' ) {
	//Enroll student in course
	enroll($_POST['id']);

	$msg->addFeedback('MEMBERS_ENROLLED');
	header('Location: index.php?current_tab=0'.SEP.'course_id='.$course_id);
	exit;
} else if (isset($_POST['submit_yes']) && $_POST['func'] =='alumni' ) {
	//Mark student as course alumnus
	alumni($_POST['id']);
	
	$msg->addFeedback('MEMBERS_ALUMNI');
	header('Location: index.php?current_tab=2'.SEP.'course_id='.$course_id);
	exit;
} else if (isset($_POST['submit_yes']) && $_POST['func'] =='group' ) {
	//Mark student as a member of the group
	group($_POST['id'],$_POST['gid']);
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php?current_tab='.$_POST['current_tab'].SEP.'course_id='.$course_id);
	exit;
} else if (isset($_POST['submit_yes']) && $_POST['func'] =='group_remove' ) {
	// Remove student as a member of the group
	group_remove($_POST['id'],$_POST['gid']);
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php?current_tab='.$_POST['current_tab'].SEP.'course_id='.$course_id);
	exit;
}
require(AT_INCLUDE_PATH.'header.inc.php');

//Store id's into a hidden element for use by functions
$j = 0;
while ($_GET['id'.$j]) {
	$_GET['id'.$j] = abs($_GET['id'.$j]);
	if ($_GET['id'.$j] == $owner) {
		//do nothing
	} else {
		$hidden_vars['id['.$j.']'] = $_GET['id'.$j];
		$member_ids .= $_GET['id'.$j].', ';
	}	
	$j++;
}
$member_ids = substr($member_ids, 0, -2);

$hidden_vars['func']     = $_GET['func'];
$hidden_vars['current_tab'] = $_GET['current_tab'];
$hidden_vars['gid']		 = abs($_GET['gid']);
$hidden_vars['course_id'] = $course_id;
//get usernames of users about to be edited
$str = get_usernames($member_ids);

//Print appropriate confirm msg for action
if ($_GET['func'] == 'remove') {
	$confirm = array('REMOVE_STUDENT',   $str);
	$msg->addConfirm($confirm, $hidden_vars);
} else if ($_GET['func'] == 'enroll') {
	$confirm = array('ENROLL_STUDENT',   $str);
	$msg->addconfirm($confirm, $hidden_vars);
} else if ($_GET['func'] == 'unenroll') {
	if (check_roles($member_ids) == 1) {
		$confirm = array('UNENROLL_PRIV', $str);
		$msg->addConfirm($confirm, $hidden_vars);
	} else {
		$confirm = array('UNENROLL_STUDENT', $str);
		$msg->addConfirm($confirm, $hidden_vars);
	}
} else if ($_GET['func'] == 'alumni') {
	$confirm = array('ALUMNI',   $str);
	$msg->addConfirm($confirm, $hidden_vars);
} else if ($_GET['func'] == 'group') {
	$sql = "SELECT title FROM ".TABLE_PREFIX."groups WHERE group_id=".$hidden_vars['gid'];
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	$confirm = array('STUDENT_GROUP', $row['title'], $str);
	$msg->addConfirm($confirm, $hidden_vars);
} else if ($_GET['func'] == 'group_remove') {
	$sql = "SELECT title FROM ".TABLE_PREFIX."groups WHERE group_id=".$hidden_vars['gid'];
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	$confirm = array('STUDENT_REMOVE_GROUP', $row['title'], $str);
	$msg->addConfirm($confirm, $hidden_vars);
}
		
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');

?>