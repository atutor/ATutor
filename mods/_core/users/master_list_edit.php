<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

$_REQUEST['id'] = $addslashes($_REQUEST['id']);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_core/users/master_list.php');
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['public_field'] = trim($_POST['public_field']);
	if ($_POST['public_field'] == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('student_id')));
	}

	if (!$msg->containsErrors()) {
		$_POST['public_field'] = $addslashes($_POST['public_field']);

		$sql = "UPDATE %smaster_list SET public_field='%s' WHERE public_field='%s'";
		$result = queryDB($sql, array(TABLE_PREFIX, $_POST['public_field'], $_POST['id']));
        global $sqlout;
		write_to_log(AT_ADMIN_LOG_UPDATE, 'master_list', $result, $sqlout);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		header('Location: '.AT_BASE_HREF.'mods/_core/users/master_list.php');
		exit;
	}
} 

require(AT_INCLUDE_PATH.'header.inc.php'); 

$sql = "SELECT * FROM %smaster_list WHERE public_field='%s'";
$row = queryDB($sql, array(TABLE_PREFIX, $_REQUEST['id']), TRUE);

if(count($row) == 0){
	$msg->addError('USER_NOT_FOUND');
	$msg->printErrors();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
} else {
	$_POST = $row;
}

$savant->display('admin/users/master_list_edit.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>