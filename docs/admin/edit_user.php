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
// $Id: create_course.php 3660 2005-03-02 20:37:03Z joel $

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

if (isset($_POST['cancel'])) {
	header('Location: ./users.php');
	exit;
}

if (isset($_POST['submit'])) {
	$member_id = intval($_POST['member_id']);

	/* email check */
	if ($_POST['email'] == '') {
		$msg->addError('EMAIL_MISSING');
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$", $_POST['email'])) {
		$msg->addError('EMAIL_INVALID');
	}
	$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE email LIKE '$_POST[email]' AND member_id <> $member_id",$db);

	if (mysql_num_rows($result) != 0) {
		$valid = 'no';
		$msg->addError('EMAIL_EXISTS');
	}

	/* login name check */
	if ($_POST['login'] == '') {
		$msg->addError('LOGIN_NAME_MISSING');
	} else {
		/* check for special characters */
		if (!(eregi("^[a-zA-Z0-9_]([a-zA-Z0-9_])*$", $_POST['login']))) {
			$msg->addError('LOGIN_CHARS');
		} else {
			$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE login='$_POST[login]' AND member_id <> $member_id",$db);
			if (mysql_num_rows($result) != 0) {
				$valid = 'no';
				$msg->addError('LOGIN_EXISTS');
			} else if ($_POST['login'] == ADMIN_USERNAME) {
				$valid = 'no';			
				$msg->addError('LOGIN_EXISTS');
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
		if (($_POST['website']) && (!ereg("://",$_POST['website']))) { 
			$_POST['website'] = "http://".$_POST['website']; 
		}
		if ($_POST['website'] == 'http://') { 
			$_POST['website'] = ''; 
		}
		$_POST['postal'] = strtoupper(trim($_POST['postal']));
		//figure out which defualt theme to apply, accessibility or ATutor default
		if($_POST['pref'] == 'access'){
			$sql = "SELECT * FROM ".TABLE_PREFIX."theme_settings where theme_id = '1'";
		}else{
			$sql = "SELECT * FROM ".TABLE_PREFIX."theme_settings where theme_id = '4'";
		}
		$result = mysql_query($sql, $db); 	
		while($row = mysql_fetch_array($result)){
			$start_prefs = $row['preferences'];
		}

		$_POST['password'] = $addslashes($_POST['password']);
		$_POST['website'] = $addslashes($_POST['website']);
		$_POST['first_name'] = $addslashes($_POST['first_name']);
		$_POST['last_name'] = $addslashes($_POST['last_name']);
		$_POST['address'] = $addslashes($_POST['address']);
		$_POST['postal'] = $addslashes($_POST['postal']);
		$_POST['city'] = $addslashes($_POST['city']);
		$_POST['province'] = $addslashes($_POST['province']);
		$_POST['country'] = $addslashes($_POST['country']);
		$_POST['phone'] = $addslashes($_POST['phone']);
		$_POST['status'] = intval($_POST['status']);

		/* insert into the db. (the last 0 for status) */
		$sql = "UPDATE ".TABLE_PREFIX."members SET	login    = '$_POST[login]',
													password = '$_POST[password]',
													email    = '$_POST[email]',
													website   = '$_POST[website]',
													first_name = '$_POST[first_name]',
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
													preferences = '$start_prefs',
													language = '$_SESSION[lang]'
				WHERE member_id = $member_id";
		$result = mysql_query($sql, $db);
		if (!$result) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$msg->addError('DB_NOT_UPDATED');
			$msg->printAll();
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}

		if ($_POST['pref'] == 'access') {
			$_SESSION['member_id'] = $member_id;
			save_prefs();
			unset($_SESSION['member_id']);
		}
	
		$msg->addFeedback('PROFILE_UPDATED_ADMIN');
		header('Location: ./users.php');
		exit;
	}
}

$id = intval($_REQUEST['id']);

if (empty($_POST)) {
	$sql    = "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id = $id";
	$result = mysql_query($sql, $db);
	if (!($row = mysql_fetch_assoc($result))) {
		
		$msg->addError('USER_NOT_FOUND');
		require(AT_INCLUDE_PATH.'header.inc.php'); 

		require(AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
	}

	$_POST  = $row;
	$_POST['password2'] = $_POST['password'];
}

$onload = 'onload="document.form.login.focus();"';

$savant->assign('languageManager', $languageManager);

/* HAVE TO SEND MEMBER_ID THROUGH FORM AS A HIDDEN POST VARIABLE!!! */
/* PUT IN IF LOOP THAT LETS YOU SEE STATUS DROP_DOWN */
$savant->display('registration.tmpl.php');

?>