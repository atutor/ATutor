<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../../include/');
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

	$sql = "SELECT type_id FROM %sgroups_types WHERE type_id=%d AND course_id=%d";
	$rows_group_types = queryDB($sql, array(TABLE_PREFIX, $type_id, $_SESSION[course_id]));


	if(count($rows_group_types) > 0){
		$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED | AT_MODULE_STATUS_DISABLED);
		$keys = array_keys($module_list);
		foreach ($keys as $module_name) {	
			$module =& $module_list[$module_name];
			$module->deleteGroup($id);
		}


		$sql = "DELETE FROM %sgroups WHERE group_id=%d AND type_id=%d";
		$result_groups = queryDB($sql, array(TABLE_PREFIX, $id, $type_id));

		if($result_groups > 0){
			//remove all listings in groups_members table
			$sql = "DELETE FROM %sgroups_members WHERE group_id=%d";
			$result = queryDB($sql, array(TABLE_PREFIX, $id));
			
			// should be handled by each module:
			$sql = "DELETE FROM ".TABLE_PREFIX."tests_groups WHERE group_id=$id";
			$result = queryDB($sql, array(TABLE_PREFIX, $id));
		}
	}

	$msg->addFeedback('GROUP_DELETED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['id'] = intval($_GET['id']);

$sql = "SELECT * FROM %sgroups WHERE group_id=%d";
$row_groups = queryDB($sql,array(TABLE_PREFIX, $_GET['id']), TRUE);

if(count($row_groups) == 0){

	$msg->printErrors('GROUP_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$sql = "SELECT title FROM %sgroups_types WHERE type_id=%d AND course_id=%d";
$rows_group_types = queryDB($sql,array(TABLE_PREFIX, $row_groups['type_id'], $_SESSION['course_id']), TRUE);


if(count($rows_group_types) == 0){
	$msg->printErrors('GROUP_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

unset($hidden_vars);
$hidden_vars['id'] = $_GET['id'];
$hidden_vars['type_id'] = $row_groups['type_id'];
$row_groups['title'] = htmlspecialchars($row_groups['title'], ENT_QUOTES); 
$msg->addConfirm(array('DELETE_GROUP',AT_print($row_groups['title'], 'groups.title')), $hidden_vars);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>