<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/file_storage.inc.php');

$owner_type = abs($_REQUEST['ot']);
$owner_id   = abs($_REQUEST['oid']);
$owner_arg_prefix = '?ot='.$owner_type.SEP.'oid='.$owner_id. SEP;
if (!($owner_status = fs_authenticate($owner_type, $owner_id)) || !query_bit($owner_status, WORKSPACE_AUTH_WRITE)) { 
	$msg->addError('ACCESS_DENIED');
	header('Location: '.url_rewrite('file_storage/index.php', AT_PRETTY_URL_IS_HEADER));
	exit;
}

$_pages['file_storage/delete_comment.php']['parent'] = 'file_storage/comments.php' . $owner_arg_prefix.'id='.$_GET['file_id'];
$_pages['file_storage/comments.php' . $owner_arg_prefix.'id='.$_GET['file_id']]['title_var'] = 'comments';
$_pages['file_storage/comments.php' . $owner_arg_prefix.'id='.$_GET['file_id']]['parent']    = 'file_storage/index.php';

$id = abs($_REQUEST['id']);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.url_rewrite('file_storage/comments.php'.$owner_arg_prefix.'id='.$_POST['file_id'], AT_PRETTY_URL_IS_HEADER));
	exit;
} else if (isset($_POST['submit_yes'])) {
	$_POST['file_id'] = abs($_POST['file_id']);
	$_POST['id'] = abs($_POST['id']);

	$sql = "DELETE FROM ".TABLE_PREFIX."files_comments WHERE file_id=$_POST[file_id] AND comment_id=$_POST[id] AND member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	if (mysql_affected_rows($db) == 1) {
		$sql = "UPDATE ".TABLE_PREFIX."files SET num_comments=num_comments-1, date=date WHERE owner_type=$owner_type AND owner_id=$owner_id AND file_id=$_POST[file_id]";
		$result = mysql_query($sql, $db);
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.url_rewrite('file_storage/comments.php'.$owner_arg_prefix.'id='.$_POST['file_id'], AT_PRETTY_URL_IS_HEADER));
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$hidden_vars = array('id' => $id, 'ot' => $owner_type, 'oid' => $owner_id, 'file_id' => $_GET['file_id']);
$msg->addConfirm(array('DELETE'), $hidden_vars);
$msg->printConfirm();


require(AT_INCLUDE_PATH.'footer.inc.php');
?>