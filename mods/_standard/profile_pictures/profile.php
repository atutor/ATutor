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

$_user_location	= 'users';

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

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
		if (($_POST['website']) && (!strstr($_POST['website'], '://'))) { $_POST['website'] = 'http://'.$_POST['website']; }
		if ($_POST['website'] == 'http://') { $_POST['website'] = ''; }

		if (isset($_POST['private_email'])) {
			$_POST['private_email'] = 1;
		} else {
			$_POST['private_email'] = 0;
		}
		
		// insert into the db.
		$sql = "UPDATE %smembers 
		        SET website='%s', 
		            first_name='%s', 
		            second_name='%s', 
		            last_name='%s', 
		            dob='%s', 
		            gender='%s', 
		            address='%s', 
		            postal='%s', 
		            city='%s', 
		            province='%s', 
		            country='%s', 
		            phone='%s', 
		            language='%s', 
		            private_email=%d, 
		            creation_date=creation_date, 
		            last_login=last_login 
		            WHERE member_id=%d";
		$result = queryDB($sql,array(TABLE_PREFIX, 
		                    $_POST['website'],
		                    $_POST['first_name'],
		                    $_POST['second_name'],
		                    $_POST['last_name'],
		                    $dob,
		                    $_POST['gender'],
		                    $_POST['address'],
		                    $_POST['postal'],
		                    $_POST['city'],
		                    $_POST['province'],
		                    $_POST['country'],
		                    $_POST['phone'],
		                    $_SESSION['lang'],
		                    $_POST['private_email'],
		                    $_SESSION['member_id']));
		if($result == 0){
		if (!$result) {
			$msg->printErrors('DB_NOT_UPDATED');
			exit;
		}

		$msg->addFeedback('PROFILE_UPDATED');

		header('Location: ./profile.php');
		exit;
	}
}

$sql	= 'SELECT * FROM %smembers WHERE member_id=%d';
$row = queryDB($sql,array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);

if (!isset($_POST['submit'])) {
	$_POST = $row;
	list($_POST['year'],$_POST['month'],$_POST['day']) = explode('-', $row['dob']);
}

/* template starts here */

$savant->assign('row', $row);
$onload = 'document.form.first_name.focus();';

$savant->display('users/profile.tmpl.php');
?>