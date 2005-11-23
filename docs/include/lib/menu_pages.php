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

/*
	5 sections: public, my_start_page, course, admin, home
*/
if (isset($_pages[AT_NAV_ADMIN])) {
	array_unshift($_pages[AT_NAV_ADMIN], 'admin/index.php', 'admin/modules/index.php');
}

$_pages[AT_NAV_PUBLIC] = array('login.php', 'registration.php', 'browse.php', 'password_reminder.php');
$_pages[AT_NAV_START]  = array('users/index.php',  'users/profile.php', 'users/preferences.php');
$_pages[AT_NAV_COURSE] = array('index.php');
$_pages[AT_NAV_HOME]   = array();

if ($_SESSION['course_id'] > 0) {
	$main_links = $home_links = $side_menu = array();

	if ($system_courses[$_SESSION['course_id']]['main_links']) {
		$main_links = explode('|', $system_courses[$_SESSION['course_id']]['main_links']);
		foreach ($main_links as $link) {
			if (isset($_pages[$link])) {
				$_pages[$link]['parent'] = AT_NAV_COURSE;
			}
		}
		$_pages[AT_NAV_COURSE] = array_merge($_pages[AT_NAV_COURSE], $main_links);
	}

	if ($system_courses[$_SESSION['course_id']]['home_links']) {
		$home_links = explode('|', $system_courses[$_SESSION['course_id']]['home_links']);
		foreach ($home_links as $link) {
			if (isset($_pages[$link])) {
				$_pages[AT_NAV_HOME][] = $link;
			}
		}
	}

	if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN) || $_SESSION['privileges']) {
		$_pages[AT_NAV_COURSE][] = 'tools/index.php';
	}
} else if ($_SESSION['course_id'] == -1) {
	/* admin pages */

		$_pages['admin/index.php']['title_var'] = 'configuration';
		$_pages['admin/index.php']['parent']    = AT_NAV_ADMIN;
		$_pages['admin/index.php']['guide']     = 'admin/?p=2.0.configuration.php';
		if (isset($_pages['admin/index.php']['children'])) {
			array_unshift($_pages['admin/index.php']['children'], 'admin/admins/my_edit.php', 'admin/config_edit.php', 'admin/error_logging.php');
		} else {
			$_pages['admin/index.php']['children'] = array('admin/admins/my_edit.php', 'admin/config_edit.php', 'admin/error_logging.php');
		}

		$_pages['admin/admins/my_edit.php']['title_var'] = 'my_account';
		$_pages['admin/admins/my_edit.php']['parent']    = 'admin/index.php';
		$_pages['admin/admins/my_edit.php']['guide']     = 'admin/?p=2.1.my_account.php';

		$_pages['admin/config_edit.php']['title_var'] = 'system_preferences';
		$_pages['admin/config_edit.php']['parent']    = 'admin/index.php';
		$_pages['admin/config_edit.php']['guide']     = 'admin/?p=2.2.system_preferences.php';

		$_pages['admin/fix_content.php']['title_var'] = 'fix_content_ordering';
		$_pages['admin/fix_content.php']['parent']    = 'admin/index.php';

		$_pages['admin/error_logging.php']['title_var'] = 'error_logging';
		$_pages['admin/error_logging.php']['parent']    = 'admin/index.php';
		$_pages['admin/error_logging.php']['guide']     = 'admin/?p=2.5.error_logging.php';
		$_pages['admin/error_logging.php']['children']  = array('admin/error_logging_bundle.php', 'admin/error_logging_reset.php');

		$_pages['admin/error_logging_reset.php']['title_var'] = 'reset_log';
		$_pages['admin/error_logging_reset.php']['parent']    = 'admin/error_logging.php';

		$_pages['admin/error_logging_bundle.php']['title_var'] = 'report_errors';
		$_pages['admin/error_logging_bundle.php']['parent']    = 'admin/error_logging.php';

		$_pages['admin/error_logging_details.php']['title_var'] = 'viewing_profile_bugs';
		$_pages['admin/error_logging_details.php']['parent']    = 'admin/error_logging.php';

		$_pages['admin/error_logging_view.php']['title_var'] = 'viewing_errors';
		$_pages['admin/error_logging_view.php']['parent']    = 'admin/error_logging_details.php';

	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		// hide modules from non-super admins
		$_pages['admin/modules/index.php']['title_var'] = 'modules';
		$_pages['admin/modules/index.php']['parent']    = AT_NAV_ADMIN;
		$_pages['admin/modules/index.php']['guide']     = 'admin/?p=5.modules.php';
		$_pages['admin/modules/index.php']['children']  = array('admin/modules/add_new.php');

		$_pages['admin/modules/details.php']['title_var'] = 'details';
		$_pages['admin/modules/details.php']['parent']    = 'admin/modules/index.php';

		$_pages['admin/modules/add_new.php']['title_var'] = 'install_modules';
		$_pages['admin/modules/add_new.php']['parent']    = 'admin/modules/index.php';

			$_pages['admin/modules/confirm.php']['title_var'] = 'confirm';
			$_pages['admin/modules/confirm.php']['parent']    = 'admin/modules/add_new.php';

		//$_pages['admin/modules/create.php']['title_var'] = 'create_module';
		//$_pages['admin/modules/create.php']['parent']    = 'admin/modules/index.php';

	}
}


/* public pages */
$_pages['registration.php']['title_var'] = 'register';
$_pages['registration.php']['parent']    = AT_NAV_PUBLIC;

$_pages['browse.php']['title_var'] = 'browse_courses';
$_pages['browse.php']['parent']    = AT_NAV_PUBLIC;

$_pages['login.php']['title_var'] = 'login';
$_pages['login.php']['parent']    = AT_NAV_PUBLIC;

$_pages['confirm.php']['title_var'] = 'confirm';
$_pages['confirm.php']['parent']    = AT_NAV_PUBLIC;

$_pages['password_reminder.php']['title_var'] = 'password_reminder';
$_pages['password_reminder.php']['parent']    = AT_NAV_PUBLIC;

$_pages['logout.php']['title_var'] = 'logout';
$_pages['logout.php']['parent']    = AT_NAV_PUBLIC;

/* my start page pages */
$_pages['users/index.php']['title_var'] = 'my_courses';
$_pages['users/index.php']['parent']    = AT_NAV_START;
$_pages['users/index.php']['children']  = array('users/browse.php', 'users/create_course.php');
	
	$_pages['users/browse.php']['title_var'] = 'browse_courses';
	$_pages['users/browse.php']['parent']    = 'users/index.php';
	
	$_pages['users/create_course.php']['title_var'] = 'create_course';
	$_pages['users/create_course.php']['parent']    = 'users/index.php';
	$_pages['users/create_course.php']['guide']    = 'instructor/?p=0.1.creating_courses.php';

	$_pages['users/private_enroll.php']['title_var'] = 'enroll';
	$_pages['users/private_enroll.php']['parent']    = 'users/index.php';

	$_pages['users/remove_course.php']['title_var'] = 'unenroll';
	$_pages['users/remove_course.php']['parent']    = 'users/index.php';

$_pages['users/profile.php']['title_var']    = 'profile';
$_pages['users/profile.php']['parent']   = AT_NAV_START;
	
$_pages['users/preferences.php']['title_var']  = 'preferences';
$_pages['users/preferences.php']['parent'] = AT_NAV_START;
$_pages['users/preferences.php']['guide']  = 'general/?p=5.3.preferences.php';


/* course pages */
$_pages['index.php']['title_var']  = 'home';
$_pages['index.php']['parent'] = AT_NAV_COURSE;

$_pages['enroll.php']['title_var']  = 'enroll';
$_pages['enroll.php']['parent'] = AT_NAV_COURSE;

/* instructor pages: */
$_pages['tools/index.php']['title_var'] = 'manage';
$_pages['tools/index.php']['parent']    = AT_NAV_COURSE;

$_pages['inbox/index.php']['title_var'] = 'inbox';
$_pages['inbox/index.php']['children']  = array('inbox/send_message.php');

	$_pages['inbox/send_message.php']['title_var'] = 'send_message';
	$_pages['inbox/send_message.php']['parent']    = 'inbox/index.php';

$_pages['profile.php']['title_var'] = 'profile';
$_pages['profile.php']['parent']    = 'index.php';


/*
if (($_SESSION['course_id'] > 0) && isset($_modules)) {
	foreach ($_modules as $module) {
		if (in_array($module, $_pages[AT_NAV_COURSE])) {
			$_pages[$module]['parent'] = AT_NAV_COURSE;
		} else {
			$_pages[$module]['parent'] = 'index.php';
		}
	}
}
*/

/* global pages */
$_pages['about.php']['title_var']  = 'about_atutor';

$_pages['404.php']['title_var']  = '404';

$_pages['help/index.php']['title_var']  = 'help';
$_pages['help/index.php']['children'] = array('help/accessibility.php', 'help/contact_support.php');

	$_pages['help/accessibility.php']['title_var']  = 'accessibility';
	$_pages['help/accessibility.php']['parent'] = 'help/index.php';

	$_pages['help/contact_support.php']['title_var']  = array ('contact_support',SITE_NAME);
	$_pages['help/contact_support.php']['parent'] = 'help/index.php';


$_pages['contact_instructor.php']['title_var']  = 'contact_instructor';

$current_page = substr($_SERVER['PHP_SELF'], strlen($_base_path));

function get_num_new_messages() {
	global $db;
	static $num_messages;

	if (isset($num_messages)) {
		return $num_messages;
	}
	$sql    = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."messages WHERE to_member_id=$_SESSION[member_id] AND new=1";
	$result = mysql_query($sql, $db);
	$row    = mysql_fetch_assoc($result);
	$num_messages = $row['cnt'];

	return $num_messages;
}

function get_main_navigation($current_page) {
	global $_pages, $_base_path;

	$parent_page = $_pages[$current_page]['parent'];
	$_top_level_pages = array();

	if (isset($parent_page) && defined($parent_page)) {
		foreach($_pages[$parent_page] as $page) {
			if (isset($_pages[$page])) {
				if (isset($_pages[$page]['title'])) {
					$_page_title = $_pages[$page]['title'];
				} else {
					$_page_title = _AT($_pages[$page]['title_var']);
				}
				
				$_top_level_pages[] = array('url' => $_base_path . $page, 'title' => $_page_title);
			}
		}
	} else if (isset($parent_page)) {
		return get_main_navigation($parent_page);
	}

	return $_top_level_pages;
}

function get_current_main_page($current_page) {
	global $_pages, $_base_path;

	$parent_page = $_pages[$current_page]['parent'];

	if (isset($parent_page) && defined($parent_page)) {
		return $_base_path . $current_page;
	} else if (isset($parent_page)) {
		return get_current_main_page($parent_page);
	}
}

function get_sub_navigation($current_page) {
	global $_pages, $_base_path;

	if (isset($current_page) && defined($current_page)) {
		// reached the top
		return array();
	} else if (isset($_pages[$current_page]['children'])) {
		if (isset($_pages[$current_page]['title'])) {
			$_page_title = $_pages[$current_page]['title'];
		} else {
			$_page_title = _AT($_pages[$current_page]['title_var']);
		}

		$_sub_level_pages[] = array('url' => $_base_path . $current_page, 'title' => $_page_title);
		foreach ($_pages[$current_page]['children'] as $child) {

			if (isset($_pages[$child]['title'])) {
				$_page_title = $_pages[$child]['title'];
			} else {
				$_page_title = _AT($_pages[$child]['title_var']);
			}

			$_sub_level_pages[] = array('url' => $_base_path . $child, 'title' => $_page_title, 'has_children' => isset($_pages[$child]['children']));
		}
	} else if (isset($_pages[$current_page]['parent'])) {
		// no children

		$parent_page = $_pages[$current_page]['parent'];
		return get_sub_navigation($parent_page);
	}

	return $_sub_level_pages;
}

function get_current_sub_navigation_page($current_page) {
	global $_pages, $_base_path;

	$parent_page = $_pages[$current_page]['parent'];

	if (isset($parent_page) && defined($parent_page)) {
		return $_base_path . $current_page;
	} else {
		return $_base_path . $current_page;
	}
}

function get_path($current_page) {
	global $_pages, $_base_path;

	$parent_page = $_pages[$current_page]['parent'];

	if (isset($_pages[$current_page]['title'])) {
		$_page_title = $_pages[$current_page]['title'];
	} else {
		$_page_title = _AT($_pages[$current_page]['title_var']);
	}

	if (isset($parent_page) && defined($parent_page)) {
		$path[] = array('url' => $_base_path . $current_page, 'title' => $_page_title);
		return $path;
	} else if (isset($parent_page)) {
		$path[] = array('url' => $_base_path . $current_page, 'title' => $_page_title);
		$path = array_merge($path, get_path($parent_page));
	} else {
		$path[] = array('url' => $_base_path . $current_page, 'title' => $_page_title);
	}
	
	return $path;
}

function get_home_navigation() {
	global $_pages, $_base_path;

	$home_links = array();
	foreach ($_pages[AT_NAV_HOME] as $child) {
		if (isset($_pages[$child])) {
			if (isset($_pages[$child]['title'])) {
				$title = $_pages[$child]['title'];
			} else {
				$title = _AT($_pages[$child]['title_var']);
			}
			$home_links[] = array('url' => $_base_path . $child, 'title' => $title, 'img' => $_base_path.$_pages[$child]['img']);
		}
	}

	return $home_links;
}
?>