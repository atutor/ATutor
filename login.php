<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$

$_user_location	= 'public';
define('AT_INCLUDE_PATH', 'include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

// For security reasons the token has to be generated anew before each login attempt.
// The entropy of SHA-1 input should be comparable to that of its output; in other words, the more randomness you feed it the better.

/***
* Remove comments below to enable a remote login form.
*/
if (isset($_POST['token']))
{
	$_SESSION['token'] = $_POST['token'];
}
else
{
	if (!isset($_SESSION['token']))
		$_SESSION['token'] = sha1(mt_rand() . microtime(TRUE));
}

/***
* Add comments 2 lines below to enable a remote login form.
*/
//if (!isset($_SESSION['token']))
//	$_SESSION['token'] = sha1(mt_rand() . microtime(TRUE));

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

//garbage collect for maximum login attempts table
if (rand(1, 100) == 1){
	$sql = 'DELETE FROM '.TABLE_PREFIX.'member_login_attempt WHERE expiry < '. time();
	mysql_query($sql, $db);
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

	//Check if this account has exceeded maximum attempts
//	$sql = 'SELECT a.login, b.attempt, b.expiry FROM (SELECT login FROM '.TABLE_PREFIX.'members UNION SELECT login FROM '.TABLE_PREFIX.'admins) AS a LEFT JOIN '.TABLE_PREFIX."member_login_attempt b ON a.login=b.login WHERE a.login='$this_login'";
	$sql = 'SELECT login, attempt, expiry FROM '.TABLE_PREFIX."member_login_attempt WHERE login='$this_login'";

	$result = mysql_query($sql, $db);
	if ($result && mysql_numrows($result) > 0){
		list($attempt_login_name, $attempt_login, $attempt_expiry) = mysql_fetch_array($result);
	} else {
		$attempt_login_name = '';
		$attempt_login = 0;
		$attempt_expiry = 0;
	}
	if($attempt_expiry > 0 && $attempt_expiry < time()){
		//clear entry if it has expired
		$sql = 'DELETE FROM '.TABLE_PREFIX."member_login_attempt WHERE login='$this_login'";
		mysql_query($sql, $db);
		$attempt_login = 0;	
		$attempt_expiry = 0;
	} 
	
	if ($used_cookie) {
		$sql = "SELECT member_id, login, first_name, second_name, last_name, preferences,password AS pass, language, status, last_login FROM ".TABLE_PREFIX."members WHERE login='$this_login' AND password='$this_password'";
	} else {
		$sql = "SELECT member_id, login, first_name, second_name, last_name, preferences, language, status, password AS pass, last_login FROM ".TABLE_PREFIX."members WHERE (login='$this_login' OR email='$this_login') AND SHA1(CONCAT(password, '$_SESSION[token]'))='$this_password'";
	}
	$result = mysql_query($sql, $db);

	if($_config['max_login'] > 0 && $attempt_login >= $_config['max_login']){
		$msg->addError('MAX_LOGIN_ATTEMPT');
	} else if (($row = mysql_fetch_assoc($result)) && ($row['status'] == AT_STATUS_UNCONFIRMED)) {
		$msg->addError('NOT_CONFIRMED');
	} else if ($row && $row['status'] == AT_STATUS_DISABLED) {
		$msg->addError('ACCOUNT_DISABLED');
	} else if ($row) {
		$_SESSION['valid_user'] = true;
		$_SESSION['member_id']	= intval($row['member_id']);
		$_SESSION['login']		= $row['login'];
		if ($row['preferences'] == "")
			assign_session_prefs(unserialize(stripslashes($_config["pref_defaults"])), 1);
		else
			assign_session_prefs(unserialize(stripslashes($row['preferences'])), 1);
		$_SESSION['is_guest']	= 0;
		$_SESSION['lang']		= $row['language'];
		$_SESSION['course_id']  = 0;

		if ($auto_login == 1) {
			$parts = parse_url($_base_href);
			// update the cookie.. increment to another 2 days
			$cookie_expire = time()+172800;
			ATutor.setcookie('ATLogin', $this_login, $cookie_expire, $parts['path']);
			ATutor.setcookie('ATPass',  $row['pass'],  $cookie_expire, $parts['path']);
		}
		
		$_SESSION['first_login'] = false;
		if ($row['last_login'] == null || $row['last_login'] == '' || $row['last_login'] == '0000-00-00 00:00:00') {
		    $_SESSION['first_login'] = true;
		}

		$sql = "UPDATE ".TABLE_PREFIX."members SET creation_date=creation_date, last_login=NOW() WHERE member_id=$_SESSION[member_id]";
		mysql_query($sql, $db);

		//clear login attempt on successful login
		$sql = 'DELETE FROM '.TABLE_PREFIX."member_login_attempt WHERE login='$this_login'";
		mysql_query($sql, $db);

		//if page variable is set, bring them there.
		if (isset($_POST['p']) && $_POST['p']!=''){
			header ('Location: '.urldecode($_POST['p']));
			exit;
		}
		
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
			//clear login attempt on successful login
			$sql = 'DELETE FROM '.TABLE_PREFIX."member_login_attempt WHERE login='$this_login'";
			mysql_query($sql, $db);

			$msg->addFeedback('LOGIN_SUCCESS');

			header('Location: admin/index.php');
			exit;

		} else {
			//Only if the user exist in our database
//			if ($attempt_login_name!=''){
				$expiry_stmt = '';
				$attempt_login++;
				if ($attempt_expiry==0){
					$expiry_stmt = ', expiry='.(time() + LOGIN_ATTEMPT_LOCKED_TIME * 60);	//an hour from now
				} else {
					$expiry_stmt = ', expiry='.$attempt_expiry;	
				}
				$sql = 'REPLACE INTO '.TABLE_PREFIX.'member_login_attempt SET attempt='.$attempt_login . $expiry_stmt .", login='$this_login'";
				mysql_query($sql, $db);				
//			}
		}
		//Different error messages depend on the number of login failure.
		if ($_config['max_login'] > 0 && ($_config['max_login']-$attempt_login)==2){
			$msg->addError('MAX_LOGIN_ATTEMPT_2');
		} elseif ($_config['max_login'] > 0 && ($_config['max_login']-$attempt_login)==1){
			$msg->addError('MAX_LOGIN_ATTEMPT_1');
		} elseif ($_config['max_login'] > 0 && ($_config['max_login']-$attempt_login)==0){
			$msg->addError('MAX_LOGIN_ATTEMPT');
		} else {
			$msg->addError('INVALID_LOGIN');
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

$_SESSION['prefs']['PREF_FORM_FOCUS'] = 1;

/*****************************/
/* template starts down here */

$onload = 'document.form.form_login.focus();';

$savant->assign('form_course_id', $_GET['course']);

if (isset($_GET['course']) && $_GET['course']) {
	$savant->assign('title',  ' '._AT('to1').' '.$system_courses[$_GET['course']]['title']);
} else {
	$savant->assign('title',  ' ');
}

header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
$savant->display('login.tmpl.php');
?>
