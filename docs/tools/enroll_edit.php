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

$page = 'enroll_edit';
$_user_location = '';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('enrollment_editor');
$_section[1][1] = 'tools/enroll_edit.php';

$db;
//	debug($_SESSION['course_id']);
//if user decides to forgo option
if (isset($_POST['cancel'])) {
	header('Location: enroll_admin.php?f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
	exit;
}
	
//Remove student from list (unenrolls automatically)
else if (isset($_POST['submit']) && $_POST['func'] =='remove' ) {

	remove($_POST['id'], $_SESSION['course_id']);

	header('Location: enroll_admin.php?f='.urlencode_feedback(AT_FEEDBACK_MEMBERS_REMOVED));
	exit;
}

//Unenroll student from course
else if (isset($_POST['submit']) && $_POST['func'] =='unenroll' ) {


	unenroll($_POST['id'], $_SESSION['course_id']);

	header('Location: enroll_admin.php?f='.urlencode_feedback(AT_FEEDBACK_MEMBERS_UNENROLLED));
	exit;
}

//Enroll student in course
else if (isset($_POST['submit']) && $_POST['func'] =='enroll' ) {

	enroll($_POST['id'], $_SESSION['course_id']);

	header('Location: enroll_admin.php?f='.urlencode_feedback(AT_FEEDBACK_MEMBERS_ENROLLED));
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
	echo '<a href="tools/enroll_admin.php?course='.$_SESSION['course_id'].'">'._AT('course_enrolment').'</a>';
}
echo '</h3><br />'."\n";

require(AT_INCLUDE_PATH.'html/feedback.inc.php')

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="func" value="<?php echo $_GET['func']; ?>" />

	<?php

		//Store id's into a hidden element for use by functions
		$j = 0;
		$i = 1;
		$member_ids = $_GET['id0'].', ';
		while ($_GET['id'.$j]) {
			echo '<input type="hidden" name="id[]" value="'.$_GET['id'.$j].'" />';
			$member_ids .= $_GET['id'.$i].', ';
			$i++;
			$j++;
		}
		$member_ids = substr($member_ids, 0, -4);
		//get usernames of users about to be edited
		$str = get_usernames($member_ids);
				
		//Print appropriate warning for action
		if ($_GET['func'] == remove) {
			$warnings[] = array(AT_WARNING_REMOVE_STUDENT,   $str);
		} else if ($_GET['func'] == enroll) {
			$warnings[] = array(AT_WARNING_ENROLL_STUDENT,   $str);
		} else if ($_GET['func'] == unenroll) {
			if (check_roles($member_ids) == 1) {
				$warnings[] = array(AT_WARNING_UNENROLL_PRIV, $str);
			} else {
				$warnings[] = array(AT_WARNING_UNENROLL_STUDENT, $str);
			}
		}
		
		print_warnings($warnings);
		
	?>
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="90%" summary="" align="center">
		<tr><td class="row1" align="center">
			<input type="submit" class="button" name="submit" value="<?php echo _AT('confirm'); ?>" /> |
			<input type="submit" class="button" name="cancel" value="<?php echo _AT('cancel');  ?>" />
		</td></tr>
	</table>
</form>

<?php 

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
function remove ($list, $form_course_id) {
	global $db;

	$members = '(member_id='.$list[0].')';
	for ($i=1; $i < count($list); $i++) {
		$members .= ' OR (member_id='.$list[$i].')';
	}
	$sql	= "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=($form_course_id) AND ($members)";	
	$result = mysql_query($sql, $db);
}

/**
* Unenrolls students from course enrollement
* @access  private
* @param   array $list			the IDs of the members to be removed
* @param   int $form_course_id	the ID of the course
* @author  Shozub Qureshi
*/
function unenroll ($list, $form_course_id) {
	global $db;
	$members = '(member_id='.$list[0].')';
	
	for ($i=1; $i < count($list); $i++)	{
		$members .= ' OR (member_id='.$list[$i].')';
	}

	$sql    = "UPDATE ".TABLE_PREFIX."course_enrollment SET approved = 'n',`privileges` = 0, `role` = '' WHERE course_id=($form_course_id) AND ($members)";
	$result = mysql_query($sql, $db);
}

/**
* Enrolls students into course enrollement
* @access  private
* @param   array $list			the IDs of the members to be removed
* @param   int $form_course_id	the ID of the course
* @author  Shozub Qureshi
*/
function enroll ($list, $form_course_id) {
	global $db;

	$members = '(member_id='.$list[0].')';
	for ($i=1; $i < count($list); $i++)	{
		$members .= ' OR (member_id='.$list[$i].')';
	}
	
	$sql    = "UPDATE ".TABLE_PREFIX."course_enrollment SET approved = 'y' WHERE course_id=($form_course_id) AND ($members)";
	$result = mysql_query($sql, $db);
}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>