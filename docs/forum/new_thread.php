<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
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

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

$fid = intval($_GET['fid']);

if ($fid == 0) {
	$fid = intval($_POST['fid']);
}
$pid = intval($_GET['pid']);

//get a list of users subscribed to this forum
$sql = "SELECT * from ".TABLE_PREFIX."forums_subscriptions WHERE forum_id = $fid ";
$result = mysql_query($sql, $db);
while($row = mysql_fetch_row($result)){
	$subscribers[] = $row['1'];
}

$sql = "SELECT * from ".TABLE_PREFIX."forums_thread_subscriptions WHERE post_id = $pid ";
$result = mysql_query($sql, $db);
while($row = mysql_fetch_row($result)){
	$subscribers[] = $row['1'];
}

$sql = "SELECT member_id, first_name, last_name, email from ".TABLE_PREFIX."members ";
$result = mysql_query($sql, $db);
while($row = mysql_fetch_array($result)){
	if(in_array($row['member_id'], $subscribers)){
		$recipients[$row['member_id']] = $row['email'];
		$poster_name[$row['member_id']] = $row['first_name']." ". $row['last_name'];

	}
	if($row['member_id'] == $_SESSION['member_id'] && !in_array($row['member_id'], $subscribers)){
		$recipients[$row['member_id']] = $row['email'];
		$poster_name[$row['member_id']] = $row['first_name']." ". $row['last_name'];
	}
}

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/';
$_section[1][0] = _AT('forums');
$_section[1][1] = 'forum/list.php';
$_section[2][0] = get_forum_name($fid);
$_section[2][1] = 'forum/index.php?fid='.$fid;
$_section[3][0] = _AT('new_thread');


if(){

}

if ($_POST['submit']) {

	if ($_POST['subject'] == '')  {
		$msg->addError('MSG_SUBJECT_EMPTY');
	}

	if ($_POST['body'] == '') {
		$msg->addError('MSG_BODY_EMPTY');
	}

	if (!$msg->containsErrors()) {
		if ($_POST['replytext'] != '') {
			$_POST['body'] .= "\n\n".'[reply][b]'._AT('in_reply_to').': [/b]'."\n";
			if (strlen($_POST['replytext']) > 200) {
				$_POST['body'] .= substr($_POST['replytext'], 0, 200).'...';
			} else {
				$_POST['body'] .= $_POST['replytext'];
			}
			$num_open_replies = substr_count($_POST['body'], '[reply]');
			$num_close_replies = substr_count($_POST['body'], '[/reply]');
			$num_replies_add = $num_open_replies - $num_close_replies - 1;
			for ($i=0; $i < $num_replies_add; $i++) {
				$_POST['body'] .= '[/reply]';
			}

			$_POST['body'] .= "\n".'[op]forum/view.php?fid='.$_POST['fid'].SEP.'pid='.$_POST['parent_id'].SEP.'page='.$_POST['page'].'#'.$_POST['reply'];
			$_POST['body'] .= '[/op][/reply]';

		}

		/* use this value instead of NOW(), because we want the parent post to have the exact */
		/* same date. and not a second off if that may happen */
		$now = date('Y-m-d H:i:s');

		$_POST['subject'] = $addslashes($_POST['subject']);
		$_POST['body']    = $addslashes($_POST['body']);

		$sql = "INSERT INTO ".TABLE_PREFIX."forums_threads VALUES(0, $_POST[parent_id], $_SESSION[member_id], $_POST[fid], '$_SESSION[login]', '$now', 0, '$_POST[subject]', '$_POST[body]', '$now', 0, 0)";
		$result = mysql_query($sql, $db);
		$this_id = mysql_insert_id($db);

		/* Increment count for posts in forums table in database */
		$sql = "UPDATE ".TABLE_PREFIX."forums SET num_posts=num_posts+1, last_post='$now' WHERE forum_id=$_POST[fid]";
		$result	 = mysql_query($sql, $db);

	//Update forum RSS feeds if they exists
	if(file_exists(AT_CONTENT_DIR."feeds/".$_SESSION[course_id]."/forum_feed.RSS2.0.xml")||
		file_exists(AT_CONTENT_DIR."feeds/".$_SESSION[course_id]."/forum_feed.RSS1.0.xml")){
		require_once('../tools/feeds/forum_feed.php');
	}

	// If there are subscribers to this forum, send them an email notification
	if ($recipients != '') {
		$sql = "UPDATE ".TABLE_PREFIX."forums_threads SET num_comments=num_comments+1, last_comment='$now' WHERE post_id=$_POST[parent_id]";
		$result = mysql_query($sql, $db);
		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

		if($_POST['parent_name'] == ''){
			$_POST['parent_name'] = $_POST['subject'];
		}

		foreach($recipients as $address){
			$mail = new ATutorMailer;
			$mail->AddAddress($address);
			$body = _AT('forum_new_submsg', $_SESSION['course_title'],  get_forum_name($_POST['fid']), $_POST['parent_name'],  $_base_href.'bounce.php?course='.$_SESSION['course_id']);
			$body .= "\n----------------------------------------------\n";
			$body .= _AT('posted_by').": ".$poster_name[$_SESSION['member_id']]."\n";
			$body .= $_POST['body']."\n";
			$mail->FromName = $poster_name[$_SESSION['member_id']];
			$mail->From     = $recipients[$_SESSION['member_id']];
			$mail->Subject = _AT('thread_notify1');
			$mail->Body    = $body;

			if(!$mail->Send()) {
				   $msg->addError('MAIL_FAILED');
				   Header('Location: new_thread.php?fid='.$fid.SEP.'pid='.$_POST['parent_id']);
				   exit;
			}

			unset($mail);
		}

	}

	if ($_REQUEST['subscribe']) {
		if($_POST['parent_id'] != 0){
			$this_id = $_POST['parent_id'];
		}
		$sql	= "INSERT INTO ".TABLE_PREFIX."forums_thread_subscriptions VALUES ($this_id, $_SESSION[member_id])";
		$result = mysql_query($sql, $db);
	}

	if ($_POST['parent_id'] == 0) {
		$sql = "UPDATE ".TABLE_PREFIX."forums SET num_topics=num_topics+1, last_post='$now' WHERE forum_id=$_POST[fid]";
		$result	 = mysql_query($sql, $db);
		$msg->addFeedback('THREAD_STARTED');
		Header('Location: ./index.php?fid='.$fid);
		exit;
	}

	$msg->addFeedback('THREAD_REPLY');
	Header('Location: view.php?fid='.$fid.SEP.'pid='.$_POST['parent_id']);

	}
}

$onload = 'onload="document.form.subject.focus()"';

require(AT_INCLUDE_PATH.'header.inc.php');

	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-discussions.gif" width="42" height="38" border="0" alt="" class="menuimage" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="discussions/index.php?g=11">'._AC('discussions').'</a>';
	}
	echo '</h2>';

echo '<h3>';

if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/forum-large.gif" width="42" height="38" border="0" alt="" class="menuimageh3" />';
}

echo '<a href="forum/index.php?fid='.$fid.SEP.'g=11">'.AT_print(get_forum_name($fid), 'forums.title').'</a></h3>';

$parent_id = 0;
require(AT_INCLUDE_PATH.'lib/new_thread.inc.php');
require(AT_INCLUDE_PATH.'footer.inc.php');

?>
