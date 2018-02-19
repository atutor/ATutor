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
 * Set up initial admin and instructor accounts as well as a few other configurations.
 * @param: A bunch of self-explanatory fields
 * @return An array of progress/error information or the same message in $msg depending on the flag $in_plain_msg.
 */
function install_step_accounts($admin_username, $admin_pwd_hidden, $admin_email, $site_name,
                               $email, $account_username, $account_pwd_hidden,
                               $account_fname, $account_lname, $account_email,
                               $just_social, $home_url, $session_path, $db_host, $db_port,
                               $db_login, $db_pwd, $db_name, $tb_prefix, $in_plain_msg = false) {
	global $addslashes, $stripslashes;
	global $errors, $progress, $msg;

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
		if ($in_plain_msg) {
			$errors[] = 'Administrator username cannot be empty.';
		} else {
			$msg->addError('EMPTY_ADMIN_USER');
		}
	} else {
		/* check for special characters */
		if (!(preg_match("/^[a-zA-Z0-9_]([a-zA-Z0-9_])*$/i", $admin_username))){
			if ($in_plain_msg) {
				$errors[] = 'Administrator username is not valid.';
			} else {
				$msg->addError('INVALID_ADMIN_USER');
			}
		}
	}
	if ($admin_pwd_hidden == '') {
		if ($in_plain_msg) {
			$errors[] = 'Administrator password cannot be empty.';
		} else {
			$msg->addError('EMPTY_ADMIN_PWD');
		}
	}
	if ($admin_email == '') {
		if ($in_plain_msg) {
			$errors[] = 'Administrator email cannot be empty.';
		} else {
			$msg->addError('EMPTY_ADMIN_EMAIL');
		}
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $admin_email)) {
		if ($in_plain_msg) {
			$errors[] = 'Administrator email is not valid.';
		} else {
			$msg->addError('INVALID_ADMIN_EMAIL');
		}
	}

	/* System Preferences checking: */
	if ($site_name == '') {
		if ($in_plain_msg) {
			$errors[] = 'Site name cannot be empty.';
		} else {
			$msg->addError('EMPTY_SITE_NAME');
		}
	}
	if ($email == '') {
		if ($in_plain_msg) {
			$errors[] = 'Contact email cannot be empty.';
		} else {
			$msg->addError('EMPTY_CONTACT_EMAIL');
		}
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $email)) {
		if ($in_plain_msg) {
			$errors[] = 'Contact email is not valid.';
		} else {
			$msg->addError('INVALID_CONTACT_EMAIL');
		}
	}

	/* Personal Account checking: */
	if ($account_username == ''){
		if ($in_plain_msg) {
			$errors[] = 'Personal Account Username cannot be empty.';
		} else {
			$msg->addError('EMPTY_PERSONAL_ACCT');
		}
	} else {
		/* check for special characters */
		if (!(preg_match("/^[a-zA-Z0-9_]([a-zA-Z0-9_])*$/i", $account_username))){
			if ($in_plain_msg) {
				$errors[] = 'Personal Account Username is not valid.';
			} else {
				$msg->addError('INVALID_PERSONAL_ACCT');
			}
		} else {
			if ($account_username == $admin_username) {
				if ($in_plain_msg) {
					$errors[] = 'That Personal Account Username is already being used for the Administrator account, choose another.';
				} else {
					$msg->addError('SAME_PERSONAL_ADMIN_ACCT');
				}
			}
		}
	}
	if ($account_pwd_hidden == '') {
		if ($in_plain_msg) {
			$errors[] = 'Personal Account Password cannot be empty.';
		} else {
			$msg->addError('EMPTY_PERSONAL_PWD');
		}
	}
	if ($account_email == '') {
		if ($in_plain_msg) {
			$errors[] = 'Personal Account email cannot be empty.';
		} else {
			$msg->addError('EMPTY_PERSONAL_EMAIL');
		}
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $account_email)) {
		if ($in_plain_msg) {
			$errors[] = 'Invalid Personal Account email is not valid.';
		} else {
			$msg->addError('INVALID_PERSONAL_EMAIL');
		}
	}

	if ($account_fname == '') {
		if ($in_plain_msg) {
			$errors[] = 'Personal Account First Name cannot be empty.';
		} else {
			$msg->addError('EMPTY_FIRST_NAME');
		}
	}
	if ($account_lname == '') {
		if ($in_plain_msg) {
			$errors[] = 'Personal Account Last Name cannot be empty.';
		} else {
			$msg->addError('EMPTY_LAST_NAME');
		}
	}

	if (isset($errors)) {
		return;
	}

	$status = 3; // for instructor account

	$sql = "INSERT INTO %sadmins (login, password, real_name, email, language, privileges, last_login)
	        VALUES ('%s', '%s', '', '%s', 'en', 1, NOW())";
	$result= queryDB($sql, array($tb_prefix, $admin_username, $admin_pwd_hidden, $admin_email));

	$sql = "INSERT INTO %smembers (member_id, login, password, email, website, first_name, ".
	       "second_name, last_name, dob, gender, address, postal, city, province, country, phone, status,".
	       "preferences, creation_date, language, inbox_notify, private_email, last_login) ".
	       "VALUES (NULL,'%s','%s','%s','','%s','','%s','0000-00-00','n', '','','','','', '',%d,'', NOW(),'en', 0, 1, NULL)";
	$result = queryDB($sql , array($tb_prefix, $account_username, $account_pwd_hidden, $account_email, $account_fname, $account_lname, $status));

	$sql = "INSERT INTO %sconfig (name, value) VALUES ('site_name', '%s')";
	$result = queryDB($sql, array($tb_prefix, $site_name));

	$sql = "INSERT INTO %sconfig (name, value) VALUES ('contact_email', '%s')";
	$result = queryDB($sql, array($tb_prefix, $email));

	$home_url = $addslashes($home_url);
	if ($home_url != '') {
		$sql = "INSERT INTO %sconfig (name, value) VALUES ('home_url', '%s')";
		$result = queryDB($sql, array($tb_prefix, $home_url));
	}

	$just_social = intval($just_social);
	if ($just_social > 0){
		$sql = "INSERT INTO %sconfig (name, value) VALUES ('just_social', '1')";
		$result = queryDB($sql, array($tb_prefix));
	}

	//if fresh install, use SET NAME to set the mysql connection to UTF8
	$sql = "INSERT INTO %sconfig (name, value) VALUES ('set_utf8', '1')";
	$result = queryDB($sql, array($tb_prefix));

	// Calculate the ATutor installation path and save into database for the usage of
	// session associated path @ include/vitals.inc.php
	$sql = "INSERT INTO %sconfig (name, value) VALUES ('session_path', '%s')";
	$result = queryDB($sql, array($tb_prefix, $session_path));
}

/**
 * Create all the content subdirectories
 */
function create_content_subdir($content_dir, $index_html_location, $in_plain_msg = false) {
	global $errors, $msg;

	if (!is_dir($content_dir.'/import')) {
		if (!@mkdir($content_dir.'/import')) {
			if ($in_plain_msg) {
				$errors[] = '<strong>'.$content_dir.'/import</strong> directory does not exist and cannot be created.';
			} else {
				$msg->addError(array('DIR_CANNOT_CREATE', $content_dir.'/import'));
			}
		}
	} else if (!is_writable($content_dir.'/import')){
		if ($in_plain_msg) {
			$errors[] = '<strong>'.$content_dir.'/import</strong> directory is not writable.';
		} else {
			$msg->addError(array('FILE_NOT_WRITABLE', $content_dir.'/import'));
		}
	}

	if (!is_dir($content_dir.'/chat')) {
		if (!@mkdir($content_dir.'/chat')) {
			if ($in_plain_msg) {
				$errors[] = '<strong>'.$content_dir.'/chat</strong> directory does not exist and cannot be created.';
			} else {
				$msg->addError(array('DIR_CANNOT_CREATE', $content_dir.'/chat'));
			}
		}
	} else if (!is_writable($content_dir.'/chat')){
		if ($in_plain_msg) {
			$errors[] = '<strong>'.$content_dir.'/chat</strong> directory is not writable.';
		} else {
			$msg->addError(array('FILE_NOT_WRITABLE', $content_dir.'/chat'));
		}
	}

	if (!is_dir($content_dir.'/backups')) {
		if (!@mkdir($content_dir.'/backups')) {
			if ($in_plain_msg) {
				$errors[] = '<strong>'.$content_dir.'/backups</strong> directory does not exist and cannot be created.';
			} else {
				$msg->addError(array('DIR_CANNOT_CREATE', $content_dir.'/backups'));
			}
		}
	} else if (!is_writable($content_dir.'/backups')){
		if ($in_plain_msg) {
			$errors[] = '<strong>'.$content_dir.'/backups</strong> directory is not writable.';
		} else {
			$msg->addError(array('FILE_NOT_WRITABLE', $content_dir.'/backups'));
		}
	}
	if (!is_dir($content_dir.'/feeds')) {
		if (!@mkdir($content_dir.'/feeds')) {
			if ($in_plain_msg) {
				$errors[] = '<strong>'.$content_dir.'/feeds</strong> directory does not exist and cannot be created.';
			} else {
				$msg->addError(array('DIR_CANNOT_CREATE', $content_dir.'/feeds'));
			}
		}
	} else if (!is_writable($content_dir.'/feeds')){
		if ($in_plain_msg) {
			$errors[] = '<strong>'.$content_dir.'/feeds</strong> directory is not writable.';
		} else {
			$msg->addError(array('FILE_NOT_WRITABLE', $content_dir.'/feeds'));
		}
	}

	if (!is_dir($content_dir.'/file_storage')) {
		if (!@mkdir($content_dir.'/file_storage')) {
			if ($in_plain_msg) {
				$errors[] = '<strong>'.$content_dir.'/file_storage</strong> directory does not exist and cannot be created.';
			} else {
				$msg->addError(array('DIR_CANNOT_CREATE', $content_dir.'/file_storage'));
			}
		}
	} else if (!is_writable($content_dir.'/file_storage')){
		if ($in_plain_msg) {
			$errors[] = '<strong>'.$content_dir.'/file_storage</strong> directory is not writable.';
		} else {
			$msg->addError(array('FILE_NOT_WRITABLE', $content_dir.'/file_storage'));
		}
	}

	if (!is_dir($content_dir.'/profile_pictures')) {
		if (!@mkdir($content_dir.'/profile_pictures')) {
			if ($in_plain_msg) {
				$errors[] = '<strong>'.$content_dir.'/profile_pictures</strong> directory does not exist and cannot be created.';
			} else {
				$msg->addError(array('DIR_CANNOT_CREATE', $content_dir.'/profile_pictures'));
			}
		} else {
			mkdir($content_dir.'/profile_pictures/originals');
			mkdir($content_dir.'/profile_pictures/thumbs');
			mkdir($content_dir.'/profile_pictures/profile');
		}
	} else if (!is_writable($content_dir.'/profile_pictures')){
		if ($in_plain_msg) {
			$errors[] = '<strong>'.$content_dir.'/profile_pictures</strong> directory is not writable.';
		} else {
			$msg->addError(array('FILE_NOT_WRITABLE', $content_dir.'/profile_pictures'));
		}
	}
	if (!is_dir($content_dir.'/patcher')) {
		if (!@mkdir($content_dir.'/patcher')) {
			if ($in_plain_msg) {
				$errors[] = '<strong>'.$content_dir.'/patcher</strong> directory does not exist and cannot be created.';
			} else {
				$msg->addError(array('DIR_CANNOT_CREATE', $content_dir.'/patcher'));
			}
		}
	} else if (!is_writable($content_dir.'/patcher')){
		if ($in_plain_msg) {
			$errors[] = '<strong>'.$content_dir.'/patcher</strong> directory is not writable.';
		} else {
			$msg->addError(array('FILE_NOT_WRITABLE', $content_dir.'/patcher'));
		}
	}
	if (!is_dir($content_dir.'/social')) {
		if (!@mkdir($content_dir.'/social')) {
			if ($in_plain_msg) {
				$errors[] = '<strong>'.$content_dir.'/social</strong> directory does not exist and cannot be created.';
			} else {
				$msg->addError(array('DIR_CANNOT_CREATE', $content_dir.'/social'));
			}
		}
	} else if (!is_writable($content_dir.'/social')){
		if ($in_plain_msg) {
			$errors[] = '<strong>'.$content_dir.'/social</strong> directory is not writable.';
		} else {
			$msg->addError(array('FILE_NOT_WRITABLE', $content_dir.'/social'));
		}
	}
	if (!is_dir($content_dir.'/photos')) {
		if (!@mkdir($content_dir.'/photos')) {
			if ($in_plain_msg) {
				$errors[] = '<strong>'.$content_dir.'/photos</strong> directory does not exist and cannot be created.';
			} else {
				$msg->addError(array('DIR_CANNOT_CREATE', $content_dir.'/photos'));
			}
		}
	} else if (!is_writable($content_dir.'/photos')){
		if ($in_plain_msg) {
			$errors[] = '<strong>'.$content_dir.'/photos</strong> directory is not writable.';
		} else {
			$msg->addError(array('FILE_NOT_WRITABLE', $content_dir.'/photos'));
		}
	}
	if (!is_dir($content_dir.'/module')) {
		if (!@mkdir($content_dir.'/module')) {
			if ($in_plain_msg) {
				$errors[] = '<strong>'.$content_dir.'/module</strong> directory does not exist and cannot be created.';
			} else {
				$msg->addError(array('DIR_CANNOT_CREATE', $content_dir.'/module'));
			}
		}
	} else if (!is_writable($content_dir.'/module')){
		$errors[] = '<strong>'.$content_dir.'/module</strong> directory is not writable.';
	}
	if (!is_dir($content_dir.'/theme')) {
		if (!@mkdir($content_dir.'/theme')) {
			if ($in_plain_msg) {
				$errors[] = '<strong>'.$content_dir.'/theme</strong> directory does not exist and cannot be created.';
			} else {
				$msg->addError(array('DIR_CANNOT_CREATE', $content_dir.'/theme'));
			}
		}
	} else if (!is_writable($content_dir.'/theme')){
		if ($in_plain_msg) {
			$errors[] = '<strong>'.$content_dir.'/theme</strong> directory is not writable.';
		} else {
			$msg->addError(array('FILE_NOT_WRITABLE', $content_dir.'/theme'));
		}
	}

	// save blank index.html pages to those directories
	@copy($index_html_location, $content_dir . '/import/index.html');
	@copy($index_html_location, $content_dir . '/chat/index.html');
	@copy($index_html_location, $content_dir . '/backups/index.html');
	@copy($index_html_location, $content_dir . '/feeds/index.html');
	@copy($index_html_location, $content_dir . '/file_storage/index.html');
	@copy($index_html_location, $content_dir . '/profile_pictures/index.html');
	@copy($index_html_location, $content_dir . '/index.html');
}

/**
 * Installation step to create and switch to ATutor database
 * @param: db_host     DB host
 *         db_port     DB port
 *         db_login    DB login ID. This id must have "create database" privilege.
 *         db_pwd      The password of the login id
 *         db_name     DB name to create
 *         schema_file The location of atutor_schema.sql
 *         in_plain_msg if true, save the progress msg into global arrays $errors & $progress,
 *                      otherwise, save into global Message object $msg
 * @return An array of progress/error information or the same message in $msg depending on the flag $in_plain_msg.
 */
function create_and_switch_db($db_host, $db_port, $db_login, $db_pwd, $tb_prefix, $db_name, $in_plain_msg = false) {

	global $addslashes;
	global $errors, $progress, $msg;

	//$db = at_db_connect($db_host, $db_port, $db_login, $db_pwd);
    if(defined('MYSQLI_ENABLED')){
 	    $db = at_db_connect($db_host, $db_port, $db_login, $db_pwd, '');
    }else{
	    $db = at_db_connect($db_host, $db_port, $db_login, $db_pwd, '');
	    //at_db_select($db_name, $db);
    }

	if (!$db) {
		if ($in_plain_msg) {
			$errors[] = 'Unable to connect to database server.';
		} else {
			$msg->addError('UNABLE_CONNECT_DB');
		}
	}

	$tb_prefix = $addslashes($tb_prefix);
	//$db_name = $addslashes($db_name);

	// check mysql version number
	$row = at_db_version($db);

	$row['version'] = str_replace (array('-community-nt', '-max', '-standard'), '', strtolower($row['version']));
	if (version_compare($row['version'], '4.1.10', '>=') === FALSE) {
		if ($in_plain_msg) {
			$errors[] = 'MySQL version '.$row['version'].' was detected. ATutor requires version 4.1.10 or later.';
		} else {
			$msg->addError(array('LOW_MYSQL_VERSION', $row['version']));
		}
	}
    if(isset($db)){
	    $isdb = at_is_db($db_name, $db);
    }

	if($isdb == 0){
		$sql = "CREATE DATABASE `".$db_name."` CHARACTER SET utf8 COLLATE utf8_general_ci";
		//$result = queryDB($sql, array($db_name));
        $result = at_db_create($sql, $db);
		if ($result == 0) {
			if ($in_plain_msg) {
				$errors[] = 'Unable to select or create database <b>'.$db_name.'</b>.';
			} else {
				$msg->addError(array('UNABLE_SELECT_DB', $db_name));
			}
		} else {
			if ($in_plain_msg) {
				$progress[] = 'Database <b>'.$db_name.'</b> created successfully.';
			} else {
				$msg->addFeedback(array('DB_CREATED', $db_name));
			}
			at_db_select($db_name, $db);
		}
	} else {
		/* Check if the database that existed is in UTF-8, if not, ask for retry */
		at_db_select($db_name, $db);
		$sql = "SHOW CREATE DATABASE `%s`";
		$row = queryDButf8($sql, $db_name, true, true, $db);

		if (!preg_match('/CHARACTER SET utf8/i', $row['Create Database'])){
			$sql2 = 'ALTER DATABASE `%s` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';
			$result2 = queryDButf8($sql2, array($db_name),false, true, $db);

			if ($result2 == 0){
				if ($in_plain_msg) {
					$errors[] = 'Database <b>'.$db_name.'</b> is not in UTF8.  Please set the database character set to UTF8 before continuing by using the following query: <br /> ALTER DATABASE `'.$db_name.'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci.  <br />To use ALTER DATABASE, you need the ALTER privilege on the database.  You can also check the MySQL manual <a href="http://dev.mysql.com/doc/refman/4.1/en/alter-database.html" target="mysql_window">here</a>.';
				} else {
					$msg->addFeedback(array('DB_NOT_UTF8', $db_name));
				}
			}
		}
	}
	return $db;
}
?>
