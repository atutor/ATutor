<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_FORUMS);

include(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: forums.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$forum_id = intval($_POST['forum']);

	$sql	= "SELECT post_id FROM %sforums_threads WHERE forum_id=%d";
	$rows_threads = queryDB($sql, array(TABLE_PREFIX, $forum_id));

    foreach($rows_threads as $row){
		$sql	 = "DELETE FROM %sforums_accessed WHERE post_id=%d";
		$result2 = queryDB($sql, array(TABLE_PREFIX, $row['post_id']));
	}

	$sql	= "DELETE FROM %sforums_subscriptions WHERE forum_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $forum_id));

	$sql    = "DELETE FROM %sforums_threads WHERE forum_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $forum_id));

	$sql = "DELETE FROM %sforums_courses WHERE forum_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $forum_id));
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_DELETE, 'forums_courses', $result, $sqlout);

	$sql    = "DELETE FROM %sforums WHERE forum_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $forum_id));
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_DELETE, 'forums', $result, $sqlout);
	
	$sql = "OPTIMIZE TABLE %sforums_threads";
	$result = queryDB($sql, array(TABLE_PREFIX,));
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
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