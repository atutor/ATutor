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

global $next_prev_links, $langEditor;
global $_base_path, $_my_uri;
global $_stacks, $db, $moduleFactory;

$side_menu = array();
$_stack_files = array();
$module_list =& $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED);

if ($_SESSION['course_id'] > 0) {
	$savant->assign('my_uri', $_my_uri);

	if (($_SESSION['prefs'][PREF_MAIN_MENU] == 1) && $_SESSION['prefs'][PREF_MAIN_MENU_SIDE] != MENU_LEFT) {
		$savant->assign('right_menu_open', TRUE);
		$savant->assign('popup_help', 'MAIN_MENU');
		$savant->assign('menu_url', '<a name="menu"></a>');
		$savant->assign('close_menu_url', $_my_uri.'disable='.PREF_MAIN_MENU);
		$savant->assign('close_menus', _AT('close_menus'));
	}	

	//copyright can be found in include/html/copyright.inc.php

	$side_menu = explode('|', $system_courses[$_SESSION['course_id']]['side_menu']);

	$_stack_files[] = array();
	foreach($_stacks as $stack) {		
		if (in_array($stack['title_var'], $side_menu) && isset($module_list[$stack['mod_name']])) {
			$_stack_files[] = $stack['file'];
		}
	}

}

$theme_img  = $_base_path . 'themes/'. $_SESSION['prefs']['PREF_THEME'] . '/images/';
$savant->assign('img', $theme_img);

if (isset($err)) {
	$err->showErrors(); // print all the errors caught on this page
}
$savant->assign('side_menu', $_stack_files);

if ($framed || $popup) {
	$savant->display('include/fm_footer.tmpl.php');
} else {
	$savant->display('include/footer.tmpl.php');
}

debug($_SESSION);

?>