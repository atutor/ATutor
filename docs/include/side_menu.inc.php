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
// $Id: footer.inc.php 6614 2006-09-27 19:32:29Z joel $

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $next_prev_links;
global $_base_path, $_my_uri;
global $_stacks, $db;
global $system_courses;
global $savant;

// $_course_id is set when a guest accessing a public course. 
// This is to solve the issue that the google indexing fails as the session vars are lost.
global $_course_id;
if (isset($_SESSION['course_id'])) $_course_id = $_SESSION['course_id'];

$savant->assign('course_id', $_course_id);

$side_menu = array();
$stack_files = array();

if ($_course_id > 0) {
	$savant->assign('my_uri', $_my_uri);

	$savant->assign('right_menu_open', TRUE);
	$savant->assign('popup_help', 'MAIN_MENU');
	$savant->assign('menu_url', '<a name="menu"></a>');
	$savant->assign('close_menu_url', htmlspecialchars($_my_uri).'disable='.PREF_MAIN_MENU);
	$savant->assign('close_menus', _AT('close_menus'));

	//copyright can be found in include/html/copyright.inc.php

	$side_menu = explode('|', $system_courses[$_course_id]['side_menu']);

	foreach ($side_menu as $side) {
		if (isset($_stacks[$side])) {
			$stack_files[] = $_stacks[$side]['file'];
		}
	}
}

$savant->assign('side_menu', $stack_files);
$savant->display('include/side_menu.tmpl.php'); ?>