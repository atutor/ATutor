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
tool_origin();

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
//        $return_url = $_SESSION['tool_origin']['url'];
//        tool_origin('off');
//header('Location: '.$return_url);
	header('Location: grade_scale.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	/* delete has been confirmed, delete this category */
	$grade_scale_id	= intval($_POST['grade_scale_id']);

	$sql = "DELETE FROM %sgrade_scales WHERE grade_scale_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $grade_scale_id));

	$sql = "DELETE FROM %sgrade_scales_detail WHERE grade_scale_id=%d";
    $result = queryDB($sql, array(TABLE_PREFIX, $grade_scale_id));

	$sql = "UPDATE %sgradebook_tests SET grade_scale_id=0 WHERE grade_scale_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $grade_scale_id));

    $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
    $return_url = $_SESSION['tool_origin']['url'];
    tool_origin('off');
    header('Location: '.$return_url);
	//header('Location: grade_scale.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['grade_scale_id'] = intval($_GET['grade_scale_id']); 

$sql = "SELECT grade_scale_id, scale_name FROM %sgrade_scales g WHERE g.grade_scale_id=%d";
$row = queryDB($sql,array(TABLE_PREFIX, $_GET['grade_scale_id']), TRUE);

if(count($row) == 0){
	$msg->printErrors('ITEM_NOT_FOUND');
} else {

	$hidden_vars['scale_name']= $row['scale_name'];
	$hidden_vars['grade_scale_id']	= $row['grade_scale_id'];

	$confirm = array('DELETE_GRADE_SCALE', $row['scale_name']);
	$msg->addConfirm($confirm, $hidden_vars);
	
	$msg->printConfirm();
}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>