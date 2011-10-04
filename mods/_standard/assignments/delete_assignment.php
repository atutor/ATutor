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
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ASSIGNMENTS);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	Header('Location: index_instructor.php');
	exit;
}
else if (isset($_POST['submit_yes'])) {
	$_POST['assignment_id'] = intval($_POST['assignment_id']);

	// delete the assignment from the table
	$sql = "DELETE FROM ".TABLE_PREFIX."assignments WHERE course_id=$_SESSION[course_id] AND assignment_id=$_POST[assignment_id]";
	$result = mysql_query($sql, $db);

	// delete all the files for this assignment
	require(AT_INCLUDE_PATH.'../mods/_standard/file_storage/file_storage.inc.php');
	fs_delete_workspace(WORKSPACE_ASSIGNMENT, $_POST['assignment_id']);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index_instructor.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['id'] = intval($_GET['id']); 

$sql = "SELECT title FROM ".TABLE_PREFIX."assignments WHERE course_id=$_SESSION[course_id] AND assignment_id=$_GET[id]";
$result = mysql_query($sql, $db);

if ($row = mysql_fetch_assoc($result)){
	$hidden_vars['assignment_id'] = $_GET['id'];
	$confirm = array('DELETE_ASSIGNMENT', AT_print($row['title'], 'assignment.title'));
	$msg->addConfirm($confirm, $hidden_vars);
	$msg->printConfirm();
}
else {
	$msg->addError('ASSIGNMENT_NOT_FOUND');
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>