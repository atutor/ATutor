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
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_COURSE_TOOLS);


if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: modules.php');
	exit;
	
}

if (isset($_POST['submit'])) {

	$side_menu = '';
	$_stack_names = array();

	$_stack_names = array_keys($_stacks);

	$_POST['stack'] = array_unique($_POST['stack']);
	$_POST['stack'] = array_intersect($_POST['stack'], $_stack_names);

	$side_menu = implode('|', $_POST['stack']);

	$sql    = "UPDATE ".TABLE_PREFIX."courses SET side_menu='$side_menu' WHERE course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);
	$msg->addFeedback('COURSE_PREFS_SAVED');
	header('Location: side_menu.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('instructor/course_tools/side_menu.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>