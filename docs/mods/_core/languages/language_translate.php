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

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_LANGUAGES);

if (isset($_POST['svn_submit'])) {
	$languageManager->liveImport($addslashes($_POST['import_lang']));
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');


$button_state = '';
if (!defined('AT_DEVEL_TRANSLATE') || !AT_DEVEL_TRANSLATE) {
	$button_state = 'disabled="disabled"';
}

 
$savant->display('admin/system_preferences/language_translate.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>