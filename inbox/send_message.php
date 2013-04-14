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

	$msg->printInfos('MSG_SEND_LOGIN');
	
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (($_POST['submit']) || ($_POST['submit_delete'])) {
	$missing_fields = array();

	if (($_POST['to'] == '') || ($_POST['to'] == 0)) {
		$missing_fields[] = _AT('to');
	}
	if ($_POST['subject'] == '') {
		$missing_fields[] = _AT('subject');
	}
	if ($_POST['message'] == '') {
		$missing_fields[] = _AT('body');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) {
		$_POST['subject'] = $addslashes($_POST['subject']);
		$_POST['message'] = $addslashes($_POST['message']);
		$_POST['to'] = intval($_POST['to']);

		$sql = "INSERT INTO ".TABLE_PREFIX."messages VALUES (NULL, $_SESSION[course_id], $_SESSION[member_id], $_POST[to], NOW(), 1, 0, '$_POST[subject]', '$_POST[message]')";
		$result = mysql_query($sql,$db);

		// sent message box:
		$sql = "INSERT INTO ".TABLE_PREFIX."messages_sent VALUES (NULL, $_SESSION[course_id], $_SESSION[member_id], $_POST[to], NOW(), '$_POST[subject]', '$_POST[message]')";
		$result = mysql_query($sql,$db);

		//send email notification if recipient has message notification enabled
		$sql_notify = "SELECT first_name, last_name, email, inbox_notify FROM ".TABLE_PREFIX."members WHERE member_id=$_POST[to]";
		$result_notify = mysql_query($sql_notify, $db);
		$row_notify = mysql_fetch_assoc($result_notify);

		if ($row_notify['inbox_notify'] == 1) {
			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

			$body = _AT('notification_new_inbox', get_display_name($_SESSION['member_id']), $_base_href.'bounce.php?course='.$_SESSION['course_id']);
			$sender = get_display_name($_SESSION['member_id']);
			$mail = new ATutorMailer;
			$mail->AddAddress($row_notify['email'], $sender);
			$mail->FromName = $_config['site_name'];
			$mail->From     = $_config['contact_email'];
			$mail->Subject  = _AT('message_notification');
			$mail->Body     = $body;

			if(!$mail->Send()) {
				$msg->addError('SENDING_ERROR');
			}
			unset($mail);
		}

		if ($_POST['submit_delete']) {
			$result = mysql_query("DELETE FROM ".TABLE_PREFIX."messages WHERE message_id=$_POST[replied] AND to_member_id=$_SESSION[member_id]",$db);
		} else if ($_POST['replied'] != '') {
			$result = mysql_query("UPDATE ".TABLE_PREFIX."messages SET replied=1, date_sent=date_sent WHERE message_id=$_POST[replied]",$db);
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		if (isset($_SESSION['last_visited_page'])){
			$page = $_SESSION['last_visited_page'];
			unset($_SESSION['last_visited_page']);
			header('Location: '.$page);
			exit;
		}		
		header('Location: index.php');
		exit;
	}
}

$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND (approved='y' OR approved='a')";
$result = mysql_query($sql, $db);
$row	= mysql_fetch_array($result);

if ($row['cnt'] == 0) {
	require(AT_INCLUDE_PATH.'header.inc.php');

	$msg->printErrors('SEND_ENROL');

	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}


if (($_GET['reply'] == '') && $_GET['id']) {
	$onload = 'document.form.subject.focus();';
} else if ($_GET['reply'] == '') {
	$onload = 'document.form.to.focus();';
} else {
	$onload = 'document.form.body.focus();';
}

require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['reply'] = intval($_GET['reply']);
$_GET['forward'] = intval($_GET['forward']);

if ($_GET['reply']) {
	// get the member_id of the sender
	$result = mysql_query("SELECT from_member_id,subject,body FROM ".TABLE_PREFIX."messages WHERE message_id=$_GET[reply] AND to_member_id=$_SESSION[member_id]",$db);
	if ($myinfo = mysql_fetch_assoc($result)) {
		$reply_to	= $myinfo['from_member_id'];
		$subject	= $myinfo['subject'];
		$body		= $myinfo['body'];
	}
} else if ($_GET['forward']) {
	// get the member_id of the sender
	$result = mysql_query("SELECT subject, body FROM ".TABLE_PREFIX."messages_sent WHERE message_id=$_GET[forward] AND from_member_id=$_SESSION[member_id]",$db);
	if ($myinfo = mysql_fetch_assoc($result)) {
		$reply_to	= 0;
		$subject	= $myinfo['subject'];
		$body		= $myinfo['body'];
	}
}
if (isset($_GET['id'])) {
	$reply_to = intval($_GET['id']);
}

/* check to make sure we're in the same course */
if ($reply_to) {
	$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment E1, ".TABLE_PREFIX."course_enrollment E2 WHERE E1.member_id=$_SESSION[member_id] AND E2.member_id=$reply_to AND E1.course_id=E2.course_id AND (E1.approved='y' OR E1.approved='a') AND (E2.approved='y' OR E2.approved='a')";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_assoc($result);
	$num_of_classmates = $row['cnt'];

	$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."social_friends SC 
	           WHERE SC.member_id = ".$_SESSION[member_id]." 
	           AND SC.friend_id = ".$reply_to." 
	           OR SC.member_id = ".$reply_to." 
	           AND SC.friend_id = ".$_SESSION[member_id];
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_assoc($result);
	$num_of_contacts = $row['cnt'];

	if ($num_of_classmates+$num_of_contacts == 0) {
		$msg->printErrors('SEND_MEMBERS');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="replied" value="<?php echo $_GET['reply']; ?>" />

<div class="input-form">
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="to"><?php echo _AT('to'); ?></label><br />
		<?php
			if (!$reply_to) {
				$sql	= "SELECT DISTINCT M.first_name, M.second_name, M.last_name, M.login, M.member_id FROM ".TABLE_PREFIX."members M, ".TABLE_PREFIX."course_enrollment E1, ".TABLE_PREFIX."course_enrollment E2 WHERE E2.member_id=$_SESSION[member_id] AND E2.course_id=E1.course_id AND M.member_id=E1.member_id AND (E1.approved='y' OR E1.approved='a') AND (E2.approved='y' OR E2.approved='a') ORDER BY M.first_name, M.second_name, M.last_name, M.login";

				$result = mysql_query($sql, $db);
				$row	= mysql_fetch_assoc($result);
				echo '<select name="to" size="1" id="to">';
				do {
					echo '<option value="'.$row['member_id'].'"';
					if ($reply_to == $row['member_id']){
						echo ' selected="selected"';
					} else if (isset($_POST['to']) && $_POST['to'] == $row['member_id']) {
						echo ' selected="selected"';
					}
					echo '>';
					echo get_display_name($row['member_id']);
					echo '</option>';
				} while ($row = mysql_fetch_assoc($result));
				echo '</select>';
			} else {
				echo '<strong>'.get_display_name($reply_to).'</strong>';
				echo '<input type="hidden" name="to" value="'.$reply_to.'" />';
			} ?>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="subject"><?php echo _AT('subject'); ?></label><br />
		<input type="text" name="subject" id="subject" value="<?php
			if (($subject != '') && ($_POST['subject'] == '')) {
				if ($_GET['reply'] && !($substr($subject, 0, 2) == _AT('re'))) {
					$subject = _AT('re').' : '.$subject;
				} else if ($_GET['forward'] && !($substr($subject, 0, 2) == _AT('fwd'))) {
					$subject = _AT('fwd').' : '.$subject;
				}
				echo ContentManager::cleanOutput($subject);
			} else {
				echo ContentManager::cleanOutput($_POST['subject']);
			}
			?>" size="40" maxlength="100" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="body"><?php echo _AT('message'); ?></label><br />
		<textarea name="message" id="body" rows="15" cols="45"><?php
			if ($body != '') {
				if ($strlen($body) > 400){
					$body = $substr($body,0,400);
					$pos = $strrpos($body,' ');
					if ($pos===false){
						/* Unicode problem, not all language has spaces in between characters
						 * No space found, chop off right on 400.
						 */
						 $body = $substr($body,0,400);
					} else {
						$body = $substr($body,0,$pos);
					}
					$body .= ' ...';
				}
				$body  = "\n\n\n"._AT('in_reply_to').":\n".$body;
				echo $body;
			} else {
				echo $_POST['message'];
			}
		?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('send'); ?>" accesskey="s" /><?php
		if ($reply != '') {
			echo '<input type="submit" name="submit_delete" value="'._AT('send_delete').'" accesskey="n" /> ';
		}
		?> <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form> 

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>