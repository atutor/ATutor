<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_core/themes/lib/themes.inc.php');
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
	    $temp_prefs = assignDefaultPrefs();
        $mnot = assignDefaultMnot();
        $auto_login = assignDefaultAutologin();
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

unset($_SESSION['first_login']);
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