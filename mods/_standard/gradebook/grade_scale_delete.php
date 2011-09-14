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

$page = 'gradebook';

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GRADEBOOK);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: grade_scale.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	/* delete has been confirmed, delete this category */
	$grade_scale_id	= intval($_POST['grade_scale_id']);

	$sql = "DELETE FROM ".TABLE_PREFIX."grade_scales WHERE grade_scale_id=$grade_scale_id";
	$result = mysql_query($sql, $db) or die(mysql_error());

	$sql = "DELETE FROM ".TABLE_PREFIX."grade_scales_detail WHERE grade_scale_id=$grade_scale_id";
	$result = mysql_query($sql, $db) or die(mysql_error());

	$sql = "UPDATE ".TABLE_PREFIX."gradebook_tests SET grade_scale_id=0 WHERE grade_scale_id=$grade_scale_id";
	$result = mysql_query($sql, $db) or die(mysql_error());

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: grade_scale.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['grade_scale_id'] = intval($_GET['grade_scale_id']); 

$sql = "SELECT grade_scale_id, scale_name FROM ".TABLE_PREFIX."grade_scales g WHERE g.grade_scale_id=$_GET[grade_scale_id]";
$result = mysql_query($sql,$db) or die(mysql_error());

if (mysql_num_rows($result) == 0) {
	$msg->printErrors('ITEM_NOT_FOUND');
} else {
	$row = mysql_fetch_assoc($result);
	
	$hidden_vars['scale_name']= $row['scale_name'];
	$hidden_vars['grade_scale_id']	= $row['grade_scale_id'];

	$confirm = array('DELETE_GRADE_SCALE', $row['scale_name']);
	$msg->addConfirm($confirm, $hidden_vars);
	
	$msg->printConfirm();
}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>