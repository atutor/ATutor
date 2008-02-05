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
} else if (isset($_POST['submit_yes'])) {
	$_POST['id'] = intval($_POST['id']);
	$_POST['type_id'] = intval($_POST['type_id']);

	$id = intval($_POST['id']);
	$type_id = intval($_POST['type_id']);

	$sql = "SELECT type_id FROM ".TABLE_PREFIX."groups_types WHERE type_id=$type_id AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED | AT_MODULE_STATUS_DISABLED);
		$keys = array_keys($module_list);
		foreach ($keys as $module_name) {	
			$module =& $module_list[$module_name];
			$module->deleteGroup($id);
		}

		$sql = "DELETE FROM ".TABLE_PREFIX."groups WHERE group_id=$id AND type_id=$type_id";
		$result = mysql_query($sql, $db);

		if (mysql_affected_rows($db)) {
			//remove all listings in groups_members table
			$sql = "DELETE FROM ".TABLE_PREFIX."groups_members WHERE group_id=$id";
			$result = mysql_query($sql, $db);

			// should be handled by each module:
			//remove all listings in tests_groups table
			$sql = "DELETE FROM ".TABLE_PREFIX."tests_groups WHERE group_id=$id";
			$result = mysql_query($sql, $db);
		}
	}

	$msg->addFeedback('GROUP_DELETED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['id'] = intval($_GET['id']);

$sql = "SELECT * FROM ".TABLE_PREFIX."groups WHERE group_id=$_GET[id]";
$result = mysql_query($sql,$db);
if (!($row = mysql_fetch_assoc($result))) {
	$msg->printErrors('GROUP_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$sql = "SELECT title FROM ".TABLE_PREFIX."groups_types WHERE type_id=$row[type_id] AND course_id=$_SESSION[course_id]";
$result = mysql_query($sql,$db);
if (!($type_row = mysql_fetch_assoc($result))) {
	$msg->printErrors('GROUP_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

unset($hidden_vars);
$hidden_vars['id'] = $_GET['id'];
$hidden_vars['type_id'] = $row['type_id'];

$msg->addConfirm(array('DELETE_GROUP',$row['title']), $hidden_vars);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>