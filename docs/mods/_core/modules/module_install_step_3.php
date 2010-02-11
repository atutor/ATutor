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

$module_folder = '../../../mods/';

if (isset($_REQUEST['cancelled']))
	$cancelled = $_REQUEST['cancelled'];
else if (isset($_POST['cancelled']))
	$cancelled = $_POST['cancelled'];

if (isset($_REQUEST['installed']))
	$installed = $_REQUEST['installed'];
else if (isset($_POST['installed']))
	$installed = $_POST['installed'];

if (!is_writable($module_folder) && isset($_POST['submit_yes'])) 
{
	if ($cancelled == 1) 
	{
		$msg->addFeedback('CANCELLED');
		header('Location: '.AT_BASE_HREF.'mods/_core/modules/install_modules.php');
	}
	if ($installed == 1) 
	{
		$msg->addFeedback('MOD_INSTALLED');
		header('Location: '.AT_BASE_HREF.'mods/_core/modules/index.php');
	}
	
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

unset($hidden_vars);
$hidden_vars['cancelled'] = $cancelled;
$hidden_vars['installed'] = $installed;

$msg->addConfirm(array('REMOVE_WRITE_PERMISSION', realpath($module_folder)), $hidden_vars, _AT("continue"), '', true);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php'); 



?>