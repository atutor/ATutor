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

define('AT_INCLUDE_PATH', '../../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: courses.php');
	exit;
}

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

if (isset($_POST['submit'])) {
	if (isset($_POST['main'])) {
		$_POST['main'] = array_unique($_POST['main']);
		$_POST['main'] = array_filter($_POST['main']); // remove empties
		$main_defaults = implode('|', $_POST['main']);

	} else {
		$main_defaults = '';
	}

	if (isset($_POST['home'])) {
		$_POST['home'] = array_unique($_POST['home']);
		$_POST['home'] = array_filter($_POST['home']); // remove empties
		$home_defaults = implode('|', $_POST['home']);
	} else {
		$home_defaults = '';
	}

	if (!($_config_defaults['main_defaults'] == $main_defaults) && (strlen($main_defaults) < 256)) {
		$sql    = "REPLACE INTO ".TABLE_PREFIX."config VALUES('main_defaults', '$main_defaults')";
		$result = mysql_query($sql, $db);

		$sql    = "DELETE FROM ".TABLE_PREFIX."config WHERE name='main_defaults_2'";
	} else if (!($_config_defaults['main_defaults'] == $main_defaults) && (strlen($main_defaults) > 255)) {
		// we don't have to worry about chopping in the middle since they'll be combined anyway
		$main_defaults_1 = substr($main_defaults, 0, 255);
		$main_defaults_2 = substr($main_defaults, 255);
		$sql    = "REPLACE INTO ".TABLE_PREFIX."config VALUES('main_defaults', '$main_defaults_1')";
		$result = mysql_query($sql, $db);

		$sql    = "REPLACE INTO ".TABLE_PREFIX."config VALUES('main_defaults_2', '$main_defaults_2')";
	} else if ($_config_defaults['main_defaults'] == $main_defaults) {
		$sql    = "DELETE FROM ".TABLE_PREFIX."config WHERE name='main_defaults' OR name='name_defaults_2'";
	}
	$result = mysql_query($sql, $db);


	if (!($_config_defaults['home_defaults'] == $home_defaults) && (strlen($home_defaults) < 256)) {
		$sql    = "REPLACE INTO ".TABLE_PREFIX."config VALUES('home_defaults', '$home_defaults')";
		$result = mysql_query($sql, $db);

		$sql    = "DELETE FROM ".TABLE_PREFIX."config WHERE name='home_defaults_2'";

	} else 	if (!($_config_defaults['home_defaults'] == $home_defaults) && (strlen($home_defaults) > 255)) {
		// we don't have to worry about chopping in the middle since they'll be combined anyway
		$home_defaults_1 = substr($home_defaults, 0, 255);
		$home_defaults_2 = substr($home_defaults, 255);
		$sql    = "REPLACE INTO ".TABLE_PREFIX."config VALUES('home_defaults', '$home_defaults_1')";
		$result = mysql_query($sql, $db);

		$sql    = "REPLACE INTO ".TABLE_PREFIX."config VALUES('home_defaults_2', '$home_defaults_2')";

	} else if ($_config_defaults['home_defaults'] == $home_defaults) {
		$sql    = "DELETE FROM ".TABLE_PREFIX."config WHERE name='home_defaults' OR name='home_defaults_2'";
	}
	$result = mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}


require(AT_INCLUDE_PATH.'header.inc.php');

$main_defaults = explode('|', $_config['main_defaults']);
$home_defaults = explode('|', $_config['home_defaults']);

$main_defaults = array_filter($main_defaults); // remove empties
$home_defaults = array_filter($home_defaults); // remove empties
?>
<?php 
$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED);
$keys = array_keys($module_list);

foreach ($keys as $dir_name) {
	$module =& $module_list[$dir_name]; 

	if ($module->getStudentTools()) {
		$student_tools[] = $module->getStudentTools();
	}
}

$count = 0;

//main mods
$_current_modules = $main_defaults;
$num_main    = count($_current_modules);
//main and home merged
$_current_modules = array_merge($_current_modules, array_diff($home_defaults, $main_defaults));
$num_modules = count($_current_modules);
//all other mods
$_current_modules = array_merge($_current_modules, array_diff($student_tools, $_current_modules));


?>

<?php 
$savant->assign('current_modules', $_current_modules);
$savant->assign('home_defaults', $home_defaults);
$savant->assign('main_defaults', $main_defaults);
$savant->assign('num_modules', $num_modules);
$savant->assign('num_main', $num_main);
$savant->assign('pages', $_pages);
$savant->display('admin/courses/default_mods.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>