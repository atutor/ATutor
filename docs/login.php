<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$page	 = 'login';
$_user_location	= 'public';
define('AT_INCLUDE_PATH', 'include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if (isset($_POST['cancel'])) {
	header('Location: about.php');
	exit;
}

// check if we have a cookie
if (isset($_COOKIE['ATLogin'])) {
	$cookie_login = $_COOKIE['ATLogin'];
}
if (isset($_COOKIE['ATPass'])) {
	$cookie_pass  = $_COOKIE['ATPass'];
}

if (isset($cookie_login, $cookie_pass) && !isset($_POST['submit'])) {
	/* auto login */
	$this_login		= $cookie_login;
	$this_password	= $cookie_pass;
	$auto_login		= 1;
	$used_cookie	= true;
	
} else if (isset($_POST['submit'])) {
	/* form post login */
	$this_login		= $_POST['form_login'];
	$this_password  = $_POST['form_password'];
	$auto_login		= intval($_POST['auto']);
	$used_cookie	= false;

}

if (isset($this_login, $this_password)) {
	if (($this_login == ADMIN_USERNAME) && (stripslashes($this_password) == stripslashes(ADMIN_PASSWORD))) {
		$_SESSION['login']		= $this_login;
		$_SESSION['valid_user'] = true;
		$_SESSION['course_id']  = -1;
		header('Location: admin/index.php');
		exit;
	}

	if ($_GET['course'] != '') {
		$_POST['form_course_id'] = intval($_GET['course']);
	} else {
		$_POST['form_course_id'] = intval($_POST['form_course_id']);
	}

	if ($used_cookie) {
		// check if that cookie is valid
		$sql = "SELECT member_id, login, preferences, PASSWORD(password) AS pass, language FROM ".TABLE_PREFIX."members WHERE login='$this_login' AND PASSWORD(password)='$this_password'";

	} else {
		$sql = "SELECT member_id, login, preferences, PASSWORD(password) AS pass, language FROM ".TABLE_PREFIX."members WHERE login='$this_login' AND PASSWORD(password)=PASSWORD('$this_password')";
	}

	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$_SESSION['login']		= $row['login'];
		$_SESSION['valid_user'] = true;
		$_SESSION['member_id']	= intval($row['member_id']);
		assign_session_prefs(unserialize(stripslashes($row['preferences'])));
		$_SESSION['is_guest']	= 0;
		$_SESSION['lang']		= $row['language'];

		if ($auto_login == 1) {
			$parts = parse_url($_base_href);
			// update the cookie.. increment to another 2 days
			$cookie_expire = time()+172800;
			setcookie('ATLogin', $this_login, $cookie_expire, $parts['path'], $parts['host'], 0);
			setcookie('ATPass',  $row['pass'],  $cookie_expire, $parts['path'], $parts['host'], 0);
		}
		header('Location: bounce.php?course='.$_POST['form_course_id']);
		exit;
	} else {
		$msg->addError('INVALID_LOGIN');
	}
}

if (isset($_SESSION['member_id'])) {
	$sql = "DELETE FROM ".TABLE_PREFIX."users_online WHERE member_id=$_SESSION[member_id]";
	$result = @mysql_query($sql, $db);
}

session_destroy(); 
unset($_SESSION['login']);
unset($_SESSION['valid_user']);
unset($_SESSION['member_id']);
unset($_SESSION['is_admin']);
unset($_SESSION['course_id']);
unset($_SESSION['prefs']);

/*****************************/
/* template starts down here */

$onload = 'onload="document.form.form_login.focus()"';


$savant->assign('tmpl_course_id', $_GET['course']);

if (isset($_GET['course'])) {
	$savant->assign('tmpl_title',  ' '._AT('to1').' '.$system_courses[$_GET['course']]['title']);
} else {
	$savant->assign('tmpl_title',  ' ');
}


$savant->display('login.tmpl.php');


?>