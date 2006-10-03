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
// $Id: delete_comment.php 5999 2006-03-20 20:18:07Z joel $

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

// authenticate ot+oid..
$owner_type = abs($_REQUEST['ot']);
$owner_id = abs($_REQUEST['oid']);
if (!($owner_status = blogs_authenticate($owner_type, $owner_id)) || !query_bit($owner_status, BLOGS_AUTH_WRITE)) {
	$msg->addError('ACCESS_DENIED');
	header('Location: index.php');
	exit;
}

$id = abs($_REQUEST['id']);
$delete_id = abs($_REQUEST['delete_id']);

$sql = "SELECT post_id FROM ".TABLE_PREFIX."blog_posts WHERE owner_type=$owner_type AND owner_id=$owner_id AND post_id=$id";
$result = mysql_query($sql, $db);
if (!$row = mysql_fetch_assoc($result)) {
	$msg->addError('ACCESS_DENIED');
	header('Location: index.php');
	exit;
}

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: post.php?ot='.$owner_type.SEP.'oid='.$owner_id.SEP.'id='.$id);
	exit;
} else if (isset($_POST['submit_yes'])) {

	$sql = "DELETE FROM ".TABLE_PREFIX."blog_posts_comments WHERE comment_id=$delete_id AND post_id=$id";
	$result = mysql_query($sql, $db);
	if (mysql_affected_rows($db) == 1) {
		$sql = "UPDATE ".TABLE_PREFIX."blog_posts SET num_comments=num_comments-1 WHERE owner_type=$owner_type AND owner_id=$owner_id AND post_id=$id";
		$result = mysql_query($sql, $db);
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: post.php?ot='.$owner_type.SEP.'oid='.$owner_id.SEP.'id='.$id);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$hidden_vars = array('id' => $id, 'ot' => $owner_type, 'oid' => $owner_id, 'delete_id' => $delete_id);
$msg->addConfirm(array('DELETE'), $hidden_vars);
$msg->printConfirm();


require(AT_INCLUDE_PATH.'footer.inc.php');
?>