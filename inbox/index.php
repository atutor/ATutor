<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2013                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (!$_SESSION['valid_user']) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('INVALID_USER');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$_GET['view'] = intval($_GET['view']);

if ($_GET['view']) {
	$sql = "UPDATE %smessages SET new=0, date_sent=date_sent WHERE to_member_id=%d AND message_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $_GET['view']));
}

if (isset($_GET['delete'])) {
	$_GET['delete'] = intval($_GET['delete']);
	
    $sql = "DELETE FROM %smessages WHERE to_member_id=%d AND message_id=%d";
    $result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'],$_GET['delete']));	

	if(isset($result) && $result > 0){
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}

	header('Location: index.php');
	exit;
} else if (isset($_POST['submit_yes'], $_POST['ids'])) {
	$ids = $addslashes($_POST['ids']);

	$sql = "DELETE FROM %smessages WHERE to_member_id=%d AND message_id IN (%s)";
	queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $ids));
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

	header('Location: index.php');
	exit;
} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');

	header('Location: index.php');
	exit;
} else if (isset($_POST['delete']) && !isset($_POST['id'])) {
	$msg->addError('NO_ITEM_SELECTED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

if (isset($_GET['view']) && $_GET['view']) {

	$sql	= "SELECT * FROM %smessages WHERE message_id=%d AND to_member_id=%d";
	$row_messages = queryDB($sql, array(TABLE_PREFIX, $_GET['view'], $_SESSION['member_id']), TRUE);
	
} else if (isset($_POST['delete'], $_POST['id'])) {
	$hidden_vars['ids'] = implode(',', $_POST['id']);

	$msg->addConfirm('DELETE_MSGS', $hidden_vars);
	$msg->printConfirm();
}


$sql	= "SELECT * FROM %smessages WHERE to_member_id=%d ORDER BY date_sent DESC";
$row_sent = queryDB($sql,array(TABLE_PREFIX, $_SESSION['member_id']));

// since Inbox isn't a module, it can't have a cron job.
// so, we delete the expires sent messages with P =  1/7.
if (!rand(0, 6)) {

	$sql = "DELETE FROM %smessages_sent WHERE from_member_id=%d AND TO_DAYS(date_sent) < (TO_DAYS(NOW()) - {$_config['sent_msgs_ttl']}) LIMIT 100";
	queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']));
}

$savant->assign('row_sent', $row_sent);
$savant->assign('row_messages', $row_messages);
$savant->display('inbox/inbox.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>