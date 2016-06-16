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

if ($_GET['delete']) {
	$_GET['delete'] = intval($_GET['delete']);
    $sql = "DELETE FROM %smessages_sent WHERE from_member_id=%d AND message_id=%d";
    $result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $_GET['delete']));

	if($result){
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}

	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if (isset($_POST['submit_yes'], $_POST['ids'])) {
	$ids = explode(",",$_POST['ids']);
	$ids = implode(',', array_map('intval', $ids));
	$sql = "DELETE FROM %smessages_sent WHERE from_member_id=%d AND message_id IN (%s)";
	queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $ids));
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');

	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if (isset($_POST['move'], $_POST['id'])) {
	$_POST['id'][] = 0; // to make it non-empty
	$ids = implode(',', array_map('intval', $_POST['id']));
	$sql = "INSERT INTO %smessages SELECT 0, course_id, from_member_id, %d, date_sent, 0, 0, subject, body FROM %smessages_sent WHERE from_member_id=%d AND message_id IN (%s)";
	queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], TABLE_PREFIX, $_SESSION['member_id'], $ids ));
	
	$sql = "DELETE FROM %smessages_sent WHERE from_member_id=%d AND message_id IN (%s)";
	queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $ids));
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if ((isset($_POST['delete']) || isset($_POST['move'])) && !isset($_POST['id'])) {
	$msg->addError('NO_ITEM_SELECTED');
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

if (isset($_GET['view']) && $_GET['view'] != '') {

	$sql	= "SELECT * FROM %smessages_sent WHERE message_id=%d AND from_member_id=%d";
	$row_messages = queryDB($sql, array(TABLE_PREFIX, $_GET['view'], $_SESSION['member_id']), TRUE);
	
} else if (isset($_POST['delete'], $_POST['id'])) {
	$hidden_vars['ids'] = implode(',', $_POST['id']);

	$msg->addConfirm('DELETE_MSGS', $hidden_vars);
	$msg->printConfirm();
}

$msg->printInfos(array('INBOX_SENT_MSGS_TTL', $_config['sent_msgs_ttl']));

$sql	= "SELECT * FROM ".TABLE_PREFIX."messages_sent WHERE from_member_id=$_SESSION[member_id] ORDER BY date_sent DESC";
$row_sent = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']));

$savant->assign('row_sent', $row_sent);
$savant->assign('row_messages', $row_messages);
$savant->display('inbox/sent_messages.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>