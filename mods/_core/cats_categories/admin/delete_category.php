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
admin_authenticate(AT_ADMIN_PRIV_CATEGORIES);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: course_categories.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	/* delete has been confirmed, delete this category */
	$cat_id	= intval($_POST['cat_id']);

	if (!is_array($categories[$cat_id]['children'])) {

		$sql = "DELETE FROM %scourse_cats WHERE cat_id=%d";
		$rows_cats = queryDB($sql, array(TABLE_PREFIX, $cat_id));
		write_to_log(AT_ADMIN_LOG_DELETE, 'course_cats', count($rows_cats), $sqlout);

		$sql = "UPDATE %scourses SET cat_id=0 WHERE cat_id=%d";
		$rows_courses = queryDB($sql, array(TABLE_PREFIX, $cat_id));		
		write_to_log(AT_ADMIN_LOG_DELETE, 'courses', count($rows_courses), $sqlout);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: course_categories.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

	$_GET['cat_id'] = intval($_GET['cat_id']); 

	$sql = "SELECT * FROM %scourse_cats WHERE cat_id=%d";
	$row_cats = queryDB($sql,array(TABLE_PREFIX, $_GET['cat_id']), TRUE);

    if (count($row_cats) == 0) {
		$msg->printErrors('ITEM_NOT_FOUND');
	} else {
		$row = $row_cats;
		
		$hidden_vars['cat_name']= $row['cat_name'];
		$hidden_vars['cat_id']	= $row['cat_id'];

		$confirm = array('DELETE_CATEGORY', AT_print($row['cat_name'], 'course_cats.cat_name'));
		$msg->addConfirm($confirm, $hidden_vars);
		
		$msg->printConfirm();
	}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>