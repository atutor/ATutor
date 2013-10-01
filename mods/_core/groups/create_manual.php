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

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GROUPS);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
} else if (isset($_POST['submit'])) {
	$modules = '';
	if (isset($_POST['modules'])) {
		$modules = implode('|', $_POST['modules']);
	}

	$_POST['type']   = abs($_POST['type']);
	$_POST['prefix'] = trim($_POST['prefix']);
	$_POST['new_type'] = trim($_POST['new_type']);

	$missing_fields = array();

	if (!$_POST['type'] && !$_POST['new_type']) {
		$missing_fields[] = _AT('groups_type');
	}
	if (!$_POST['prefix']) {
		$missing_fields[] = _AT('title');
	}
	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) {
		$_POST['new_type'] = $addslashes($_POST['new_type']);
		$_POST['prefix']      = $addslashes($_POST['prefix']);
		$_POST['description'] = $addslashes($_POST['description']);

		if ($_POST['new_type']) {

			$sql = "INSERT INTO %sgroups_types VALUES (NULL, %d, '%s')";
			$result_group_types = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $_POST['new_type']));
			$type_id = at_insert_id();
			
		} else {

			$sql = "SELECT type_id FROM %sgroups_types WHERE course_id=%d AND type_id=%d";
			$rows_groups_types = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $_POST['type']), TRUE);

			if(count($rows_groups_types) > 0 ){
				$type_id = $rows_groups_types['type_id'];
			} else {
				$type_id = FALSE;
			}
		}

		if ($type_id) {
			$sql = "INSERT INTO %sgroups VALUES (NULL, %d, '%s', '%s', '%s')";
			$result = queryDB($sql, array(TABLE_PREFIX, $type_id, $_POST['prefix'], $_POST['description'], $modules ));
			$group_id = at_insert_id($db);

			$_SESSION['groups'][$group_id] = $group_id;
			// call module init scripts:
			if (isset($_POST['modules'])) {
				foreach ($_POST['modules'] as $mod) {
					$module =& $moduleFactory->getModule($mod);
					$module->createGroup($group_id);
				}
			}
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
	} else {
		$_POST['new_type']    = $stripslashes($_POST['new_type']);
		$_POST['prefix']      = $stripslashes($_POST['prefix']);
		$_POST['description'] = $stripslashes($_POST['description']);
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

$types = array();
$sql = "SELECT type_id, title FROM %sgroups_types WHERE course_id=%d ORDER BY title";
$rows_group_types = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));

foreach($rows_group_types as $row){
	$types[$row['type_id']] = htmlentities_utf8($row['title']);
}

$savant->assign('types', $types);
$savant->display('instructor/groups/create_manual.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>