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
// $Id$

$page = 'preferences';
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

if ($_GET['pref_id'] != '') {

	if ($_GET['pref_id'] > 0) {
		/* load a preset set of preferences */
		$my_prefs = get_prefs(intval($_GET['pref_id']));

		if ($my_prefs) {
			assign_session_prefs($my_prefs);
		} else {
			$msg->addError('THEME_NOT_FOUND');
		}

	} else {
		/* use this course's prefs */
		$sql	= "SELECT preferences FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql,$db);
		$row	= mysql_fetch_array($result);

		if ($row['preferences']) {
			assign_session_prefs(unserialize(stripslashes($row['preferences'])));

			/* these prefs have not yet been saved */
			$_SESSION['prefs_saved'] = false;

		} else {
			$msg->addError('CPREFS_NOT_FOUND');
		}
	}
	$action = true;
} else if ($_GET['submit']) {
	/* custom prefs */

//	$temp_prefs[PREF_MAIN_MENU_SIDE]= intval($_GET['pos']);
	$temp_prefs[PREF_SEQ]		    = intval($_GET['seq']);
	$temp_prefs[PREF_TOC]		    = intval($_GET['toc']);
	$temp_prefs[PREF_NUMBERING]	    = intval($_GET['numbering']);
//	$temp_prefs[PREF_SEQ_ICONS]	    = intval($_GET['seq_icons']);
//	$temp_prefs[PREF_NAV_ICONS]	    = intval($_GET['nav_icons']);
//	$temp_prefs[PREF_LOGIN_ICONS]	= intval($_GET['login_icons']);
//	$temp_prefs[PREF_CONTENT_ICONS]	= intval($_GET['content_icons']);
	$temp_prefs[PREF_HEADINGS]	    = intval($_GET['headings']);
//	$temp_prefs[PREF_BREADCRUMBS]	= intval($_GET['breadcrumbs']);
	$temp_prefs[PREF_HELP]	        = intval($_GET['use_help']);
	$temp_prefs[PREF_MINI_HELP]	    = intval($_GET['use_mini_help']);
	$temp_prefs[PREF_THEME]	        = $_GET['theme'];
	$temp_prefs[PREF_JUMP_REDIRECT] = intval($_GET['use_jump_redirect']);

	/* we do this instead of assigning to the $_SESSION directly, b/c	*/
	/* assign_session_prefs functionality might change slightly.		*/
	assign_session_prefs($temp_prefs);

	/* save as pref for ALL courses */
	save_prefs();

	/* also update message notification pref */
	$_GET['mnot'] = intval($_GET['mnot']);
	$sql = "UPDATE ".TABLE_PREFIX."members SET inbox_notify = $_GET[mnot] WHERE member_id = $_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	
	$msg->addFeedback('PREFS_SAVED2');
	$_SESSION['prefs_saved'] = true;
	$action = true;

} else if ($_GET['save'] == 3) {
	/* get prefs: */
	$sql	= "SELECT preferences FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	if ($row2 = mysql_fetch_array($result)) {
		assign_session_prefs(unserialize(stripslashes($row2['preferences'])));
	}
	
	$msg->addFeedback('PREFS_RESTORED');
	$_SESSION['prefs_saved'] = true;
	$action = true;

}

$sql	= "SELECT inbox_notify FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
$result = mysql_query($sql, $db);
$row_notify = mysql_fetch_assoc($result);

/* page contents starts here */
$savant->assign('notify', $row_notify['inbox_notify']);
$savant->display('users/preferences.tmpl.php', $row_notify);

?>