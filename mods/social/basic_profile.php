<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: profile.php 7208 2008-01-09 16:07:24Z greg $

//$_user_location	= 'users';

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/social/module.css'; // use a custom stylesheet
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

if (isset($_POST['submit'])) {
	$missing_fields = array();

	if (!$_POST['first_name']) { 
		$missing_fields[] = _AT('first_name');
	}

	if (!$_POST['last_name']) { 
		$missing_fields[] = _AT('last_name');
	}

	$_POST['first_name'] = str_replace('<', '', $_POST['first_name']);
	$_POST['second_name'] = str_replace('<', '', $_POST['second_name']);
	$_POST['last_name'] = str_replace('<', '', $_POST['last_name']);

	// check if first+last is unique
	if ($_POST['first_name'] && $_POST['last_name']) {
		$first_name_sql  = $addslashes($_POST['first_name']);
		$last_name_sql   = $addslashes($_POST['last_name']);
		$second_name_sql = $addslashes($_POST['second_name']);

		$sql = "SELECT member_id FROM ".TABLE_PREFIX."members WHERE first_name='$first_name_sql' AND second_name='$second_name_sql' AND last_name='$last_name_sql' AND member_id<>$_SESSION[member_id] LIMIT 1";
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

	if (($_POST['gender'] != 'm') && ($_POST['gender'] != 'f')) {
		$_POST['gender'] = 'n'; // not specified
	}
	
	
	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}
	$login = strtolower($_POST['login']);
	if (!$msg->containsErrors()) {			
		if (($_POST['website']) && (!ereg('://',$_POST['website']))) { $_POST['website'] = 'http://'.$_POST['website']; }
		if ($_POST['website'] == 'http://') { $_POST['website'] = ''; }

		if (isset($_POST['private_email'])) {
			$_POST['private_email'] = 1;
		} else {
			$_POST['private_email'] = 0;
		}

		// insert into the db.
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

		$sql = "UPDATE ".TABLE_PREFIX."members SET website='$_POST[website]', first_name='$_POST[first_name]', second_name='$_POST[second_name]', last_name='$_POST[last_name]', dob='$dob', gender='$_POST[gender]', address='$_POST[address]', postal='$_POST[postal]', city='$_POST[city]', province='$_POST[province]', country='$_POST[country]', phone='$_POST[phone]', language='$_SESSION[lang]', private_email=$_POST[private_email], creation_date=creation_date, last_login=last_login WHERE member_id=$_SESSION[member_id]";

		$result = mysql_query($sql,$db);
		if (!$result) {
			$msg->printErrors('DB_NOT_UPDATED');
			exit;
		}

		$msg->addFeedback('PROFILE_UPDATED');

		header('Location: basic_profile.php');
		exit;
	}
}

$sql	= 'SELECT * FROM '.TABLE_PREFIX.'members WHERE member_id='.$_SESSION['member_id'];
$result = mysql_query($sql,$db);
$row = mysql_fetch_assoc($result);

if (!isset($_POST['submit'])) {
	$_POST = $row;
	list($_POST['year'],$_POST['month'],$_POST['day']) = explode('-', $row['dob']);
}

/* template starts here */

$savant->assign('row', $row);
$onload = 'document.form.first_name.focus();';

//$savant->display('registration.tmpl.php');
$savant->display('html/basic_profile.tmpl.php');
?>