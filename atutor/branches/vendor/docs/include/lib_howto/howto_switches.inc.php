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

// Used to make links toggle preference settings in ATutor HowTo "Personal Preferences"
if($_GET['pos'] !=''){
	$temp_prefs[PREF_MAIN_MENU_SIDE]= intval($_GET['pos']);
}
if($_GET['seq'] !=''){
	$temp_prefs[PREF_SEQ]= intval($_GET['seq']);
}
if($_GET['numering'] !=''){
	$temp_prefs[PREF_NUMBERING]= intval($_GET['numering']);
}
if($_GET['toc'] !=''){
	$temp_prefs[PREF_TOC]= intval($_GET['toc']);
}
if($_GET['seq_icons'] !=''){
	$temp_prefs[PREF_SEQ_ICONS]= intval($_GET['seq_icons']);
}
if($_GET['nav_icons'] !=''){
	$temp_prefs[PREF_NAV_ICONS]= intval($_GET['nav_icons']);
}
if($_GET['login_icons'] !=''){
	$temp_prefs[PREF_LOGIN_ICONS]= intval($_GET['login_icons']);
}
if($_GET['headings'] !=''){
	$temp_prefs[PREF_HEADINGS]= intval($_GET['headings']);
}
if($_GET['breadcrumbs'] !=''){
	$temp_prefs[PREF_BREADCRUMBS]= intval($_GET['breadcrumbs']);
}
if($_GET['font'] !=''){
	$temp_prefs[PREF_FONT]= intval($_GET['font']);
}

if($_GET['stylesheet'] !=''){
	$temp_prefs[PREF_STYLESHEET]= intval($_GET['stylesheet']);
}
if($_GET['use_help'] !=''){
	$temp_prefs[PREF_HELP]= intval($_GET['use_help']);
}
if($_GET['use_mini_help'] !=''){
	$temp_prefs[PREF_MINI_HELP]= intval($_GET['use_mini_help']);
}

if($_GET['content_icons'] !=''){
	$temp_prefs[PREF_CONTENT_ICONS]= intval($_GET['content_icons']);
}

assign_session_prefs($temp_prefs);
?>
