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
	header('Location: ./users.php');
	exit;
}

if (isset($_POST['submit'])) {
	$id = intval($_POST['id']);

	/* email check */
	if ($_POST['email'] == '') {
		$msg->addError('EMAIL_MISSING');
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['email'])) {
		$msg->addError('EMAIL_INVALID');
	}
	$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE email LIKE '$_POST[email]' AND member_id <> $id",$db);

	if (mysql_num_rows($result) != 0) {
		$valid = 'no';
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

		$sql = "SELECT member_id FROM ".TABLE_PREFIX."members WHERE first_name='$first_name_sql' AND second_name='$second_name_sql' AND last_name='$last_name_sql' AND member_id<>$id LIMIT 1";
		$result = mysql_query($sql, $db);
		if (mysql_fetch_assoc($result)) {
			$msg->addError('FIRST_LAST_NAME_UNIQUE');
		}
	}

	
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
		if (($_POST['website']) && (!ereg("://",$_POST['website']))) { 
			$_POST['website'] = "http://".$_POST['website']; 
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

		//$_POST['password']   = $addslashes($_POST['password']);
		$_POST['website']    = $addslashes($_POST['website']);
		$_POST['first_name'] = $addslashes($_POST['first_name']);
		$_POST['second_name'] = $addslashes($_POST['second_name']);
		$_POST['last_name']  = $addslashes($_POST['last_name']);
		$_POST['address']    = $addslashes($_POST['address']);
		$_POST['postal']     = $addslashes($_POST['postal']);
		$_POST['city']       = $addslashes($_POST['city']);
		$_POST['province']   = $addslashes($_POST['province']);
		$_POST['country']    = $addslashes($_POST['country']);
		$_POST['phone']      = $addslashes($_POST['phone']);
		$_POST['status']     = intval($_POST['status']);
		$_POST['old_status']     = intval($_POST['old_status']);
		$_POST['gender']     = $addslashes($_POST['gender']);

		/* insert into the db. (the last 0 for status) */
		$sql = "UPDATE ".TABLE_PREFIX."members SET	email      = '$_POST[email]',
													website    = '$_POST[website]',
													first_name = '$_POST[first_name]',
													second_name= '$_POST[second_name]',
													last_name  = '$_POST[last_name]', 
													dob      = '$dob',
													gender   = '$_POST[gender]', 
													address  = '$_POST[address]',
													postal   = '$_POST[postal]',
													city     = '$_POST[city]',
													province = '$_POST[province]',
													country  = '$_POST[country]', 
													phone    = '$_POST[phone]',
													status   = $_POST[status],
													language = '$_SESSION[lang]', 
													private_email = $_POST[private_email]
				WHERE member_id = $id";
		$result = mysql_query($sql, $db);
		if (!$result) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$msg->addError('DB_NOT_UPDATED');
			$msg->printAll();
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}

		if (defined('AT_MASTER_LIST') && AT_MASTER_LIST) {
			$_POST['student_id'] = $addslashes($_POST['student_id']);
			$student_pin = $addslashes($_POST['student_pin']);

			$sql = "UPDATE ".TABLE_PREFIX."master_list SET member_id=0 WHERE member_id=$id";
			$result = mysql_query($sql, $db);

			if ($_POST['student_id']) {
				$sql = "UPDATE ".TABLE_PREFIX."master_list SET member_id=$id WHERE public_field='$_POST[student_id]'";
				$result = mysql_query($sql, $db);
				if (mysql_affected_rows($db) == 0) {
					$sql = "SELECT member_id FROM ".TABLE_PREFIX."master_list WHERE member_id=$id AND public_field='$_POST[student_id]'";
					$result = mysql_query($sql, $db);
					if (!$row = mysql_fetch_assoc($result)) {
						$sql = "REPLACE INTO ".TABLE_PREFIX."master_list VALUES ('$_POST[student_id]', '$student_pin', $id)";
						mysql_query($sql, $db);
					}
				}
			}
		}


		if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION && ($_POST['status'] == AT_STATUS_UNCONFIRMED) && ($_POST['old_status'] != AT_STATUS_UNCONFIRMED)) {

			$sql    = "SELECT email, creation_date FROM ".TABLE_PREFIX."members WHERE member_id=$id";
			$result = mysql_query($sql, $db);
			$row    = mysql_fetch_assoc($result);

			$code = substr(md5($row['email'] . $row['creation_date']. $id), 0, 10);
			$confirmation_link = $_base_href . 'confirm.php?id='.$id.SEP.'m='.$code;

			/* send the email confirmation message: */
			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
			$mail = new ATutorMailer();

			$mail->AddAddress($row['email']);
			$mail->From    = $_config['contact_email'];
			$mail->Subject = $_config['site_name'] . ' - ' . _AT('email_confirmation_subject');
			$mail->Body    = _AT('email_confirmation_message', $_config['site_name'], $confirmation_link);

			$mail->Send();
		}

		$msg->addFeedback('PROFILE_UPDATED_ADMIN');
		header('Location: '.$_base_href.'admin/users.php');
		exit;
	}
}

$id = intval($_REQUEST['id']);

if (empty($_POST)) {
	$sql    = "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id = $id";
	$result = mysql_query($sql, $db);
	if (!($row = mysql_fetch_assoc($result))) {
		require(AT_INCLUDE_PATH.'header.inc.php'); 	
		$msg->addError('USER_NOT_FOUND');	
		$msg->printAll();
		require(AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
	}
	
	$_POST  = $row;
	list($_POST['year'],$_POST['month'],$_POST['day']) = explode('-', $row['dob']);
	//$_POST['password2']  = $_POST['password'];
	$_POST['old_status'] = $_POST['status'];

	if (admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE) && defined('AT_MASTER_LIST') && AT_MASTER_LIST) {
		$sql    = "SELECT public_field FROM ".TABLE_PREFIX."master_list WHERE member_id=$id";
		$result = mysql_query($sql, $db);
		if ($row = mysql_fetch_assoc($result)) {
			$_POST['student_id'] = $row['public_field'];
		}
	}
}

$savant->assign('languageManager', $languageManager);

/* HAVE TO SEND MEMBER_ID THROUGH FORM AS A HIDDEN POST VARIABLE!!! */
/* PUT IN IF LOOP THAT LETS YOU SEE STATUS RADIO BUTTONS */
$savant->display('registration.tmpl.php');

?>