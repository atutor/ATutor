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

$_user_location = 'public';

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_GET['e'], $_GET['id'], $_GET['m'])) {
	$id = intval($_GET['id']);
	$m  = $_GET['m'];
	$e  = $addslashes($_GET['e']);

	$sql    = "SELECT creation_date FROM ".TABLE_PREFIX."members WHERE member_id=$id";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$code = substr(md5($_GET['e'] . $row['creation_date'] . $id), 0, 10);

		if ($code == $m) {
			$sql = "UPDATE ".TABLE_PREFIX."members SET confirmed=1, email='$_GET[e]' WHERE member_id=$id";
			$result = mysql_query($sql, $db);

			$msg->addFeedback('CONFIRM_GOOD');

			header('Location: '.$_base_href.'login.php');
			exit;
		} else {
			$msg->addError('CONFIRM_BAD');
		}
	} else {
		$msg->addError('CONFIRM_BAD');
	}

} else if (isset($_GET['id'], $_GET['m'])) {
	$id = intval($_GET['id']);
	$m  = $_GET['m'];

	$sql    = "SELECT email, creation_date FROM ".TABLE_PREFIX."members WHERE member_id=$id";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$code = substr(md5($row['email'] . $row['creation_date'] . $id), 0, 10);

		if ($code == $m) {
			$sql = "UPDATE ".TABLE_PREFIX."members SET confirmed=1 WHERE member_id=$id";
			$result = mysql_query($sql, $db);

			$msg->addFeedback('CONFIRM_GOOD');

			header('Location: '.$_base_href.'login.php');
			exit;
		} else {
			$msg->addError('CONFIRM_BAD');
		}
	} else {
		$msg->addError('CONFIRM_BAD');
	}
} else if (isset($_POST['submit'])) {
	$_POST['email'] = $addslashes($_POST['email']);

	$sql    = "SELECT member_id, email, creation_date FROM ".TABLE_PREFIX."members WHERE email='$_POST[email]' AND confirmed=0";
	$result = mysql_query($sql, $db);

	if ($row = mysql_fetch_assoc($result)) {
		$code = substr(md5($row['email'] . $row['creation_date']. $row['member_id']), 0, 10);
		$confirmation_link = $_base_href . 'confirm.php?id='.$row['member_id'].SEP.'m='.$code;

		/* send the email confirmation message: */
		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
		$mail = new ATutorMailer();

		$mail->From     = EMAIL;
		$mail->AddAddress($row['email']);
		$mail->Subject = SITE_NAME . ' - ' . _AT('email_confirmation_subject');
		$mail->Body    = _AT('email_confirmation_message', SITE_NAME, $confirmation_link);

		$mail->Send();

		$msg->addFeedback('CONFIRMATION_SENT');

		header('Location: '.$_base_href.'login.php');
		exit;
	} else {
		$msg->addError('EMAIL_NOT_FOUND');
	}
}

require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">

<div class="input-form" style="max-width: 400px">
	<div class="row">
		<p><?php echo _AT('send_confirmation'); ?></p>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="email"><?php echo _AT('email'); ?></label><br />
		<input type="text" name="email" id="email" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('send'); ?>" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>