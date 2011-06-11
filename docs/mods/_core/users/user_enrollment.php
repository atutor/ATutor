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
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ENROLLMENT);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_core/users/users.php');
	exit;
} else if (isset($_POST['enrolled_unenroll'])) {
	$_POST['id'] = intval($_POST['id']);

	if (!is_array($_POST['enrolled'])) {
		$msg->addError('NO_ITEM_SELECTED');
	} else {
		$cids = implode(',', $_POST['enrolled']);
		$sql = "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE member_id={$_POST['id']} AND course_id IN ($cids)";
		mysql_query($sql, $db);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.$_SERVER['PHP_SELF'] . '?id='.$_POST['id']);
		exit;
	}
} else if (isset($_POST['pending_remove'])) {
	$_POST['id'] = intval($_POST['id']);

	if (!is_array($_POST['pending'])) {
		$msg->addError('NO_ITEM_SELECTED');
	} else {
		$cids = implode(',', $_POST['pending']);
		$sql = "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE member_id={$_POST['id']} AND course_id IN ($cids)";
		mysql_query($sql, $db);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.$_SERVER['PHP_SELF'] . '?id='.$_POST['id']);
		exit;
	}
} else if (isset($_POST['pending_enroll'])) {
	$_POST['id'] = intval($_POST['id']);

	if (!is_array($_POST['pending'])) {
		$msg->addError('NO_ITEM_SELECTED');
	} else {
		$cids = implode(',', $_POST['pending']);
		$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET approved='y' WHERE member_id={$_POST['id']} AND course_id IN ($cids)";
		mysql_query($sql, $db);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.$_SERVER['PHP_SELF'] . '?id='.$_POST['id']);
		exit;
	}
} else if (isset($_POST['not_enrolled_enroll'])) {
	$_POST['id'] = intval($_POST['id']);

	if (!is_array($_POST['not_enrolled'])) {
		$msg->addError('NO_ITEM_SELECTED');
	} else {
		foreach ($_POST['not_enrolled'] as $cid) {
			$sql = "INSERT INTO ".TABLE_PREFIX."course_enrollment VALUES ({$_POST['id']}, $cid, 'y', 0, '', 0)";
			mysql_query($sql, $db);
		}
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.$_SERVER['PHP_SELF'] . '?id='.$_POST['id']);
		exit;
	}
}

$id = intval($_GET['id']);

// add the user's name to the page heading:
$_pages['mods/_core/users/user_enrollment.php']['title'] = _AT('enrollment').': '.get_display_name($id);

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT login FROM ".TABLE_PREFIX."members WHERE member_id=$id";
$result = mysql_query($sql, $db);

if (!$row = mysql_fetch_assoc($result)) {
	$msg->printErrors('USER_NOT_FOUND');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$enrollment = array();
$sql = "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$id";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	$enrollment[$row['course_id']] = $row;
}

$instruct     = array();
$enrolled     = array();
$pending      = array();
$not_enrolled = array();

foreach ($system_courses as $cid => $course) {
	if ($course['member_id'] == $id) {
		$instruct[] = $cid;
	} else if (isset($enrollment[$cid]) && $enrollment[$cid]['approved'] == 'y') {
		$enrolled[] = $cid;
	} else if (isset($enrollment[$cid]) && $enrollment[$cid]['approved'] == 'n') {
		$pending[] = $cid;
	} else {
		$not_enrolled[] = $cid;
	}
}
$savant->assign('system_courses', $system_courses);
$savant->assign('instruct', $instruct);
$savant->assign('enrolled', $enrolled);
$savant->assign('pending', $pending);
$savant->assign('not_enrolled', $not_enrolled);
$savant->assign('id', $id);
$savant->display('admin/users/user_enrollment.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>