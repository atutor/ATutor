<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

if (headers_sent()) {
	echo '<br /><br /><code><strong>An error occurred. Output sent before it should have. Please correct the above error(s).</strong></code><br /><hr /><br />';
}

@set_magic_quotes_runtime(0);
@set_time_limit(0);
@ini_set('session.gc_maxlifetime', '36000'); /* 10 hours */

@session_cache_limiter('private, must-revalidate');
session_name('ATutorID');
error_reporting(E_ALL ^ E_NOTICE);

if (headers_sent()) {
	echo '<br /><code><strong>Headers already sent. Cannot initialise session.</strong></code><br /><hr /><br />';
	exit;
}

ob_start();
	session_start();
	$str = ob_get_contents();
ob_end_clean();

if ($str) {
	echo '<br /><code><strong>Error initializing session. Please varify that session.save_path is correctly set in your php.ini file and the directory exists.</strong></code><br /><hr /><br />';
	exit;
}

/* session_register() is deprecated since we're using $_SESSION. */
/* the following is a list of $_SESSION variables: */

/*
$_SESSION['login']        : login name
$_SESSION['valid_user']   : true or false/[empty]
$_SESSION['member_id']    : duh
$_SESSION['is_admin']     : is this an instructor, T/F
$_SESSION['lang']         : language
$_SESSION['course_id']    : 
$_SESSION['menus']        : the menus array
$_SESSION['is_guest']     :
$_SESSION['edit_mode']    : true/false for admin only
$_SESSION['prefs']        : array of preferences
$_SESSION['cprefs']       : array of course default preferences
$_SESSION['layout']       : array of layout options
$_SESSION['use_default_prefs'] : override personal prefs with course prefs
$_SESSION['s_cid']        : content id
$_SESSION['from_cid']     : from cid
$_SESSION['course_title'] : course title
$_SESSION['enroll']       : true iff a user is enrolled or pending.
$_SESSION['last_updated'] : last time the online list was updated
$_SESSION['my_referer']   : previous page
$_SESSION['prefs_saved']  : true|false have prefs been saved?
$_SESSION['track_me']     : true|false whether or not this user gets tracked
$_SESSION['pretime']      : keep track of the timestamp for the previous page for duration calculation
$_SESSION['privileges']   : course privilages/permissions
**/


/***
 * authenticate this user. 'public' pages do not require
 * authentication either.
 */
if (!isset($_SESSION['course_id']) && !isset($_SESSION['valid_user']) && ($_user_location != 'public')) {
	header('Location: '.$_base_href.'login.php');
	exit;
}

?>