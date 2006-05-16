<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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

if (isset($_POST['cancel'])) {
	header('Location: '.$_base_href.'admin/users.php');
	exit;
}

if (isset($_POST['submit'])) {
	/* login name check */
	if ($_POST['login'] == '') {
		$msg->addError('LOGIN_NAME_MISSING');
	} else {
		/* check for special characters */
		if (!(eregi("^[a-zA-Z0-9_]([a-zA-Z0-9_])*$", $_POST['login']))) {
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
	if ($_POST['password'] == '') { 
		$msg->addError('PASSWORD_MISSING');
	} else {
		// check for valid passwords
		if ($_POST['password'] != $_POST['password2']){
			$valid= 'no';
			$msg->addError('PASSWORD_MISMATCH');
		}
	}
	
	/* email check */
	if ($_POST['email'] == '') {
		$msg->addError('EMAIL_MISSING');
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['email'])) {
		$msg->addError('EMAIL_INVALID');
	}

	$_POST['email'] = $addslashes($_POST['email']);
	$result = mysql_query("SELECT member_id FROM ".TABLE_PREFIX."members WHERE email LIKE '$_POST[email]'",$db);
	if (mysql_num_rows($result) != 0) {
		$msg->addError('EMAIL_EXISTS');
	}

	if (!$_POST['first_name']) { 
		$msg->addError('FIRST_NAME_MISSING');
	}

	if (!$_POST['last_name']) { 
		$msg->addError('LAST_NAME_MISSING');
	}

	// check if first+last is unique
	if ($_POST['first_name'] && $_POST['last_name']) {
		$first_name_sql  = $addslashes($_POST['first_name']);
		$last_name_sql   = $addslashes($_POST['last_name']);
		$second_name_sql = $addslashes($_POST['second_name']);

		$sql = "SELECT member_id FROM ".TABLE_PREFIX."members WHERE first_name='$first_name_sql' AND second_name='$second_name_sql' AND last_name='$last_name_sql' LIMIT 1";
		$result = mysql_query($sql, $db);
		if (mysql_fetch_assoc($result)) {
			$msg->addError('FIRST_LAST_NAME_UNIQUE');
		}
	}


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

	if (!$msg->containsErrors()) {
		if (($_POST['website']) && (!ereg('://',$_POST['website']))) { 
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

		$now = date('Y-m-d H:i:s'); // we use this later for the email confirmation.

		/* insert into the db. (the last 0 for status) */
		$sql = "INSERT INTO ".TABLE_PREFIX."members VALUES (0,'$_POST[login]','$_POST[password]','$_POST[email]','$_POST[website]','$_POST[first_name]', '$_POST[second_name]', '$_POST[last_name]', '$dob', '$_POST[gender]', '$_POST[address]','$_POST[postal]','$_POST[city]','$_POST[province]','$_POST[country]', '$_POST[phone]',$_POST[status], '$_config[pref_defaults]', '$now','$_config[default_language]', $_config[pref_inbox_notify], $_POST[private_email])";

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
			$student_pin = $addslashes($_POST['student_pin']);
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
			$confirmation_link = $_base_href . 'confirm.php?id='.$m_id.SEP.'m='.$code;

			/* send the email confirmation message: */
			$mail->Subject = $_config['site_name'] . ': ' . _AT('email_confirmation_subject');
			$body .= _AT('admin_new_account_confirm', $_config['site_name'], $confirmation_link)."\n\n";

		} else {
			$mail->Subject = $_config['site_name'].": "._AT('account_information');
			$body .= _AT('admin_new_account', $_config['site_name'])."\n\n";
		}
		$body .= _AT('web_site') .' : '.$_base_href."\n";
		$body .= _AT('login_name') .' : '.$_POST['login'] . "\n";
		$body .= _AT('password') .' : '.$_POST['password'] . "\n";
		$mail->Body    = $body;
		$mail->Send();

		$msg->addFeedback('PROFILE_CREATED_ADMIN');
		header('Location: '.$_base_href.'admin/users.php');
		exit;
	}
}

$onload = 'document.form.login.focus();';

$savant->assign('languageManager', $languageManager);

if (!isset($_POST['status'])) {
	if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION) {
		$_POST['status'] = AT_STATUS_UNCONFIRMED;
	} else {
		$_POST['status'] = AT_STATUS_STUDENT;
	}
}

$savant->display('registration.tmpl.php');

?>