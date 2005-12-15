<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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
global $savant;
global $onload;
global $_base_href, $content_base_href, $course_base_href;
global $_base_path;
global $cid;
global $contentManager;
global $db;
global $_pages;
global $_stacks;
global $framed, $popup;
global $_custom_css;

require(AT_INCLUDE_PATH . 'lib/menu_pages.php');

$savant->assign('lang_code', $_SESSION['lang']);
$savant->assign('lang_charset', $myLang->getCharacterSet());
$savant->assign('base_path', $_base_path);
$savant->assign('theme', $_SESSION['prefs']['PREF_THEME']);
$savant->assign('current_date', AT_date(_AT('announcement_date_format')));

$theme_img  = $_base_path . 'themes/'. $_SESSION['prefs']['PREF_THEME'] . '/images/';
$savant->assign('img', $theme_img);

$_tmp_base_href = $_base_href;
if (isset($course_base_href) || isset($content_base_href)) {
	$_tmp_base_href .= $course_base_href;
	if ($content_base_href) {
		$_tmp_base_href .= $content_base_href;
	}
}

$savant->assign('content_base_href', $_tmp_base_href);
$savant->assign('base_href', $_base_href);

if ($myLang->isRTL()) {
	$savant->assign('rtl_css', '<link rel="stylesheet" href="'.$_base_path.'themes/'.$_SESSION['prefs']['PREF_THEME'].'/rtl.css" type="text/css" />');
} else {
	$savant->assign('rtl_css', '');
}

if (isset($_custom_css)) {
	$savant->assign('custom_css', '<link rel="stylesheet" href="'.$_custom_css.'" type="text/css" />');
} else {
	$savant->assign('custom_css', '');
}

if ($onload && ($_SESSION['prefs']['PREF_FORM_FOCUS'] || (substr($onload, -8) != 'focus();'))) {
	$savant->assign('onload', $onload);
}

if ($_SESSION['valid_user'] === true) {
	$savant->assign('user_name', AT_print($_SESSION['login'], 'members.login'));
} else {
	$savant->assign('user_name', _AT('guest'));
}

$current_page = substr($_SERVER['PHP_SELF'], strlen($_base_path));

if (!isset($_pages[$current_page])) {
	global $msg;
	debug($_pages[$current_page]);
	exit;
	$msg->addError('PAGE_NOT_FOUND'); // probably the wrong error
	header('location: '.$_base_href.'index.php');
	exit;
}

$_top_level_pages        = get_main_navigation($current_page);

$_current_top_level_page = get_current_main_page($current_page);

if (empty($_top_level_pages)) {
	if (!$_SESSION['member_id'] && !$_SESSION['course_id']) {
		$_top_level_pages = get_main_navigation($_pages[AT_NAV_PUBLIC][0]);
	} else if ($_SESSION['course_id'] < 0) {
		$_top_level_pages = get_main_navigation($_pages[AT_NAV_ADMIN][0]);
	} else if (!$_SESSION['course_id']) {
		$_top_level_pages = get_main_navigation($_pages[AT_NAV_START][0]);
	} else {
		$_top_level_pages = get_main_navigation($_pages[AT_NAV_COURSE][0]);
	}
}

$_sub_level_pages        = get_sub_navigation($current_page);
$_current_sub_level_page = get_current_sub_navigation_page($current_page);

$_path = get_path($current_page);

unset($_path[0]);
if ($_path[2]['url'] == $_sub_level_pages[0]['url']) {
	$back_to_page = $_path[3];
} else if ($_path[1]['url'] == $_sub_level_pages[0]['url']) {
	$back_to_page = $_path[2];
} else {
	$back_to_page = $_path[1];
}

$_path = array_reverse($_path);
if (isset($_pages[$current_page]['title'])) {
	$_page_title = $_pages[$current_page]['title'];
} else {
	$_page_title = _AT($_pages[$current_page]['title_var']);
}

/* calculate the section_title: */
if ($_SESSION['course_id'] > 0) {
	$section_title = $_SESSION['course_title'];
} else if (!$_SESSION['valid_user']) {
	$section_title = SITE_NAME;
	if (defined('HOME_URL') && HOME_URL) {
		$_top_level_pages[] = array('url' => HOME_URL, 'title' => _AT('home'));
	}
} else if ($_SESSION['course_id'] < 0) {
	$section_title = _AT('administration');
} else if (!$_SESSION['course_id']) {
	$section_title = _AT('my_start_page');
}
$savant->assign('current_top_level_page', $_current_top_level_page);
$savant->assign('sub_level_pages', $_sub_level_pages);
$savant->assign('current_sub_level_page', $_current_sub_level_page);

$savant->assign('path', $_path);
$savant->assign('back_to_page', $back_to_page);
$savant->assign('page_title', $_page_title);
$savant->assign('top_level_pages', $_top_level_pages);
$savant->assign('section_title', $section_title);

if (isset($_pages[$current_page]['guide'])) {
	$savant->assign('guide', AT_GUIDES_PATH . $_pages[$current_page]['guide']);
}

$myLang->sendContentTypeHeader();

if ($_SESSION['course_id'] > -1) {

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
		$savant->assign('nav_courses',    $nav_courses);
	}

	if (($_SESSION['course_id'] > 0) && isset($_SESSION['prefs'][PREF_JUMP_REDIRECT]) && $_SESSION['prefs'][PREF_JUMP_REDIRECT]) {
		$savant->assign('rel_url', $_rel_url);
	} else {
		$savant->assign('rel_url', '');
	}

	/* course specific elements: */
	/* != 'public' special case for the about.php page, which is available from a course but hides the content menu */
	$sequence_links = array();
	if ($_SESSION['course_id'] > 0) {
		$sequence_links = $contentManager->generateSequenceCrumbs($cid);
		$savant->assign('sequence_links', $sequence_links);
	}

	//side menu array
	if ($_SESSION['course_id'] > 0) {
		$side_menu = array();
		$side_menu = explode('|', $system_courses[$_SESSION['course_id']]['side_menu']);
		$side_menu = array_intersect($side_menu, $_stacks);
		$savant->assign('side_menu', $side_menu);
	}
}

/* Register our Errorhandler on everypage */
//require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
//$err =& new ErrorHandler();


// if filemanager is a inside a popup or a frame
// i don't like this code. i don't know were these two variables are coming from
// anyone can add ?framed=1 to a URL to alter the behaviour.
if ($_REQUEST['framed'] || $_REQUEST['popup']) {
	$savant->assign('framed', 1);
	$savant->assign('popup', 1);
	$savant->display('include/fm_header.tmpl.php');
} else {
	$savant->display('include/header.tmpl.php');
}


?>