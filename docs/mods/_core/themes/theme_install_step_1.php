<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: 

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

$theme_folder = '../../../themes/';

if (isset($_GET["theme"])) $theme = $_GET["theme"];
else if (isset($_POST["theme"])) $theme = $_POST["theme"];

if (isset($_GET["title"])) $title = $_GET["title"];
else if (isset($_POST["title"])) $title = $_POST["title"];

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_core/themes/install_themes.php');
	exit;
} 
else if (is_writable($theme_folder) || (is_writable($theme_folder) && isset($_POST['submit_yes']))) 
{
	header('Location: '.AT_BASE_HREF.'mods/_core/themes/theme_install_step_2.php?theme='.$theme.SEP.'permission_granted='.$_POST["permission_granted"].SEP.'title='.$title);
	exit;
}

if (!is_writable($theme_folder))
{
	unset($hidden_vars);
	$hidden_vars['theme'] = $theme;
	$hidden_vars['title'] = $title;
	$hidden_vars['permission_granted'] = 1;

	require(AT_INCLUDE_PATH.'header.inc.php'); 
	
	$msg->addConfirm(array('GRANT_WRITE_PERMISSION', realpath($theme_folder)), $hidden_vars, _AT("continue"), _AT("cancel"));
	$msg->printConfirm();
	
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
}

?>