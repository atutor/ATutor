<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$page = 'my_courses';
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');
require(AT_INCLUDE_PATH.'lib/course.inc.php');

/* verify that this user has status to create courses */
$sql	= "SELECT status FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
$result = mysql_query($sql, $db);
$row	= mysql_fetch_assoc($result);

/*if ($row['status'] != 1) {
	require(AT_INCLUDE_PATH.'header.inc.php');

	$msg->addError('CREATE_NOPERM');
	$msg->printAll();

	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}*/

$_section[0][0] = _AT('my_courses');
$_section[0][1] = 'users/index.php';
$_section[1][0] = _AT('create_course');
$_section[1][1] = 'users/create_course.php';

$title = _AT('create_course');

$course = 0;
$isadmin   = FALSE;

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['form_course'])) {
	$_POST['instructor'] = $_SESSION['member_id'];

	$errors = add_update_course($_POST);
	
	if ($errors !== FALSE) {
		$msg->addFeedback('COURSE_CREATED');
		header('Location: '.$_base_href.'bounce.php?course='.$errors.SEP.'p='.urlencode('index.php'));
		exit;
	}

}

$onload = 'onload="document.course_form.title.focus()"';

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printAll();

//echo '<br />';
require(AT_INCLUDE_PATH.'html/course_properties.inc.php');

require(AT_INCLUDE_PATH.'footer.inc.php');

?>