<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

global $next_prev_links, $langEditor;
global $_base_path, $_my_uri;
global $_stacks, $db;

/* != 'public' special case for the about.php page, which is available from a course but hides the content menu */
if (($_SESSION['course_id'] > 0) && ($_user_location != 'public')) {
	$savant->assign('tmpl_my_uri', $_my_uri);

	/* next and previous link:	*/
	if ($_SESSION['prefs'][PREF_SEQ] != TOP) {
		$savant->assign('tmpl_next_prev_links', '<div align="right" id="seqbottom">'.$next_prev_links.'</div>');
	} else {
		$savant->assign('tmpl_next_prev_links', '');
	}

	if (is_array($help)) {
		$savant->assign('tmpl_help_link', '<a href="'.$_base_path.'help/about_help.php"><em>'._AT('help_available').'</em>.</a>');
	} else {
		$savant->assign('tmpl_help_link', '');
	}

	if ($_SESSION['prefs'][PREF_SEQ_ICONS] != 2) {
		$savant->assign('tmpl_show_imgs', TRUE);
	} else {
		$savant->assign('tmpl_show_imgs', FALSE);
	}

	if ($_SESSION['prefs'][PREF_SEQ_ICONS] == 1) {
		$savant->assign('tmpl_show_seq_icons', TRUE);
	} else {
		$savant->assign('tmpl_show_seq_icons', FALSE);
	}

	if (($_SESSION['prefs'][PREF_MAIN_MENU] == 1) && $_SESSION['prefs'][PREF_MAIN_MENU_SIDE] != MENU_LEFT) {
		$savant->assign('tmpl_right_menu_open', TRUE);
		$savant->assign('tmpl_popup_help', 'MAIN_MENU');
		$savant->assign('tmpl_menu_url', '<a name="menu"></a>');
		$savant->assign('tmpl_close_menu_url', $_my_uri.'disable='.PREF_MAIN_MENU);
		$savant->assign('tmpl_close_menus', _AT('close_menus'));
	}	
	$sql = "SELECT copyright FROM ".TABLE_PREFIX."courses WHERE course_id=".$_SESSION['course_id'];
	if($result = mysql_query($sql, $db)) {
		while($row=mysql_fetch_row($result)) {
			if(strlen($row[0])>0) {
				$custom_copyright= $row[0];
				$custom_copyright = str_replace('CONTENT_DIR/', '', $custom_copyright);
			}
		}
		$savant->assign('tmpl_custom_copyright', $custom_copyright);
	} else {
		$savant->assign('tmpl_custom_copyright', '');
	}

	$savant->display('course_footer.tmpl.php');
}

if (isset($err)) {
	$err->showErrors(); // print all the errors caught on this page
}

if ($_user_location == 'admin') {
	$savant->display('admin_footer.tmpl.php');
} else {
	$savant->display('footer.tmpl.php');
}

if (AT_DEVEL_TRANSLATE) {
	$langEditor->showMissingTermsFrame();
}

debug($_SESSION);

?>
