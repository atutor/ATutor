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

require_once(AT_INCLUDE_PATH . '../mods/_core/languages/classes/LanguageEditor.class.php'); 

$lang = $languageManager->getLanguage($_GET['lang_code']);
if ($lang === FALSE) {
	require(AT_INCLUDE_PATH.'header.inc.php'); 
	echo '<h3>'._AT('edit_language').'</h3>';
	$msg->addError('NO_LANGUAGE');
	
	$msg->printAll();

	require(AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: language.php');
	exit;
} else if (isset($_POST['submit'])) {
	$languageEditor = new LanguageEditor($_GET['lang_code']);
	$state = $languageEditor->updateLanguage($_POST, $languageManager->exists($_POST['code'], $_POST['locale']));

	if (!$msg->containsErrors() && $state !== FALSE) {
		$msg->addFeedback('LANG_UPDATED');
		header('Location: language.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php'); 


$msg->printAll();

if (!isset($_POST['submit'])) {
	$_POST['code']         = $lang->getParentCode();
	$_POST['locale']       = $lang->getLocale();
	$_POST['charset']      = $lang->getCharacterSet();
	$_POST['direction']    = $lang->getDirection();
	$_POST['reg_exp']      = $lang->getRegularExpression();
	$_POST['native_name']  = $lang->getNativeName();
	$_POST['english_name'] = $lang->getEnglishName();
}

$savant->assign('lang', $lang);
$savant->display('admin/system_preferences/language_edit.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php');  ?>