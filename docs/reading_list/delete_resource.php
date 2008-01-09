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
// $Id$
define('AT_INCLUDE_PATH', '../include/');
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
	$sql = "DELETE FROM ".TABLE_PREFIX."external_resources WHERE course_id=$_SESSION[course_id] AND resource_id=$resource_id";
	$result = mysql_query($sql, $db);

	// find any readings that use this resource and delete them too
	$sql = "DELETE FROM ".TABLE_PREFIX."reading_list WHERE course_id=$_SESSION[course_id] AND resource_id=$resource_id";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: display_resources.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['id'] = intval($_GET['id']); 
$resource_id = $_GET['id'];

// get the resource ID for this reading
$sql = "SELECT title FROM ".TABLE_PREFIX."external_resources WHERE course_id=$_SESSION[course_id] AND resource_id=$resource_id";
$result = mysql_query($sql, $db);

if ($row = mysql_fetch_assoc($result)){
	$hidden_vars['id'] = $resource_id;
	$confirm = array('RL_DELETE_RESOURCE', $row['title']);
	$msg->addConfirm($confirm, $hidden_vars);
	$msg->printConfirm();
}
else {
	$msg->addError('ITEM_NOT_FOUND');
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>