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
} 
else if (isset($_POST['submit_yes'])) {
	$type_id = abs($_POST['id']);

	$sql = "DELETE FROM %sgroups_types WHERE type_id=%d AND course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $type_id, $_SESSION['course_id'])); 

	if ($result == 1) {
	
		$sql = "SELECT group_id FROM %sgroups WHERE type_id=%d";
		$rows_groups = queryDB($sql, array(TABLE_PREFIX, $type_id));
		
		foreach($rows_groups as $row){
			$sql = "DELETE FROM %sgroups_members WHERE group_id=%d";
			queryDB($sql, array(TABLE_PREFIX, $row['group_id']));
			
			// should be handled by each module:
			//remove all listings in tests_groups table
			$sql = "DELETE FROM %stests_groups WHERE group_id=%d";
			queryDB($sql, array(TABLE_PREFIX, $row['group_id']));
		}

		$sql = "DELETE FROM %sgroups WHERE type_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $type_id));
	}

	$msg->addFeedback('GROUP_TYPE_DELETED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['id'] = intval($_GET['id']);

$sql = "SELECT * FROM %sgroups_types WHERE type_id=%d AND course_id=%d";
$row_group_types = queryDB($sql, array(TABLE_PREFIX, $_GET['id'], $_SESSION['course_id']), TRUE);

if(count($row_group_types) == 0){
	$msg->printErrors('GROUP_TYPE_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

unset($hidden_vars);
$hidden_vars['id'] = $_GET['id'];
$row_group_types['title'] = htmlspecialchars($row_group_types['title'], ENT_QUOTES) ;
$msg->addConfirm(array('DELETE_GROUP_TYPE',$row_group_types['title']), $hidden_vars);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>