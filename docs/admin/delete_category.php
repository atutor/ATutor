<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
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
		$sql = "DELETE FROM ".TABLE_PREFIX."course_cats WHERE cat_id=$cat_id";
		$result = mysql_query($sql, $db);

		write_to_log(AT_ADMIN_LOG_DELETE, 'course_cats', mysql_affected_rows($db), $sql);

		$sql = "UPDATE ".TABLE_PREFIX."courses SET cat_id=0 WHERE cat_id=$cat_id";
		$result = mysql_query($sql, $db);

		write_to_log(AT_ADMIN_LOG_DELETE, 'courses', mysql_affected_rows($db), $sql);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: course_categories.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

	$_GET['cat_id'] = intval($_GET['cat_id']); 

	$sql = "SELECT * FROM ".TABLE_PREFIX."course_cats WHERE cat_id=$_GET[cat_id]";
	$result = mysql_query($sql,$db);

	if (mysql_num_rows($result) == 0) {
		$msg->printErrors('ITEM_NOT_FOUND');
	} else {
		$row = mysql_fetch_assoc($result);
		
		$hidden_vars['cat_name']= $row['cat_name'];
		$hidden_vars['cat_id']	= $row['cat_id'];

		$confirm = array('DELETE_CATEGORY', AT_print($row['cat_name'], 'course_cats.cat_name'));
		$msg->addConfirm($confirm, $hidden_vars);
		
		$msg->printConfirm();
	}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>