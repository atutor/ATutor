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

//header('Cache-Control: private, pre-check=0, post-check=0, max-age=0');

//Harris Timer
  $mtime = microtime(); 
  $mtime = explode(' ', $mtime); 
  $mtime = $mtime[1] + $mtime[0]; 
  $starttime = $mtime; 
//Harris Timer Ends

global $myLang;
global $savant;
global $onload;
global $content_base_href, $course_base_href;
global $_base_path;
global $cid;
global $contentManager;
global $db;
global $_pages;
global $_stacks;
global $framed, $popup;
global $_custom_css;
global $_custom_head;
global $substr, $strlen, $_course_id;

require(AT_INCLUDE_PATH . 'lib/menu_pages.php');
//require(AT_INCLUDE_PATH."../jscripts/opensocial/all_opensocial.php");

$savant->assign('lang_code', $_SESSION['lang']);
$savant->assign('lang_charset', $myLang->getCharacterSet());
$savant->assign('base_path', $_base_path);
$savant->assign('base_tmpl_path', $_SERVER['HTTP_HOST']);
$savant->assign('theme', $_SESSION['prefs']['PREF_THEME']);
$savant->assign('current_date', AT_date(_AT('announcement_date_format')));
$savant->assign('just_social', $_config['just_social']);

$theme_img  = $_base_path . 'themes/'. $_SESSION['prefs']['PREF_THEME'] . '/images/';
$savant->assign('img', $theme_img);

$_tmp_base_href = AT_BASE_HREF;
if (isset($course_base_href) || isset($content_base_href)) {
	$_tmp_base_href .= $course_base_href;
	if ($content_base_href) {
		$_tmp_base_href .= $content_base_href;
	}
}

$savant->assign('content_base_href', $_tmp_base_href);
$savant->assign('base_href', AT_BASE_HREF);

//Handle pretty url pages
if ((($_config['course_dir_name'] + $_config['pretty_url']) > 0) && ($temp = strpos($_SERVER['PHP_SELF'], AT_PRETTY_URL_HANDLER)) > 0){
	$current_page = $pretty_current_page; //this is set in AT_PRETTY_URL_HANDLER
}

if ($myLang->isRTL()) {
	$savant->assign('rtl_css', '<link rel="stylesheet" href="'.$_base_path.'themes/'.$_SESSION['prefs']['PREF_THEME'].'/rtl.css" type="text/css" />');
} else {
	$savant->assign('rtl_css', '');
}

$custom_head = '';
if (isset($_custom_css)) {
	$custom_head = '<link rel="stylesheet" href="'.$_custom_css.'" type="text/css" />';
}

if (isset($_custom_head)) {
	$custom_head .= '
' . $_custom_head;
}

$savant->assign('custom_css', $custom_head);

if ($onload && ($_SESSION['prefs']['PREF_FORM_FOCUS'] || ($substr($onload, -8) != 'focus();'))) {
	$savant->assign('onload', $onload);
}

if (isset($_SESSION['valid_user']) && $_SESSION['valid_user'] === true) {
	if (!empty($_SESSION['member_id'])) {
		$savant->assign('user_name', get_display_name($_SESSION['member_id']));
	} else {
		$savant->assign('user_name', $_SESSION['login']);
	}
} else {
	$savant->assign('user_name', _AT('guest'));
}

if (!isset($_pages[$current_page])) {
	global $msg;
	$msg->addError('PAGE_NOT_FOUND'); // probably the wrong error
	header('location: '.AT_BASE_HREF.'index.php');
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
if (isset($_path[2]['url'], $_sub_level_pages[0]['url']) && $_path[2]['url'] == $_sub_level_pages[0]['url']) {
	$back_to_page = $_path[3];
} else if (isset($_path[1]['url'], $_sub_level_pages[0]['url']) && $_path[1]['url'] == $_sub_level_pages[0]['url']) {
	$back_to_page = isset($_path[2]) ? $_path[2] : null;
} else if (isset($_path[1])) {
	$back_to_page = $_path[1];
}

if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0) {
	$_path[] = array('url' => $_base_path . url_rewrite('index.php'), 'title' => $_SESSION['course_title']);
} else if (isset($_SESSION['course_id']) && $_SESSION['course_id'] < 0) {
	$_path[] = array('url' => $_base_path . 'admin/index.php', 'title' => _AT('administration'));
}

if (isset($_SESSION['member_id']) && $_SESSION['member_id']) {
	$_path[] = array('url' => $_base_path . 'bounce.php?course=0', 'title' => _AT('my_start_page'));
} else if (!isset($_SESSION['course_id']) || !$_SESSION['course_id']) {
	$_path[] = array('url' => $_base_path . 'login.php', 'title' => SITE_NAME);
}

$_path = array_reverse($_path);

if (isset($_pages[$current_page]['title'])) {
	$_page_title = $_pages[$current_page]['title'];
} else {
	$_page_title = _AT($_pages[$current_page]['title_var']);
}



/* calculate the section_title: */
if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0) {
	//Truncate course title if it's > 45.
	$session_course_title = htmlentities($_SESSION['course_title'], ENT_QUOTES, 'UTF-8');
	$section_title = validate_length($session_course_title, 45, VALIDATE_LENGTH_FOR_DISPLAY);
	// If there is an icon, display it on the header
	$sql = 'SELECT icon FROM '.TABLE_PREFIX.'courses WHERE course_id='.$_SESSION['course_id'];
	$result =  mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	if (!empty($row['icon'])){
		//Check if this is a custom icon, if so, use get_course_icon.php to get it
		//Otherwise, simply link it from the images/
		$custom_icon_path = AT_CONTENT_DIR.$_SESSION['course_id']."/custom_icons/";
		if (file_exists($custom_icon_path.$row['icon'])) {
			if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
				$course_icon = $_base_path.'get_course_icon.php/?id='.$_SESSION['course_id'];
			} else {
				$course_icon = $_base_path.'content/' . $_SESSION['course_id'] . '/';
			}
		} else {
			$course_icon = $_base_path.'images/courses/'.$row['icon'];
		}
		$savant->assign('icon', $course_icon);
	}
} else if (!isset($_SESSION['valid_user']) || !$_SESSION['valid_user']) {
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
$savant->assign('back_to_page', isset($back_to_page) ? $back_to_page : null);
$savant->assign('page_title', htmlspecialchars($_page_title, ENT_COMPAT, "UTF-8"));
$savant->assign('top_level_pages', $_top_level_pages);
$savant->assign('section_title', $section_title);

if (isset($_pages[$current_page]['guide'])) {
	$savant->assign('guide', AT_GUIDES_PATH . $_pages[$current_page]['guide']);
}

$myLang->sendContentTypeHeader();

if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > -1) {

	/* the list of our courses: */
	/* used for the courses drop down */
	global $system_courses;
	if ($_SESSION['valid_user']) {
		$sql	= "SELECT E.course_id FROM ".TABLE_PREFIX."course_enrollment E WHERE E.member_id=$_SESSION[member_id] AND E.approved<>'n'";
		$result = @mysql_query($sql, $db);

		$nav_courses = array(); /* the list of courses we're enrolled in or own */
		while ($row = @mysql_fetch_assoc($result)) {
			//Truncate course title if it's > 45.
			$system_courses[$row['course_id']]['title'] = htmlentities($system_courses[$row['course_id']]['title'], ENT_QUOTES, 'UTF-8');
			$nav_courses[$row['course_id']] = validate_length($system_courses[$row['course_id']]['title'], 45, VALIDATE_LENGTH_FOR_DISPLAY);
		}

		natcasesort($nav_courses);
		reset($nav_courses);
		$savant->assign('nav_courses',    $nav_courses);
	}

	if (($_SESSION['course_id'] > 0) && isset($_SESSION['prefs']['PREF_JUMP_REDIRECT']) && $_SESSION['prefs']['PREF_JUMP_REDIRECT']) {
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


// strstr matching for "content" is a hack until there's a better way to ensure // the content shortcut tools are available on all content related pages.
if($_SESSION['cid'] > 0 || $_REQUEST['cid'] > 0 || strstr($_SERVER['PHP_SELF'], 'content')){

// Setup array of content tools for shortcuts tool bar.
$shortcuts = array();
if ((	($content_row['r_date'] <= $content_row['n_date'])
		&& ((!$content_row['content_parent_id'] && ($_SESSION['packaging'] == 'top'))
			|| ($_SESSION['packaging'] == 'all'))
	) || authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {

	$shortcuts[] = array('title' => _AT('export_content'), 'url' => $_base_href . 'mods/_core/imscp/ims_export.php?cid='.$cid, 'icon' => $_base_href . 'images/download.png');
	
}

if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
	if($_SESSION['cid'] > 0 || $_REQUEST['cid'] > 0 ){
		$shortcuts[] = array(
		'title' => _AT('edit_this_page'),   
		'url' => $_base_href . 'mods/_core/editor/edit_content.php?cid='.$cid,
		'icon' => $_base_href . 'images/medit.gif');
	}
	$shortcuts[] = array(
		'title' => _AT('add_sibling_folder'), 
		'url' => $_base_href.'mods/_core/editor/edit_content_folder.php?pid='.$contentManager->_menu_info[$cid]['content_parent_id'], 
		'icon' => $_base_href . 'images/folder_new_sibling.gif');
			
			
	$shortcuts[] = array(
		'title' => _AT('add_sub_folder'),   
		'url' => $_base_href . 'mods/_core/editor/edit_content_folder.php?pid='.$cid, 
		'icon' => $_base_href . 'images/folder_new_sibling.gif');
	
	$shortcuts[] = array(
		'title' => _AT('add_sibling_page'), 
		'url' => $_base_href.'mods/_core/editor/edit_content.php?pid='.$contentManager->_menu_info[$cid]['content_parent_id'], 
		'icon' => $_base_href . 'images/page_add_sibling.gif');

	$shortcuts[] = array(
		'title' => _AT('add_sub_page'),     
		'url' => $_base_href . 'mods/_core/editor/edit_content.php?pid='.$cid, 
		'icon' => $_base_href . 'images/page_add_sibling.gif');
	
	if($_SESSION['cid'] > 0 || $_REQUEST['cid'] > 0 ){
	$shortcuts[] = array(
		'title' => _AT('delete_this_page'), 
		'url' => $_base_href . 'mods/_core/editor/delete_content.php?cid='.$cid, 
		'icon' => $_base_href . 'images/page_delete.gif');
	}
}
$savant->assign('shortcuts', $shortcuts);
}

/* Register our Errorhandler on everypage */
//require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
//$err = new ErrorHandler();


//TODO*******************BOLOGNA*******************REMOVE ME*******************/
// if filemanager is a inside a popup or a frame
// i don't like this code. i don't know were these two variables are coming from
// anyone can add ?framed=1 to a URL to alter the behaviour.

// global $_course_id is set when a guest accessing a public course. 
// This is to solve the issue that the google indexing fails as the session vars are lost.
if (isset($_SESSION['course_id'])) 
	$_course_id = $_SESSION['course_id'];
else if (isset($_GET['p_course'])) // p_course is set when pretty url is turned on and public course is accessed
	$_course_id = $_GET['p_course'];

$savant->assign('course_id', $_course_id);

if ((isset($_REQUEST['framed']) && $_REQUEST['framed']) || (isset($_REQUEST['popup']) && $_REQUEST['popup'])) {
    $savant->assign('framed', 1);
    $savant->assign('popup', 1);

    if(isset($tool_flag) && ($tool_flag))
        $savant->display('include/tm_header.tmpl.php');         //header for toolmanager
    else
        $savant->display('include/fm_header.tmpl.php');

} else {
    //$savant->assign('opensocial', open_social_libs($_base_href));
    $savant->display('include/header.tmpl.php');
}


?>
