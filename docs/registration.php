<?php
/****************************************************************/
/* ATutor                                                       */
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$page	 = 'register';
$_user_location	= 'public';

	define('AT_INCLUDE_PATH', 'include/');
	require (AT_INCLUDE_PATH.'vitals.inc.php');
	if (isset($_POST['cancel'])) {
		Header('Location: ./about.php');
		exit;
	}

	if (isset($_POST['submit'])) {
		/* email check */
		if ($_POST['email'] == '') {
			$errors[] = AT_ERROR_EMAIL_MISSING;
		} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$", $_POST['email'])) {
			$errors[] = AT_ERROR_EMAIL_INVALID;
		}
		$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE email LIKE '$_POST[email]'",$db);
		if (mysql_num_rows($result) != 0) {
			$valid = 'no';
			$errors[] = AT_ERROR_EMAIL_EXISTS;
		}

		/* login name check */
		if ($_POST['login'] == ''){
			$errors[] = AT_ERROR_LOGIN_NAME_MISSING;
		} else {
			/* check for special characters */
			if (!(eregi("^[a-zA-Z0-9_]([a-zA-Z0-9_])*$", $_POST['login']))){
				$errors[] = AT_ERROR_LOGIN_CHARS;
			} else {
				$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE login='$_POST[login]'",$db);
				if (mysql_num_rows($result) != 0) {
					$valid = 'no';
					$errors[] = AT_ERROR_LOGIN_EXISTS;
				} else if ($_POST['login'] == ADMIN_USERNAME) {
					$valid = 'no';			
					$errors[] = AT_ERROR_LOGIN_EXISTS;
				}
			}
		}

		/* password check:	*/
		if ($_POST['password'] == '') { 
			$errors[] = AT_ERROR_PASSWORD_MISSING;
		} else {
			// check for valid passwords
			if ($_POST['password'] != $_POST['password2']){
				$valid= 'no';
				$errors[] = AT_ERROR_PASSWORD_MISMATCH;
			}
		}
		
		$_POST['login'] = strtolower($_POST['login']);

		if (!$errors) {
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

			/* insert into the db. (the last 0 for status) */
			$sql = "INSERT INTO ".TABLE_PREFIX."members VALUES (0,'$_POST[login]','$_POST[password]','$_POST[email]','$_POST[website]','$_POST[first_name]','$_POST[last_name]', '$_POST[age]', '$_POST[gender]', '$_POST[address]','$_POST[postal]','$_POST[city]','$_POST[province]','$_POST[country]', '$_POST[phone]',0,'$start_prefs', NOW(),'$_SESSION[lang]')";
			$result = mysql_query($sql, $db);
			$m_id	= mysql_insert_id($db);
			if (!$result) {
				require(AT_INCLUDE_PATH.'header.inc.php');
				$error[] = AT_ERROR_DB_NOT_UPDATED;
				print_errors($errors);
				require(AT_INCLUDE_PATH.'footer.inc.php');
				exit;
			}

			if ($_POST['pref'] == 'access') {
				$_SESSION['member_id'] = $m_id;
				save_prefs();
			}

			$feedback[]=AT_FEEDBACK_REG_THANKS;
			require(AT_INCLUDE_PATH.'header.inc.php');
			print_feedback($feedback);
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}
}

unset($_SESSION['member_id']);
unset($_SESSION['valid_user']);
unset($_SESSION['login']);
unset($_SESSION['is_admin']);
unset($_SESSION['course_id']);
unset($_SESSION['is_guest']);

/*****************************/
/* template starts down here */

$onload = 'onload="document.form.login.focus();"';

$savant->assign('tmpl_available_languages', $available_languages);

$savant->display('registration.tmpl.php');

?>