<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

$page = 'courses';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

if (isset($_POST['submit_yes'])) {
	$sql = "SELECT M.member_id, M.login FROM ".TABLE_PREFIX."members M, ".TABLE_PREFIX."courses C WHERE C.course_id=".$_POST['course']." and C.member_id=M.member_id";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$_SESSION['login']      = $row['login'];
		$_SESSION['valid_user'] = true;
		$_SESSION['member_id']	= intval($row['member_id']);

		header('Location: ../bounce.php?course='.$_POST['course']);
		exit;
	}
}
else if (isset($_POST['submit_no'])) {

	$msg->addFeedback('CANCELLED');
	Header('Location: courses.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 


	$sql = "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=".$_REQUEST['course'];
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_array($result);

	$hidden_vars['course'] = $_GET['course'];

	$msg->addConfirm(array('LOGIN_INSTRUCTOR', SITE_NAME, $row['title']), $hidden_vars);
	$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>