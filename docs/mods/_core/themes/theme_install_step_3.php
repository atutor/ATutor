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

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

$theme_folder = '../../../themes/';

if (isset($_REQUEST['installed']))
	$installed = $_REQUEST['installed'];
else if (isset($_POST['installed']))
	$installed = $_POST['installed'];

if (isset($_REQUEST['error']))
	$error = $_REQUEST['error'];
else if (isset($_POST['error']))
	$error = $_POST['error'];

if (!is_writable($theme_folder) && isset($_POST['submit_yes'])) 
{
	if ($error == 1) 
	{
		$msg->addError('IMPORT_FAILED');
		header('Location: '.AT_BASE_HREF.'mods/_core/themes/install_themes.php');
	}
	if ($installed == 1) 
	{
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.AT_BASE_HREF.'mods/_core/themes/index.php');
	}
	
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

unset($hidden_vars);
$hidden_vars['installed'] = $installed;
$hidden_vars['error'] = $error;

$msg->addConfirm(array('REMOVE_WRITE_PERMISSION', realpath($theme_folder)), $hidden_vars, _AT("continue"), '', true);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php'); 



?>