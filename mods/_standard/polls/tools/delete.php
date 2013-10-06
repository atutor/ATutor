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
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_POLLS);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$_POST['pid'] = intval($_POST['pid']);

	$sql = "DELETE FROM %spolls WHERE poll_id=%d AND course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $_POST['pid'], $_SESSION['course_id']));

	$sql = "DELETE FROM %spolls_members WHERE poll_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $_POST['pid']));
    
    if($result > 0){
	    $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

$_GET['pid'] = intval($_GET['pid']); 

$sql = "SELECT * FROM %spolls WHERE poll_id=%d AND course_id=%d";
$rows_polls = queryDB($sql, array(TABLE_PREFIX, $_GET['pid'], $_SESSION['course_id']), TRUE);

if(count($rows_polls) == 0){
	$msg->addError('ITEM_NOT_FOUND');
} else {

	$hidden_vars['delete_poll'] = TRUE;
	$hidden_vars['pid'] = $_GET['pid'];

	$confirm = array('DELETE_POLL', AT_print($row_polls['question'], 'polls.question'));
	$msg->addConfirm($confirm, $hidden_vars);
	$msg->printConfirm();

}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>