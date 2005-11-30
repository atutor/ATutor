<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_LANGUAGES);

require(AT_INCLUDE_PATH.'classes/pclzip.lib.php');
require_once(AT_INCLUDE_PATH.'classes/Language/LanguageEditor.class.php');
require_once(AT_INCLUDE_PATH.'classes/Language/LanguagesParser.class.php');

/* to avoid timing out on large files */
@set_time_limit(0);


if (isset($_POST['submit_import'])) {
	require_once(AT_INCLUDE_PATH.'classes/Language/RemoteLanguageManager.class.php');
	$remoteLanguageManager =& new RemoteLanguageManager();
	$filename = AT_CONTENT_DIR . 'import/ATutor_language_file.zip';
	$remoteLanguageManager->fetchLanguage($_POST['language'], $filename);

	$_FILES['file']['name']     = 'ATutor_language_file.zip';
	$_FILES['file']['tmp_name'] = $filename;

} else if (!is_uploaded_file($_FILES['file']['tmp_name']) || !$_FILES['file']['size']) {
	$_SESSION['done'] = 1;
	$msg->addError('LANG_IMPORT_FAILED');
	header('Location: language.php');
	exit;
}

$_SESSION['done'] = 1;

if (!$_FILES['file']['name']) {
	$msg->addError('IMPORTFILE_EMPTY');
	header('Location: language.php');
	exit;
}

/* check if ../content/import/ exists */
$import_path = AT_CONTENT_DIR . 'import/';

if (!is_dir($import_path)) {
	if (!@mkdir($import_path, 0700)) {
		$msg->addError('IMPORTDIR_FAILED');
		header('Location: language.php');
		exit;
	}
}

$import_path = AT_CONTENT_DIR . 'import/';
$archive = new PclZip($_FILES['file']['tmp_name']);
if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path) == 0) {
	exit('Error : ' . $archive->errorInfo(true));
}

$language_xml = @file_get_contents($import_path.'language.xml');

$languageParser =& new LanguageParser();
$languageParser->parse($language_xml);
$languageEditor =& $languageParser->getLanguageEditor(0);

if (($languageEditor->getAtutorVersion() != VERSION) 
	&& (!defined('AT_DEVEL_TRANSLATE') || !AT_DEVEL_TRANSLATE)) 
	{
		$msg->addError('LANG_WRONG_VERSION');
}

if (($languageEditor->getStatus() != AT_LANG_STATUS_PUBLISHED) 
	&& ($languageEditor->getStatus() != AT_LANG_STATUS_COMPLETE) 
	&& (!defined('AT_DEVEL_TRANSLATE') || !AT_DEVEL_TRANSLATE)) 
	{
		$msg->addError('LANG_NOT_COMPLETE');
}

if ($languageManager->exists($languageEditor->getCode(), $languageEditor->getLocale())) {
	$msg->addError('LANG_EXISTS');
}

if (!$msg->containsErrors()) {
	$languageEditor->import($import_path . 'language_text.sql');
	$msg->addFeedback('IMPORT_LANG_SUCCESS');
}


// remove the files:
@unlink($import_path . 'language.xml');
@unlink($import_path . 'language_text.sql');
@unlink($import_path . 'readme.txt');
@unlink($_FILES['file']['tmp_name']);

header('Location: language.php');
exit;

?>