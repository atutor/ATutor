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

global $available_languages;
global $_rtl_languages;
global $page;
global $savant;
global $errors, $onload;
global $_base_href;
global $_user_location;

$savant->assign('tmpl_lang',	$available_languages[$_SESSION['lang']][2]);
$savant->assign('tmpl_title',	stripslashes(SITE_NAME));
$savant->assign('tmpl_charset', $available_languages[$_SESSION['lang']][1]);
$savant->assign('tmpl_base_href', $_base_href);


if (in_array($_SESSION['lang'], $_rtl_languages)) {
	$savant->assign('tmpl_rtl_css', '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />');
} else {
	$savant->assign('tmpl_rtl_css', '');
}

if (!isset($errors) && $onload) {
	$savant->assign('tmpl_onload', $onload);
}

$savant->assign('tmpl_page', $page);

header('Content-Type: text/html; charset='.$available_languages[$_SESSION['lang']][1]);

if ($_user_location == 'public') {
	/* the public section */
	if (defined('HOME_URL') && HOME_URL) {
		$nav[] = array('name' => _AT('home'),  'url' => HOME_URL);
	}

	$nav[] = array('name' => _AT('register'),          'url' => 'registration.php');
	$nav[] = array('name' => _AT('browse_courses'),    'url' => 'browse.php');
	$nav[] = array('name' => _AT('login'),             'url' => 'login.php');
	$nav[] = array('name' => _AT('password_reminder'), 'url' => 'password_reminder.php');

	$savant->assign('tmpl_nav', $nav);
	$savant->assign('tmpl_section', '[not sure of the section name?]');

} else if ($_user_location == 'users') {
	/* the /users/ section */

	$sql = 'SELECT * FROM '.TABLE_PREFIX.'members WHERE member_id='.$_SESSION['member_id'];
	$result = mysql_query($sql,$db);
	if ($row = mysql_fetch_assoc($result)) {
		if ($row['status']) {
			$is_instructor = true;
		}
	}

	$nav[] = array('name' => _AT('home'),           'url' => 'users/index.php', 'img' => '');
	$nav[] = array('name' => _AT('profile'),        'url' => 'users/edit.php', 'img' => '');
	$nav[] = array('name' => _AT('browse_courses'), 'url' => 'users/browse.php', 'img' => '');
	if ($is_instructor) {
		$nav[] = array('name' => _AT('create_course'), 'url' => 'users/create_course.php', 'img' => '');
	}
	$nav[] = array('name' => _AT('logout'), 'url' => 'logout.php', 'img' => '');

	$savant->assign('tmpl_nav', $nav);
	$savant->assign('tmpl_section', _AT('control_centre'));

} else if ($_user_location == 'admin') {
	/* the /admin/ section */

	$nav[] = array('name' => _AT('home'),          'url' => 'admin/users.php');
	$nav[] = array('name' => _AT('courses'),    'url' => 'admin/courses.php');
	$nav[] = array('name' => _AT('cats_course_categories'),             'url' => 'admin/course_categories.php');
	$nav[] = array('name' => _AT('language'), 'url' => 'admin/language.php');

	$nav[] = array('name' => _AT('server_configuration'), 'url' => 'admin/config_info.php');
	$nav[] = array('name' => _AT('logout'), 'url' => 'logout.php');

	$savant->assign('tmpl_nav', $nav);
	$savant->assign('tmpl_section', _AT('administration'));
}

$savant->display('include/header_footer/header.tmpl.php');

?>