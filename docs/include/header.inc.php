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
// $Id: header.inc.php,v 1.52 2004/04/26 17:33:55 joel Exp $

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $available_languages;
global $_rtl_languages;
global $page;
global $savant;
global $errors, $onload;
global $_base_href, $content_base_href, $course_base_href;
global $_user_location;
global $_base_path;
global $cid;
global $contentManager;
global $_section;

$savant->assign('tmpl_lang',	$available_languages[$_SESSION['lang']][2]);
$savant->assign('tmpl_charset', $available_languages[$_SESSION['lang']][1]);
$savant->assign('tmpl_base_path', $_base_path);
if (!isset($_SESSION['prefs']['PREF_THEME']) || is_numeric($_SESSION['prefs']['PREF_THEME'])) {
	$savant->assign('tmpl_theme', 'default');
	$_SESSION['prefs']['PREF_THEME'] = 'default';
} else {
	$savant->assign('tmpl_theme', $_SESSION['prefs']['PREF_THEME']);
}
$savant->addPath('template', AT_INCLUDE_PATH . '../templates/themes/' . $_SESSION['prefs']['PREF_THEME'] . '/');


if (!defined(BACKWARDS_COMPATIBILITY) || !BACKWARDS_COMPATIBILITY || $content_base_href) {
	$_base_href .= $course_base_href;
	if ($content_base_href) {
		$_base_href .= $content_base_href;
	}
}
$savant->assign('tmpl_base_href', $_base_href);

/* bypass links */
	$bypass_links = '<a href="#content" accesskey="c"><img src="'.$_base_path.'images/clr.gif" height="1" width="1" border="0" alt="'._AT('goto_content').': ALT-c" /></a>';

	$bypass_links .= '<a href="'.$_my_uri;

	if(($_SESSION['prefs'][PREF_MAIN_MENU] !='' && ( $_SESSION['prefs'][PREF_MENU] == 1) || ($_SESSION['prefs'][PREF_LOCAL] == 1)) && !$_GET['menu_jump'] && $_GET['disable'] != PREF_MAIN_MENU && $_SESSION['course_id'] != 0){
		$bypass_links .= '#menu';
		if($_GET['collapse']){
			$bypass_links .= $_GET['collapse'];
		}else if ($_GET['cid'] && !$_GET['disable'] && !$_GET['expand']){
			$bypass_links .= $_GET['cid'];
		}else if ($_GET['expand']){
			$bypass_links .= $_GET['expand'];
		}else{
			$bypass_links .= $_SESSION['s_cid'];
		}
	}else if($_GET['menu_jump']){
		$bypass_links .= SEP.'menu_jump='.$_GET['menu_jump'].'#menu_jump'.$_GET['menu_jump'];
	}else{
		$bypass_links .= '#menu';
	}

	$bypass_links .= '" accesskey="m">';

	$bypass_links .= '<img src="'.$_base_path.'images/clr.gif" height="1" width="1" border="0" alt="'._AT('goto_menu').' Alt-m" /></a>';
	if ($_SESSION['course_id'] != 0) {
		$bypass_links .= '<a href="'.substr($_my_uri, 0, strlen($_my_uri)-1).'#navigation" accesskey="y">';
		$bypass_links .= '<img src="'.$_base_path.'images/clr.gif" height="1" width="1" border="0" alt="'._AT('goto_mainnav').' ALT-y" /></a>';
		$bypass_links .= '<a href="'.$_base_path.'help/accessibility.php#content">';
		$bypass_links .= '<img src="'.$_base_path.'images/clr.gif" height="1" width="1" border="0" alt="'._AT('goto_accessibility').'" /></a>';
	}
	$savant->assign('tmpl_bypass_links', $bypass_links);

/*login/log-out link*/
	if ($_SESSION['valid_user'] === true) {
		$log_link = '<a href="'.$_base_path.'logout.php">'._AT('logout').'</a>';
	} else {
		$log_link = '<a href="'.$_base_path.'login.php">'._AT('login').'</a>';
	}
	$savant->assign('tmpl_log_link', $log_link);

/* construct the page <title> */
	$title = stripslashes(SITE_NAME).' - '.$_SESSION['course_title'];
	$breadcrumbs[] = array('link'  => $_base_path, 'title' => _AT('home'));
	if ($cid != 0) {
		$myPath = $contentManager->getContentPath($cid);
		$num_path = count($myPath);
		for ($i =0; $i<$num_path; $i++) {
			$title .= ' - ';
			$title .= $myPath[$i]['title'];

			$breadcrumbs[] = array('link'  => $_base_path . '?cid='.$myPath[$i]['content_id'], 'title' => $myPath[$i]['title']);
		}
	} else if (is_array($_section) ) {
		$num_sections = count($_section);
		for($i = 0; $i < $num_sections; $i++) {
			$title .= ' - ';
			$title .= $_section[$i][0];

			$breadcrumbs[] = array('link'  => $_base_path . $_section[$i][1] , 'title' => $_section[$i][0]);
		}
	}
	/* remove the 'link' from the last item in the list: */
	$current = array_pop($breadcrumbs);
	unset($current['link']);
	$breadcrumbs[] = $current;
	$savant->assign('tmpl_title',$title);

if (in_array($_SESSION['lang'], $_rtl_languages)) {
	$savant->assign('tmpl_rtl_css', '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />');
} else {
	$savant->assign('tmpl_rtl_css', '');
}
if ($_SESSION['prefs'][PREF_NAV_ICONS] == 2) {
	$savant->assign('tmpl_nav_images_css', '<link rel="stylesheet" href="'.$_base_path.'basic_styles_imageless.css" type="text/css" />');
}

if (!isset($errors) && $onload) {
	$savant->assign('tmpl_onload', $onload);
}

$savant->assign('tmpl_page', $page);

if ($_SESSION['valid_user'] === true) {
	$savant->assign('tmpl_user_name', AT_print($_SESSION['login'], 'members.login'));
} else {
	$savant->assign('tmpl_user_name', _AT('guest'));
}

if ($_SESSION['prefs'][PREF_BREADCRUMBS] && ($_SESSION['course_id'] >0)) { $savant->assign('tmpl_breadcrumbs', true); }

if ($_user_location == 'public') {
	/* the public section */
	if (defined('HOME_URL') && HOME_URL) {
		$nav[] = array('name' => _AT('home'),  'url' => HOME_URL, 'page' => 'home');
	}

	$nav[] = array('name' => _AT('register'),          'url' => 'registration.php',     'page' => 'register');
	$nav[] = array('name' => _AT('browse_courses'),    'url' => 'browse.php',           'page' => 'browse');
	$nav[] = array('name' => _AT('login'),             'url' => 'login.php',            'page' => 'login');
	$nav[] = array('name' => _AT('password_reminder'), 'url' => 'password_reminder.php','page' => 'password_reminder');

	$savant->assign('tmpl_nav', $nav);

} else if ($_user_location == 'admin') {
	/* the /admin/ section */

	$nav[] = array('name' => _AT('home'),                   'url' => 'admin/index.php',            'page' => 'home');
	$nav[] = array('name' => _AT('users'),                  'url' => 'admin/users.php',            'page' => 'users');
	$nav[] = array('name' => _AT('courses'),                'url' => 'admin/courses.php',          'page' => 'courses');
	$nav[] = array('name' => _AT('cats_course_categories'), 'url' => 'admin/course_categories.php','page' => 'course_cats');
	$nav[] = array('name' => _AT('language'),               'url' => 'admin/language.php',         'page' => 'language');
	$nav[] = array('name' => _AT('server_configuration'),   'url' => 'admin/config_info.php',      'page' => 'server_config');
	$nav[] = array('name' => _AT('logout'),                 'url' => 'logout.php',                 'page' => 'logout');

	$savant->assign('tmpl_nav', $nav);
	$savant->assign('tmpl_breadcrumbs', false);
	$savant->assign('tmpl_section', '<h2>' . _AT('administration') . '</h2>');

} else {

	/* the list of our courses: */
	/* used for the courses drop down */
	global $system_courses, $db;
	$sql	= "SELECT E.course_id FROM ".TABLE_PREFIX."course_enrollment E WHERE E.member_id=$_SESSION[member_id] AND E.approved='y'";
	$result = mysql_query($sql, $db);

	$nav_courses = array(); /* the list of courses we're enrolled in or own */
	while ($row = mysql_fetch_assoc($result)) {
		$nav_courses[] = array('course_id' => $row['course_id'], 'title' => $system_courses[$row['course_id']]['title']);
	}

	$nav[] = array('name' => _AT('my_courses'),  'url' => $_base_path . 'users/index.php',       'page' => 'my_courses',     'id' => '');
	$nav[] = array('name' => _AT('preferences'), 'url' => $_base_path . 'users/preferences.php', 'page' => 'preferences',    'id' => '');
	$nav[] = array('name' => _AT('profile'),     'url' => $_base_path . 'users/edit.php',        'page' => 'profile',        'id' => '');
	$nav[] = array('name' => _AT('browse_courses'), 'url' => $_base_path . 'users/browse.php',   'page' => 'browse_courses', 'id' => '');
	$nav[] = array('name' => _AT('inbox'),       'url' => $_base_path . 'inbox.php',             'page' => 'inbox',          'id' => '');
	$nav[] = array('name' => _AT('help'),        'url' => $_base_path . 'help/index.php',        'page' => 'help',           'id' => '');
	$nav[] = array('name' => 'jump_menu');
	
	$savant->assign('tmpl_nav',            $nav);

	/* course menus */
	if ($_SESSION['course_id'] > 0) {
		unset($nav);

		$nav[] = array('name' => _AT('home'), 'page' => 'home', 'attributes' => 'href="'.$_base_path.'index.php" id="home-nav" accesskey="1"');
		$nav[] = array('name' => _AT('tools'), 'page' => 'tools', 'attributes' => 'href="'.$_base_path.'tools/index.php" id="tools-nav" accesskey="2"');
		$nav[] = array('name' => _AT('resources'), 'page' => 'resources', 'attributes' => 'href="'.$_base_path.'resources/index.php" id="resources-nav" accesskey="3"');
		$nav[] = array('name' => _AT('discussions'), 'page' => 'discussions', 'attributes' => 'href="'.$_base_path.'discussions/index.php" id="discussions-nav" accesskey="4"');
		$nav[] = array('name' => _AT('sitemap'), 'page' => 'sitemap', 'attributes' => 'href="'.$_base_path.'tools/sitemap/index.php" id="sitemap-nav" accesskey="5"');

		if ($_SESSION['prefs'][PREF_NAV_ICONS] == 1) {
			unset($nav);
			$nav[] = array('name' => '&nbsp;',  'url' => $_base_path . 'index.php',             'page' => 'home',        'id' => 'home-nav');
			$nav[] = array('name' => '&nbsp;',  'url' => $_base_path . 'tools/index.php',       'page' => 'tools',       'id' => 'tools-nav');
			$nav[] = array('name' => '&nbsp;',  'url' => $_base_path . 'resources/index.php',   'page' => 'resources',   'id' => 'resources-nav');
			$nav[] = array('name' => '&nbsp;',  'url' => $_base_path . 'discussions/index.php', 'page' => 'discussions', 'id' => 'discussions-nav');
			$nav[] = array('name' => '&nbsp;',  'url' => $_base_path . 'tools/sitemap/index.php', 'page' => 'sitemap',   'id' => 'sitemap-nav');
		}

		$savant->assign('tmpl_course_nav', $nav);
	

		/* the instructor nav bar */
		if (show_pen()) {
			if ($_SESSION['prefs']['PREF_EDIT'] == 0) {
				$pen_link = '<a href="'.$_my_uri.'enable='.PREF_EDIT.'" id="enable-editor-user-nav" border="0">'._AT('enable_editor').'</a>';
			} else {
				$pen_link = '<a href="'.$_my_uri.'disable='.PREF_EDIT.'" id="disable-editor-user-nav" >'._AT('disable_editor').'</a>';
			}
			$savant->assign('tmpl_pen_link', $pen_link);
		}

		$savant->assign('tmpl_breadcrumbs_actual', $breadcrumbs);

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
				$banner_style = get_theme_info($_SESSION['prefs']['PREF_THEME']);
				$banner_style = make_css($banner_style['banner']);
			}
			$savant->assign('tmpl_banner_style', $banner_style);
		}
	}
	$savant->assign('tmpl_nav_courses',    $nav_courses);
	$savant->assign('tmpl_user_nav',       $user_nav);
}

header('Content-Type: text/html; charset='.$available_languages[$_SESSION['lang']][1]);

$savant->display('header.tmpl.php');


/* course specific elements: */
if ($_SESSION['course_id'] > 0) {
	if (($_SESSION['prefs'][PREF_MAIN_MENU] == 1) && ($_SESSION['prefs'][PREF_MAIN_MENU_SIDE] == MENU_LEFT)) { 
		$savant->assign('tmpl_menu_open', TRUE);
	}

	if (($_SESSION['prefs'][PREF_MAIN_MENU] == 0) || ($_SESSION['prefs'][PREF_MAIN_MENU_SIDE] == MENU_LEFT)) { 
		     $savant->assign('tmpl_width', '100%');
	} else { $savant->assign('tmpl_width', '80%'); }

	if ($_SESSION['prefs'][PREF_MAIN_MENU] != 1) {              $savant->assign('tmpl_menu_closed', TRUE); }
	if ($_SESSION['prefs'][PREF_MAIN_MENU_SIDE] == MENU_LEFT) { $savant->assign('tmpl_menu_left', TRUE); }
	$savant->assign('tmpl_close_menu_url', $_my_uri.'disable='.PREF_MAIN_MENU);
	$savant->assign('tmpl_open_menu_url', $_my_uri.($_SESSION['prefs'][PREF_MAIN_MENU] ? 'disable' : 'enable').'='.PREF_MAIN_MENU.$cid_url);


	$savant->display('course_header.tmpl.php');

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
}

?>