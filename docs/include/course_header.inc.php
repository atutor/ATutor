<?php
exit('did not think this file gets used: '. __FILE__);

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
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

global $myLang;
global $page;
global $savant;
global $onload;
global $_base_href, $content_base_href, $course_base_href;
global $_base_path;

//content & course base_hrefs are set in docs/index.php
if ($content_base_href) {
	$_base_href .= $course_base_href;
	if ($content_base_href) {
		$_base_href .= $content_base_href;
	}
}
$savant->assign('tmpl_base_href', $_base_href);

$savant->assign('tmpl_base_path', $_base_path);

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

if (!isset($errors) && $onload) {
	$savant->assign('tmpl_onload', $onload);
}

$savant->assign('tmpl_popup_help', 'MAIN_MENU');
$savant->assign('tmpl_page', $page);

if ($_SESSION['prefs'][PREF_BREADCRUMBS]) {
	$savant->assign('tmpl_breadcrumbs', TRUE);
}

if (($_SESSION['prefs'][PREF_MAIN_MENU] == 1)) {
	$savant->assign('tmpl_menu_open', TRUE);
}

if (($_SESSION['prefs'][PREF_MAIN_MENU] == 0)) {
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

$myLang->sendContentTypeHeader();

$savant->display('include/course_header.tmpl.php');


//comes after header template

$cid = intval($_GET['cid']);

global $contentManager;
$next_prev_links = $contentManager->generateSequenceCrumbs($cid);

if ($_SESSION['prefs'][PREF_SEQ] != BOTTOM) {
	echo '<div align="right" id="seqtop">' . $next_prev_links . '</div>';
}

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

$msg->printFeedbacks();
/*
if ($_GET['f']) {
	$f = intval($_GET['f']);
	if ($f > 0) {
		print_feedback($f);
	} else {
		/* it's probably an array *
		$f = unserialize(urldecode(stripslashes($_GET['f'])));
		print_feedback($f);
	}
}
*/

if(ereg('Mozilla' ,$HTTP_USER_AGENT) && ereg('4.', $BROWSER['Version'])){
	$msg->addHelp('NETSCAPE4');
}

$msg->printErrors();
$msg->printWarnings();
?>