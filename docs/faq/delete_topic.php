<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
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

authenticate(AT_PRIV_FAQ);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	Header('Location: index_instructor.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$_POST['id'] = intval($_POST['id']);

	// check that this topic_id belongs to this course:
	$sql = "DELETE FROM ".TABLE_PREFIX."faq_topics WHERE topic_id=$_POST[id] AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);
	if (mysql_affected_rows($db) == 1) {
		$sql = "DELETE FROM ".TABLE_PREFIX."faq_entries WHERE topic_id=$_POST[topic_id]";
		$result = mysql_query($sql, $db);
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index_instructor.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');


$_GET['id'] = intval($_GET['id']); 

$sql = "SELECT name, topic_id FROM ".TABLE_PREFIX."faq_topics WHERE topic_id=$_GET[id]";

$result = mysql_query($sql,$db);
if ($row = mysql_fetch_assoc($result)) {
	$hidden_vars['id'] = $_GET['id'];

	$confirm = array('DELETE_FAQ_TOPIC', $row['name']);
	$msg->addConfirm($confirm, $hidden_vars);
	$msg->printConfirm();
} else {
	$msg->addError('TOPIC_NOT_FOUND');
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>