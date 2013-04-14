<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

if (isset($_POST['cancel'])) {
	header('Location: '.AT_BASE_HREF.'mods/_core/users/users.php');
	exit;
}

if (isset($_POST['submit'])) {
	$missing_fields = array();
        $_POST['password']   = $addslashes($_POST['password']);
		$_POST['website']    = $addslashes($_POST['website']);
		$_POST['first_name'] = $addslashes($_POST['first_name']);
		$_POST['second_name']  = $addslashes($_POST['second_name']);
		$_POST['last_name']  = $addslashes($_POST['last_name']);
		$_POST['address']    = $addslashes($_POST['address']);
		$_POST['postal']     = $addslashes($_POST['postal']);
		$_POST['city']       = $addslashes($_POST['city']);
		$_POST['province']   = $addslashes($_POST['province']);
		$_POST['country']    = $addslashes($_POST['country']);
		$_POST['phone']      = $addslashes($_POST['phone']);
		$_POST['status']     = intval($_POST['status']);
		$_POST['gender']     = $addslashes($_POST['gender']);
		$_POST['login']      = $addslashes($_POST['login']);
        $_POST['email'] = $addslashes($_POST['email']);


	//check if student id (public field) is already being used
	if (!$_POST['overwrite'] && !empty($_POST['student_id'])) {
		$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."master_list WHERE public_field='$_POST[student_id]' && member_id<>0",$db);
		if (mysql_num_rows($result) != 0) {
			$msg->addError('CREATE_MASTER_USED');
		}
	}

	/* login name check */
	if ($_POST['login'] == '') {
		$missing_fields[] = _AT('login_name');
	} else {
		/* check for special characters */
		if (!(preg_match("/^[a-zA-Z0-9_.-]([a-zA-Z0-9_.-])*$/i", $_POST['login']))) {
			$msg->addError('LOGIN_CHARS');
		} else {
			$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE login='$_POST[login]'",$db);
			if (mysql_num_rows($result) != 0) {
				$valid = 'no';
				$msg->addError('LOGIN_EXISTS');
			}  else {
				$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."admins WHERE login='$_POST[login]'",$db);
				if (mysql_num_rows($result) != 0) {
					$msg->addError('LOGIN_EXISTS');
				}
			}
		}
	}

	/* password check:	*/
	$_POST['password'] = $_POST['form_password_hidden'];

	/* password check: password is verified front end by javascript. here is to handle the errors from javascript */
	if ($_POST['password_error'] <> "")
	{
		$pwd_errors = explode(",", $_POST['password_error']);

		foreach ($pwd_errors as $pwd_error)
		{
			if ($pwd_error == "missing_password")
				$missing_fields[] = _AT('password');
			else
				$msg->addError($pwd_error);
		}
	}

	/* email check */
	if ($_POST['email'] == '') {
		$missing_fields[] = _AT('email');
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $_POST['email'])) {
		$msg->addError('EMAIL_INVALID');
	}

	$result = mysql_query("SELECT member_id FROM ".TABLE_PREFIX."members WHERE email LIKE '$_POST[email]'",$db);
	if (mysql_num_rows($result) != 0) {
		$msg->addError('EMAIL_EXISTS');
	}

	if (!$_POST['first_name']) {
		$missing_fields[] = _AT('first_name');
	}

	if (!$_POST['last_name']) {
		$missing_fields[] = _AT('last_name');
	}

	$_POST['first_name'] = str_replace('<', '', $_POST['first_name']);
	$_POST['second_name'] = str_replace('<', '', $_POST['second_name']);
	$_POST['last_name'] = str_replace('<', '', $_POST['last_name']);

	$_POST['login'] = strtolower($_POST['login']);

	//check date of birth
	$mo = intval($_POST['month']);
	$day = intval($_POST['day']);
	$yr = intval($_POST['year']);

	/* let's us take (one or) two digit years (ex. 78 = 1978, 3 = 2003) */
	if ($yr < date('y')) { 
		$yr += 2000; 
	} else if ($yr < 1900) { 
		$yr += 1900; 
	} 

	$dob = $yr.'-'.$mo.'-'.$day;

	if ($mo && $day && $yr && !checkdate($mo, $day, $yr)) {	
		$msg->addError('DOB_INVALID');
	} else if (!$mo || !$day || !$yr) {
		$dob = '0000-00-00';
		$yr = $mo = $day = 0;
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) {
		if (($_POST['website']) && (!strstr($_POST['website'], '://'))) { 
			$_POST['website'] = 'http://' . $_POST['website']; 
		}
		if ($_POST['website'] == 'http://') { 
			$_POST['website'] = ''; 
		}
		$_POST['postal'] = strtoupper(trim($_POST['postal']));
	
		if (isset($_POST['private_email'])) {
			$_POST['private_email'] = 1;
		} else {
			$_POST['private_email'] = 0;
		}

		$now = date('Y-m-d H:i:s'); // we use this later for the email confirmation.

		/* insert into the db. (the last 0 for status) */
		$sql = "INSERT INTO ".TABLE_PREFIX."members VALUES (NULL,'$_POST[login]','$_POST[password]','$_POST[email]','$_POST[website]','$_POST[first_name]', '$_POST[second_name]', '$_POST[last_name]', '$dob', '$_POST[gender]', '$_POST[address]','$_POST[postal]','$_POST[city]','$_POST[province]','$_POST[country]', '$_POST[phone]',$_POST[status], '$_config[pref_defaults]', '$now','$_config[default_language]', $_config[pref_inbox_notify], $_POST[private_email], '0000-00-00 00:00:00')";

		$result = mysql_query($sql, $db);

		$m_id	= mysql_insert_id($db);
		if (!$result) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$msg->addError('DB_NOT_UPDATED');
			$msg->printAll();
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}

		if (defined('AT_MASTER_LIST') && AT_MASTER_LIST) {
			$student_id  = $addslashes($_POST['student_id']);
			$student_pin = md5($addslashes($_POST['student_pin']));
			if ($student_id) {
				$sql = "UPDATE ".TABLE_PREFIX."master_list SET member_id=$m_id WHERE public_field='$student_id'";
				mysql_query($sql, $db);
				if (mysql_affected_rows($db) == 0) {
					$sql = "REPLACE INTO ".TABLE_PREFIX."master_list VALUES ('$student_id', '$student_pin', $m_id)";
					mysql_query($sql, $db);
				}
			}
		}


		if ($_POST['pref'] == 'access') {
			$_SESSION['member_id'] = $m_id;
			save_prefs();
			unset($_SESSION['member_id']);
		}


		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
		$mail = new ATutorMailer();
		$mail->AddAddress($_POST['email']);
		$mail->From    = $_config['contact_email'];
		
		if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION && ($_POST['status'] == AT_STATUS_UNCONFIRMED)) {
			$code = substr(md5($_POST['email'] . $now . $m_id), 0, 10);
			$confirmation_link = AT_BASE_HREF . 'confirm.php?id='.$m_id.SEP.'m='.$code;

			/* send the email confirmation message: */
			$mail->Subject = $_config['site_name'] . ': ' . _AT('email_confirmation_subject');
			$body .= _AT('admin_new_account_confirm', $_config['site_name'], $confirmation_link)."\n\n";

		} else {
			$mail->Subject = $_config['site_name'].": "._AT('account_information');
			$body .= _AT('admin_new_account', $_config['site_name'])."\n\n";
		}
		$body .= _AT('web_site') .' : '.AT_BASE_HREF."\n";
		$body .= _AT('login_name') .' : '.$_POST['login'] . "\n";
//		$body .= _AT('password') .' : '.$_POST['password'] . "\n";
		$mail->Body    = $body;
		$mail->Send();

		$msg->addFeedback('PROFILE_CREATED_ADMIN');
		header('Location: '.AT_BASE_HREF.'mods/_core/users/users.php');
		exit;
	}
}

$onload = 'document.form.login.focus();';

$savant->assign('languageManager', $languageManager);
$savant->assign('no_captcha', true);

if (!isset($_POST['status'])) {
	if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION) {
		$_POST['status'] = AT_STATUS_UNCONFIRMED;
	} else {
		$_POST['status'] = AT_STATUS_STUDENT;
	}
}

$savant->display('registration.tmpl.php');

?>
