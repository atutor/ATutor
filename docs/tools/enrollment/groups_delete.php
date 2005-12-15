<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TEST_CREATE);

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('course_enrolment');
$_section[1][1] = 'tools/enrollment/index.php';
$_section[2][0] = _AT('groups');
$_section[2][1] = 'tools/enrollment/groups.php';
$_section[3][0] = _AT('delete_group');

if (isset($_POST['submit_no'])) {
		$msg->addFeedback('CANCELLED');
		header('Location: groups.php');
		exit;
} 
else if (isset($_POST['submit_yes'])) {
	$_POST['gid'] = intval($_POST['gid']);

	//remove cat
	$sql = "DELETE FROM ".TABLE_PREFIX."groups WHERE course_id=$_SESSION[course_id] AND group_id=".$_POST['gid'];
	$result = mysql_query($sql, $db);

	if (mysql_affected_rows($db)) {
		//remove all listings in groups_members table
		$sql = "DELETE FROM ".TABLE_PREFIX."groups_members WHERE group_id=".$_POST['gid'];
		$result = mysql_query($sql, $db);

		//remove all listings in tests_groups table
		$sql = "DELETE FROM ".TABLE_PREFIX."tests_groups WHERE group_id=".$_POST['gid'];
		$result = mysql_query($sql, $db);
	}

	$msg->addFeedback('GROUP_DELETED');
	header('Location: groups.php');
	exit;
}

	require(AT_INCLUDE_PATH.'header.inc.php');

	$sql	= "SELECT title FROM ".TABLE_PREFIX."groups WHERE course_id=$_SESSION[course_id] AND group_id=$_GET[gid]";
	$result	= mysql_query($sql, $db);
	$row = mysql_fetch_array($result);

	unset($hidden_vars);
	$hidden_vars['gid'] = $_GET['gid'];

	$msg->addConfirm(array('DELETE_GROUP',$row['title']), $hidden_vars);
	$msg->printConfirm();

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>