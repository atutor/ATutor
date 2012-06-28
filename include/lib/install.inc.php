<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

/**
 * Installation step to create and set up ATutor database
 * @param: db_host     DB host
 *         db_port     DB port
 *         db_login    DB login ID. This id must have "create database" privilege.
 *         db_pwd      The password of the login id
 *         db_name     DB name to create
 *         schema_file The location of atutor_schema.sql
 * @return An array of progress or error information.
 * @see function construct_rtn()
 */
function install_step_db($db_host, $db_port, $db_login, $db_pwd, $tb_prefix, $db_name) {
	global $progress, $errors, $db;
	
	$db = @mysql_connect($db_host . ':' . $db_port, $db_login, $db_pwd);
	
	if (!$db) {
		$errors[] = 'Unable to connect to database server.';
		return construct_rtn(null, $errors);
	} 
	
	// check mysql version number
	$sql = "SELECT VERSION() AS version";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	$row['version'] = str_replace (array('-community-nt', '-max', '-standard'), '', strtolower($row['version']));
	if (version_compare($row['version'], '4.1.10', '>=') === FALSE) {
		$errors[] = 'MySQL version '.$row['version'].' was detected. ATutor requires version 4.1.10 or later.';
		return construct_rtn(null, $errors);
	}

	if (!mysql_select_db($db_name, $db)) {
		$sql = "CREATE DATABASE `$db_name` CHARACTER SET utf8 COLLATE utf8_general_ci";
		$result = mysql_query($sql, $db);
		if (!$result) {
			$errors[] = 'Unable to select or create database <b>'.$db_name.'</b>.';
			return construct_rtn(null, $errors);
		} else {
			$progress[] = 'Database <b>'.$db_name.'</b> created successfully.';
			mysql_select_db($db_name, $db);
		}
	} else {
		/* Check if the database that existed is in UTF-8, if not, ask for retry */
		$sql = "SHOW CREATE DATABASE `$db_name`";
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);
		
		if (!preg_match('/CHARACTER SET utf8/i', $row['Create Database'])){
			$sql2 = 'ALTER DATABASE `'.$db_name.'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';
			$result2 = mysql_query($sql2);
			if (!$result2){
				$errors[] = 'Database <b>'.$db_name.'</b> is not in UTF8.  Please set the database character set to UTF8 before continuing by using the following query: <br /> ALTER DATABASE `'.$db_name.'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci.  <br />To use ALTER DATABASE, you need the ALTER privilege on the database.  You can also check the MySQL manual <a href="http://dev.mysql.com/doc/refman/4.1/en/alter-database.html" target="mysql_window">here</a>.';
				return construct_rtn(null, $errors);
			}
		}
	}

	$progress[] = 'Connected to database <b>'.$db_name.'</b> successfully.';
	$errors = array();
	
	$sqlUtility = new SqlUtility();
	$sqlUtility->queryFromFile(AT_INCLUDE_PATH . 'install/db/atutor_schema.sql', $tb_prefix);
	$sqlUtility->queryFromFile(AT_INCLUDE_PATH . 'install/db/atutor_language_text.sql', $tb_prefix);

	return construct_rtn($progress, $errors);
}

/**
 * Set up initial admin and instructor accounts as well as a few other configurations.
 * @param: A bunch of self-explanatory fields
 * @return An array of progress or error information.
 * @see function construct_rtn()
 */
function install_step_accounts($admin_username, $admin_pwd_hidden, $admin_email, $site_name,
                               $email, $account_username, $account_pwd_hidden,
                               $account_fname, $account_lname, $account_email,
                               $just_social, $home_url, $db_host, $db_port, 
                               $db_login, $db_pwd, $db_name, $tb_prefix) {
	global $addslashes, $stripslashes, $db;
	
	$admin_username = trim($admin_username);
	$admin_email    = trim($admin_email);
	$site_name      = trim($site_name);
	$home_url       = trim($home_url);
	$email          = trim($email);
	$account_email  = trim($account_email);
	$account_fname  = trim($account_fname);
	$account_lname  = trim($account_lname);

	/* Super Administrator Account checking: */
	if ($admin_username == ''){
		$errors[] = 'Administrator username cannot be empty.';
	} else {
		/* check for special characters */
		if (!(preg_match("/^[a-zA-Z0-9_]([a-zA-Z0-9_])*$/i", $admin_username))){
			$errors[] = 'Administrator username is not valid.';
		}
	}
	if ($admin_pwd_hidden == '') {
		$errors[] = 'Administrator password cannot be empty.';
	}
	if ($admin_email == '') {
		$errors[] = 'Administrator email cannot be empty.';
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $admin_email)) {
		$errors[] = 'Administrator email is not valid.';
	}

	/* System Preferences checking: */
	if ($site_name == '') {
		$errors[] = 'Site name cannot be empty.';
	}
	if ($email == '') {
		$errors[] = 'Contact email cannot be empty.';
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $email)) {
		$errors[] = 'Contact email is not valid.';
	}

	/* Personal Account checking: */
	if ($account_username == ''){
		$errors[] = 'Personal Account Username cannot be empty.';
	} else {
		/* check for special characters */
		if (!(preg_match("/^[a-zA-Z0-9_]([a-zA-Z0-9_])*$/i", $account_username))){
			$errors[] = 'Personal Account Username is not valid.';
		} else {
			if ($account_username == $admin_username) {
				$errors[] = 'That Personal Account Username is already being used for the Administrator account, choose another.';
			}
		}
	}
	if ($account_pwd_hidden == '') {
		$errors[] = 'Personal Account Password cannot be empty.';
	}
	if ($account_email == '') {
		$errors[] = 'Personal Account email cannot be empty.';
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $account_email)) {
		$errors[] = 'Invalid Personal Account email is not valid.';
	}

	if ($account_fname == '') {
		$errors[] = 'Personal Account First Name cannot be empty.';
	}
	if ($account_lname == '') {
		$errors[] = 'Personal Account Last Name cannot be empty.';
	}
	
	if (isset($errors)) {
		return construct_rtn(null, $errors);
	}
	
	$db = @mysql_connect($db_host . ':' . $db_port, $db_login, urldecode($db_pwd));
	@mysql_select_db($db_name, $db);

	$account_email = $addslashes($account_email);
	$account_fname = $addslashes($account_fname);
	$account_lname = $addslashes($account_lname);

	$status = 3; // for instructor account

	$sql = "INSERT INTO ".$tb_prefix."admins (login, password, real_name, email, language, privileges, last_login) ".
	       "VALUES ('$admin_username', '$admin_pwd_hidden', '', '$admin_email', 'en', 1, NOW())";
	$result= mysql_query($sql, $db);

	$sql = "INSERT INTO ".$tb_prefix."members (member_id, login, password, email, website, first_name, ".
	       "second_name, last_name, dob, gender, address, postal, city, province, country, phone, status,".
	       "preferences, creation_date, language, inbox_notify, private_email, last_login) ". 
	       "VALUES (NULL,'$account_username','$account_pwd_hidden','$account_email','','$account_fname','','$account_lname','0000-00-00','n', '','','','','', '',$status,'', NOW(),'en', 0, 1, '0000-00-00 00:00:00')";
	$result = mysql_query($sql ,$db);

	$site_name = $addslashes($site_name);
	$sql = "INSERT INTO ".$tb_prefix."config (name, value) VALUES ('site_name', '$site_name')";
	$result = mysql_query($sql ,$db);

	$email = $addslashes($email);
	$sql = "INSERT INTO ".$tb_prefix."config (name, value) VALUES ('contact_email', '$email')";
	$result = mysql_query($sql ,$db);

	$home_url = $addslashes($home_url);
	if ($home_url != '') {
		$sql = "INSERT INTO ".$tb_prefix."config (name, value) VALUES ('home_url', '$home_url')";
		$result = mysql_query($sql ,$db);
	}

	$just_social = intval($just_social);
	if ($just_social > 0){
		$sql = "INSERT INTO ".$tb_prefix."config (name, value) VALUES ('just_social', '1')";
		$result = mysql_query($sql ,$db);
	}

	//if fresh install, use SET NAME to set the mysql connection to UTF8
	$sql = "INSERT INTO ".$tb_prefix."config (name, value) VALUES ('set_utf8', '1')";
	mysql_query($sql ,$db);

	// Calculate the ATutor installation path and save into database for the usage of
	// session associated path @ include/vitals.inc.php
	$sql = "INSERT INTO ".$tb_prefix."config (name, value) VALUES ('session_path', '".get_atutor_installation_path(AT_INSTALLER_INCLUDE_PATH)."')";
	mysql_query($sql ,$db);
}

/**
 * Construct the return value from the installation step functions
 * @param: progress   The array of the progress information. Null if error happens and no progress
 *         errors     The array of the progress information. Leave blank if no errors
 * @return: An array of progress and/or error information
 * Array(
 *     "progress" => Array,
 *     "errors" => Array
 * )
 */
function construct_rtn($progress, $errors = null) {
	$rtn = Array();
	
	if (is_array($progress) && count($progress) > 0) {
		$rtn["progress"] = $progress;
	}
	if (is_array($errors) && count($errors) > 0) {
		$rtn["errors"] = $errors;
	}
	
	return $rtn;
}

?>
