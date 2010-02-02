<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008                                      */
/* Written by Greg Gay & Joel Kronenberg & Chris Ridpath        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: delete_reading.php 7208 2008-01-09 16:07:24Z greg $
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_READING_LIST);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	Header('Location: index_instructor.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$_POST['id'] = intval($_POST['id']);
	$reading_id = $_POST['id'];

	// delete the reading from the list
	$sql = "DELETE FROM ".TABLE_PREFIX."reading_list WHERE course_id=$_SESSION[course_id] AND reading_id=$reading_id";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index_instructor.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['id'] = intval($_GET['id']); 
$reading_id = $_GET['id'];

// get the resource ID for this reading
$sql = "SELECT resource_id FROM ".TABLE_PREFIX."reading_list WHERE course_id=$_SESSION[course_id] AND reading_id=$reading_id";
$result = mysql_query($sql, $db);

if ($row = mysql_fetch_assoc($result)){
	// get the external resource using the resource ID from the reading
	$resource_id = $row['resource_id'];
	$sql = "SELECT title, date FROM ".TABLE_PREFIX."external_resources WHERE course_id=$_SESSION[course_id] AND resource_id=$resource_id";
	$resource_result = mysql_query($sql, $db);
	if ($resource_row = mysql_fetch_assoc($resource_result)){
		$hidden_vars['id'] = $reading_id;
		$confirm = array('RL_DELETE_READING', $resource_row['title']);
		$msg->addConfirm($confirm, $hidden_vars);
		$msg->printConfirm();
	}
} else {
	$msg->printErrors('ITEM_NOT_FOUND');
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>