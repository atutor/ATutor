<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: language_delete.php 8901 2009-11-11 19:10:19Z cindy $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_LANGUAGES);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	//shozub -- is this supposed to be lang_codeinstead of delete_lang???
	header('Location: language.php?lang_code='.$_POST['delete_lang']);
	exit;
}

if (isset($_POST['submit_yes'])) {
	require_once(AT_INCLUDE_PATH . '../mods/_core/languages/classes/LanguageEditor.class.php');

	$lang = $languageManager->getLanguage($_POST['lang_code']);
	$languageEditor = new LanguageEditor($lang);
	$languageEditor->deleteLanguage();

	$msg->addFeedback('LANG_DELETED');
	header('Location: language.php');
	exit;
}


$language = $languageManager->getLanguage($_GET['lang_code']);
if ($language === FALSE) {
	$msg->addError('ITEM_NOT_FOUND'); // Originally AT_LANG_NOT_FOUND, make error code

	header('Location: language.php?lang_code='.$_POST['delete_lang']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$hidden_vars['lang_code'] = $_GET['lang_code'];

$confirm = array('DELETE_LANG', $language->getEnglishName());
$msg->addConfirm($confirm, $hidden_vars);
$msg->printConfirm();
	
require(AT_INCLUDE_PATH.'footer.inc.php'); 

?>