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
// $Id: header.inc.php,v 1.20 2004/04/12 20:41:44 heidi Exp $
if (!defined('AT_INCLUDE_PATH')) { exit; }

global $available_languages;
global $_rtl_languages;
global $page;
global $savant;
global $onload;

$savant->assign('tmpl_lang', $available_languages[$_SESSION['lang']][2]);

$tmpl_title = stripslashes(SITE_NAME).' - '.$_SESSION['course_title'];
if ($cid != 0) {
	$myPath = $contentManager->getContentPath($cid);
	$num_path = count($myPath);
	for ($i =0; $i<$num_path; $i++) {
		$tmpl_title .= ' - ';
		$tmpl_title .= $myPath[$i]['title'];
	}
} else if (is_array($_section) ) {
	$num_sections = count($_section);
	for($i = 0; $i < $num_sections; $i++) {
		$tmpl_title .= ' - ';
		$tmpl_title .= $_section[$i][0];
	}
}

$savant->assign('tmpl_title',$tmpl_title);
$savant->assign('tmpl_course_title',$_SESSION['course_title']);
$savant->assign('tmpl_charset', $available_languages[$_SESSION['lang']][1]);

if (in_array($_SESSION['lang'], $_rtl_languages)) {
	$savant->assign('tmpl_rtl_css', '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />');
} else {
	$savant->assign('tmpl_rtl_css', '');
}

/*
	if ($_SESSION['prefs'][PREF_OVERRIDE] && file_exists(AT_INCLUDE_PATH.'../content/'.$_SESSION['course_id'].'/stylesheet.css')) {
		echo '<link rel="stylesheet" href="'.$_base_path.'content/'.$_SESSION['course_id'].'/stylesheet.css" type="text/css" />'."\n";
	} else {
		echo '<link rel="stylesheet" href="'.$_base_path.'css/'.$_colours[$_SESSION['prefs'][PREF_STYLESHEET]]['FILE'].'.css" type="text/css" />'."\n";

		if ($_SESSION['prefs'][PREF_FONT]) {
			echo '<link rel="stylesheet" href="'.$_base_path.'css/'.$_fonts[$_SESSION['prefs'][PREF_FONT]]['FILE'].'.css" type="text/css" />'."\n";
		}	
	}
*/

if (!BACKWARDS_COMPATIBILITY || $content_base_href) {
	$tmpl_base_href = $course_base_href;
	if ($content_base_href) {
		$tmpl_base_href .= $content_base_href;
	}
}
$savant->assign('tmpl_base_href',$tmpl_base_href);

if (!isset($errors) && $onload) {
	$savant->assign('tmpl_onload', $onload);
}

$savant->assign('tmpl_page', $page);


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


header('Content-Type: text/html; charset='.$available_languages[$_SESSION['lang']][1]);
$savant->display('include/header.tmpl.php');


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