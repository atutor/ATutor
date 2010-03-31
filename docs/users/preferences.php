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

$is_auto_login = checkAutoLoginCookie();

if (isset($_POST['submit']) || isset($_POST['set_default'])) {
	if (isset($_POST['submit']))
	{
	    //copy posted variables to a temporary array
		$temp_prefs = assignPostVars();
    
		//email notification and auto-login settings are handled
		//separately from other preferences
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

	//save most preferences to session and db
	assign_session_prefs($temp_prefs);
	save_prefs();

	//update email notification and auto-login settings separately
    save_email_notification($mnot);
	if (isset($auto_login)) {
        $is_auto_login = setAutoLoginCookie($auto_login);
    }

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
}

$sql	= "SELECT inbox_notify FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
$result = mysql_query($sql, $db);
$row_notify = mysql_fetch_assoc($result);

$languages = $languageManager->getAvailableLanguages();

/* page contents starts here */
$savant->assign('notify', $row_notify['inbox_notify']);
$savant->assign('languages', $languages);

//problem here - if auto login is enabled, but we don't check that there is a cookie, how do we know if it is enabled?

$savant->assign('is_auto_login', $is_auto_login);

$savant->display('users/preferences.tmpl.php');

?>