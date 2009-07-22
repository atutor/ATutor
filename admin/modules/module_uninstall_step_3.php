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

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_REQUEST['cancelled']))
	$cancelled = $_REQUEST['cancelled'];
else if (isset($_POST['cancelled']))
	$cancelled = $_POST['cancelled'];

if (isset($_REQUEST['uninstalled']))
	$uninstalled = $_REQUEST['uninstalled'];
else if (isset($_POST['uninstalled']))
	$uninstalled = $_POST['uninstalled'];

if (isset($_REQUEST['args']))
	$args = $_REQUEST['args'];
else if (isset($_POST['args']))
	$args = $_POST['args'];

if (isset($_REQUEST['mod']))
	$mod = $_REQUEST['mod'];
else if (isset($_POST['mod']))
	$mod = $_POST['mod'];

$mods_folder = '../../mods/';

if (!is_writable($mods_folder) && isset($_POST['submit_yes'])) 
{
	if ($cancelled == 1) 
	{
		$msg->addFeedback('CANCELLED');
	}
	
	if ($uninstalled == 1) 
	{
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}

	header('Location: '.AT_BASE_HREF.'admin/modules/index.php?'.$_POST['args']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

unset($hidden_vars);
$hidden_vars['cancelled'] = $cancelled;
$hidden_vars['uninstalled'] = $uninstalled;
$hidden_vars['args'] = $args;
$hidden_vars['mod'] = $mod;

$msg->addConfirm(array('REMOVE_WRITE_PERMISSION', realpath($mods_folder)), $hidden_vars, _AT("continue"), '', true);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php'); 

?>