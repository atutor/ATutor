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

if (!defined('AT_INCLUDE_PATH')) { exit; }

header('Cache-Control: private, pre-check=0, post-check=0, max-age=0');

global $myLang;
global $page;
global $savant;
global $errors, $onload;
global $_base_href, $content_base_href, $course_base_href;
global $_user_location;
global $_base_path;
global $cid;
global $contentManager;
global $_section;
global $addslashes;
global $db;
global $_pages; require(AT_INCLUDE_PATH . 'lib/menu_pages.php');


if ( !isset($_SESSION['prefs']['PREF_THEME']) || ($_SESSION['login'] == 'admin') || ($_SESSION['login'] == '')
	|| !file_exists(AT_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME'])) {

		$row = get_default_theme();
		$_SESSION['prefs']['PREF_THEME'] = $row['dir_name'];
} 

$theme_info = get_theme_info($_SESSION['prefs']['PREF_THEME']);

$savant->addPath('template', AT_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME'] . '/');

$savant->assign('tmpl_lang',	$_SESSION['lang']);
$savant->assign('tmpl_charset', $myLang->getCharacterSet());
$savant->assign('tmpl_base_path', $_base_path);
$savant->assign('tmpl_theme', $_SESSION['prefs']['PREF_THEME']);
$savant->assign('tmpl_current_date', AT_date(_AT('announcement_date_format')));

$theme_img  = $_base_path . 'themes/'. $_SESSION['prefs']['PREF_THEME'] . '/images/';

$_tmp_base_href = $_base_href;
if (isset($course_base_href) || isset($content_base_href)) {
	$_tmp_base_href .= $course_base_href;
	if ($content_base_href) {
		$_tmp_base_href .= $content_base_href;
	}
}

$savant->assign('tmpl_content_base_href', $_tmp_base_href);
$savant->assign('tmpl_base_href', $_base_href);

if ($myLang->isRTL()) {
	$savant->assign('tmpl_rtl_css', '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />');
} else {
	$savant->assign('tmpl_rtl_css', '');
}

if (!isset($errors) && $onload) {
	$savant->assign('tmpl_onload', $onload);
}

if ($_SESSION['valid_user'] === true) {
	$savant->assign('tmpl_user_name', AT_print($_SESSION['login'], 'members.login'));
} else {
	$savant->assign('tmpl_user_name', _AT('guest'));
}

$current_page = substr($_SERVER['PHP_SELF'], strlen($_base_path));

$_top_level_pages        = get_main_navigation($current_page);
$_current_top_level_page = get_current_main_page($current_page);
if (empty($_top_level_pages)) {
	if (!$_SESSION['valid_user']) {
		$_top_level_pages = get_main_navigation($_pages[AT_NAV_PUBLIC][0]);
	} else if ($_SESSION['course_id'] < 0) {
		//$_section_title = 'Administration';
		$_top_level_pages = get_main_navigation($_pages[AT_NAV_ADMIN][0]);
	} else if (!$_SESSION['course_id']) {
		//$_section_title = _AT('my_start_page');
		$_top_level_pages = get_main_navigation($_pages[AT_NAV_START][0]);
	} else {
		//$_section_title = $_SESSION['course_title'];
		$_top_level_pages = get_main_navigation($_pages[AT_NAV_COURSE][0]);
	}
}

$_sub_level_pages        = get_sub_navigation($current_page);
$_current_sub_level_page = get_current_sub_navigation_page($current_page);

$_path = get_path($current_page);
unset($_path[0]);
if ($_path[1]['url'] == $_sub_level_pages[0]['url']) {
	$back_to_page = $_path[2];
	//debug('back to : '.$_path[2]['title']);
} else {
	$back_to_page = $_path[1];
	//debug('back to : '.$_path[1]['title']);
}
$_path = array_reverse($_path);
$_page_title = $_pages[$current_page]['title'];

/* calculate the section_title: */
if ($_SESSION['course_id'] > 0) {
	$_section_title = $_SESSION['course_title'];
} else if (!$_SESSION['valid_user']) {
	$_section_title = SITE_NAME;
	if (defined('HOME_URL') && HOME_URL) {
		$_top_level_pages[] = array('url' => HOME_URL, 'title' => _AT('home'));
	}
} else if ($_SESSION['course_id'] < 0) {
	$_section_title = _AT('administration');
} else if (!$_SESSION['course_id']) {
	$_section_title = _AT('my_start_page');
}

$savant->assign('current_top_level_page', $_current_top_level_page);
$savant->assign('sub_level_pages', $_sub_level_pages);
$savant->assign('current_sub_level_page', $_current_sub_level_page);

$savant->assign('path', $_path);
$savant->assign('back_to_page', $back_to_page);
$savant->assign('page_title', $_page_title);
$savant->assign('top_level_pages', $_top_level_pages);
$savant->assign('section_title', $_section_title);

$myLang->sendContentTypeHeader();

if ($_user_location == 'public') {
	/* the public section */
	$savant->display('include/header.tmpl.php');

} else if ($_user_location == 'admin') {
	/* the /admin/ section */

	$savant->display('include/header.tmpl.php');

} else {

	/* the list of our courses: */
	/* used for the courses drop down */
	global $system_courses;
	if ($_SESSION['valid_user']) {
		$sql	= "SELECT E.course_id FROM ".TABLE_PREFIX."course_enrollment E WHERE E.member_id=$_SESSION[member_id] AND E.approved<>'n'";
		$result = @mysql_query($sql, $db);

		$nav_courses = array(); /* the list of courses we're enrolled in or own */
		while ($row = @mysql_fetch_assoc($result)) {
			if (strlen($system_courses[$row['course_id']]['title']) > 33) {
				$tmp_title = substr($system_courses[$row['course_id']]['title'], 0, 30). '...';
			} else {
				$tmp_title = $system_courses[$row['course_id']]['title'];
			}
			$nav_courses[$row['course_id']] = $tmp_title;
		}

		natcasesort($nav_courses);
		reset($nav_courses);
		$savant->assign('tmpl_nav_courses',    $nav_courses);
	}

	/* course menus */
	if ($_SESSION['course_id'] > 0) {
		$sql	= "SELECT banner_text, banner_styles FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);
		if ($row = mysql_fetch_assoc($result)) {
			if ($row['banner_text'] != '') {
				$savant->assign('tmpl_section', $row['banner_text']);
			} else {
				$savant->assign('tmpl_section', $_SESSION['course_title']);
			}

			if ($row['banner_styles'] != '') {
				/* use custom banner styles */
				$banner_style = $row['banner_styles'];
			} else {
				/* use course banner default styles (config file) */
				$banner_style = make_css($theme_info['banner']);
			}
			$savant->assign('tmpl_banner_style', $banner_style);
		}
	}

	if (isset($_SESSION['prefs'][PREF_JUMP_REDIRECT]) && $_SESSION['prefs'][PREF_JUMP_REDIRECT]) {
		$savant->assign('tmpl_rel_url', $_rel_url);
	} else {
		$savant->assign('tmpl_rel_url', '');
	}

	/* course specific elements: */
	/* != 'public' special case for the about.php page, which is available from a course but hides the content menu */
	$sequence_links = array();
	if ($_SESSION['course_id'] > 0) {
		$sequence_links = $contentManager->generateSequenceCrumbs($cid);

		$savant->assign('sequence_links', $sequence_links);
	}

	$savant->display('include/header.tmpl.php');
}

/* Register our Errorhandler on everypage */
require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
$err =& new ErrorHandler();
		
if (defined('AT_DEVEL') && AT_DEVEL) {
	$microtime = microtime();
	$microsecs = substr($microtime, 2, 8);
	$secs = substr($microtime, 11);
	$endTime = "$secs.$microsecs";
	$t .= 'Timer: Vitals parsed in ';
	$t .= sprintf("%.4f",($endTime - $startTime));
	$t .= ' seconds.';
}

?>