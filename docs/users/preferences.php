<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
echo 'This page is deprecated. Used in 1.0 only.';
exit;
$section = 'users';
define('AT_INCLUDE_PATH', '../include/');

	require(AT_INCLUDE_PATH.'vitals.inc.php');
	$_section[0][0] = 'Users';
	$_section[0][1] = 'users/';
	$_section[1][0] = 'Preferences';

	/* whether or not, any settings are being changed when this page loads. */
	/* ie. is ANY action being performed right now?							*/
	$action = false;


	if ($_GET['pref_id'] != '') {
		if ($_GET['pref_id'] > 0) {
			/* load a preset set of preferences */
			$my_prefs = get_prefs(intval($_GET['pref_id']));

			if ($my_prefs) {
				assign_session_prefs($my_prefs);
				$feedback[] =AT_FEEDBACK_PREFS_CHANGED;
				$feedback[] =AT_FEEDBACK_APPLY_PREFS;

				/* these prefs have not yet been saved */
				$_SESSION['prefs_saved'] = false;
			} else {
				$errors[] = AT_ERROR_THEME_NOT_FOUND;
			}

		} else {
			/* use this course's prefs */
			$sql	= "SELECT preferences FROM courses WHERE course_id=$_SESSION[course_id]";
			$result	= mysql_query($sql,$db);
			$row	= mysql_fetch_array($result);

			if ($row['preferences']) {
				assign_session_prefs(unserialize(stripslashes($row['preferences'])));
				$feedback[] =AT_FEEDBACK_PREFS_CHANGED;
				$feedback[] =AT_FEEDBACK_APPLY_PREFS;

				/* these prefs have not yet been saved */
				$_SESSION['prefs_saved'] = false;

			} else {
				$errors[] = AT_ERROR_CPREFS_NOT_FOUND;
			}
		}
		$action = true;
	} else if ($_GET['submit']) {
		/* custom prefs */

		$temp_prefs[PREF_MAIN_MENU_SIDE]= intval($_GET['pos']);
		$temp_prefs[PREF_SEQ]		    = intval($_GET['seq']);
		$temp_prefs[PREF_TOC]		    = intval($_GET['toc']);
		$temp_prefs[PREF_NUMBERING]	    = intval($_GET['numering']);
		$temp_prefs[PREF_SEQ_ICONS]	    = intval($_GET['seq_icons']);
		$temp_prefs[PREF_NAV_ICONS]	    = intval($_GET['nav_icons']);
		$temp_prefs[PREF_LOGIN_ICONS]	= intval($_GET['login_icons']);
		$temp_prefs[PREF_HEADINGS]	    = intval($_GET['headings']);
		$temp_prefs[PREF_BREADCRUMBS]	= intval($_GET['breadcrumbs']);
		$temp_prefs[PREF_FONT]	        = intval($_GET['font']);
		$temp_prefs[PREF_STYLESHEET]	= intval($_GET['stylesheet']);
		$temp_prefs[PREF_OVERRIDE]	    = intval($_GET['use_default_prefs']);
		$temp_prefs[PREF_HELP]	        = intval($_GET['use_help']);

		/* we do this instead of assigning to the $_SESSION directly, b/c	*/
		/* assign_session_prefs functionality might change slightly.				*/
		assign_session_prefs($temp_prefs);

		$feedback[] =AT_FEEDBACK_PREFS_CHANGED;
		if ($_SESSION['valid_user'] && $_SESSION['enroll']) {
			/* we're logged in, and enrolled */
			$feedback[] = AT_FEEDBACK_APPLY_PREFS;
		} else if ($_SESSION['valid_user']) {
			/* we're logged in, but not enrolled */
			$feedback[] = AT_FEEDBACK_APPLY_PREFS2;
		} else {
			/* we're not logged in */
			$feedback[] = AT_FEEDBACK_PREFS_LOGIN;
		}

		/* these prefs have not yet been saved */
		$_SESSION['prefs_saved'] = false;
		$action = true;
	} else if ($_GET['save'] == 1) {
		/* save to this course only */
		save_prefs();
		$feedback[] = AT_FEEDBACK_PREFS_SAVED1;
		$_SESSION['prefs_saved'] = true;
		$action = true;

	} else if ($_GET['save'] == 2) {
		/* save as pref for ALL courses */
		save_prefs(true);
		$feedback[] = AT_FEEDBACK_PREFS_SAVED2;
		$_SESSION['prefs_saved'] = true;
		$action = true;

	} else if ($_GET['save'] == 3) {
		/* get prefs: */
		$sql	= "SELECT preferences FROM preferences WHERE member_id=$_SESSION[member_id] AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);
		if ($row2 = mysql_fetch_array($result)) {
			assign_session_prefs(unserialize(stripslashes($row2['preferences'])));
		} else {
			$sql	= "SELECT preferences FROM members WHERE member_id=$_SESSION[member_id]";
			$result = mysql_query($sql, $db);
			if ($row2 = mysql_fetch_array($result)) {
				assign_session_prefs(unserialize(stripslashes($row2['preferences'])));
			}
		}
		$feedback[] = AT_FEEDBACK_PREFS_RESTORED;
		$_SESSION['prefs_saved'] = true;
		$action = true;

	} else if (($_GET['save'] == 4) && authenticate(AT_PRIV_STYLES, AT_PRIV_RETURN)) {
		/* save prefs as this course's default, as an admin only. */

		$data	= addslashes(serialize($_SESSION['prefs']));
		$sql	= "UPDATE courses SET preferences='$data' WHERE course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);

		Header('Location: ?f='.urlencode_feedback(AT_FEEDBACK_COURSE_PREFS_SAVED));
		exit;
	}

	require(AT_INCLUDE_PATH.'header_footer/header.inc.php');

	if (($_SESSION['prefs_saved'] === false) && !$action && $_SESSION['valid_user']) {
		$feedback[] = AT_FEEDBACK_APPLY_PREFS;
	}

	print_errors($errors);
	/* this is where we want the feedback to appear */
	//print_feedback($feedback);
	$help[]=AT_HELP_PREFERENCES;
	$help[]=AT_HELP_PREFERENCES1;
	$help[]=AT_HELP_PREFERENCES2;
	print_help($help);
	require(AT_INCLUDE_PATH.'lib/preferences.inc.php');

	require(AT_INCLUDE_PATH.'header_footer/footer.inc.php');
?>