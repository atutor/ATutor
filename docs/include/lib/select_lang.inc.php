<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/* [Modified version of the phpMyAdmin Language Loading File]   */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

/* This file initialises the correct language for this session.
 * By the end of this script $_SESSION['lang'] will correctly be
 * set to the desired language and the correct Content-Type header
 * would have been set.
 *
 * If the desired language is not available then it will default
 * to the one specified in the config file.
 */

if (!defined('AT_INCLUDE_PATH')) { exit; }

require(AT_INCLUDE_PATH . 'classes/Language/LanguageManager.class.php');
$languageManager =& new LanguageManager();
$myLang =& $languageManager->getMyLanguage();
if ($myLang === FALSE) {
	echo 'There are no languages installed!';
	exit;
}


$myLang->saveToSession();

$myLang->sendContentTypeHeader();


/* set right-to-left language */
$rtl = '';
if ($myLang->isRTL()) {
	$rtl = 'rtl_'; /* basically the prefix to a rtl variant directory/filename. rtl_tree */
}

if (AT_DEVEL_TRANSLATE) {
	require(AT_INCLUDE_PATH . 'classes/Language/LanguageEditor.class.php');
	$langEditor =& new LanguageEditor($myLang);
	//$langEditor->addMissingTerm('mooo');
}

?>