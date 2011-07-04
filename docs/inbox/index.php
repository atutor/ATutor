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

if ($_GET['view']) {
	$result = mysql_query("UPDATE ".TABLE_PREFIX."messages SET new=0, date_sent=date_sent WHERE to_member_id=$_SESSION[member_id] AND message_id=$_GET[view]",$db);
}

if (isset($_GET['delete'])) {
	$_GET['delete'] = intval($_GET['delete']);

	if($result = mysql_query("DELETE FROM ".TABLE_PREFIX."messages WHERE to_member_id=$_SESSION[member_id] AND message_id=$_GET[delete]",$db)){
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}

	header('Location: index.php');
	exit;
} else if (isset($_POST['submit_yes'], $_POST['ids'])) {
	$ids = $addslashes($_POST['ids']);

	$sql = "DELETE FROM ".TABLE_PREFIX."messages WHERE to_member_id=$_SESSION[member_id] AND message_id IN ($ids)";
	mysql_query($sql, $db);

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
	$sql	= "SELECT * FROM ".TABLE_PREFIX."messages WHERE message_id=$_GET[view] AND to_member_id=$_SESSION[member_id]";
	$result_messages = mysql_query($sql, $db);

} else if (isset($_POST['delete'], $_POST['id'])) {
	$hidden_vars['ids'] = implode(',', $_POST['id']);

	$msg->addConfirm('DELETE_MSGS', $hidden_vars);
	$msg->printConfirm();
}

$sql	= "SELECT * FROM ".TABLE_PREFIX."messages WHERE to_member_id=$_SESSION[member_id] ORDER BY date_sent DESC";
$result = mysql_query($sql,$db);

// since Inbox isn't a module, it can't have a cron job.
// so, we delete the expires sent messages with P =  1/7.
if (!rand(0, 6)) {
	$sql = "DELETE FROM ".TABLE_PREFIX."messages_sent WHERE from_member_id=$_SESSION[member_id] AND TO_DAYS(date_sent) < (TO_DAYS(NOW()) - {$_config['sent_msgs_ttl']}) LIMIT 100";
	mysql_query($sql, $db);
}

$savant->assign('result', $result);
$savant->assign('result_messages', $result_messages);
$savant->display('inbox/inbox.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>