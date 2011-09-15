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

require_once(AT_INCLUDE_PATH.'../mods/_core/languages/classes/LanguageEditor.class.php');
require_once(AT_INCLUDE_PATH.'../mods/_core/languages/classes/LanguagesParser.class.php');

if ( (isset($_POST['delete']) || isset($_POST['export']) || isset($_POST['edit'])) && !isset($_POST['id'])){
	$msg->addError('NO_ITEM_SELECTED');
} else if (isset($_POST['delete'])) {
	// check if this language is the only one that exists:
	if ($languageManager->getNumLanguages() == 1) {
		$msg->addError('LAST_LANGUAGE');
	} else {
		header('Location: language_delete.php?lang_code='.$_POST['id']);
		exit;
	}
} else if (isset($_POST['export'])) {
	$language = $languageManager->getLanguage($_POST['id']);
	if ($language === FALSE) {
		$msg->addError('ITEM_NOT_FOUND');
	} else {
		$languageEditor = new LanguageEditor($language);
		$languageEditor->export();
	}
} else if (isset($_POST['edit'])) {
	header('Location: language_edit.php?lang_code='.$_POST['id']);
	exit;
}

if (AT_DEVEL_TRANSLATE == 1) { 
	$msg->addWarning('TRANSLATE_ON');	
}

require(AT_INCLUDE_PATH.'header.inc.php');

$savant->display('admin/system_preferences/language.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>