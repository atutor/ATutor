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


$savant->assign('tmpl_lang',	$_SESSION['lang']);
$savant->assign('tmpl_charset', $myLang->getCharacterSet());
$savant->assign('tmpl_base_path', $_base_path);

if (   !isset($_SESSION['prefs']['PREF_THEME']) 
	|| !file_exists(AT_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME'])) {

		$savant->assign('tmpl_theme', 'default');
		$_SESSION['prefs']['PREF_THEME'] = 'default';
} else {
	$savant->assign('tmpl_theme', $_SESSION['prefs']['PREF_THEME']);
}
$savant->addPath('template', AT_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME'] . '/');

$savant->assign('tmpl_current_date', AT_date(_AT('announcement_date_format')));

$theme_img  = $_base_path . 'themes/'. $_SESSION['prefs']['PREF_THEME'] . '/images/';
$theme_info = get_theme_info($_SESSION['prefs']['PREF_THEME']);

$_tmp_base_href = $_base_href;
if (isset($course_base_href) || isset($content_base_href)) {
	$_tmp_base_href .= $course_base_href;
	if ($content_base_href) {
		$_tmp_base_href .= $content_base_href;
	}
}

$savant->assign('tmpl_content_base_href', $_tmp_base_href);
$savant->assign('tmpl_base_href', $_base_href);

/* bypass links */

	if ($_SESSION['course_id'] > 0) {
		$bypass_links = '<a href="'.$_SERVER['REQUEST_URI'].'#course-content" accesskey="c"><img src="'.$_base_path.'images/clr.gif" height="1" width="1" border="0" alt="'._AT('goto_content').': ALT-c" /></a>';
	} else {
		$bypass_links = '<a href="'.$_SERVER['REQUEST_URI'].'#content" accesskey="c"><img src="'.$_base_path.'images/clr.gif" height="1" width="1" border="0" alt="'._AT('goto_content').': ALT-c" /></a>';
	}

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
	if ($_SESSION['course_id'] > 0) {
		$bypass_links .= '<a href="'.$_SERVER['REQUEST_URI'].'#navigation" accesskey="y">';
		$bypass_links .= '<img src="'.$_base_path.'images/clr.gif" height="1" width="1" border="0" alt="'._AT('goto_mainnav').' ALT-y" /></a>';
		$bypass_links .= '<a href="'.$_base_path.'help/accessibility.php#course-content">';
		$bypass_links .= '<img src="'.$_base_path.'images/clr.gif" height="1" width="1" border="0" alt="'._AT('goto_accessibility').'" /></a>';
	}
	$savant->assign('tmpl_bypass_links', $bypass_links);

/*login/log-out link*/
	if ($_SESSION['valid_user'] === true) {
		$log_link = '<a href="'.$_base_path.'logout.php?g=19">'._AT('logout').'</a>';
	} else {
		$log_link = '<a href="'.$_base_path.'login.php">'._AT('login').'</a>';
	}
	$savant->assign('tmpl_log_link', $log_link);

/* construct the page <title> */
	$title = stripslashes(SITE_NAME);
	if ($_SESSION['course_title'] != '') { 
		$title .= ' - '.$_SESSION['course_title'];
	}
	$breadcrumbs   = array();
	$breadcrumbs[] = array('link'  => $_base_path.'?g=10', 'title' => _AT('home'));
	if ($cid != 0) {
		$myPath = $contentManager->getContentPath($cid);
		$num_path = count($myPath);
		for ($i =0; $i<$num_path; $i++) {
			$title .= ' - ';
			$title .= $myPath[$i]['title'];

			$breadcrumbs[] = array('link'  => $_base_path . '?cid='.$myPath[$i]['content_id'].SEP.'g=10', 'title' => $myPath[$i]['title']);
		}
	} else if (is_array($_section) ) {
		$num_sections = count($_section);
		for($i = 0; $i < $num_sections; $i++) {
			$title .= ' - ';
			$title .= $_section[$i][0];

			if (strpos($_section[$i][1], '?') === false) {
				$delim = '?';
			} else {
				$delim = SEP;
			}


			$breadcrumbs[] = array('link'  => $_base_path . $_section[$i][1].$delim.'g=10' , 'title' => $_section[$i][0]);
		}
	}
	/* remove the 'link' from the last item in the list: */
	$current = array_pop($breadcrumbs);
	unset($current['link']);
	$breadcrumbs[] = $current;
	$savant->assign('tmpl_title',stripslashes($addslashes($title)));

if ($myLang->isRTL()) {
	$savant->assign('tmpl_rtl_css', '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />');
} else {
	$savant->assign('tmpl_rtl_css', '');
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


if ($_user_location == 'public') {
	/* the public section */
	if (!defined('HOME_URL') || !HOME_URL) {
		unset($theme_info['pub_nav']['home']);
	}

	$savant->assign('tmpl_user_nav', $theme_info['pub_nav']);
	$myLang->sendContentTypeHeader();
	$savant->display('header.tmpl.php');

} else if ($_user_location == 'admin') {
	/* the /admin/ section */

	$savant->assign('tmpl_user_nav', $theme_info['admin_nav']);
	$savant->assign('tmpl_section', _AT('administration'));

	$myLang->sendContentTypeHeader();
	$savant->display('admin_header.tmpl.php');

} else {

	/* text for jump menu when guest */
	$tmpl_login_jump = _AT('account_login');
	$savant->assign('tmpl_login_jump', $tmpl_login_jump);

	/* the list of our courses: */
	/* used for the courses drop down */
	global $system_courses, $db;
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

	if ($_SESSION['prefs'][PREF_LOGIN_ICONS] == 1) {
		$savant->assign('tmpl_main_icons_only', true);
	} else if ($_SESSION['prefs'][PREF_LOGIN_ICONS] == 2) {
		$savant->assign('tmpl_main_text_only', true);
	}

	/* check for inbox msgs */
	$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."messages WHERE to_member_id=$_SESSION[member_id] AND new=1";
	$result	= mysql_query($sql, $db);
	$row	= @mysql_fetch_assoc($result);

	if ($row['cnt'] > 0) {
		$theme_info['user_nav']['inbox'] = $theme_info['user_nav']['inbox_on'];
	} else {
		$theme_info['user_nav']['inbox'] = $theme_info['user_nav']['inbox_off'];
	}
	unset($theme_info['user_nav']['inbox_on']);
	unset($theme_info['user_nav']['inbox_off']);

	$savant->assign('tmpl_user_nav', $theme_info['user_nav']);

	/* course menus */
	if ($_SESSION['course_id'] > 0) {

		if ($_SESSION['prefs'][PREF_NAV_ICONS] == 1) {
			$savant->assign('tmpl_course_icons_only', true);
		} else if ($_SESSION['prefs'][PREF_NAV_ICONS] == 2) {
			$savant->assign('tmpl_course_text_only', true);
		}

		if (!defined('AC_PATH') || !AC_PATH) {
			unset($theme_info['nav']['acollab']);
		}
		
		unset($nav);
		$savant->assign('tmpl_course_nav', $theme_info['nav']);
	
		/* the instructor nav bar */
		if (show_pen()) {
			if ($_SESSION['prefs']['PREF_EDIT'] == 0) {
				$pen_image = $_base_path.'images/pen.gif';
				$pen_link = '<a href="'.$_my_uri.'enable='.PREF_EDIT.'">'._AT('enable_editor').'</a>';
			} else {
				$pen_image = $_base_path.'images/pen2.gif';
				$pen_link = '<a href="'.$_my_uri.'disable='.PREF_EDIT.'">'._AT('disable_editor').'</a>';
			}
			$savant->assign('tmpl_pen_image', $pen_image);
			$savant->assign('tmpl_pen_link', $pen_link);
		}
		
		if ($_SESSION['prefs'][PREF_BREADCRUMBS] && ($_SESSION['course_id'] >0)) { 
			$savant->assign('tmpl_breadcrumbs', $breadcrumbs);
		}

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

	$myLang->sendContentTypeHeader();
	$savant->display('header.tmpl.php');

	/* course specific elements: */
	/* != 'public' special case for the about.php page, which is available from a course but hides the content menu */
	if (($_SESSION['course_id'] > 0) && ($_user_location != 'public')) {
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

		if(ereg('Mozilla' ,$HTTP_USER_AGENT) && ereg('4.', $BROWSER['Version'])){
			require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

			global $savant;
			$msg =& new Message($savant);

			$msg->addHelp('NETSCAPE4');
		}

	}
}

/* Register our Errorhandler on everypage */
require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
$err =& new ErrorHandler();
		
if (AT_DEVEL) {
	$microtime = microtime();
	$microsecs = substr($microtime, 2, 8);
	$secs = substr($microtime, 11);
	$endTime = "$secs.$microsecs";
	$t .= 'Timer: Vitals parsed in ';
	$t .= sprintf("%.4f",($endTime - $startTime));
	$t .= ' seconds.';
}

?>