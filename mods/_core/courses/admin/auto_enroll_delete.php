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

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: auto_enroll.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	/* delete has been confirmed, delete this category */
	$auto_enroll_id	= intval($_POST['auto_enroll_id']);

	$sql = "DELETE FROM %sauto_enroll WHERE auto_enroll_id=%d";
	$rows_auto_enroll = queryDB($sql, array(TABLE_PREFIX,$auto_enroll_id));

	write_to_log(AT_ADMIN_LOG_DELETE, 'auto_enroll', $rows_auto_enroll, $sqlout);
	
	$sql = "DELETE FROM %sauto_enroll_courses WHERE auto_enroll_id=%d";
	$rows_auto_enroll_courses = queryDB($sql, array(TABLE_PREFIX, $auto_enroll_id));
	
	write_to_log(AT_ADMIN_LOG_DELETE, 'auto_enroll_courses', $rows_auto_enroll_courses, $sqlout);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: auto_enroll.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

	$_GET['auto_enroll_id'] = intval($_GET['auto_enroll_id']); 

	$sql = "SELECT * FROM %sauto_enroll WHERE auto_enroll_id=%d";
	$rows_auto_enroll = queryDB($sql, array(TABLE_PREFIX, $_GET['auto_enroll_id']), TRUE);
	
	if (count($rows_auto_enroll) == 0) {
			$msg->printErrors('ITEM_NOT_FOUND');
	} else {
	    $row = $rows_auto_enroll;
		
		$hidden_vars['name']= $row['name'];
		$hidden_vars['auto_enroll_id']	= $row['auto_enroll_id'];

		$confirm = array('DELETE_AUTO_ENROLL', $row['name']);
		$msg->addConfirm($confirm, $hidden_vars);
		
		$msg->printConfirm();
	}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>