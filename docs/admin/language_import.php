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

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

require(AT_INCLUDE_PATH.'classes/pclzip.lib.php');
require_once(AT_INCLUDE_PATH.'classes/language/LanguageEditor.class.php');
require_once(AT_INCLUDE_PATH.'classes/language/LanguageParser.class.php');

/* to avoid timing out on large files */
set_time_limit(0);

$_SESSION['done'] = 1;

if (!$_FILES['file']['name']) {
	require(AT_INCLUDE_PATH.'header.inc.php'); 
	$errors[]  = AT_ERROR_IMPORTFILE_EMPTY;
	print_errors($errors);
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

if (!is_uploaded_file($_FILES['file']['tmp_name']) || !$_FILES['file']['size']) {
	require(AT_INCLUDE_PATH.'header.inc.php'); 
	$errors[]  = AT_ERROR_LANG_IMPORT_FAILED;
	@unlink($import_path . 'language.csv');
	print_errors($errors);
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

$new_lang = substr($_FILES['file']['name'], -2);
//check to see if the language is already installed
if (isset($available_languages[$new_lang])){
	header('Location: language.php?lang_exists=1'.SEP.'upload_filename='.urlencode($_FILES['file']['name']));
	exit;
}

/* check if ../content/import/ exists */
$import_path = AT_CONTENT_DIR . 'import/';

if (!is_dir($import_path)) {
	if (!@mkdir($import_path, 0700)) {
		require(AT_INCLUDE_PATH.'header.inc.php'); 
		$errors[] = AT_ERROR_IMPORTDIR_FAILED;
		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
	}
}

$import_path = AT_CONTENT_DIR . 'import/';
$archive = new PclZip($_FILES['file']['tmp_name']);
if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path) == 0) {
	exit('Error : ' . $archive->errorInfo(true));
}


$language_xml = @file_get_contents($import_path.'language.xml');

//xml_parser_free($xml_parser);
$languageParser =& new LanguageParser();
$languageParser->parse($language_xml);
$languageEditor =& $languageParser->getLanguageEditor(0);

if ($languageManager->exists($languageEditor->getCode())) {
	require(AT_INCLUDE_PATH.'header.inc.php'); 
	$errors[]  = AT_ERROR_LANG_EXISTS;
	print_errors($errors);
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
} // else:

$languageEditor->import($import_path . 'language_text.sql');

// remove the files:
@unlink($import_path . 'language.xml');
@unlink($import_path . 'language_text.sql');
@unlink($import_path . 'readme.txt');

header('Location: language.php?f='.urlencode_feedback(AT_FEEDBACK_IMPORT_LANG_SUCCESS));
exit;

?>