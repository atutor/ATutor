<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }
global $_config;
global $_pages;
global $system_courses;

/*
	5 sections: public, my_start_page, course, admin, home
*/
if (isset($_pages[AT_NAV_ADMIN])) {
    array_unshift($_pages[AT_NAV_ADMIN], 'admin/index.php');
}

if($_config['allow_browse'] && $_config['just_social'] != "1") {
if(isset($_SESSION['valid_user'])){
    $browse_tab = "users/browse.php";
}else{
    $browse_tab = "browse.php";
}
}
if($_config['allow_registration']) {
    $reg_tab = "registration.php";
}

if($_config['just_social']) {

    $_pages[AT_NAV_START]  = array_merge(array('users/profile.php', 'users/preferences.php'), (isset($_pages[AT_NAV_START]) ? (array) $_pages[AT_NAV_START] : array()));

}else {

    $_pages[AT_NAV_START]  = array_merge(array('users/index.php' , $browse_tab, 'users/profile.php', 'users/preferences.php'), (isset($_pages[AT_NAV_START]) ? (array) $_pages[AT_NAV_START] : array()));

}
$_pages[AT_NAV_PUBLIC] = array_merge(array('login.php',$reg_tab,$browse_tab), (isset($_pages[AT_NAV_PUBLIC]) ? $_pages[AT_NAV_PUBLIC] : array()));


//$_pages[AT_NAV_START]  = array_merge(array($my_tab , 'users/profile.php', 'users/preferences.php'), (isset($_pages[AT_NAV_START]) ? (array) $_pages[AT_NAV_START] : array()));
//The following line is needed to add MyCourses to the main nav tabs, but other adaptations are needed to have this function properly
//$_pages[AT_NAV_COURSE] = array('users/index.php','index.php');
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
         // Hack to fix arrange-content privilege failing
          global $moduleFactory;
          ////
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
        $_pages['admin/index.php']['title_var'] = 'admin_home';
        $_pages['admin/index.php']['parent']    = AT_NAV_ADMIN;
        $_pages['admin/index.php']['guide']     = 'admin/?p=configuration.php';
        $_pages['admin/index.php']['children'] = array_merge(array('mods/_core/users/admins/my_edit.php', 'mods/_core/users/admins/my_password.php'), isset($_pages['mods/_core/users/index.php']['children']) ?  $_pages['admin/index.php']['children'] : array());

        $_pages['mods/_core/users/admins/my_edit.php']['title_var'] = 'my_account';
        $_pages['mods/_core/users/admins/my_edit.php']['parent']    = 'admin/index.php';
        $_pages['mods/_core/users/admins/my_edit.php']['guide']     = 'admin/?p=my_account.php';

        $_pages['mods/_core/users/admins/my_password.php']['title_var'] = 'change_password';
        $_pages['mods/_core/users/admins/my_password.php']['parent']    = 'admin/index.php';

        if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, AT_PRIV_RETURN)) {
        //hide system preference from non-super admins
            $_pages[AT_NAV_ADMIN][] = 'admin/config_edit.php';

            $_pages['admin/config_edit.php']['title_var'] = 'system_preferences';
            $_pages['admin/config_edit.php']['parent']    = AT_NAV_ADMIN;
            $_pages['admin/config_edit.php']['guide']     = 'admin/?p=system_preferences.php';
            $_pages['admin/config_edit.php']['children']  = array_merge((array) $_pages['admin/config_edit.php']['children'], array('admin/error_logging.php','mods/_standard/social/index_admin.php'));
        }

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
            $_pages['admin/cron_config.php']['title_var'] = 'cron_config';
            $_pages['admin/cron_config.php']['parent']    = 'admin/config_edit.php';
            $_pages['admin/cron_config.php']['guide']     = 'admin/?p=cron_setup.php';
            $_pages['admin/config_edit.php']['children']  = array_merge((array) $_pages['admin/config_edit.php']['children'], array('admin/cron_config.php'));
        }
    }

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
	//$_pages['users/index.php']['children']  = array_merge(array('mods/_core/courses/users/create_course.php'), isset($_pages['users/index.php']['children']) ? $_pages['users/index.php']['children'] : array());
}
$_pages['users/browse.php']['title_var'] = 'browse_courses';
$_pages['users/browse.php']['parent']    = AT_NAV_START;
$_pages['users/browse.php']['guide']     = 'general/?p=browse_courses.php';

$_pages['users/private_enroll.php']['title_var'] = 'enroll';
$_pages['users/private_enroll.php']['parent']    = 'users/index.php';

$_pages['users/remove_course.php']['title_var'] = 'unenroll';
$_pages['users/remove_course.php']['parent']    = 'users/index.php';

$_pages['mods/_standard/profile_pictures/profile_picture.php']['title_var']    = 'picture';

$_pages['users/profile.php']['title_var']    = 'profile';
$_pages['users/profile.php']['parent']   = AT_NAV_START;
$_pages['users/profile.php']['guide']     = 'general/?p=profile.php';
if(isset($_pages['users/profile.php']['children'])){
$_pages['users/profile.php']['children']  = array_merge(array('users/password_change.php', 'users/email_change.php','mods/_standard/profile_pictures/profile_picture.php'), (array) $_pages['users/profile.php']['children']);
}
$_pages['users/password_change.php']['title_var'] = 'change_password';
$_pages['users/password_change.php']['parent']    = 'users/profile.php';
//$_pages['users/password_change.php']['guide']    = 'instructor/?p=creating_courses.php';

$_pages['users/email_change.php']['title_var'] = 'change_email';
$_pages['users/email_change.php']['parent']    = 'users/profile.php';

$_pages['users/preferences.php']['title_var']  = 'preferences';
$_pages['users/preferences.php']['parent'] = AT_NAV_START;
$_pages['users/preferences.php']['guide']  = 'general/?p=preferences.php';

$_pages['users/pref_wizard/index.php']['title_var']  = 'preferences';
$_pages['users/pref_wizard/index.php']['parent'] = AT_NAV_START;

$_pages['enroll.php']['title_var']  = 'enroll';
$_pages['enroll.php']['parent'] = 'users/browse.php';

/* course pages */
$_pages['index.php']['title_var']  = 'course_home';
$_pages['index.php']['parent'] = AT_NAV_COURSE;

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

/**
 * Iterate $_pages, distribute the "avail_in_mobile" setting on the parent page to all its child pages
 * as long as it's not initially set on the child page.
 * @param $this_page - string, the page location used as a key in $_pages
 */ 
function distribute_avail_in_mobile($this_page) {
	global $_pages;
	// return in the cases:
	// 1. the given page is not defined in $_pages 
	// 2. it does not have any child
	// 3. the given page does not have "avail_in_mobile" setting defined
	if (!isset($_pages[$this_page]) || !isset($_pages[$this_page]["avail_in_mobile"]) || 
	    !isset($_pages[$this_page]['children']) || !is_array($_pages[$this_page]['children'])) {
    	return;
	}
	
	foreach ($_pages[$this_page]['children'] as $child_page) {
		// Initial "avail_in_mobile" setting on the child page wins over the one fromt the parent
		if (!isset($_pages[$child_page])) {
			continue;
		} else {
			if (!isset($_pages[$child_page]["avail_in_mobile"])) {
				$_pages[$child_page]["avail_in_mobile"] = $_pages[$this_page]["avail_in_mobile"];
			}
			distribute_avail_in_mobile($child_page);
		}
	}
}

// The page can be turned on/off in mobile themes by adjusting page setting "avail_in_mobile" in module.php
// Here is to populate this setting to child pages.
if (is_mobile_device()) {
	foreach ($_pages as $page => $garbage) {
		distribute_avail_in_mobile($page);
	}
}

$current_page = substr($_SERVER['PHP_SELF'], strlen($_base_path));

function get_num_new_messages() {
    global $db;
    static $num_messages;

    if (isset($num_messages)) {
        return $num_messages;
    }

    $sql    = "SELECT COUNT(*) AS cnt FROM %smessages WHERE to_member_id=%d AND new=1";
    $row    = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);
    $num_messages = $row['cnt'];

    return $num_messages;
}

/**
 *  Check the page availability
 *  The page is unavailable if it's accessed by mobile device and the setting "avail_in_mobile" is set to false
 *  @param	$this_page - string, the page location used as a key in $_pages 
 */
function page_available($this_page) {
	global $_pages;
	if (is_mobile_device() && isset($_pages[$this_page]["avail_in_mobile"]) && !$_pages[$this_page]["avail_in_mobile"]) {
		return false;
	} else {
		return isset($_pages[$this_page]);
	}
}

//TODO****************BOLOGNA*******************REMOVE ME**********************/
function get_main_navigation($current_page) {
    global $_pages, $_base_path, $_tool;

    $parent_page = $_pages[$current_page]['parent'];
    $_top_level_pages = array();

    $tool_file= $table = '';

    if (isset($parent_page) && defined($parent_page)) {
        foreach($_pages[$parent_page] as $page) {
        	if (page_available($page)) {
                if (isset($_pages[$page]['title'])) {
                    $_page_title = $_pages[$page]['title'];
                } else {
                    $_page_title = _AT($_pages[$page]['title_var']);
                }

                if(isset($_tool[$_pages[$page]['title_var']])){                 //viene prelevato il file nel caso in cui lo strumento sia valodo per essere inserito nella toolbar in fase di editing dei conenuti del corso
                    $tool_file = $_tool[$_pages[$page]['title_var']]['file'];
                    $table = $_tool[$_pages[$page]['title_var']]['table'];
                } else {
					$tool_file = '';
					$table = '';
				}

                $_top_level_pages[] = array('url' => AT_print($_base_path, 'url.page') . url_rewrite($page), 'title' => $_page_title,  'tool_file' => $tool_file, 'table' => $table, 'img' => AT_print($_base_path, 'url.page').$_pages[$page]['img']);
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
        return AT_print($_base_path, 'url.page'). url_rewrite($current_page);
    } else if (isset($parent_page)) {
            return get_current_main_page($parent_page);
        }
}

function get_sub_navigation($current_page) {
    global $_pages, $_base_path;

    if (isset($current_page) && defined($current_page)) {
    // reached the top
        return array();
    } else if (isset($_pages[$current_page]['children']) && page_available($current_page)) {
    		if (isset($_pages[$current_page]['title'])) {
                $_page_title = $_pages[$current_page]['title'];
            } else {
                $_page_title = _AT($_pages[$current_page]['title_var']);
            }

            $_sub_level_pages[] = array('url' => AT_print($_base_path, 'url.page') . $current_page, 'title' => $_page_title);
            foreach ($_pages[$current_page]['children'] as $child) {
            	if (!page_available($child)) continue;
            	
            	if (isset($_pages[$child]['title'])) {
                    $_page_title = $_pages[$child]['title'];
                } else {
                    $_page_title = _AT($_pages[$child]['title_var']);
                }

                $_sub_level_pages[] = array('url' => AT_print($_base_path, 'url.page') . $child, 'title' => $_page_title, 'has_children' => isset($_pages[$child]['children']));
            }
        } else if (isset($_pages[$current_page]['parent'])) {
            // no children

                $parent_page = $_pages[$current_page]['parent'];
                return get_sub_navigation($parent_page);
            }

    return $_sub_level_pages;
}
/**
 *  Get tools for the instructors admin tool bar
 *  gatheres the $_pages_i array of instrtor tools
 *  @param	$current_page - string, the page location used as a key in $_pages_i 
 */
function get_sub_navigation_i($current_page) {
    global $_pages_i, $_base_path;
    $_sub_level_pages_i = '';

    if (isset($current_page) && defined($current_page)) {
    // reached the top
        return array();
    } else if (isset($_pages_i[$current_page]['children']) && page_available($current_page)) {

            foreach ($_pages_i[$current_page]['children'] as $child) {
            	if (!page_available($child)) continue;
            	
            	if (isset($_pages_i[$child]['title']) && $_pages_i[$child]['title'] != '') {
                    $_page_title = $_pages_i[$child]['title'];
                } else {
                    $_page_title = _AT($_pages_i[$child]['title_var']);
                }

                    $_sub_level_pages_i[] = array('url' => AT_print($_base_path, 'url.page') . $child, 'title' => $_page_title, 'has_children' => isset($_pages_i[$child]['children']));

            }
        } else if (isset($_pages_i[$current_page]['parent'])) {
            // no children
                $parent_page = $_pages_i[$current_page]['parent'];
                return get_sub_navigation_i($parent_page);
            }
    return $_sub_level_pages_i;
}
function get_current_sub_navigation_page($current_page) {
    global $_pages, $_base_path;
    
    if (!page_available($current_page)) return;
    
    $parent_page = $_pages[$current_page]['parent'];

    if (isset($parent_page) && defined($parent_page)) {
        return AT_print($_base_path, 'url.page') . $current_page;
    } else {
        return AT_print($_base_path, 'url.page') . $current_page;
    }
}
function get_current_sub_navigation_page_i($current_page) {
    global $_pages_i, $_base_path;
    
    if (!page_available($current_page)) return;
    
    $parent_page = $_pages_i[$current_page]['other_parent'];

    if (isset($parent_page) && defined($parent_page)) {
        return AT_print($_base_path, 'url.page') . $current_page;
    } else {
        return AT_print($_base_path, 'url.page') . $current_page;
    }
}
function get_path($current_page) {
    global $_pages, $_pages_i, $_base_path, $_base_href;

    $path = array();
    
    if (!page_available($current_page)){
    	return $path;
    }
    if($_pages_i[$current_page]['other_parent'] && $_SERVER['HTTP_REFERER'] == $_base_href.'tools/index.php'){
        $parent_page = $_pages_i[$current_page]['other_parent'];
    } else if($_pages_i[$current_page]['children']){
        $parent_page = $_pages_i[$current_page]['other_parent'];
    }else{
        $parent_page = $_pages[$current_page]['parent'];
    }
    //$parent_page = $_pages[$current_page]['parent'];

    if (isset($_pages[$current_page]['title'])) {
        $_page_title = $_pages[$current_page]['title'];
    } else {
        $_page_title = _AT($_pages[$current_page]['title_var']);
    }
    if (isset($parent_page) && defined($parent_page)) {
        $path[] = array('url' => AT_print($_base_path, 'url.page') . url_rewrite($current_page), 'title' => $_page_title);
        return $path;
    } else if (isset($parent_page)) {
            $path[] = array('url' => AT_print($_base_path, 'url.page') . url_rewrite($current_page), 'title' => $_page_title);
            $path = array_merge((array) $path, get_path($parent_page));
        } else {
            $path[] = array('url' => AT_print($_base_path, 'url.page') . url_rewrite($current_page), 'title' => $_page_title);
        }

    return $path;
}

//TODO****************BOLOGNA*********************REMOVE ME*****************/
function get_home_navigation($home_array='') {
    global $_pages, $_list, $_base_path, $_tool;

    // set default home_array to course index navigation array
    if (!is_array($home_array)) $home_array = $_pages[AT_NAV_HOME];

    $home_links = array();
    foreach ($home_array as $child) {                                           //esecuzione del ciclo fin quando non saranno terminati i moduli presenti nella home-page del corso
        if (page_available($child)) {
        // initialization
            $title = $icon = $sub_file = $image = $text = $tool_file = $table ='';

            if (isset($_pages[$child]['title'])) {				//viene prelevato il titolo che dovr� poi essere utilizzato nella visualizzazione
                $title = $_pages[$child]['title'];
            } else {
                $title = _AT($_pages[$child]['title_var']);
            }
            if(isset($_pages[$child]['icon'])) {                                //si controlla se è presente l'icona inserita nel modulo di rifrimento. si ricorda che l'icona � inserita solo per i moduli che prevedono possibili sottocontenuti.
                $icon = AT_print($_base_path, 'url.page').$_pages[$child]['icon'];                    //in caso positivo viene prelevata e inserita in una variabile di appoggio che poi sar� a sua volta inserita all'interno dell'array finale home_links[]
            } 
            if(isset($_pages[$child]['text'])) {                         //nel caso in cui non sia presente un' icona associata si controlla se � stato settata il testo (per moduli privi di sottocontenuti).
                $text = $_pages[$child]['text'];				//il testo viene inserito in una variabile d'appoggio e successivamente nell'array.
            }

            if (isset($_list[$_pages[$child]['title_var']]))                    //viene prelevato il path del file che dovr� poi essere richiamato nella visualizzazione dei sottocontenuti. solo i moduli che prevedono sottocontenuti avranno un file di riferimento.
                $sub_file = $_list[$_pages[$child]['title_var']]['file'];

             if(isset($_tool[$_pages[$child]['title_var']])){                    //viene prelevato il file nel caso in cui lo strumento sia valido per essere inserito nella toolbar in fase di editing dei conenuti del corso
                $tool_file = $_tool[$_pages[$child]['title_var']]['file'];
                $table = $_tool[$_pages[$child]['title_var']]['table'];
             }

            $real_image_in_theme = AT_INCLUDE_PATH.'../themes/'.$_SESSION['prefs']['PREF_THEME'].'/'.$_pages[$child]['img'];
            $image_in_theme = AT_print($_base_path, 'url.page').'themes/'.$_SESSION['prefs']['PREF_THEME'].'/'.$_pages[$child]['img'];

            // look for images in theme folder. If not exists, use images relative to ATutor root folder
            if (file_exists($real_image_in_theme))
                $image = $image_in_theme;
            else
                $image = AT_print($_base_path, 'url.page').$_pages[$child]['img'];

            // inclusion of all data necessary for displaying the modules on the home-page. Set by default to check the visible because the modules will be loaded all visible in the home.
            $home_links[] = array('url' => AT_print($_base_path, 'url.page') . url_rewrite($child), 'title' => $title, 'img' => $image, 'icon' => $icon, 'text' => $text, 'sub_file' => $sub_file, 'tool_file' => $tool_file, 'table' => $table);
        }
    }
    return $home_links;
}

?>