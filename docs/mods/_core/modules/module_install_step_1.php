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
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

$module_folder = '../../../mods/';

if (isset($_GET["mod"])) $mod = $_GET["mod"];
else if (isset($_POST["mod"])) $mod = $_POST["mod"];

if (isset($_GET["new"])) $new = $_GET["new"];
else if (isset($_POST["new"])) $new = $_POST["new"];

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_core/modules/install_modules.php');
	exit;
} 
else if (is_writable($module_folder) || (is_writable($module_folder) && isset($_POST['submit_yes']))) 
{
	header('Location: '.AT_BASE_HREF.'mods/_core/modules/module_install_step_2.php?mod='.$mod.SEP.'new='.$new.SEP.'permission_granted='.$_POST["permission_granted"]);
	exit;
}

if (!is_writable($module_folder))
{
	unset($hidden_vars);
	$hidden_vars['mod'] = $mod;
	$hidden_vars['new'] = $new;
	$hidden_vars['permission_granted'] = 1;

	require(AT_INCLUDE_PATH.'header.inc.php'); 
	
	$msg->addConfirm(array('GRANT_WRITE_PERMISSION', realpath($module_folder)), $hidden_vars, _AT("continue"), _AT("cancel"));
	$msg->printConfirm();
	
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
}

?>