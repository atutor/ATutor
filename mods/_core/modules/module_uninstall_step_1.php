<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: 

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_MODULES);

if (isset($_GET["mod"])) $mod = $_GET["mod"];
else if (isset($_POST["mod"])) $mod = $_POST["mod"];

if (isset($_GET["args"])) $args = $_GET["args"];
else if (isset($_POST["args"])) $args = $_POST["args"];

$mods_folder = AT_SUBSITE_MODULE_PATH;

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_core/modules/index.php?'.urlencode($_POST['args']));
	exit;
} 
else if (is_writable($mods_folder) || (is_writable($mods_folder) && isset($_POST['submit_yes'])))
{
	header('Location: '.AT_BASE_HREF.'mods/_core/modules/module_uninstall_step_2.php?mod='.$mod.SEP.'args='.urlencode($args).SEP.'permission_granted='.$_POST["permission_granted"]);
	exit;
}

if (!is_writable($mods_folder))
{
	unset($hidden_vars);
	$hidden_vars['mod'] = $mod;
	$hidden_vars['args'] = $args;
	$hidden_vars['permission_granted'] = 1;

	require(AT_INCLUDE_PATH.'header.inc.php'); 
	
	$msg->addConfirm(array('GRANT_WRITE_PERMISSION', realpath($mods_folder)), $hidden_vars, _AT("continue"), _AT("cancel"));
	$msg->printConfirm();
	
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
}

?>