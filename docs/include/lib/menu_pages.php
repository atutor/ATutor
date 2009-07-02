<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

global $_pages;

/*
	5 sections: public, my_start_page, course, admin, home
*/
if (isset($_pages[AT_NAV_ADMIN])) {
	array_unshift($_pages[AT_NAV_ADMIN], 'admin/index.php', 'admin/modules/index.php');
}
global $_config;
if($_config['allow_registration']){
	$reg_tab = "registration.php";
}
$_pages[AT_NAV_PUBLIC] = array_merge(array('login.php',$reg_tab,'browse.php'), (isset($_pages[AT_NAV_PUBLIC]) ? $_pages[AT_NAV_PUBLIC] : array()));
$_pages[AT_NAV_START]  = array_merge(array('users/index.php',  'users/profile.php', 'users/preferences.php'), (isset($_pages[AT_NAV_START]) ? (array) $_pages[AT_NAV_START] : array()));
$_pages[AT_NAV_COURSE] = array('index.php');
$_pages[AT_NAV_HOME]   = array();

if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0) {
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

	if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
		$_pages[AT_NAV_COURSE][] = 'tools/index.php';		
	} else if ($_SESSION['privileges']) {
		
		/**
		 * the loop and all this module priv checking is done to hide the Manage tab
		 * when this student has privileges, but no items linked from the Manage tab.
		 * Example: the File Storage privilege does not have a Manage tab item.
		 * In the best case it stops after the first found link.
		 * In the worst case it goes through all the modules and doesn't find a link.
		 */
		$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED, 0, TRUE);
		$keys = array_keys($module_list);

		foreach ($keys as $module_name) {
			$module =& $module_list[$module_name];
			if ($module->getPrivilege() && authenticate($module->getPrivilege(), AT_PRIV_RETURN) && ($module->getChildPage('tools/index.php'))) {
				$_pages[AT_NAV_COURSE][] = 'tools/index.php';
				break;
			}
		}
	}
} else if (isset($_SESSION['course_id']) && $_SESSION['course_id'] == -1) {
	/* admin pages */

	$_pages['admin/index.php']['title_var'] = 'home';
	$_pages['admin/index.php']['parent']    = AT_NAV_ADMIN;
	$_pages['admin/index.php']['guide']     = 'admin/?p=configuration.php';
	$_pages['admin/index.php']['children'] = array_merge(array('admin/admins/my_edit.php', 'admin/admins/my_password.php'), isset($_pages['admin/index.php']['children']) ?  $_pages['admin/index.php']['children'] : array());

	$_pages['admin/admins/my_edit.php']['title_var'] = 'my_account';
	$_pages['admin/admins/my_edit.php']['parent']    = 'admin/index.php';
	$_pages['admin/admins/my_edit.php']['guide']     = 'admin/?p=my_account.php';

	$_pages['admin/admins/my_password.php']['title_var'] = 'change_password';
	$_pages['admin/admins/my_password.php']['parent']    = 'admin/index.php';

	if (admin_authenticate(AT_ADMIN_PRIV_USERS, AT_PRIV_RETURN)) {
		$_pages[AT_NAV_ADMIN][] = 'admin/config_edit.php';

		$_pages['admin/config_edit.php']['title_var'] = 'system_preferences';
		$_pages['admin/config_edit.php']['parent']    = AT_NAV_ADMIN;
		$_pages['admin/config_edit.php']['guide']     = 'admin/?p=system_preferences.php';
		$_pages['admin/config_edit.php']['children']  = array_merge((array) $_pages['admin/config_edit.php']['children'], array('admin/error_logging.php'));
	}
	$_pages['admin/fix_content.php']['title_var'] = 'fix_content_ordering';
	$_pages['admin/fix_content.php']['parent']    = 'admin/index.php';

	$_pages['admin/error_logging.php']['title_var'] = 'error_logging';
	$_pages['admin/error_logging.php']['parent']    = 'admin/config_edit.php';
	$_pages['admin/error_logging.php']['guide']     = 'admin/?p=error_logging.php';
	$_pages['admin/error_logging.php']['children']  = array_merge(array('admin/error_logging_bundle.php', 'admin/error_logging_reset.php'), isset($_pages['admin/error_logging.php']['children']) ? $_pages['admin/error_logging.php']['children'] : array());

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
		$_pages['admin/modules/index.php']['guide']     = 'admin/?p=modules.php';
		$_pages['admin/modules/index.php']['children']  = array('admin/modules/install_modules.php');

		$_pages['admin/modules/details.php']['title_var'] = 'details';
		$_pages['admin/modules/details.php']['parent']    = 'admin/modules/index.php';

		$_pages['admin/modules/module_uninstall_step_1.php']['title_var'] = 'module_uninstall';
		$_pages['admin/modules/module_uninstall_step_1.php']['parent']    = 'admin/modules/index.php';

		$_pages['admin/modules/module_uninstall_step_2.php']['title_var'] = 'module_uninstall';
		$_pages['admin/modules/module_uninstall_step_2.php']['parent']    = 'admin/modules/index.php';

		$_pages['admin/modules/module_uninstall_step_3.php']['title_var'] = 'module_uninstall';
		$_pages['admin/modules/module_uninstall_step_3.php']['parent']    = 'admin/modules/index.php';

		$_pages['admin/modules/install_modules.php']['title_var'] = 'install_modules';
		$_pages['admin/modules/install_modules.php']['parent']    = 'admin/modules/index.php';
		$_pages['admin/modules/install_modules.php']['guide']     = 'admin/?p=modules.php';

		$_pages['admin/modules/version_history.php']['title_var'] = 'version_history';
		$_pages['admin/modules/version_history.php']['parent']    = 'admin/modules/install_modules.php';

		$_pages['admin/modules/module_install_step_1.php']['title_var'] = 'details';
		$_pages['admin/modules/module_install_step_1.php']['parent']    = 'admin/modules/install_modules.php';

		$_pages['admin/modules/module_install_step_2.php']['title_var'] = 'details';
		$_pages['admin/modules/module_install_step_2.php']['parent']    = 'admin/modules/install_modules.php';

		$_pages['admin/modules/module_install_step_3.php']['title_var'] = 'details';
		$_pages['admin/modules/module_install_step_3.php']['parent']    = 'admin/modules/install_modules.php';

			$_pages['admin/modules/confirm.php']['title_var'] = 'confirm';
			$_pages['admin/modules/confirm.php']['parent']    = 'admin/modules/add_new.php';

		$_pages['admin/cron_config.php']['title_var'] = 'cron_config';
		$_pages['admin/cron_config.php']['parent']    = 'admin/config_edit.php';
		$_pages['admin/cron_config.php']['guide']     = 'admin/?p=cron_setup.php';
		$_pages['admin/config_edit.php']['children']  = array_merge((array) $_pages['admin/config_edit.php']['children'], array('admin/cron_config.php'));

		$_pages['admin/auto_enroll.php']['title_var'] = 'auto_enroll';
		$_pages['admin/auto_enroll.php']['parent']    = 'admin/config_edit.php';
		$_pages['admin/auto_enroll.php']['guide']     = 'admin/?p=auto_enroll.php';
		$_pages['admin/auto_enroll.php']['children']  = array_merge(array('admin/auto_enroll_edit.php'));
		$_pages['admin/config_edit.php']['children']  = array_merge((array) $_pages['admin/config_edit.php']['children'], array('admin/auto_enroll.php'));

		$_pages['admin/auto_enroll_edit.php']['title_var'] = 'auto_enroll_edit';
		$_pages['admin/auto_enroll_edit.php']['parent']    = 'admin/auto_enroll.php';
	
		$_pages['admin/auto_enroll_delete.php']['title_var'] = 'auto_enroll_delete';
		$_pages['admin/auto_enroll_delete.php']['parent']    = 'admin/auto_enroll.php';
	
	}
}


/* public pages */
$_pages['registration.php']['title_var'] = 'register';
$_pages['registration.php']['parent']    = AT_NAV_PUBLIC;
$_pages['registration.php']['children']  = isset($_pages['browse.php']['children']) ? $_pages['browse.php']['children'] : array();
$_pages['registration.php']['guide']     = 'general/?p=register.php';

$_pages['browse.php']['title_var'] = 'browse_courses';
$_pages['browse.php']['parent']    = AT_NAV_PUBLIC;
$_pages['browse.php']['children']  = isset($_pages['browse.php']['children']) ? $_pages['browse.php']['children'] : array();
$_pages['browse.php']['guide']     = 'general/?p=browse_courses.php';

$_pages['login.php']['title_var'] = 'login';
$_pages['login.php']['parent']    = AT_NAV_PUBLIC;
$_pages['login.php']['children']  = array_merge(array('password_reminder.php'), isset($_pages['login.php']['children']) ? $_pages['login.php']['children'] : array());
$_pages['login.php']['guide']     = 'general/?p=login.php';

$_pages['confirm.php']['title_var'] = 'confirm';
$_pages['confirm.php']['parent']    = AT_NAV_PUBLIC;

$_pages['password_reminder.php']['title_var'] = 'password_reminder';
$_pages['password_reminder.php']['parent']    = 'login.php';
$_pages['password_reminder.php']['guide']     = 'general/?p=password_reminder.php';

$_pages['logout.php']['title_var'] = 'logout';
$_pages['logout.php']['parent']    = AT_NAV_PUBLIC;

/* my start page pages */
$_pages['users/index.php']['title_var'] = 'my_courses';
$_pages['users/index.php']['parent']    = AT_NAV_START;
$_pages['users/index.php']['guide']     = 'general/?p=my_courses.php';
if (isset($_SESSION['member_id']) && $_SESSION['member_id'] && (!isset($_SESSION['course_id']) || !$_SESSION['course_id'])) {
	if ((get_instructor_status() === FALSE) && (!defined('ALLOW_INSTRUCTOR_REQUESTS') || !ALLOW_INSTRUCTOR_REQUESTS)) {
		$_pages['users/index.php']['children']  = array_merge(array('users/browse.php'), (array) $_pages['users/index.php']['children']);
	} else {
		$_pages['users/index.php']['children']  = array_merge(array('users/browse.php', 'users/create_course.php'), isset($_pages['users/index.php']['children']) ? $_pages['users/index.php']['children'] : array());
	}
}
	
	$_pages['users/browse.php']['title_var'] = 'browse_courses';
	$_pages['users/browse.php']['parent']    = 'users/index.php';
	$_pages['users/browse.php']['guide']     = 'general/?p=browse_courses.php';
	
	$_pages['users/create_course.php']['title_var'] = 'create_course';
	$_pages['users/create_course.php']['parent']    = 'users/index.php';
	$_pages['users/create_course.php']['guide']    = 'instructor/?p=creating_courses.php';

	$_pages['users/private_enroll.php']['title_var'] = 'enroll';
	$_pages['users/private_enroll.php']['parent']    = 'users/index.php';

	$_pages['users/remove_course.php']['title_var'] = 'unenroll';
	$_pages['users/remove_course.php']['parent']    = 'users/index.php';

$_pages['users/profile.php']['title_var']    = 'profile';
$_pages['users/profile.php']['parent']   = AT_NAV_START;
$_pages['users/profile.php']['guide']     = 'general/?p=profile.php';
$_pages['users/profile.php']['children']  = array_merge(array('users/password_change.php', 'users/email_change.php'), (array) $_pages['users/profile.php']['children']);

	$_pages['users/password_change.php']['title_var'] = 'change_password';
	$_pages['users/password_change.php']['parent']    = 'users/profile.php';
	//$_pages['users/password_change.php']['guide']    = 'instructor/?p=creating_courses.php';

	$_pages['users/email_change.php']['title_var'] = 'change_email';
	$_pages['users/email_change.php']['parent']    = 'users/profile.php';

$_pages['users/preferences.php']['title_var']  = 'preferences';
$_pages['users/preferences.php']['parent'] = AT_NAV_START;
$_pages['users/preferences.php']['guide']  = 'general/?p=preferences.php';


/* course pages */
$_pages['index.php']['title_var']  = 'home';
$_pages['index.php']['parent'] = AT_NAV_COURSE;

$_pages['enroll.php']['title_var']  = 'enroll';
$_pages['enroll.php']['parent'] = AT_NAV_COURSE;

/* instructor pages: */
$_pages['tools/index.php']['title_var'] = 'manage';
$_pages['tools/index.php']['parent']    = AT_NAV_COURSE;

$_pages['inbox/index.php']['title_var'] = 'inbox';
$_pages['inbox/index.php']['children']  = array_merge(array('inbox/sent_messages.php', 'inbox/send_message.php', 'inbox/export.php'), isset($_pages['inbox/index.php']['children']) ? $_pages['inbox/index.php']['children'] : array());

	$_pages['inbox/sent_messages.php']['title_var'] = 'sent_messages';
	$_pages['inbox/sent_messages.php']['parent']    = 'inbox/index.php';

	$_pages['inbox/send_message.php']['title_var'] = 'send_message';
	$_pages['inbox/send_message.php']['parent']    = 'inbox/index.php';

	$_pages['inbox/export.php']['title_var'] = 'export';
	$_pages['inbox/export.php']['parent']    = 'inbox/index.php';

$_pages['profile.php']['title_var'] = 'profile';
$_pages['profile.php']['parent']    = 'index.php';


/* global pages */
$_pages['about.php']['title_var']  = 'about_atutor';

$_pages['404.php']['title_var']  = '404';

$_pages['help/index.php']['title_var']  = 'help';
$_pages['help/index.php']['children'] = array_merge(array('help/accessibility.php', 'help/contact_support.php'), isset($_pages['help/index.php']['children']) ? $_pages['help/index.php']['children'] : array());

	$_pages['help/accessibility.php']['title_var']  = 'accessibility';
	$_pages['help/accessibility.php']['parent'] = 'help/index.php';

	$_pages['help/contact_support.php']['title_var']  = 'contact_support';
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
				
				$_top_level_pages[] = array('url' => $_base_path . url_rewrite($page), 'title' => $_page_title, 'img' => $_base_path.$_pages[$page]['img'], 'tool_file' => $tool_file);
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
		return $_base_path . url_rewrite($current_page);
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
		$path[] = array('url' => $_base_path . url_rewrite($current_page), 'title' => $_page_title);
		return $path;
	} else if (isset($parent_page)) {
		$path[] = array('url' => $_base_path . url_rewrite($current_page), 'title' => $_page_title);
		$path = array_merge((array) $path, get_path($parent_page));
	} else {
		$path[] = array('url' => $_base_path . url_rewrite($current_page), 'title' => $_page_title);
	}
	
	return $path;
}

function get_home_navigation($home_array='') {
	global $_pages, $_list, $_base_path;
		
	// set default home_array to course index navigation array
	if (!is_array($home_array)) $home_array = $_pages[AT_NAV_HOME];
	
	$home_links = array();
	foreach ($home_array as $child) {					//esecuzione del ciclo fin quando non saranno terminati i moduli presenti nella home-page del corso
		if (isset($_pages[$child])) {
			if (isset($_pages[$child]['title'])) {				//viene prelevato il titolo che dovrà poi essere utilizzato nella visualizzazione
				$title = $_pages[$child]['title'];	
			} else {
				$title = _AT($_pages[$child]['title_var']);	
			}
			if(isset($_pages[$child]['icon'])){					//si controlla se è presente l'icona inserita nel modulo di rifrimento. si ricorda che l'icona è inserita solo per i moduli che prevedono possibili sottocontenuti.
				$icon = $_base_path.$_pages[$child]['icon'];	//in caso positivo viene prelevata e inserita in una variabile di appoggio che poi sarà a sua volta inserita all'interno dell'array finale home_links[]
			} else if(isset($_pages[$child]['text'])){			//nel caso in cui non sia presente un' icona associata si controlla se è stato settata il testo (per moduli privi di sottocontenuti).
				$text = $_pages[$child]['text'];				//il testo viene inserito in una variabile d'appoggio e successivamente nell'array.
			}
			
			if (isset($_list[$_pages[$child]['title_var']])) 	//viene prelevato il path del file che dovrà poi essere richiamato nella visualizzazione dei sottocontenuti. solo i moduli che prevedono sottocontenuti avranno un file di riferimento.
				$sub_file = $_list[$_pages[$child]['title_var']]['file'];
			
			$real_image_in_theme = AT_INCLUDE_PATH.'../themes/'.$_SESSION['prefs']['PREF_THEME'].'/'.$_pages[$child]['img'];
			$image_in_theme = $_base_path.'themes/'.$_SESSION['prefs']['PREF_THEME'].'/'.$_pages[$child]['img'];
			
			// look for images in theme folder. If not exists, use images relative to ATutor root folder
			if (file_exists($real_image_in_theme))
				$image = $image_in_theme;
			else
				$image = $_base_path.$_pages[$child]['img'];
				
			// inclusion of all data necessary for displaying the modules on the home-page. Set by default to check the visible because the modules will be loaded all visible in the home.
			$home_links[] = array('url' => $_base_path . url_rewrite($child), 'title' => $title, 'img' => $image, 'icon' => $icon, 'text' => $text, 'sub_file' => $sub_file, 'tool_file' => $tool_file, 'check'=> 'visible');
			$icon="";											//azzeramento in modo che per i moduli che non prevedono mini-icons non verrà inserito nulla
			$text="";
		}
	}
	return $home_links;
}

?>