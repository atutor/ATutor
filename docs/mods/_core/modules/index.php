<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_MODULES);

require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');

$dir_name = str_replace(array('.','..'), '', $_GET['mod_dir']);

$args = '';

if (isset($_GET['enabled'])  && $_GET['enabled'])  {  $args .= 'enabled=1';      }
if (isset($_GET['disabled']) && $_GET['disabled']) {  $args .= SEP.'disabled=1'; }
if (isset($_GET['missing'])  && $_GET['missing'])  {  $args .= SEP.'missing=1';  }
if (isset($_GET['partially_uninstalled'])  && $_GET['partially_uninstalled'])  {  $args .= SEP.'partially_uninstalled=1';  }
if (isset($_GET['core'])     && $_GET['core'])     {  $args .= SEP.'core=1';     }
if (isset($_GET['standard']) && $_GET['standard']) {  $args .= SEP.'standard=1'; }
if (isset($_GET['extra'])    && $_GET['extra'])    {  $args .= SEP.'extra=1';    }

if (isset($_GET['reset_filter'])) {
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

if (isset($_GET['mod_dir'], $_GET['enable'])) {
	$module = $moduleFactory->getModule($_GET['mod_dir']);
	if (!$module->isEnabled() && !$module->isCore() && !$module->isMissing() && !$module->isPartiallyUninstalled()) {
		$module->enable();
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}

	header('Location: '.$_SERVER['PHP_SELF'] . '?' . $args);
	exit;
} else if (isset($_GET['mod_dir'], $_GET['disable'])) {
	$module = $moduleFactory->getModule($_GET['mod_dir']);
	if ($module->isCore()) {
		// core modules cannot be disabled!
		$msg->addError('DISABLE_CORE_MODULE');
	} else if ($module->isMissing()) {
		$msg->addError('DISABLE_MISSING_MODULE');
	} else if ($module->isPartiallyUninstalled()) {
		$msg->addError('DISABLE_PARTIALLY_UNINSTALLED_MODULE');
	} else if ($module->isEnabled()) {
		$module->disable();
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}
	header('Location: '.$_SERVER['PHP_SELF'] . '?' . $args);
	exit;
} else if (isset($_GET['mod_dir'], $_GET['details'])) {
	header('Location: details.php?mod='.$_GET['mod_dir'] . SEP . $args);
	exit;

} else if (isset($_GET['mod_dir'], $_GET['uninstall'])) {
	$module = $moduleFactory->getModule($_GET['mod_dir']);

	$module_folder = '../../../mods/'.$_GET['mod_dir'];
	// check if the module has been un-installed
	if (!file_exists($module_folder))
	{
		$msg->addError('ALREADY_UNINSTALLED');
	}

	// only extra modules can be uninstalled
	if (!$module->isExtra()) {
		$msg->addError('ONLY_UNINSTALL_EXTRA_MODULE');
	} 
	// check if the module is installed via "Available Extra Modules"
	// which are the modules can be un-installed 
	else if (!file_exists($module_folder.'/module_uninstall.php') || !is_writable($module_folder))
	{
		$msg->addError('CANNOT_UNINSTALL_MANUAL_MODULE');
	}
	
  if (!$msg->containsErrors())
	{
		header('Location: module_uninstall_step_1.php?mod=' . urlencode($_GET['mod_dir']) . SEP.'args='.urlencode($args));
		exit;
	}

} else if (isset($_GET['mod_dir'], $_GET['export'])) {
	$module = $moduleFactory->getModule($_GET['mod_dir']);

	$module_folder = '../../../mods/'.$_GET['mod_dir'];
	// check if the module has been un-installed
	if (!file_exists($module_folder))
	{
		$msg->addError('ITEM_NOT_FOUND');
	}

	// only extra modules can be uninstalled
	if (!$module->isExtra()) {
		$msg->addError('ONLY_EXPORT_EXTRA_MODULE');
	} 
	
  if (!$msg->containsErrors())
	{
		require(AT_INCLUDE_PATH.'classes/zipfile.class.php');				/* for zipfile */
		
		$zipfile = new zipfile();
		$zipfile->add_dir('../../../mods/'.$_GET['mod_dir'].'/', $_GET['mod_dir'].'/');
		$zipfile->close();
		$zipfile->send_file($_GET['mod_dir']);
		exit;
	}

} else if (isset($_GET['disable']) || isset($_GET['enable']) || isset($_GET['details']) || isset($_GET['uninstall']) || isset($_GET['export'])) {
	$msg->addError('NO_ITEM_SELECTED');
	header('Location: '.$_SERVER['PHP_SELF'] . '?' . $args);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$module_status_bits = $module_type_bits = 0;

if ($_GET['enabled'])  { $module_status_bits += AT_MODULE_STATUS_ENABLED;  }
if ($_GET['disabled']) {	$module_status_bits += AT_MODULE_STATUS_DISABLED; }
if ($_GET['missing'])  {	$module_status_bits += AT_MODULE_STATUS_MISSING;  }
if ($_GET['partially_uninstalled'])  {	$module_status_bits += AT_MODULE_STATUS_PARTIALLY_UNINSTALLED;  }

if ($_GET['core'])     {  $module_type_bits += AT_MODULE_TYPE_CORE;     }
if ($_GET['standard']) {  $module_type_bits += AT_MODULE_TYPE_STANDARD; }
if ($_GET['extra'])    {  $module_type_bits += AT_MODULE_TYPE_EXTRA;    }

if ($module_status_bits == 0) {
	$module_status_bits = AT_MODULE_STATUS_DISABLED | AT_MODULE_STATUS_ENABLED | AT_MODULE_STATUS_MISSING | AT_MODULE_STATUS_PARTIALLY_UNINSTALLED;
	$_GET['enabled'] = $_GET['disabled'] = $_GET['missing'] = $_GET['partially_uninstalled'] = 1;
}

if ($module_type_bits == 0) {
	$module_type_bits = AT_MODULE_TYPE_STANDARD + AT_MODULE_TYPE_EXTRA;
	$_GET['standard'] = $_GET['extra'] = 1;
}


$module_list = $moduleFactory->getModules($module_status_bits, $module_type_bits, $sort = TRUE);
$keys = array_keys($module_list);
$savant->assign('module_list', $module_list);
$savant->assign('keys', $keys);
$savant->display('admin/modules/index.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>