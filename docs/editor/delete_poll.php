<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

authenticate(AT_PRIV_POLLS);

if ($_POST['submit_no']) {
	$msg->addFeedback('CANCELLED');
	Header('Location: '.$_base_href.'discussions/polls.php');
	exit;
}

if ($_POST['submit_yes'] && (authenticate(AT_PRIV_POLLS, AT_PRIV_RETURN))) {
	$_POST['pid'] = intval($_POST['pid']);

	$sql = "DELETE FROM ".TABLE_PREFIX."polls WHERE poll_id=$_POST[pid] AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);

	$sql = "DELETE FROM ".TABLE_PREFIX."polls_members WHERE poll_id=$_POST[pid] AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('POLL_DELETED');
	Header('Location: '.$_base_href.'discussions/polls.php');
	exit;
}

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/index.php';
$_section[1][0] = _AT('polls');
$_section[1][1] = 'discussions/polls.php';
$_section[2][0] = _AT('delete_poll');

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="discussions/" class="hide" ><img src="images/icons/default/square-large-discussions.gif" vspace="2" border="0"  class="menuimageh2" width="42" height="40" alt="" /></a> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="discussions/" class="hide" >'._AT('discussions').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/polls-large.gif" class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="discussions/polls.php" class="hide" >'._AT('polls').'</a>';
	}
echo '</h3>';

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