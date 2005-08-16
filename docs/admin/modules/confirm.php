<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

require(AT_INCLUDE_PATH.'classes/Module/ModuleParser.class.php');
require(AT_INCLUDE_PATH.'lib/mods.inc.php');

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'admin/modules/add_new.php');
	exit;
} else if (isset($_POST['mod']) && isset($_POST['submit_yes'])) {
	install($_POST['mod']);
	$msg->addFeedback('MOD_INSTALLED');
	header('Location: '.$_base_href.'admin/modules/index.php');
	exit;
}  

require(AT_INCLUDE_PATH.'header.inc.php'); 

$_GET['mod'] = str_replace(array('.','..','/'), '', $_GET['mod']);  
$hidden_vars['mod']   = $_GET['mod'];
$msg->addConfirm(array('ADD_MODULE', $_GET['mod']), $hidden_vars);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php'); ?>