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

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

$fid = intval($_REQUEST['fid']);
$_POST['parent_id'] = intval($_REQUEST['parent_id']);

$_pages['mods/_standard/forums/forum/index.php?fid='.$fid]['title']    = get_forum_name($fid);
$_pages['mods/_standard/forums/forum/index.php?fid='.$fid]['parent']   = 'mods/_standard/forums/forum/list.php';
$_pages['mods/_standard/forums/forum/index.php?fid='.$fid]['children'] = array('mods/_standard/forums/forum/new_thread.php');

$_pages['mods/_standard/forums/forum/new_thread.php']['title_var'] = 'new_thread';
$_pages['mods/_standard/forums/forum/new_thread.php']['parent']    = 'mods/_standard/forums/forum/index.php?fid='.$fid;

if (!valid_forum_user($fid) || !$_SESSION['enroll']) {
	$msg->addError('FORUM_DENIED');
	require(AT_INCLUDE_PATH.'header.inc.php');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}


if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.url_rewrite('mods/_standard/forums/forum/index.php?fid='.$fid, AT_PRETTY_URL_IS_HEADER));
	exit;
} else if (isset($_POST['submit'])) {
	$missing_fields = array();

	if ($_POST['subject'] == '')  {
		$missing_fields[] = _AT('subject');
	} else {
		//60 was set by db
		$_POST['subject'] = validate_length($_POST['subject'], 60);
	}

	if ($_POST['body'] == '') {
		$missing_fields[] = _AT('body');
	}
	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}
	if (!$msg->containsErrors()) {
		if ($_POST['replytext'] != '') {
			$_POST['body'] .= "\n\n".'[reply][b]'._AT('in_reply_to').': [/b]'."\n";
			if ($strlen($_POST['replytext']) > 200) {
				$_POST['body'] .= $substr($_POST['replytext'], 0, 200).'...';
			} else {
				$_POST['body'] .= $_POST['replytext'];
			}
			$num_open_replies = substr_count($_POST['body'], '[reply]');
			$num_close_replies = substr_count($_POST['body'], '[/reply]');
			$num_replies_add = $num_open_replies - $num_close_replies - 1;
			for ($i=0; $i < $num_replies_add; $i++) {
				$_POST['body'] .= '[/reply]';
			}

			$_POST['body'] .= "\n".'[op]mods/_standard/forums/forum/view.php?fid='.$_POST['fid'].SEP.'pid='.$_POST['parent_id'].SEP.'page='.$_POST['page'].'#'.$_POST['reply'];
			$_POST['body'] .= '[/op][/reply]';
		}

		/* use this value instead of NOW(), because we want the parent post to have the exact */
		/* same date. and not a second off if that may happen */
		/* this fails however */
		$now = date('Y-m-d H:i:s');

		$sql_subject = $addslashes($_POST['subject']);
		$sql_body    = $addslashes($_POST['body']);

		$sql = "INSERT INTO %sforums_threads VALUES (NULL, %d, %d, %d, '%s', 0, '%s', '%s', NOW(), 0, 0)";
		$result = queryDB($sql, array(TABLE_PREFIX, $_POST['parent_id'], $_SESSION['member_id'], $_POST['fid'], $now, $sql_subject, $sql_body, $now));
		$this_id = at_insert_id();
		
		/* Increment count for posts in forums table in database */

		$sql = "UPDATE %sforums SET num_posts=num_posts+1, last_post='%s' WHERE forum_id=%d";
		$result	 = queryDB($sql, array(TABLE_PREFIX, $now, $_POST['fid']));
		
		// If there are subscribers to this forum, send them an email notification
		$subscriber_email_list = array(); // list of subscribers array('email', 'full_name')
		$subscriber_list       = '';
		$enrolled = array();
		
		// get list of student enrolled in this course
		// This needs to be replaced with a tool to clean forum subscriptions when unenrolling

		$sql = "SELECT member_id from %scourse_enrollment WHERE course_id = %d AND approved = 'y'";
		$rows_enrolled = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
		
		foreach($rows_enrolled as $row){
			$enrolled[] = $row['member_id'];
		}
		//get a list of users subscribed to this forum

		$sql = "SELECT member_id FROM %sforums_subscriptions WHERE forum_id=%d";
		$rows_subscribed = queryDB($sql, array(TABLE_PREFIX, $fid));	
		
		foreach($rows_subscribed as $row){	
			$subscriber_list .= $row['member_id'] . ',';
		}
		if ($_POST['parent_id']) {

			$sql = "SELECT member_id FROM %sforums_accessed WHERE post_id=%d AND subscribe=1";
			$rows_subscribed = queryDB($sql, array(TABLE_PREFIX, $_POST['parent_id']));
			
			foreach($rows_subscribed as $row){

				if(in_array($row['member_id'], $enrolled)){
					$subscriber_list .= $row['member_id'] . ',';
				}
			}
		}
		$subscriber_list = $substr($subscriber_list, 0, -1);

		if ($subscriber_list != '') {

			$sql = "SELECT first_name, second_name, last_name, email, member_id FROM %smembers WHERE member_id IN (%s)";
			$rows_email = queryDB($sql, array(TABLE_PREFIX, $subscriber_list));
			
			foreach($rows_email as $row){
				$subscriber_email_list[] = array('email'=> $row['email'], 'full_name' => $row['first_name'] . ' '. $row['second_name'] . ' ' . $row['last_name'], 'member_id'=>$row['member_id']);
			}
		}

		$sql = "UPDATE %sforums_threads SET num_comments=num_comments+1, last_comment='%s', date=date WHERE post_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $now, $_POST['parent_id']));

		if ($subscriber_email_list) {
			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

			if ($_POST['parent_name'] == ''){
				$_POST['parent_name'] = $_POST['subject'];
			}
			$_POST['parent_name'] = urldecode($_POST['parent_name']);
			foreach ($subscriber_email_list as $subscriber){
				$mail = new ATutorMailer;
				$mail->AddAddress($subscriber['email'], get_display_name($subscriber['member_id']));
				$body = _AT('forum_new_submsg', $_SESSION['course_title'],  get_forum_name($_POST['fid']), $_POST['parent_name'],  AT_BASE_HREF.'mods/_standard/forums/forum/view.php?fid='.$_POST['fid'].SEP.'pid='.$_POST['parent_id']);
				$body .= "\n----------------------------------------------\n";
				$body .= _AT('course').': '.$_SESSION['course_title']."\n";
				$body .= _AT('forum').': '.get_forum_name($_POST['fid'])."\n";
				$body .= _AT('thread').': '.$_POST['parent_name']."\n";
				$body .= _AT('posted_by').": ".get_display_name($_SESSION['member_id'])."\n";
				$body .= $_POST['body']."\n";
				$mail->FromName = $_config['site_name'];
				$mail->From     = $_config['contact_email'];
				$mail->Subject = _AT('thread_notify1').': '.$_POST['parent_name'];
				$mail->Body    = $body;

				if(!$mail->Send()) {
					$msg->addError('SENDING_ERROR');
				}

				unset($mail);
			}
		}
		if ($_REQUEST['subscribe']) {
			if($_POST['parent_id'] != 0){
				$this_id = $_POST['parent_id'];
				$subject = $_POST['parent_name'];
			} else {
				$subject = $_POST['subject'];
			}

			$sql	= "REPLACE INTO %sforums_accessed VALUES (%d, %d, NOW(), 1)";
			$result = queryDB($sql, array(TABLE_PREFIX, $this_id, $_SESSION['member_id']));
			
			$msg->addFeedback(array('THREAD_SUBSCRIBED', $subject));
		} else if ($_POST['parent_id'] == 0) {
			// not subscribe and it's a new thread, mark read:

			$sql	= "REPLACE INTO %sforums_accessed VALUES (%d, %d, NOW(), 0)";
			$result = queryDB($sql, array(TABLE_PREFIX, $this_id, $_SESSION['member_id']));
		}

		if ($_POST['parent_id'] == 0) {

			$sql = "UPDATE %sforums SET num_topics=num_topics+1, last_post='%s' WHERE forum_id=%d";
			$result	 = queryDB($sql, array(TABLE_PREFIX, $now, $_POST['fid']));
			
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			$_POST['parent_id'] = $this_id;
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.url_rewrite('mods/_standard/forums/forum/view.php?fid='.$fid.SEP.'pid='.$_POST['parent_id'].SEP.'page='.$_POST['page'], AT_PRETTY_URL_IS_HEADER));
		exit;
	} else if(isset($missing_fields)){
	    $msg->addError(array('EMPTY_FIELDS', $missing_fields));
	    if(strstr($_SERVER['HTTP_REFERER'], "view.php" )){
			header('Location: '.url_rewrite('mods/_standard/forums/forum/view.php?fid='.$fid.SEP.'pid='.$_POST['parent_id'].SEP.'page='.$_POST['page'], AT_PRETTY_URL_IS_HEADER));
			exit;
	    }	
	}
}

$onload = 'document.form.subject.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');
	
$parent_id = 0;
$new_thread = TRUE;
require(AT_INCLUDE_PATH.'../mods/_standard/forums/html/new_thread.inc.php');
require(AT_INCLUDE_PATH.'footer.inc.php');

?>