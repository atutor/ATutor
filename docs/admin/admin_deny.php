<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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

// message options
$msg_options = array (_AT('leave_blank'),
			_AT('instructor_request_denymsg1'),
			_AT('instructor_request_denymsg2'),
			_AT('instructor_request_denymsg3'),
			_AT('instructor_request_denymsg4'),
			_AT('other'));
$other_option = count($msg_options)-1;

if (isset($_POST['submit'])) {
	$sql = 'DELETE FROM '.TABLE_PREFIX.'instructor_approvals WHERE member_id='.$request_id;
	$result = mysql_query($sql, $db);
	write_to_log(AT_ADMIN_LOG_DELETE, 'instructor_approvals', mysql_affected_rows($db), $sql);

	$msg->addFeedback('PROFILE_UPDATED_ADMIN');

	/* notify the users that they have been denied: */
	$sql   = "SELECT email, first_name, last_name FROM ".TABLE_PREFIX."members WHERE member_id=".$_POST['id'];
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_array($result)) {
		$to_email = $row['email'];

		$message = _AT('instructor_request_deny', AT_BASE_HREF)." \n";
		if ($_POST['msg_option'] == $other_option) {
			$message.=addslashes($_POST['other_msg']);
		} else if ($_POST['msg_option']) {
			$message.= '\n'.$msg_options[$_POST['msg_option']];
		}

		if ($to_email != '') {
			
			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

			$mail = new ATutorMailer;

			$mail->From     = $_config['contact_email'];
			$mail->AddAddress($to_email);
			$mail->Subject = _AT('instructor_request');
			$mail->Body    = $message;

			if(!$mail->Send()) {
			   //echo 'There was an error sending the message';
			   $msg->printErrors('SENDING_ERROR');
			   exit;
			}

			unset($mail);
		}
	}
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	Header('Location: index.php');
	exit;
} else if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

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
		echo "<ul><li>$username</li></ul>"; 
		?>
		</label>
	</div>

	<div class="row">
		<?php echo _AT('instructor_request_enterdenymsg'); ?><br />

		<?php 
			$radio_buttons = '';
			$i = 0;
			echo '<input type="radio" name="msg_option" id="c'.$i.'" value="'.$i.'" checked="checked" /><label for="c'.$i.'">'.$msg_options[$i].'</label><br />';

			$num_msgs = count($msg_options) - 1;
			for ($i = 1; $i<$num_msgs; $i++) {
				echo '<input type="radio" name="msg_option" id="c'.$i.'" value="'.$i.'" /><label for="c'.$i.'">'.$msg_options[$i].'</label><br />';
			}

			echo '<input type="radio" name="msg_option" id="c'.$i.'" value="'.$i.'" /><label for="c'.$i.'">'.$msg_options[$i].'</label>';
		?>
		<input type="text" class="formfield" name="other_msg" id="other_msg" size="30" onmousedown="document.form['c<?php echo $other_option; ?>'].checked = true;">
		<br />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('deny'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>