<?php
/****************************************************************/
/* ATutor                                                       */
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
$_user_location	= 'public';

define('AT_INCLUDE_PATH', 'include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_INCLUDE_PATH."securimage/securimage.php");
include(AT_INCLUDE_PATH."login_functions.inc.php");

if($_config['allow_registration'] != 1){
		$msg->addInfo('REG_DISABLED');
		require(AT_INCLUDE_PATH.'header.inc.php');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
}

if (isset($_POST['cancel'])) {
    if(isset($_SESSION['member_id']) && $_SESSION['member_id'] >0 && $_SESSION['login']) {
        $msg->addFeedback('CANCELLED');
        header('Location: index.php');
    }
    else
        header('Location: ./login.php');
	exit;
} else if (isset($_POST['submit'])) {
	check_csrf_token();

    if(isset($_SESSION['member_id']) && $_SESSION['member_id'] > 0 && $_SESSION['login']) {
        $member_id = $_SESSION['member_id'];
        require (AT_INCLUDE_PATH.'html/auto_enroll_courses.inc.php');
        if($course_registered_names != "")
        $msg->addInfo(array(AUTO_ENROLL_ALREADY_ENROLLED,$course_registered_names));
        if(isset($_SESSION['course_id'])){
            $sql = "SELECT title from %scourses WHERE course_id=%d";
            $course_names = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
            $course_names = stripslashes($course_names[0]['title']);
        }
        if($course_names != "")
        $msg->addFeedback(array(LOGIN_SUCCESS_AUTO_ENROLL,$course_names));
        header('Location: index.php');
        exit;
    }

	$missing_fields = array();

	/* email check */
	$chk_email = $addslashes($_POST['email']);
	$chk_login = $addslashes($_POST['login']);

	//CAPTCHA
	if (isset($_config['use_captcha']) && $_config['use_captcha']==1) {
		$img = new Securimage();
		$valid = $img->check($_POST['secret']);
		if (!$valid)
			$msg->addError('SECRET_ERROR');
	}

	$_POST['password'] = $_POST['form_password_hidden'];
	$_POST['first_name'] = trim($_POST['first_name']);
	$_POST['second_name'] = trim($_POST['second_name']);
	$_POST['last_name'] = trim($_POST['last_name']);

	$_POST['first_name'] = str_replace('<', '', $_POST['first_name']);
	$_POST['second_name'] = str_replace('<', '', $_POST['second_name']);
	$_POST['last_name'] = str_replace('<', '', $_POST['last_name']);

	/* login name check */
	if ($_POST['login'] == '') {
		$missing_fields[] = _AT('login_name');
	} else {
		/* check for special characters */
		if (!(preg_match("/^[a-zA-Z0-9_.-]([a-zA-Z0-9_.-])*$/i", $_POST['login']))) {
			$msg->addError('LOGIN_CHARS');
		} else {
			$sql = "SELECT * FROM %smembers WHERE login='%s'";
			$rows_logins = queryDB($sql, array(TABLE_PREFIX, $chk_login));
			$num_rows_logins = count($rows_logins);

			if ($num_rows_logins != 0) {
				$msg->addError('LOGIN_EXISTS');
			} else {
			    $sql = "SELECT * FROM %sadmins WHERE login='%s'";
                $rows_admins = queryDB($sql, array(TABLE_PREFIX, $chk_login));
                $num_rows_admins = count($rows_admins);
				if ($num_rows_admins != 0) {
					$msg->addError('LOGIN_EXISTS');
				}
			}
		}
	}

	/* password check: password is verified front end by javascript. here is to handle the errors from javascript */
	$password_error = $_POST['password_error'];
	if ($password_error && $password_error <> "") {
		$separator = ',';
		$pwd_errors = explode($separator, $password_error);

		foreach ($pwd_errors as $pwd_error) {
			$pwd_error = strip_tags(urldecode($pwd_error));
			if ($pwd_error == "missing_password") {
    			$missing_fields[] = _AT('password');
			} else {
    			$msg->addError($pwd_error);
			}
		}
	}

	if ($_POST['email'] == '') {
		$missing_fields[] = _AT('email');
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $_POST['email'])) {
		$msg->addError('EMAIL_INVALID');
	}
	$sql = "SELECT * FROM %smembers WHERE email='%s'";
    $rows_email = queryDB($sql,array(TABLE_PREFIX, $chk_email));
    $num_rows_email = count($rows_email);

	if ($num_rows_email != 0) {
		$msg->addError('EMAIL_EXISTS');
	} else if ($_POST['email'] != $_POST['email2']) {
		$msg->addError('EMAIL_MISMATCH');
	}

	if (!$_POST['first_name']) {
		$missing_fields[] = _AT('first_name');
	}

	if (!$_POST['last_name']) {
		$missing_fields[] = _AT('last_name');
	}

	$_POST['login'] = strtolower($_POST['login']);

	//check date of birth
	$mo = $_POST['month'] = intval($_POST['month']);
	$day = $_POST['day'] = intval($_POST['day']);
	$yr = $_POST['year'] = intval($_POST['year']);

	/* let's us take (one or) two digit years (ex. 78 = 1978, 3 = 2003) */
	if ($yr <= date('y')) {
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

	unset($master_list_sql);
	if (defined('AT_MASTER_LIST') && AT_MASTER_LIST) {

		$student_id  = $addslashes($_POST['student_id']);
		$student_pin = md5($_POST['student_pin']);

        $sql    = "SELECT member_id FROM %smaster_list WHERE public_field='%s' AND hash_field='%s'";
		$row_master_list = queryDB($sql, array(TABLE_PREFIX, $student_id, $student_pin), TRUE);

		if (!isset($row_master_list['member_id']) || $row_master_list['member_id'] != 0) {
			// the row wasn't found, or it was found but already used
			$msg->addError('REGISTER_MASTER_USED');
		} else {
			$master_list_sql = "UPDATE %smaster_list SET member_id=LAST_INSERT_ID() WHERE public_field='%s' AND hash_field='%s'";
		}
	}

	if (($_POST['gender'] != 'm') && ($_POST['gender'] != 'f')) {
		$_POST['gender'] = 'n'; // not specified
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) {
		if (($_POST['website']) && (!strstr($_POST['website'],"://"))) {
			$_POST['website'] = "http://".$_POST['website'];
		}
		if ($_POST['website'] == 'http://') {
			$_POST['website'] = '';
		}
		if (isset($_POST['private_email'])) {
			$_POST['private_email'] = 1;
		} else {
			$_POST['private_email'] = 0;
		}
		$_POST['postal'] = strtoupper(trim($_POST['postal']));

		$_POST['email']      = $addslashes($_POST['email']);
		$_POST['login']      = $addslashes($_POST['login']);
		$_POST['password']   = $addslashes($_POST['password']);
		$_POST['website']    = $addslashes($_POST['website']);
		$_POST['first_name'] = $addslashes($_POST['first_name']);
		$_POST['second_name']= $addslashes($_POST['second_name']);
		$_POST['last_name']  = $addslashes($_POST['last_name']);
		$_POST['address']    = $addslashes($_POST['address']);
		$_POST['postal']     = $addslashes($_POST['postal']);
		$_POST['city']       = $addslashes($_POST['city']);
		$_POST['province']   = $addslashes($_POST['province']);
		$_POST['country']    = $addslashes($_POST['country']);
		$_POST['phone']      = $addslashes($_POST['phone']);

		if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION) {
			$status = AT_STATUS_UNCONFIRMED;
		} else {
			$status = AT_STATUS_STUDENT;
		}
		$now = date('Y-m-d H:i:s'); // we use this later for the email confirmation.

		/* insert into the db */
		$sql = "INSERT INTO %smembers
		              (login,
		               password,
		               email,
		               website,
		               first_name,
		               second_name,
		               last_name,
		               dob,
		               gender,
		               address,
		               postal,
		               city,
		               province,
		               country,
		               phone,
		               status,
		               preferences,
		               creation_date,
		               language,
		               inbox_notify,
		               private_email,
		               last_login)
		       VALUES ('$_POST[login]',
		               '$_POST[password]',
		               '$_POST[email]',
		               '$_POST[website]',
		               '$_POST[first_name]',
		               '$_POST[second_name]',
		               '$_POST[last_name]',
		               '$dob',
		               '$_POST[gender]',
		               '$_POST[address]',
		               '$_POST[postal]',
		               '$_POST[city]',
		               '$_POST[province]',
		               '$_POST[country]',
		               '$_POST[phone]',
		               $status,
		               '$_config[pref_defaults]',
		               '$now',
		               '$_SESSION[lang]',
		               $_config[pref_inbox_notify],
		               $_POST[private_email],
		               NULL)";


		$result = queryDB($sql, array(TABLE_PREFIX)) or die(at_db_error());
		$m_id	= at_insert_id();

		if (!$result) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$msg->addError('DB_NOT_UPDATED');
			$msg->printAll();
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}

		if (isset($master_list_sql)) {
			queryDB($master_list_sql, array(TABLE_PREFIX,$student_id, $student_pin));
		}

        // enroll in the course, if registering from there
        if(isset($_SESSION['course_id']) && $course_info['access'] != 'private'){
            $sql	= "INSERT INTO %scourse_enrollment VALUES (%d, %d, 'y', 0, '"._AT('student')."', 0)";
		    $result = queryDB($sql, array(TABLE_PREFIX,$m_id, $_SESSION['course_id']));
        }
		//reset login attempts
			if ($result){
				$sql = "DELETE FROM %smember_login_attempt WHERE login='%s'";
				queryDB($sql, array(TABLE_PREFIX, $_POST['login']));
			}

		if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION) {
			$msg->addFeedback('REG_THANKS_CONFIRM');

			$code = substr(md5($_POST['email'] . $now . $m_id), 0, 10);

			if (isset($_REQUEST["en_id"]) && $_REQUEST["en_id"] <> "")
				$confirmation_link = $_base_href . 'confirm.php?id='.$m_id.SEP.'m='.$code.SEP.'en_id='.$_REQUEST["en_id"];
			else
				$confirmation_link = $_base_href . 'confirm.php?id='.$m_id.SEP.'m='.$code;

			/* send the email confirmation message: */
			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
			$mail = new ATutorMailer();

			$mail->From     = $_config['contact_email'];
			$mail->AddAddress($_POST['email']);
			$mail->Subject = SITE_NAME . ' - ' . _AT('email_confirmation_subject');
			$mail->Body    = _AT('email_confirmation_message', SITE_NAME, $confirmation_link);

			$mail->Send();

		}
		else
		{
			// if en_id is set, automatically enroll into courses that links with en_id and go to "My Start Page"
			$member_id	= $m_id;

			require (AT_INCLUDE_PATH.'html/auto_enroll_courses.inc.php');

			// update last_login
			$sql = "UPDATE %smembers
			           SET last_login=now(), creation_date=creation_date
			         WHERE member_id=%d";
			queryDB($sql, array(TABLE_PREFIX, $member_id));

        $msg->addFeedback(array(LOGIN_SUCCESS_AUTO_ENROLL,$course_names));

			// auto login
			$_SESSION['valid_user'] = true;
			$_SESSION['member_id']	= $m_id;
			//$_SESSION['course_id']  = 0;
			$_SESSION['login']		= $_POST[login];
			assign_session_prefs(unserialize(stripslashes($_config[pref_defaults])), 1);
			$_SESSION['is_guest']	= 0;
			$_SESSION['lang']		= $_SESSION[lang];
			session_write_close();

            if(isset($_SESSION['course_id'])){
                header('Location: '.AT_BASE_HREF.'bounce.php?course='.$_SESSION['course_id']);
            } else{
			    header('Location: '.AT_BASE_HREF.'bounce.php?course='.$_POST['course']);
			}
		}

		require(AT_INCLUDE_PATH.'header.inc.php');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
} else {
	$_POST = array();
}

function validate_enid($en_id){
    if(preg_match("/^[a-zA-Z0-9]{6,10}$/", $en_id)){
        return $en_id;
    } else {
        return;
    }
}


/*****************************/
/* template starts down here */

if (defined('AT_MASTER_LIST') && AT_MASTER_LIST) {
	$onload = 'document.form.student_id.focus();';
} else {
	$onload = 'document.form.login.focus();';
}

$savant->assign('languageManager', $languageManager);

$savant->display('registration.tmpl.php');

?>
