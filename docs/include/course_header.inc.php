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
// $Id: course_header.inc.php,v 1.3 2004/04/15 19:23:53 joel Exp $
if (!defined('AT_INCLUDE_PATH')) { exit; }

global $available_languages;
global $_rtl_languages;
global $page;
global $savant;
global $onload;
global $_base_href, $content_base_href, $course_base_href;
global $_base_path;


if ($_SESSION['prefs'][PREF_BREADCRUMBS]) {
	$savant->assign('tmpl_breadcrumbs', TRUE);
}

if (($_SESSION['prefs'][PREF_MAIN_MENU] == 1) && ($_SESSION['prefs'][PREF_MAIN_MENU_SIDE] == MENU_LEFT)) {
	$savant->assign('tmpl_menu_open', TRUE);
}

if (($_SESSION['prefs'][PREF_MAIN_MENU] == 0) || ($_SESSION['prefs'][PREF_MAIN_MENU_SIDE] == MENU_LEFT)) {
	$savant->assign('tmpl_width', "100%");
} else {
	$savant->assign('tmpl_width', "75%");
}

if ($_SESSION['prefs'][PREF_MAIN_MENU] != 1) {
	$savant->assign('tmpl_menu_closed', TRUE);
}
	
if ($_SESSION['prefs'][PREF_MAIN_MENU_SIDE] == MENU_LEFT) {
	$savant->assign('tmpl_menu_left', TRUE);
}

$savant->assign('tmpl_close_menu_url', $_my_uri.'disable='.PREF_MAIN_MENU);

$savant->assign('tmpl_open_menu_url', $_my_uri.($_SESSION['prefs'][PREF_MAIN_MENU] ? 'disable' : 'enable').'='.PREF_MAIN_MENU.$cid_url);



$savant->display('include/course_header.tmpl.php');


//comes after header template

$cid = intval($_GET['cid']);

global $contentManager;
$next_prev_links = $contentManager->generateSequenceCrumbs($cid);

if ($_SESSION['prefs'][PREF_SEQ] != BOTTOM) {
	echo '<div align="right" id="seqtop">' . $next_prev_links . '</div>';
}

if ($_GET['f']) {
	$f = intval($_GET['f']);
	if ($f > 0) {
		print_feedback($f);
	} else {
		/* it's probably an array */
		$f = unserialize(urldecode(stripslashes($_GET['f'])));
		print_feedback($f);
	}
}

if(ereg('Mozilla' ,$HTTP_USER_AGENT) && ereg('4.', $BROWSER['Version'])){
	$help[]= AT_HELP_NETSCAPE4;
}

if (isset($errors)) {
	print_errors($errors);
	unset($errors);
}
print_warnings($warnings);

?>