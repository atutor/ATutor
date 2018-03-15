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

require_once(AT_INCLUDE_PATH.'classes/pclzip.lib.php');
require_once(AT_INCLUDE_PATH.'../mods/_core/languages/classes/LanguageEditor.class.php');
require_once(AT_INCLUDE_PATH.'../mods/_core/languages/classes/LanguagesParser.class.php');

/* to avoid timing out on large files */
@set_time_limit(0);

$_SESSION['done'] = 1;

if (isset($_POST['submit_import'])){
	require_once(AT_INCLUDE_PATH.'../mods/_core/languages/classes/RemoteLanguageManager.class.php');
	$remoteLanguageManager = new RemoteLanguageManager();
	$language_code = explode("_",$_POST['language']);
	$remoteLanguageManager->import($_POST['language']);
	header('Location: language_import.php');
	exit;
} else if (isset($_POST['submit']) && (!is_uploaded_file($_FILES['file']['tmp_name']) || !$_FILES['file']['size'])) {
	$msg->addError('LANG_IMPORT_FAILED');
} else if (isset($_POST['submit']) && !$_FILES['file']['name']) {
	$msg->addError('IMPORTFILE_EMPTY');
} else if (isset($_POST['submit']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
	$languageManager->import($_FILES['file']['tmp_name']);
	header('Location: ./language_import.php');
	exit;
}

//Get language list from atutor.github.io
$ch = curl_init();
curl_setopt($ch, CURLOPT_USERAGENT,'atutorlangs');
curl_setopt($ch, CURLOPT_URL, "https://api.github.com/users/atutorlangs/repos?per_page=100");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);
$response = json_decode($output, true);

require(AT_INCLUDE_PATH.'header.inc.php');

require_once(AT_INCLUDE_PATH.'../mods/_core/languages/classes/RemoteLanguageManager.class.php'); 
$savant->assign('response', $response);
$savant->assign('remoteLanguageManager', $remoteLanguageManager);
$savant->display('admin/system_preferences/language_import.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>