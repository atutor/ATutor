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

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_POLLS);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	Header('Location: index.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$_POST['pid'] = intval($_POST['pid']);

	$sql = "DELETE FROM ".TABLE_PREFIX."polls WHERE poll_id=$_POST[pid] AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);

	$sql = "DELETE FROM ".TABLE_PREFIX."polls_members WHERE poll_id=$_POST[pid] AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('POLL_DELETED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

$_GET['pid'] = intval($_GET['pid']); 

$sql = "SELECT * FROM ".TABLE_PREFIX."polls WHERE poll_id=$_GET[pid] AND course_id=$_SESSION[course_id]";

$result = mysql_query($sql,$db);
if (mysql_num_rows($result) == 0) {
	$msg->addError('POLL_NOT_FOUND');
} else {
	$row = mysql_fetch_assoc($result);

	$hidden_vars['delete_poll'] = TRUE;
	$hidden_vars['pid'] = $_GET['pid'];

	$confirm = array('DELETE_POLL', AT_print($row['question'], 'polls.question'));
	$msg->addConfirm($confirm, $hidden_vars);
	$msg->printConfirm();

}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>