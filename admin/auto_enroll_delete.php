<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: auto_enroll_delete.php 7208 2008-02-20 16:07:24Z cindy $

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: auto_enroll.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	/* delete has been confirmed, delete this category */
	$auto_enroll_id	= intval($_POST['auto_enroll_id']);

	$sql = "DELETE FROM ".TABLE_PREFIX."auto_enroll WHERE auto_enroll_id=$auto_enroll_id";
	$result = mysql_query($sql, $db);

	write_to_log(AT_ADMIN_LOG_DELETE, 'auto_enroll', mysql_affected_rows($db), $sql);

	$sql = "DELETE FROM ".TABLE_PREFIX."auto_enroll_courses WHERE auto_enroll_id=$auto_enroll_id";
	$result = mysql_query($sql, $db);

	write_to_log(AT_ADMIN_LOG_DELETE, 'auto_enroll_courses', mysql_affected_rows($db), $sql);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: auto_enroll.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

	$_GET['auto_enroll_id'] = intval($_GET['auto_enroll_id']); 

	$sql = "SELECT * FROM ".TABLE_PREFIX."auto_enroll WHERE auto_enroll_id=$_GET[auto_enroll_id]";
	$result = mysql_query($sql,$db) or die(mysql_error());

	if (mysql_num_rows($result) == 0) {
		$msg->printErrors('ITEM_NOT_FOUND');
	} else {
		$row = mysql_fetch_assoc($result);
		
		$hidden_vars['name']= $row['name'];
		$hidden_vars['auto_enroll_id']	= $row['auto_enroll_id'];

//		$confirm = array('DELETE_AUTO_ENROLL', AT_print($row['name'], 'auto_enroll.name'));
		$confirm = array('DELETE_AUTO_ENROLL', $row['name']);
		$msg->addConfirm($confirm, $hidden_vars);
		
		$msg->printConfirm();
	}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>