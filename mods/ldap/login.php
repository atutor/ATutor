<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: login.php 7396 2008-04-15 19:46:57Z cindy $

$_user_location	= 'public';
define('AT_INCLUDE_PATH', 'include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
/*
smal
09-09-2008
Add aditional libraries required by ATutor.ldap.mod
*/
require ('admin/ldap_lib.php');
require (AT_INCLUDE_PATH.'lib/rsa.inc.php');


if (isset($_GET['course'])) {
	$_GET['course'] = intval($_GET['course']);
} else {
	$_GET['course'] = 0;
}

// check if we have a cookie
if (!$msg->containsFeedbacks()) {
	if (isset($_COOKIE['ATLogin'])) {
		$cookie_login = $_COOKIE['ATLogin'];
	}
	if (isset($_COOKIE['ATPass'])) {
		$cookie_pass  = $_COOKIE['ATPass'];
	}
}

if (isset($cookie_login, $cookie_pass) && !isset($_POST['submit'])) {
	/* auto login */
	$this_login		= $cookie_login;
	$this_password	= $cookie_pass;
	$auto_login		= 1;
	$used_cookie	= true;
} else if (isset($_POST['submit'])) {
	/* form post login */
	$this_password = $_POST['form_password_hidden'];
	$this_login		= $_POST['form_login'];
	$auto_login		= isset($_POST['auto']) ? intval($_POST['auto']) : 0;
	$used_cookie	= false;
        $hash_password = $addslashes($_POST['form_hash_password']);
        /*
	smal
	09-09-2008
	RSA Decoded, required by ldap.mod
	*/
	$auth_string = rsa_decode(PRIVATE_KEY, $_POST['form_password_ldap']);
	
	if ($auth_string = rsa_decode(PRIVATE_KEY, $_POST['form_password_ldap'])){
		if(check_valid_login($auth_string)){
			$this_password_ldap = check_valid_login($auth_string);
			clear_auth_cookie();
		}else{
			$msg->addError('INVALID_LOGIN_RSA_TIMEOUT');
			header('Location: login.php');
			exit;
		}
	}else{
		$msg->addError('INVALID_LOGIN_RSA');
		header('Location: login.php');
		exit;
	}

}


if (isset($this_login, $this_password)) {
	if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
		session_regenerate_id(TRUE);
	}


	if ($_GET['course']) {
		$_POST['form_course_id'] = intval($_GET['course']);
	} else {
		$_POST['form_course_id'] = intval($_POST['form_course_id']);
	}
	$this_login    = $addslashes($this_login);
	$this_password = $addslashes($this_password);

	if ($used_cookie) {
		// check if that cookie is valid
		//$sql = "SELECT member_id, login, first_name, second_name, last_name, preferences, password AS pass, language, status FROM ".TABLE_PREFIX."members WHERE login='$this_login' AND password='$this_password'";
		$sql = "SELECT member_id, login, first_name, second_name, last_name, preferences,password AS pass, language, status FROM ".TABLE_PREFIX."members WHERE login='$this_login' AND SHA1(CONCAT(password, '$tstu_salt'))='$this_password'";
	} else {
//echo DB_PASSWORD;
//exit;
		$sql = "SELECT member_id, login, first_name, second_name, last_name, preferences, language, status, password AS pass FROM ".TABLE_PREFIX."members WHERE (login='$this_login' OR email='$this_login') AND SHA1(CONCAT(password, '$_SESSION[token]'))='$this_password'";
	}
	$result = mysql_query($sql, $db);

	if (($row = mysql_fetch_assoc($result)) && ($row['status'] == AT_STATUS_UNCONFIRMED)) {
		$msg->addError('NOT_CONFIRMED');
	} else if ($row && $row['status'] == AT_STATUS_DISABLED) {
		$msg->addError('ACCOUNT_DISABLED');
	} else if ($row) {
		$_SESSION['valid_user'] = true;
		$_SESSION['member_id']	= intval($row['member_id']);
		$_SESSION['login']		= $row['login'];
		assign_session_prefs(unserialize(stripslashes($row['preferences'])));
		$_SESSION['is_guest']	= 0;
		$_SESSION['lang']		= $row['language'];
		$_SESSION['course_id']  = 0;

		if ($auto_login == 1) {
			$parts = parse_url($_base_href);
			// update the cookie.. increment to another 2 days
			$cookie_expire = time()+172800;
			setcookie('ATLogin', $this_login, $cookie_expire, $parts['path'], $parts['host'], 0);
			setcookie('ATPass',  sha1($row['pass'].$tstu_salt),  $cookie_expire, $parts['path'], $parts['host'], 0);
		}

		$sql = "UPDATE ".TABLE_PREFIX."members SET creation_date=creation_date, last_login=NOW() WHERE member_id=$_SESSION[member_id]";
		mysql_query($sql, $db);

		$msg->addFeedback('LOGIN_SUCCESS');
		header('Location: bounce.php?course='.$_POST['form_course_id']);
		exit;
	} else {
		// check if it's an admin login.
		$sql = "SELECT login, `privileges`, language FROM ".TABLE_PREFIX."admins WHERE login='$this_login' AND SHA1(CONCAT(password, '$_SESSION[token]'))='$this_password' AND `privileges`>0";
		$result = mysql_query($sql, $db);

		if ($row = mysql_fetch_assoc($result)) {
			$sql = "UPDATE ".TABLE_PREFIX."admins SET last_login=NOW() WHERE login='$this_login'";
			mysql_query($sql, $db);

			$_SESSION['login']		= $row['login'];
			$_SESSION['valid_user'] = true;
			$_SESSION['course_id']  = -1;
			$_SESSION['privileges'] = intval($row['privileges']);
			$_SESSION['lang'] = $row['language'];

			write_to_log(AT_ADMIN_LOG_UPDATE, 'admins', mysql_affected_rows($db), $sql);

			$msg->addFeedback('LOGIN_SUCCESS');

			header('Location: admin/index.php');
			exit;

		} else {
                        /*
			smal
			09-09-2008
			Add LDAP auth provided by ATutor.ldap.mod
			*/
			if (ldap_bind_connect($this_login,$this_password_ldap)){
				if ($arr = get_ldap_entry_info($this_login,$this_password_ldap, $hash_password)){
					if (insert_user_info($arr)){
						$sql = "SELECT member_id, login, preferences, language, status FROM ".TABLE_PREFIX."members WHERE login='$this_login'";
						$result = mysql_query($sql, $db);
						if (($row = mysql_fetch_assoc($result)) && ($row['status'] == AT_STATUS_UNCONFIRMED)) {
							$msg->addError('NOT_CONFIRMED');
						} else if ($row && $row['status'] == AT_STATUS_DISABLED) {
							$msg->addError('ACCOUNT_DISABLED');
						} else if ($row) {
							$_SESSION['valid_user'] = true;
							$_SESSION['member_id']	= intval($row['member_id']);
							$_SESSION['login']= get_login($_SESSION['member_id']);
							assign_session_prefs(unserialize(stripslashes($row['preferences'])));
							$_SESSION['is_guest']	= 0;
							$_SESSION['lang']		= $row['language'];
							$_SESSION['course_id']  = 0;
							add_ldap_log('YOUR LDAP SERVER'); #Define LDAP server name or Null
						}
                                                $msg->addFeedback('LOGIN_SUCCESS');
						header('Location: bounce.php?course='.$_POST['form_course_id']);
						exit;
					}else{
						$msg->addError('INVALID_LOGIN');
					}
				}
			}
		}
	}
}

$_SESSION['session_test'] = TRUE;

if (isset($_SESSION['member_id'])) {
	$sql = "DELETE FROM ".TABLE_PREFIX."users_online WHERE member_id=$_SESSION[member_id]";
	$result = @mysql_query($sql, $db);
}

unset($_SESSION['login']);
unset($_SESSION['valid_user']);
unset($_SESSION['member_id']);
unset($_SESSION['is_admin']);
unset($_SESSION['course_id']);
unset($_SESSION['is_super_admin']);
unset($_SESSION['dd_question_ids']);

// Refresh the security token
refresh_token();

$_SESSION['prefs']['PREF_FORM_FOCUS'] = 1;

/*****************************/
/* template starts down here */

$onload = 'document.form.form_login.focus();';

$savant->assign('course_id', $_GET['course']);

if (isset($_GET['course']) && $_GET['course']) {
	$savant->assign('title',  ' '._AT('to1').' '.$system_courses[$_GET['course']]['title']);
} else {
	$savant->assign('title',  ' ');
}

header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
$savant->display('login.tmpl.php');
?>
