<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$
//http://localhost/atutorgit/confirm.php?id=18&m=3a4f4d38ba 
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
 
    $sql    = "SELECT creation_date FROM %smembers WHERE member_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $id), TRUE);
	
	if ($row['creation_date'] != '') {
		$code = substr(md5($e . $row['creation_date'] . $id), 0, 10);

		if ($code === $m) {
			$sql = "UPDATE %smembers SET email='%s', last_login=NOW(), creation_date=creation_date WHERE member_id=%d";
			$result = queryDB($sql, array(TABLE_PREFIX, $e, $id));
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
	
	$sql    = "SELECT email, creation_date FROM %smembers WHERE member_id=%d AND status=".AT_STATUS_UNCONFIRMED;
	$row = queryDB($sql, array(TABLE_PREFIX, $id), TRUE);	

	if ($row['creation_date'] != '') {
		$code = substr(md5($row['email'] . $row['creation_date'] . $id), 0, 10);

		if ($code == $m) {
			if (defined('AUTO_APPROVE_INSTRUCTORS') && AUTO_APPROVE_INSTRUCTORS) {
				$sql = "UPDATE %smembers SET status=".AT_STATUS_INSTRUCTOR.", creation_date=creation_date, last_login=NOW() WHERE member_id=%d";				
			} else {
					$sql = "UPDATE %smembers SET status=".AT_STATUS_STUDENT.", creation_date=creation_date, last_login=NOW() WHERE member_id=%d";
			}
			$result = queryDB($sql, array(TABLE_PREFIX, $id));

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
				echo '<p style="text-align:center"><a href="'. $_SERVER['PHP_SELF'] . '?auto_login=1'.SEP.'member_id='. $id.SEP.'code=' . $code .'">' . _AT("go_to_my_start_page") . '</a></p>';
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
 $_REQUEST["code"] = $code;
				$_REQUEST["code"] = $code;
			}
		} else {
			$msg->addError('CONFIRM_BAD');
		}
	} else {
		$msg->addError('CONFIRM_BAD');
	}
} else if (isset($_POST['submit'])) {
	$_POST['email'] = $addslashes($_POST['email']);

	$sql    = "SELECT member_id, email, creation_date, status FROM %smembers WHERE email='%s'";
	$row = queryDB($sql, array(TABLE_PREFIX, $_POST['email']), TRUE);
	
	if ($row['creation_date'] != '') {
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
	
	$sql = "SELECT M.member_id, M.login, M.creation_date, M.preferences, M.language FROM %smembers M WHERE M.member_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $_REQUEST["member_id"]), TRUE);

	$code = substr(md5($e . $row['creation_date'] . $id), 0, 10);
	
	if ($row['member_id'] != '' && isset($_REQUEST['code']) && $_REQUEST['code'] === $code) 
	{
		$_SESSION['valid_user'] = true;
		$_SESSION['member_id']	= intval($_REQUEST["member_id"]);
		$_SESSION['course_id']  = 0;
		$_SESSION['login']		= $row[login];
		if ($row['preferences'] == "")
			assign_session_prefs(unserialize(stripslashes($_config["pref_defaults"])), 1);
		else
			assign_session_prefs(unserialize(stripslashes($row['preferences'])), 1);
		$_SESSION['is_guest']	= 0;
		$_SESSION['lang']		= $row[lang];
		session_write_close();

		header('Location: '.AT_BASE_HREF.'bounce.php?course='.$_POST['course']);
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$savant->display('confirm.tmpl.php');

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
