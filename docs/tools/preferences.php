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

	define('AT_INCLUDE_PATH', '../include/');

	require(AT_INCLUDE_PATH.'vitals.inc.php');
	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/';
	$_section[1][0] = _AT('preferences');

	/* whether or not, any settings are being changed when this page loads. */
	/* ie. is ANY action being performed right now?							*/
	$action = false;

	if ($_GET['pref_id'] != '') {
		if ($_GET['pref_id'] > 0) {
			/* load a preset set of preferences */
			$my_prefs = get_prefs(intval($_GET['pref_id']));

			if ($my_prefs) {
				assign_session_prefs($my_prefs);
				$feedback[] = AT_FEEDBACK_PREFS_CHANGED;
					if ($_SESSION['valid_user'] && $_SESSION['enroll']) {
					$feedback[] = array(AT_FEEDBACK_APPLY_PREFS, $_SERVER['PHP_SELF']);
				} else if ($_SESSION['valid_user']) {
					/* we're logged in, but not enrolled */
					$feedback[] = array(AT_FEEDBACK_APPLY_PREFS2, $_SERVER['PHP_SELF'], $_SESSION['course_id']);
				} else {
					/* we're not logged in */
					$feedback[] = AT_FEEDBACK_PREFS_LOGIN;
				}

				/* these prefs have not yet been saved */
				$_SESSION['prefs_saved'] = false;
			} else {
				$errors[] = AT_ERROR_THEME_NOT_FOUND;
			}

		} else {
			/* use this course's prefs */
			$sql	= "SELECT preferences FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
			$result	= mysql_query($sql,$db);
			$row	= mysql_fetch_array($result);

			if ($row['preferences']) {
				assign_session_prefs(unserialize(stripslashes($row['preferences'])));
				$feedback[] = AT_FEEDBACK_PREFS_CHANGED;
				if ($_SESSION['valid_user'] && $_SESSION['enroll']) {
					$feedback[] = array(AT_FEEDBACK_APPLY_PREFS, $_SERVER['PHP_SELF']);
				} else if ($_SESSION['valid_user']) {
					/* we're logged in, but not enrolled */
					$feedback[] = array(AT_FEEDBACK_APPLY_PREFS2, $_SERVER['PHP_SELF'], $_SESSION['course_id']);
				} else {
					/* we're not logged in */
					$feedback[] = AT_FEEDBACK_PREFS_LOGIN;
				}

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
		$temp_prefs[PREF_CONTENT_ICONS]	= intval($_GET['content_icons']);
		$temp_prefs[PREF_HEADINGS]	    = intval($_GET['headings']);
		$temp_prefs[PREF_BREADCRUMBS]	= intval($_GET['breadcrumbs']);
		$temp_prefs[PREF_FONT]	        = intval($_GET['font']);
		$temp_prefs[PREF_STYLESHEET]	= intval($_GET['stylesheet']);
		$temp_prefs[PREF_OVERRIDE]	    = intval($_GET['override']);
		$temp_prefs[PREF_HELP]	        = intval($_GET['use_help']);
		$temp_prefs[PREF_MINI_HELP]	    = intval($_GET['use_mini_help']);

		for ($i = 0; $i< 6; $i++) {
			if ($_GET['stack'.$i] != '') {
				$stack_array[] = $_GET['stack'.$i];
			}
		}
		$temp_prefs[PREF_STACK]	= $stack_array;

		/* we do this instead of assigning to the $_SESSION directly, b/c	*/
		/* assign_session_prefs functionality might change slightly.		*/
		assign_session_prefs($temp_prefs);

		$feedback[] = AT_FEEDBACK_PREFS_CHANGED;
		if ($_SESSION['valid_user'] && $_SESSION['enroll']) {
			/* we're logged in, and enrolled */
			$feedback[] = array(AT_FEEDBACK_APPLY_PREFS, $_SERVER['PHP_SELF']);
		} else if ($_SESSION['valid_user']) {
			/* we're logged in, but not enrolled */
			$feedback[] = array(AT_FEEDBACK_APPLY_PREFS2, $_SERVER['PHP_SELF'], $_SESSION['course_id']);
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
		$sql	= "SELECT preferences FROM ".TABLE_PREFIX."preferences WHERE member_id=$_SESSION[member_id] AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);
		if ($row2 = mysql_fetch_array($result)) {
			assign_session_prefs(unserialize(stripslashes($row2['preferences'])));
		} else {
			$sql	= "SELECT preferences FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
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
		$sql	= "UPDATE ".TABLE_PREFIX."courses SET preferences='$data' WHERE course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);

		header('Location: preferences.php?f='.urlencode_feedback(AT_FEEDBACK_COURSE_PREFS_SAVED));
		exit;
	}

	/* page contents starts here */
	require(AT_INCLUDE_PATH.'header.inc.php');
	
	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/index.php?g=11"><img src="images/icons/default/square-large-tools.gif" vspace="2"  class="menuimageh2" width="41" height="40" border="0" alt="" /></a> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/index.php?g=11">'._AT('tools').'</a>';
	}
	echo '</h2>';

	echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/preferences-large.gif"  class="menuimageh3" width="42" height="38" alt="" />';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('preferences');
	}
	echo '</h3>';

	if (($_SESSION['prefs_saved'] === false) && !$action && $_SESSION['valid_user']) {
		$feedback[] = array(AT_FEEDBACK_APPLY_PREFS, $_SERVER['PHP_SELF']);
	}

	print_errors($errors);

	/* this is where we want the feedback to appear */
	print_feedback($feedback);

	$help[] = AT_HELP_PREFERENCES;
	$help[] = AT_HELP_PREFERENCES1;
	$help[] = AT_HELP_PREFERENCES2;

	print_help($help);
	
	/* the page contents with the form */
	require(AT_INCLUDE_PATH.'lib/preferences.inc.php');

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>