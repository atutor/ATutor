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
// $Id$

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/themes.inc.php');
require_once(AT_INCLUDE_PATH.'../mods/_core/users/lib/pref_tab_functions.inc.php');
/* whether or not, any settings are being changed when this page loads. */
/* ie. is ANY action being performed right now?							*/

$action = false;

if (!$_SESSION['valid_user']) {				
	/* we're not logged in */
	$msg->addFeedback('PREFS_LOGIN');
}

if (isset($_POST['submit']) || isset($_POST['set_default'])) {
	if (isset($_POST['submit']))
	{
		$temp_prefs = assignPostVars();
		$mnot = intval($_POST['mnot']);
		if (isset($_POST['auto'])) $auto_login = $_POST['auto'];
	}
	else if (isset($_POST['set_default']))
	{
		$sql	= "SELECT value FROM ".TABLE_PREFIX."config WHERE name='pref_defaults'";
		$result = mysql_query($sql, $db);
		
		if (mysql_num_rows($result) > 0)
		{
			$row_defaults = mysql_fetch_assoc($result);
			$default = $row_defaults["value"];
			
			$temp_prefs = unserialize($default);
			
			// Many new preferences are introduced in 1.6.2 that are missing in old admin 
			// default preference string. Solve this case by completing settings on new
			// preferences with $_config_defaults
			foreach (unserialize($_config_defaults['pref_defaults']) as $name => $value)
				if (!isset($temp_prefs[$name])) $temp_prefs[$name] = $value;
		}
		else
			$temp_prefs = unserialize($_config_defaults['pref_defaults']);

		$sql	= "SELECT value FROM ".TABLE_PREFIX."config WHERE name='pref_inbox_notify'";
		$result = mysql_query($sql, $db);
		if (mysql_num_rows($result) > 0)
		{
			$row_notify = mysql_fetch_assoc($result);
			$mnot = $row_notify["value"];
		}
		else
			$mnot = $_config_defaults['pref_inbox_notify'];
		
		$sql	= "SELECT value FROM ".TABLE_PREFIX."config WHERE name='pref_is_auto_login'";
		$result = mysql_query($sql, $db);
		if (mysql_num_rows($result) > 0)
		{
			$row_is_auto_login = mysql_fetch_assoc($result);
			$auto_login = $row_is_auto_login["value"];
		}
		else
			$auto_login = $_config_defaults['pref_is_auto_login'];
		
		unset($_POST);
	}

	/* we do this instead of assigning to the $_SESSION directly, b/c	*/
	/* assign_session_prefs functionality might change slightly.		*/
	assign_session_prefs($temp_prefs);

	/* save as pref for ALL courses */
	save_prefs();

	//update auto-login settings
	if (isset($auto_login) && ($auto_login == 'disable')) {
		$parts = parse_url(AT_BASE_HREF);
		$is_cookie_login_set = setcookie('ATLogin', '', time()-172800, $parts['path']);
		$is_cookie_paa_set = setcookie('ATPass',  '', time()-172800, $parts['path']);

		// The usage of flag $is_auto_login is because the set cookies are only accessible at the next page reload
		if ($is_cookie_login_set && $is_cookie_pass_set) $is_auto_login = 'enable';
		else $is_auto_login = 'disable';
	} else if (isset($auto_login) && ($auto_login == 'enable')) {
		$parts = parse_url(AT_BASE_HREF);
		$sql	= "SELECT password FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
		$result = mysql_query($sql, $db);
		$row	= mysql_fetch_assoc($result);
		$password = $row["password"];
		$is_cookie_login_set = setcookie('ATLogin', $_SESSION['login'], time()+172800, $parts['path']);
		$is_cookie_pass_set = setcookie('ATPass',  $password, time()+172800, $parts['path']);
		
		if ($is_cookie_login_set && $is_cookie_pass_set) $is_auto_login = 'enable';
		else $is_auto_login = 'disable';
	}

	/* also update message notification pref */
	$sql = "UPDATE ".TABLE_PREFIX."members SET inbox_notify =". $mnot .", creation_date=creation_date, last_login=last_login WHERE member_id = $_SESSION[member_id]";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
}

$sql	= "SELECT inbox_notify FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
$result = mysql_query($sql, $db);
$row_notify = mysql_fetch_assoc($result);

$languages = $languageManager->getAvailableLanguages();

/* page contents starts here */
$savant->assign('notify', $row_notify['inbox_notify']);
$savant->assign('languages', $languages);
$savant->assign('is_auto_login', $is_auto_login);

$savant->display('users/preferences.tmpl.php');

?>