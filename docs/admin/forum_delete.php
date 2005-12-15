<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_FORUMS);

require(AT_INCLUDE_PATH.'lib/forums.inc.php');

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: forums.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$forum_id = intval($_POST['forum']);

	$sql	= "SELECT post_id FROM ".TABLE_PREFIX."forums_threads WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_array($result)) {
		$sql	 = "DELETE FROM ".TABLE_PREFIX."forums_accessed WHERE post_id=$row[post_id]";
		$result2 = mysql_query($sql, $db);
	}

	$sql	= "DELETE FROM ".TABLE_PREFIX."forums_subscriptions WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);

	$sql    = "DELETE FROM ".TABLE_PREFIX."forums_threads WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);

	$sql = "DELETE FROM ".TABLE_PREFIX."forums_courses WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);
	write_to_log(AT_ADMIN_LOG_DELETE, 'forums_courses', mysql_affected_rows($db), $sql);

	$sql    = "DELETE FROM ".TABLE_PREFIX."forums WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);
	write_to_log(AT_ADMIN_LOG_DELETE, 'forums', mysql_affected_rows($db), $sql);
	
	$sql = "OPTIMIZE TABLE ".TABLE_PREFIX."forums_threads";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('FORUM_DELETED');
	header('Location: forums.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

	$_GET['forum'] = intval($_GET['forum']); 

	$row = get_forum($_GET['forum']);

	if (!is_array($row)) {
		$msg->addError('FORUM_NOT_FOUND');
		$msg->printErrors();
	} else {

		$hidden_vars['delete_forum'] = TRUE;
		$hidden_vars['forum'] = $_GET['forum'];
		$msg->addConfirm(array('DELETE_FORUM', AT_print($row['title'], 'forums.title')), $hidden_vars);
		$msg->printConfirm();
	}

require(AT_INCLUDE_PATH.'footer.inc.php'); 

?>