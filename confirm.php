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

$_user_location = 'public';

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'login.php');
	exit;
}

if (isset($_GET['e'], $_GET['id'], $_GET['m'])) {
	$id = intval($_GET['id']);
	$m  = $_GET['m'];
	$e  = $addslashes($_GET['e']);

	$sql    = "SELECT creation_date FROM ".TABLE_PREFIX."members WHERE member_id=$id";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$code = substr(md5($_GET['e'] . $row['creation_date'] . $id), 0, 10);

		if ($code == $m) {
			$sql = "UPDATE ".TABLE_PREFIX."members SET email='$_GET[e]', last_login=last_login WHERE member_id=$id";
			$result = mysql_query($sql, $db);

			$msg->addFeedback('CONFIRM_GOOD');

			header('Location: '.$_base_href.'users/index.php');
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

	$sql    = "SELECT email, creation_date FROM ".TABLE_PREFIX."members WHERE member_id=$id AND status=".AT_STATUS_UNCONFIRMED;
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$code = substr(md5($row['email'] . $row['creation_date'] . $id), 0, 10);

		if ($code == $m) {
			if (defined('AUTO_APPROVE_INSTRUCTORS') && AUTO_APPROVE_INSTRUCTORS) {
				$sql = "UPDATE ".TABLE_PREFIX."members SET status=".AT_STATUS_INSTRUCTOR.", creation_date=creation_date, last_login=last_login WHERE member_id=$id";
			} else {
				$sql = "UPDATE ".TABLE_PREFIX."members SET status=".AT_STATUS_STUDENT.", creation_date=creation_date, last_login=last_login WHERE member_id=$id";
			}
			$result = mysql_query($sql, $db);

			if (isset($_REQUEST["en_id"]) && $_REQUEST["en_id"] <> "")
			{
				$msg->addFeedback('CONFIRM_GOOD');

				$member_id	= $id;
				require (AT_INCLUDE_PATH.'html/auto_enroll_courses.inc.php');
				unset($_SESSION['valid_user']);
				unset($_SESSION['member_id']);
				
				$table_title="
				<div class=\"row\">
					<h3>" . _AT('auto_enrolled_msg'). "<br /></h3>
				</div>";
		
				require(AT_INCLUDE_PATH.'header.inc.php');
				echo "<div class=\"input-form\">";
				require(AT_INCLUDE_PATH.'html/auto_enroll_list_courses.inc.php');
				echo '<p style="text-align:center"><a href="'. $_SERVER['PHP_SELF'] . '?auto_login=1&member_id='. $id .'">' . _AT("go_to_my_start_page") . '</a></p>';
				echo "</div>";
				require(AT_INCLUDE_PATH.'footer.inc.php');
				exit;
			}
			else
			{
				$msg->addFeedback('CONFIRM_GOOD');
				
				// enable auto login student into "my start page"
				$_REQUEST["auto_login"] = 1;
				$_REQUEST["member_id"] = $id;
			}
		} else {
			$msg->addError('CONFIRM_BAD');
		}
	} else {
		$msg->addError('CONFIRM_BAD');
	}
} else if (isset($_POST['submit'])) {
	$_POST['email'] = $addslashes($_POST['email']);

	$sql    = "SELECT member_id, email, creation_date, status FROM ".TABLE_PREFIX."members WHERE email='$_POST[email]'";
	$result = mysql_query($sql, $db);

	if ($row = mysql_fetch_assoc($result)) {

		if ($row['status'] == AT_STATUS_UNCONFIRMED) {
			$code = substr(md5($row['email'] . $row['creation_date']. $row['member_id']), 0, 10);
			
			if ($_POST["en_id"] <> "")
				$confirmation_link = $_base_href . 'confirm.php?id='.$row['member_id'].SEP.'m='.$code.'&en_id='.$_POST["en_id"];
			else
				$confirmation_link = $_base_href . 'confirm.php?id='.$row['member_id'].SEP.'m='.$code;

			/* send the email confirmation message: */
			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
			$mail = new ATutorMailer();

			$mail->From     = $_config['contact_email'];
			$mail->AddAddress($row['email']);
			$mail->Subject = SITE_NAME . ': ' . _AT('email_confirmation_subject');
			$mail->Body    = _AT('email_confirmation_message', $_base_href, $confirmation_link)."\n\n";
			$mail->Send();

			$msg->addFeedback('CONFIRMATION_SENT');
		} else {
			$msg->addFeedback('ACCOUNT_CONFIRMED');
		}

		header('Location: '.$_base_href.'login.php');
		exit;
	} else {
		$msg->addError('EMAIL_NOT_FOUND');
	}
}

if (isset($_REQUEST['auto_login']))
{
	
	$sql = "SELECT M.member_id, M.login, M.preferences, M.language FROM ".TABLE_PREFIX."members M WHERE M.member_id=".$_REQUEST["member_id"];
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) 
	{
		$_SESSION['valid_user'] = true;
		$_SESSION['member_id']	= $_REQUEST["member_id"];
		$_SESSION['course_id']  = 0;
		$_SESSION['login']		= $row[login];
		assign_session_prefs(unserialize(stripslashes($row['preferences'])));
		$_SESSION['is_guest']	= 0;
		$_SESSION['lang']		= $row[lang];
		session_write_close();

		header('Location: '.AT_BASE_HREF.'bounce.php?course='.$_POST['course']);
		exit;
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
		<input type="text" name="email" id="email" size="50" />
		<input type="hidden" name="en_id" id="en_id" value="<?php echo $_REQUEST['en_id']; ?>" size="50" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('send'); ?>" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>