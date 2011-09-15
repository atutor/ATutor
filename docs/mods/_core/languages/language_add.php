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
admin_authenticate(AT_ADMIN_PRIV_LANGUAGES);
if (!AT_DEVEL_TRANSLATE) { exit; }

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: language.php');
	exit;
} else if (isset($_POST['submit'])) {
	require_once(AT_INCLUDE_PATH . '../mods/_core/languages/classes/LanguageEditor.class.php'); 
	
	if ($languageManager->exists($_POST['code'], $_POST['locale'])) {
		$msg->addError('LANG_EXISTS');
	} else {
		$state = LanguageEditor::addLanguage($_POST, $db);
	}

	if (!$msg->containsErrors() && $state !== FALSE) {
		$msg->addFeedback('LANG_ADDED');
		header('Location: language.php');
		exit;
	} 
}

require(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('admin/system_preferences/language_add.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php');  ?>