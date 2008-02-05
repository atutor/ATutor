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
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_GROUPS);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} 
else if (isset($_POST['submit_yes'])) {
	$type_id = abs($_POST['id']);

	$sql = "DELETE FROM ".TABLE_PREFIX."groups_types WHERE type_id=$type_id AND course_id=$_SESSION[course_id]";
	mysql_query($sql, $db);
	if (mysql_affected_rows($db) == 1) {
		$sql = "SELECT group_id FROM ".TABLE_PREFIX."groups WHERE type_id=$type_id";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			$sql = "DELETE FROM ".TABLE_PREFIX."groups_members WHERE group_id=$row[group_id]";
			mysql_query($sql, $db);

			// should be handled by each module:
			//remove all listings in tests_groups table
			$sql = "DELETE FROM ".TABLE_PREFIX."tests_groups WHERE group_id=$row[group_id]";
			mysql_query($sql, $db);
		}
		$sql = "DELETE FROM ".TABLE_PREFIX."groups WHERE type_id=$type_id";
		$result = mysql_query($sql, $db);
	}

	$msg->addFeedback('GROUP_TYPE_DELETED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['id'] = intval($_GET['id']);

$sql = "SELECT * FROM ".TABLE_PREFIX."groups_types WHERE type_id=$_GET[id] AND course_id=$_SESSION[course_id]";
$result = mysql_query($sql,$db);
if (!($row = mysql_fetch_assoc($result))) {
	$msg->printErrors('GROUP_TYPE_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

unset($hidden_vars);
$hidden_vars['id'] = $_GET['id'];

$msg->addConfirm(array('DELETE_GROUP_TYPE',$row['title']), $hidden_vars);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>