<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_STYLES);

if (isset($_POST['up'])) {
	$up = key($_POST['up']);
	$_new_modules  = array();
	if (isset($_POST['main'])) {
		foreach ($_POST['main'] as $m) {
			if ($m == $up) {
				$last_m = array_pop($_new_modules);
				$_new_modules[] = $m;
				$_new_modules[] = $last_m;
			} else {
				$_new_modules[] = $m;
			}
		}

		$_POST['main'] = $_new_modules;
	}

	if (isset($_POST['home'])) {
		$_new_modules  = array();
		foreach ($_POST['home'] as $m) {
			if ($m == $up) {
				$last_m = array_pop($_new_modules);
				$_new_modules[] = $m;
				$_new_modules[] = $last_m;
			} else {
				$_new_modules[] = $m;
			}
		}

		$_POST['home'] = $_new_modules;
	}

	$_POST['submit'] = TRUE;
} else if (isset($_POST['down'])) {
	$_new_modules  = array();

	$down = key($_POST['down']);

	if (isset($_POST['main'])) {
		foreach ($_POST['main'] as $m) {
			if ($m == $down) {
				$found = TRUE;
				continue;
			}
			$_new_modules[] = $m;
			if ($found) {
				$_new_modules[] = $down;
				$found = FALSE;
			}
		}

		$_POST['main'] = $_new_modules;
	}

	if (isset($_POST['home'])) {
		$_new_modules  = array();
		foreach ($_POST['home'] as $m) {
			if ($m == $down) {
				$found = TRUE;
				continue;
			}
			$_new_modules[] = $m;
			if ($found) {
				$_new_modules[] = $down;
				$found = FALSE;
			}
		}

		$_POST['home'] = $_new_modules;
	}

	$_POST['submit'] = TRUE;
}

// 'search.php',  removed
if (isset($_POST['submit'])) {

	if (isset($_POST['main'])) {
		$_POST['main'] = array_intersect($_POST['main'], $_modules);
		$_POST['main'] = array_unique($_POST['main']);
		$main_links = implode('|', $_POST['main']);
	} else {
		$main_links = '';
	}

	if (isset($_POST['home'])) {
		$_POST['home'] = array_intersect($_POST['home'], $_modules);
		$_POST['home'] = array_unique($_POST['home']);
		$home_links = implode('|', $_POST['home']);
	} else {
		$home_links = '';
	}

	$sql    = "UPDATE ".TABLE_PREFIX."courses SET home_links='$home_links', main_links='$main_links' WHERE course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: modules.php');
	exit;
}


require(AT_INCLUDE_PATH.'header.inc.php');


//being displayed
$_current_modules = array_slice($_pages[AT_NAV_COURSE], 1, -1); // removes index.php and tools/index.php
$num_main    = count($_current_modules);
//main and home merged
$_current_modules = array_merge( (array) $_current_modules, array_diff($_pages[AT_NAV_HOME],$_pages[AT_NAV_COURSE]) );
$num_modules = count($_current_modules);
//all other mods
$_current_modules = array_merge( (array) $_current_modules, array_diff($_modules, $_current_modules));

$savant->assign('num_modules', $num_modules);
$savant->assign('num_main', $num_main);
$savant->assign('current_modules', $_current_modules);
$savant->display('instructor/course_tools/modules.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>