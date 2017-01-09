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
	if (isset($_POST['ml']) && $_REQUEST['ml']) {
	    $msg->addFeedback('CANCELLED');
		header('Location: '.AT_BASE_HREF.'mods/_core/users/master_list.php');
	} else {
		$msg->addFeedback('CANCELLED');
		header('Location: '.AT_BASE_HREF.'mods/_core/users/users.php');
	}
	exit;
}

if (isset($_POST['submit'])) {
	check_csrf_token();

	$missing_fields = array();

	$id = intval($_POST['id']);
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
    $_POST['student_id'] = intval($_POST['student_id']);
    $_POST['email']      = $addslashes($_POST['email']);

	//check if student id (public field) is already being used
	if (!$_POST['overwrite'] && !empty($_POST['student_id'])) {
	
	    $sql = "SELECT public_field FROM %smaster_list WHERE public_field='%s' AND member_id<>0 AND member_id<>%d";
        $row_master = queryDB($sql, array(TABLE_PREFIX, $_POST['student_id'], $id), TRUE);

		if(count($row_master) > 0){
			$msg->addError('CREATE_MASTER_USED');
		}
	}

	/* email check */
	if ($_POST['email'] == '') {
		$missing_fields[] = _AT('email');
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $_POST['email'])) {
		$msg->addError('EMAIL_INVALID');
	}
	
    $sql = "SELECT * FROM %smembers WHERE email LIKE '%s' AND member_id <> %d";
    $row_members = queryDB($sql, array(TABLE_PREFIX, $_POST['email'], $id), TRUE);
    
    if(count($row_members) > 0){
		$valid = 'no';
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
		if (isset($_POST['profile_pic_delete'])) {
			profile_image_delete($id);
		}
		if (($_POST['website']) && (!strstr($_POST['website'], "://"))) { 
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

		/* insert into the db. (the last 0 for status) */

        $sql = "UPDATE %smembers SET	email   = '$_POST[email]',
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
                                        private_email = $_POST[private_email],
                                        creation_date=creation_date,
                                        last_login=last_login 
                                        WHERE member_id = $id";
		$result = queryDB($sql, array(TABLE_PREFIX));

		if (defined('AT_MASTER_LIST') && AT_MASTER_LIST) {
			$_POST['student_id'] = $addslashes($_POST['student_id']);
			$student_pin = sha1($addslashes($_POST['student_pin']));

			//if changed, delete old stud id
			if (!empty($_POST['old_student_id']) && $_POST['old_student_id'] != $_POST['student_id']) {
			
				$sql = "DELETE FROM %smaster_list WHERE public_field='%s' AND member_id=%d";
				$result = queryDB($sql, array(TABLE_PREFIX, $_POST['old_student_id'], $id));
			}
			//if new is set
			if (!empty($_POST['student_id']) && $_POST['old_student_id'] != $_POST['student_id']) {

				$sql = "REPLACE INTO %smaster_list VALUES ('%s', '', %d)";
				$result = queryDB($sql, array(TABLE_PREFIX, $_POST['student_id'], $id));

			}
		}


		if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION && ($_POST['status'] == AT_STATUS_UNCONFIRMED) && ($_POST['old_status'] != AT_STATUS_UNCONFIRMED)) {

			$sql    = "SELECT email, creation_date FROM %smembers WHERE member_id=%d";
			$row = queryDB($sql, array(TABLE_PREFIX, $id), TRUE);
			
			$code = substr(md5($row['email'] . $row['creation_date']. $id), 0, 10);
			$confirmation_link = AT_BASE_HREF . 'confirm.php?id='.$id.SEP.'m='.$code;

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
		if (isset($_POST['ml']) && $_REQUEST['ml']) {
			header('Location: '.AT_BASE_HREF.'mods/_core/users/master_list.php');
		} else {
			header('Location: '.AT_BASE_HREF.'mods/_core/users/users.php');
		}
		exit;
	}
}

$id = intval($_REQUEST['id']);

if (empty($_POST)) {
	$sql    = "SELECT * FROM %smembers WHERE member_id = %d";
	$row = queryDB($sql, array(TABLE_PREFIX, $id), TRUE);

	if(count($row) < 1){
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
		$sql    = "SELECT public_field FROM %smaster_list WHERE member_id=%d";
		$row = queryDB($sql, array(TABLE_PREFIX, $id), TRUE);
		if(count($row) > 0){
			$_POST['old_student_id'] = $row['public_field'];
			$_POST['student_id'] = $row['public_field'];
		}
	}
}

$savant->assign('languageManager', $languageManager);

if (isset($_REQUEST['ml']) && $_REQUEST['ml']) {
	// redirect back to the master list
	$savant->assign('ml', 1);
} else {
	$savant->assign('ml', 0);
}


/* HAVE TO SEND MEMBER_ID THROUGH FORM AS A HIDDEN POST VARIABLE!!! */
/* PUT IN IF LOOP THAT LETS YOU SEE STATUS RADIO BUTTONS */
$savant->assign('no_captcha', true);
$savant->display('registration.tmpl.php');

?>
