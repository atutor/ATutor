<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: edit.php 3111 2005-01-18 19:32:00Z joel $

$page = 'profile';
$_user_location	= 'users';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('profile');

if ($_SESSION['valid_user'] !== true) {
	require(AT_INCLUDE_PATH.'header.inc.php');

	$info = array('INVALID_USER', $_SESSION['course_id']);
	$msg->printInfos($info);
	
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	Header('Location: profile.php');
	exit;
}

if (isset($_GET['auto']) && ($_GET['auto'] == 'disable')) {

	$parts = parse_url($_base_href);

	setcookie('ATLogin', '', time()-172800, $parts['path'], $parts['host'], 0);
	setcookie('ATPass',  '', time()-172800, $parts['path'], $parts['host'], 0);
	
	$msg->addFeedback('AUTO_DISABLED');
	Header('Location: profile.php');
	exit;
} else if (isset($_GET['auto']) && ($_GET['auto'] == 'enable')) {
	$parts = parse_url($_base_href);

	$sql	= "SELECT PASSWORD(password) AS pass FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_array($result);

	setcookie('ATLogin', $_SESSION['login'], time()+172800, $parts['path'], $parts['host'], 0);
	setcookie('ATPass',  $row['pass'], time()+172800, $parts['path'], $parts['host'], 0);

	$msg->addFeedback('AUTO_ENABLED');
	header('Location: profile.php');
	exit;
}

if ($_POST['submit']) {
	$error = '';

	// email check
	if ($_POST['email'] == '') {
		$msg->addError('EMAIL_MISSING');
	} else {
		if(!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$", $_POST['email'])) {
			$msg->addError('EMAIL_INVALID');
		}
		$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE email='$_POST[email]' AND member_id<>$_SESSION[member_id]",$db);
		if(mysql_num_rows($result) != 0) {
			$msg->addError('EMAIL_EXISTS');
		}
	}

	// password check
	if ($_POST['password'] == '') { 
		$msg->addError('PASSWORD_MISSING');
	}
	// check for valid passwords
	if ($_POST['password'] != $_POST['password2']) {
		$msg->addError('PASSWORD_MISMATCH');
	}
		
	//check date of birth
	$mo = intval($_POST['month']);
	$day = intval($_POST['day']);
	$yr = intval($_POST['year']);

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
		
	$login = strtolower($_POST['login']);
	if (!$msg->containsErrors()) {			
		if (($_POST['web_site']) && (!ereg('://',$_POST['web_site']))) { $_POST['web_site'] = 'http://'.$_POST['web_site']; }
		if ($_POST['web_site'] == 'http://') { $_POST['web_site'] = ''; }

		// insert into the db.
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

		$sql = "UPDATE ".TABLE_PREFIX."members SET password='$_POST[password]', email='$_POST[email]', website='$_POST[website]', first_name='$_POST[first_name]', last_name='$_POST[last_name]', dob='$dob', gender='$_POST[gender]', address='$_POST[address]', postal='$_POST[postal]', city='$_POST[city]', province='$_POST[province]', country='$_POST[country]', phone='$_POST[phone]', language='$_SESSION[lang]' WHERE member_id=$_SESSION[member_id]";

		$result = mysql_query($sql,$db);
		if (!$result) {
			$msg->printErrors('DB_NOT_UPDATED');
			exit;
		}

		$msg->addFeedback('PROFILE_UPDATED');
		header('Location: ./profile.php');
		exit;
	}
}


/* verify that this user owns this profile */
$sql	= "SELECT status FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
$result = mysql_query($sql, $db);

if (!($row = mysql_fetch_array($result))) {
	$msg->printErrors('CREATE_NOPERM');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}


$sql	= 'SELECT * FROM '.TABLE_PREFIX.'members WHERE member_id='.$_SESSION['member_id'];
$result = mysql_query($sql,$db);
$row = mysql_fetch_array($result);

if ($_POST['submit']){
	$row['password']	= $_POST['password'];
	$row['email']		= $_POST['email'];
	$row['first_name']	= $_POST['first_name'];
	$row['last_name']	= $_POST['last_name'];
	$row['dob']			= $dob;
	$row['address']		= $_POST['address'];
	$row['postal']		= $_POST['postal'];
	$row['city']		= $_POST['city'];
	$row['province']	= $_POST['province'];
	$row['country']		= $_POST['country'];
	$row['phone']		= $_POST['phone'];
	$row['website']		= $_POST['website'];
}

/* template starts here */

$savant->assign('row', $row);

$months = array(	_AT('date_january'), 
					_AT('date_february'), 
					_AT('date_march'), 
					_AT('date_april'), 
					_AT('date_may'),
					_AT('date_june'), 
					_AT('date_july'), 
					_AT('date_august'), 
					_AT('date_september'), 
					_AT('date_october'), 
					_AT('date_november'),
					_AT('date_december'));

$savant->assign('months', $months);

$savant->display('users/profile.tmpl.php');

?>