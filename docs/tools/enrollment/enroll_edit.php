<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

$page = 'enroll_edit';

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

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
function remove ($list) {
	global $db;

	$members = '(member_id='.$list[0].')';
	for ($i=1; $i < count($list); $i++) {
		$members .= ' OR (member_id='.$list[$i].')';
	}
	$sql	= "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE course_id = $_SESSION[course_id] AND ($members)";	
	$result = mysql_query($sql, $db);
}

/**
* Unenrolls students from course enrollement
* @access  private
* @param   array $list			the IDs of the members to be removed
* @author  Shozub Qureshi
*/
function unenroll ($list) {
	global $db;
	$members = implode(',', $list);

	if ($members) {
		$members = addslashes($members);
		$sql    = "UPDATE ".TABLE_PREFIX."course_enrollment SET approved='n',`privileges`=0, role='' WHERE course_id=$_SESSION[course_id] AND member_id IN ($members)";
		$result = mysql_query($sql, $db);

		$sql    = "DELETE FROM ".TABLE_PREFIX."groups_members WHERE member_id IN ($members)";
		$result = mysql_query($sql, $db);
	}
}

/**
* Enrolls students into course enrollement
* @access  private
* @param   array $list			the IDs of the members to be added
* @author  Shozub Qureshi
*/
function enroll ($list) {
	global $db, $msg;	
	require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

	$members = '(member_id='.$list[0].')';
	for ($i=1; $i < count($list); $i++)	{
		$members .= ' OR (member_id='.$list[$i].')';
	}
	
	$sql    = "UPDATE ".TABLE_PREFIX."course_enrollment SET approved = 'y' WHERE course_id = $_SESSION[course_id] AND ($members)";
	$result = mysql_query($sql, $db);

	if ($result) {
		//get First_name, Last_name of course Instructor
		$sql_from    = "SELECT first_name, last_name, email FROM ".TABLE_PREFIX."members WHERE member_id = $_SESSION[member_id]";
		$result_from = mysql_query($sql_from, $db);
		$row_from    = mysql_fetch_assoc($result_from);

		$email_from_name  = $row_from['first_name'] . ' ' . $row_from['last_name'];
		$email_from = $row_from['email'];

		//get email addresses of users:
		$sql_to    = "SELECT email FROM ".TABLE_PREFIX."members WHERE ($members)";
		$result_to = mysql_query($sql_to, $db);

		while ($row_to = mysql_fetch_assoc($result_to)) {
			// send email here.
			$subject = SITE_NAME.': '._AT('enrol_message_subject');
			$body = SITE_NAME.': '._AT('enrol_message_approved', $_SESSION['course_title'], SITE_NAME)."\n\n";

			$mail = new ATutorMailer;
			$mail->From     = $email_from;
			$mail->FromName = $email_from_name;
			$mail->AddAddress($row_to['email']);
			$mail->Subject  = $subject;
			$mail->Body     = $body;
			
			if (!$mail->Send()) {
				$msg->printErrors('SENDING_ERROR');
			}

			unset($mail);
		}
	}
}

/**
* Marks a student as an alumni of the course (not enrolled, but can view course material and participate in forums)
* @access  private
* @param   array $list			the IDs of the members to be alumni
* @author  Heidi Hazelton
*/
function alumni ($list) {
	global $db;
	$members = '(member_id='.$list[0].')';
	for ($i=1; $i < count($list); $i++)	{
		$members .= ' OR (member_id='.$list[$i].')';
	}
	
	$sql    = "UPDATE ".TABLE_PREFIX."course_enrollment SET approved = 'a' WHERE course_id = $_SESSION[course_id] AND ($members)";
	$result = mysql_query($sql, $db);
}

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('course_enrolment');
$_section[1][1] = 'tools/enrollment/index.php';
$_section[2][0] = _AT('enrollment_editor');
$_section[2][1] = 'tools/enroll_edit.php';

//if user decides to forgo option
if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?current_tab='.$_POST['curr_tab']);
	exit;
}
	
//Remove student from list (unenrolls automatically)
else if (isset($_POST['submit_yes']) && $_POST['func'] =='remove' ) {

	//echo 'atleast this worked';
	remove($_POST['id']);

	$msg->addFeedback('MEMBERS_REMOVED');
	header('Location: index.php?current_tab='.$_POST['curr_tab']);
	exit;
}

//Unenroll student from course
else if (isset($_POST['submit_yes']) && $_POST['func'] =='unenroll' ) {

	unenroll($_POST['id']);

	$msg->addFeedback('MEMBERS_UNENROLLED');
	header('Location: index.php?current_tab='.$_POST['curr_tab']);
	exit;
}

//Enroll student in course
else if (isset($_POST['submit_yes']) && $_POST['func'] =='enroll' ) {

	enroll($_POST['id']);

	$msg->addFeedback('MEMBERS_ENROLLED');
	header('Location: index.php?current_tab='.$_POST['curr_tab']);
	exit;
}

//Mark student as course alumnus
else if (isset($_POST['submit_yes']) && $_POST['func'] =='alumni' ) {

	alumni($_POST['id']);
	
	$msg->addFeedback('MEMBERS_ALUMNI');
	header('Location: index.php?current_tab='.$_POST['curr_tab']);
	exit;
}
require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printAll();

//Store id's into a hidden element for use by functions
$j = 0;
while ($_GET['id'.$j]) {
	$hidden_vars['id['.$j.']'] = $_GET['id'.$j];
	$member_ids .= $_GET['id'.$j].', ';
	$j++;
}
$member_ids = substr($member_ids, 0, -2);

$hidden_vars['func']     = $_GET['func'];
$hidden_vars['curr_tab'] = $_GET['curr_tab'];

//get usernames of users about to be edited
$str = get_usernames($member_ids);
				
//Print appropriate warning for action
if ($_GET['func'] == 'remove') {
	$confirm = array('REMOVE_STUDENT',   $str);
	$msg->addConfirm($confirm, $hidden_vars);
}

else if ($_GET['func'] == 'enroll') {
	$confirm = array('ENROLL_STUDENT',   $str);
	$msg->addconfirm($confirm, $hidden_vars);
} 

else if ($_GET['func'] == 'unenroll') {
	if (check_roles($member_ids) == 1) {
		$confirm = array('UNENROLL_PRIV', $str);
		$msg->addConfirm($confirm, $hidden_vars);
	} else {
		$confirm = array('UNENROLL_STUDENT', $str);
		$msg->addConfirm($confirm, $hidden_vars);
	}
} 

else if ($_GET['func'] == 'alumni') {
	$confirm = array('ALUMNI',   $str);
	$msg->addConfirm($confirm, $hidden_vars);
}
		
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');

?>