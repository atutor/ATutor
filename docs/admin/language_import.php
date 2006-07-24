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

$_SESSION['done'] = 1;

if (isset($_POST['submit_import'])) {
	require_once(AT_INCLUDE_PATH.'classes/Language/RemoteLanguageManager.class.php');
	$remoteLanguageManager =& new RemoteLanguageManager();
	$remoteLanguageManager->import($_POST['language']);
} else if (!is_uploaded_file($_FILES['file']['tmp_name']) || !$_FILES['file']['size']) {
	$_SESSION['done'] = 1;
	$msg->addError('LANG_IMPORT_FAILED');
} else if (!$_FILES['file']['name']) {
	$msg->addError('IMPORTFILE_EMPTY');
} else if (is_uploaded_file($_FILES['file']['tmp_name'])) {
	$languageManager->import($_FILES['file']['tmp_name']);
}

header('Location: language.php');
exit;
?>