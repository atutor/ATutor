<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

//check valid requester id
$request_id = intval($_REQUEST['id']);
$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id=".$request_id;
$result	= mysql_query($sql, $db);
if (!($row = mysql_fetch_array($result))) {
	require(AT_INCLUDE_PATH.'header.inc.php'); 
	echo _AT('no_user_found');
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

if ($_POST['submit']) {
	$sql = 'DELETE FROM '.TABLE_PREFIX.'instructor_approvals WHERE member_id='.$request_id;
	$result = mysql_query($sql, $db);
	write_to_log(AT_ADMIN_LOG_DELETE, 'instructor_approvals', mysql_affected_rows($db), $sql);

	$msg->addFeedback('PROFILE_UPDATED_ADMIN');

	/* notify the users that they have been denied: */
	$sql   = "SELECT email, first_name, last_name FROM ".TABLE_PREFIX."members WHERE member_id=".$_POST['id'];
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_array($result)) {
		$to_email = $row['email'];

		/* assumes that there is a first and last name for this user, but not required during registration */
		$message .= _AT('instructor_request_deny', $_base_href)." \n\n".$_POST['msg_option'];		

		if ($to_email != '') {
			
			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

			$mail = new ATutorMailer;

			$mail->From     = EMAIL;
			$mail->AddAddress($to_email);
			$mail->Subject = _AT('instructor_request');
			$mail->Body    = $tmp_message;

			if(!$mail->Send()) {
			   //echo 'There was an error sending the message';
			   $msg->printErrors('SENDING_ERROR');
			   exit;
			}

			unset($mail);
		}
	}
	$msg->addFeedback('MSG_SENT');
	Header('Location: index.php');
	exit;
} else if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

?>


<?php 

$sql   = "SELECT email, first_name, last_name FROM ".TABLE_PREFIX."members WHERE member_id=".$request_id;
$result = mysql_query($sql, $db);

if ($row = mysql_fetch_array($result)) {
	$username = '';
	if ($row['first_name']!="") {
		$username .= $row['first_name'].' ';
	}

	if ($row['last_name']!="") {
		$username .= $row['last_name'].' ';
	}
	$username .= $row['email'];
} else {
	require(AT_INCLUDE_PATH.'header.inc.php'); 
	echo _AT('no_user_found');
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="action" value="process" />
<input type="hidden" name="id" value="<?php echo $request_id; ?>" />

<div class="input-form">
	<div class="row">
		<label for="confirm">
		<?php 
		echo _AT('confirm_deny_instructor');
		echo "<ul><li> $username</li></ul>"; 
		?>
		</label>
	</div>

	<div class="row">
		<label for="msg_option"><?php echo _AT('instructor_request_enterdenymsg'); ?></label><br />

		<input type="radio" name="msg_option" id="0" value="" checked="checked" />
			<label for="0"><?php echo _AT('leave_blank'); ?></label><br />
		<input type="radio" name="msg_option" id="1" value="<?php echo _AT('instructor_request_denymsg1'); ?>" />
			<label for="1"><?php echo _AT('instructor_request_denymsg1'); ?></label><br />
		<input type="radio" name="msg_option" id="2" value="<?php echo _AT('instructor_request_denymsg2'); ?>" />
			<label for="2"><?php echo _AT('instructor_request_denymsg2'); ?></label><br />
		<input type="radio" name="msg_option" id="3" value="<?php echo _AT('instructor_request_denymsg3'); ?>" />
			<label for="3"><?php echo _AT('instructor_request_denymsg3'); ?></label><br />
		<input type="radio" name="msg_option" id="4" value="<?php echo _AT('instructor_request_denymsg4'); ?>" />
			<label for="4"><?php echo _AT('instructor_request_denymsg4'); ?></label><br />
		<input type="radio" name="msg_option" id="5" value="<?php echo _AT('other'); ?>" />
			<label for="5"><?php echo _AT('other'); ?> </label><input type="text" class="formfield" name="other_msg" id="other_msg" size="30" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('deny')." "._AT('user'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<php?
require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>