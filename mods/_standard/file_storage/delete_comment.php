<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
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
require(AT_INCLUDE_PATH.'../mods/_standard/file_storage/file_storage.inc.php');

$owner_type = abs($_REQUEST['ot']);
$owner_id   = abs($_REQUEST['oid']);
$owner_arg_prefix = '?ot='.$owner_type.SEP.'oid='.$owner_id. SEP;
if (!($owner_status = fs_authenticate($owner_type, $owner_id)) || !query_bit($owner_status, WORKSPACE_AUTH_WRITE)) { 
	$msg->addError('ACCESS_DENIED');
	header('Location: '.url_rewrite('mods/_standard/file_storage/index.php', AT_PRETTY_URL_IS_HEADER));
	exit;
}

$_pages['mods/_standard/file_storage/delete_comment.php']['parent'] = 'mods/_standard/file_storage/comments.php' . $owner_arg_prefix.'id='.$_GET['file_id'];
$_pages['mods/_standard/file_storage/comments.php' . $owner_arg_prefix.'id='.$_GET['file_id']]['title_var'] = 'comments';
$_pages['mods/_standard/file_storage/comments.php' . $owner_arg_prefix.'id='.$_GET['file_id']]['parent']    = 'mods/_standard/file_storage/index.php';

$id = abs($_REQUEST['id']);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.url_rewrite('mods/_standard/file_storage/comments.php'.$owner_arg_prefix.'id='.$_POST['file_id'], AT_PRETTY_URL_IS_HEADER));
	exit;
} else if (isset($_POST['submit_yes'])) {
	$_POST['file_id'] = abs($_POST['file_id']);
	$_POST['id'] = abs($_POST['id']);

	$sql = "DELETE FROM %sfiles_comments WHERE file_id=%d AND comment_id=%d AND member_id=%d";
	$rows_comments = queryDB($sql, array(TABLE_PREFIX, $_POST['file_id'], $_POST['id'], $_SESSION['member_id']));

    if(count($rows_comments) == 1){
		$sql = "UPDATE %sfiles SET num_comments=num_comments-1, date=date WHERE owner_type=%d AND owner_id=%d AND file_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $owner_type, $owner_id, $_POST['file_id']));
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.url_rewrite('mods/_standard/file_storage/comments.php'.$owner_arg_prefix.'id='.$_POST['file_id'], AT_PRETTY_URL_IS_HEADER));
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$hidden_vars = array('id' => $id, 'ot' => $owner_type, 'oid' => $owner_id, 'file_id' => $_GET['file_id']);
$sql = "SELECT comment FROM %sfiles_comments WHERE comment_id = %d";
$result = queryDB($sql, array(TABLE_PREFIX, $_GET['id']), TRUE);

$msg->addConfirm(array('DELETE', $result['comment']), $hidden_vars);
$msg->printConfirm();


require(AT_INCLUDE_PATH.'footer.inc.php');
?>