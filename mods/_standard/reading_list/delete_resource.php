<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008                                      */
/* Written by Greg Gay & Joel Kronenberg & Chris Ridpath        */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_READING_LIST);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	Header('Location: display_resources.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$_POST['id'] = intval($_POST['id']);
	$resource_id = $_POST['id'];

	// delete the resource from the list
	$sql = "DELETE FROM %sexternal_resources WHERE course_id=%d AND resource_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $resource_id));
	
	// find any readings that use this resource and delete them too
	$sql = "DELETE FROM %sreading_list WHERE course_id=%d AND resource_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $resource_id));
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: display_resources.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['id'] = intval($_GET['id']); 
$resource_id = $_GET['id'];

// get the resource ID for this reading
$sql = "SELECT title FROM %sexternal_resources WHERE course_id=%d AND resource_id=%d";
$row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $resource_id), TRUE);

if(count($row) > 0){
	$hidden_vars['id'] = $resource_id;
	$confirm = array('RL_DELETE_RESOURCE', AT_print($row['title'], 'reading_list.title'));
	$msg->addConfirm($confirm, $hidden_vars);
	$msg->printConfirm();
}
else {
	$msg->addError('ITEM_NOT_FOUND');
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>