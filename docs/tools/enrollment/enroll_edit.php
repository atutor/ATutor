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
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

$msg =& new Message($savant);

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
* @param   int $form_course_id	the ID of the course
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
* @param   int $form_course_id	the ID of the course
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
* @param   int $form_course_id	the ID of the course
* @author  Shozub Qureshi
*/
function enroll ($list) {
	global $db;

	$members = '(member_id='.$list[0].')';
	for ($i=1; $i < count($list); $i++)	{
		$members .= ' OR (member_id='.$list[$i].')';
	}
	
	$sql    = "UPDATE ".TABLE_PREFIX."course_enrollment SET approved = 'y' WHERE course_id = $_SESSION[course_id] AND ($members)";
	$result = mysql_query($sql, $db);
}

/**
* Marks a student as an alumni of the course (not enrolled, but can view course material and participate in forums)
* @access  private
* @param   array $list			the IDs of the members to be alumni
* @param   int $form_course_id	the ID of the course
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

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
}
echo '</h2>'."\n";

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/enrol_mng-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo '<a href="tools/enrollment/index.php?course='.$_SESSION['course_id'].'">'._AT('course_enrolment').'</a>';
}
echo '</h3><br />'."\n";

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