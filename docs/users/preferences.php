<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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

/* whether or not, any settings are being changed when this page loads. */
/* ie. is ANY action being performed right now?							*/
$action = false;

if (!$_SESSION['valid_user']) {				
	/* we're not logged in */
	$msg->addFeedback('PREFS_LOGIN');
}

if (isset($_GET['submit'])) {
	/* custom prefs */

	$temp_prefs['PREF_NUMBERING']	   = intval($_GET['numbering']);
	$temp_prefs['PREF_THEME']	       = $addslashes($_GET['theme']);
	$temp_prefs['PREF_JUMP_REDIRECT']  = intval($_GET['use_jump_redirect']);
	$temp_prefs['PREF_FORM_FOCUS']     = intval($_GET['form_focus']);
	$temp_prefs['PREF_CONTENT_EDITOR'] = intval($_GET['content_editor']);

	/* we do this instead of assigning to the $_SESSION directly, b/c	*/
	/* assign_session_prefs functionality might change slightly.		*/
	assign_session_prefs($temp_prefs);

	/* save as pref for ALL courses */
	save_prefs();

	//update auto-login settings
	if (isset($_GET['auto']) && ($_GET['auto'] == 'disable')) {
		$parts = parse_url($_base_href);
		setcookie('ATLogin', '', time()-172800, $parts['path'], $parts['host'], 0);
		setcookie('ATPass',  '', time()-172800, $parts['path'], $parts['host'], 0);		
	} else if (isset($_GET['auto']) && ($_GET['auto'] == 'enable')) {
		$parts = parse_url($_base_href);
		$sql	= "SELECT password FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
		$result = mysql_query($sql, $db);
		$row	= mysql_fetch_assoc($result);
		$password = sha1($row['password'] . '-' . DB_PASSWORD);
		setcookie('ATLogin', $_SESSION['login'], time()+172800, $parts['path'], $parts['host'], 0);
		setcookie('ATPass',  $password, time()+172800, $parts['path'], $parts['host'], 0);
	}

	/* also update message notification pref */
	$_GET['mnot'] = intval($_GET['mnot']);
	$sql = "UPDATE ".TABLE_PREFIX."members SET inbox_notify = $_GET[mnot], creation_date=creation_date, last_login=last_login WHERE member_id = $_SESSION[member_id]";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.$_base_href.'users/preferences.php');
	exit;
}

$sql	= "SELECT inbox_notify FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
$result = mysql_query($sql, $db);
$row_notify = mysql_fetch_assoc($result);

/* page contents starts here */
$savant->assign('notify', $row_notify['inbox_notify']);

$savant->display('users/preferences.tmpl.php');
?>