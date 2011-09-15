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
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/tinymce.inc.php');

authenticate(AT_PRIV_ANNOUNCEMENTS);
/*
if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$content_base_href = 'content/' . $_SESSION['course_id'] . '/';
} */

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/announcements/index.php');
	exit;
} 

if ((!$_POST['setvisual'] && $_POST['settext']) || !$_GET['setvisual']){
	$onload = 'document.form.title.focus();';
}

if (isset($_POST['add_news'])&& isset($_POST['submit'])) {
	$_POST['formatting'] = intval($_POST['formatting']);
	$_POST['title'] = trim($_POST['title']);
	$_POST['body_text'] = trim($_POST['body_text']);
	
	$missing_fields = array();

	if (!$_POST['body_text']) {
		$missing_fields[] = _AT('body');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors() && (!isset($_POST['setvisual']) || isset($_POST['submit']))) {

		$_POST['formatting']  = intval($_POST['formatting']);
		$_POST['title']  = $addslashes($_POST['title']);
		$_POST['body_text']  = $addslashes($_POST['body_text']);

		//The following checks if title length exceed 100, defined by DB structure
		$_POST['title'] = validate_length($_POST['title'], 100);

		$sql	= "INSERT INTO ".TABLE_PREFIX."news VALUES (NULL, $_SESSION[course_id], $_SESSION[member_id], NOW(), $_POST[formatting], '$_POST[title]', '$_POST[body_text]')";
		mysql_query($sql, $db);
	
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		/* update announcement RSS: */
		if (file_exists(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS1.0.xml')) {
			@unlink(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS1.0.xml');
		}
		if (file_exists(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS2.0.xml')) {
			@unlink(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS2.0.xml');
		}

		header('Location: '.AT_BASE_HREF.'mods/_standard/announcements/index.php');
		exit;
	}
}

if (!isset($_REQUEST['setvisual']) && !isset($_REQUEST['settext'])) {
	if ($_SESSION['prefs']['PREF_CONTENT_EDITOR'] == 1) {
		$_POST['formatting'] = 1;
		$_REQUEST['settext'] = 0;
		$_REQUEST['setvisual'] = 0;

	} else if ($_SESSION['prefs']['PREF_CONTENT_EDITOR'] == 2) {
		$_POST['formatting'] = 1;
		$_POST['settext'] = 0;
		$_POST['setvisual'] = 1;

	} else { // else if == 0
		$_POST['formatting'] = 0;
		$_REQUEST['settext'] = 0;
		$_REQUEST['setvisual'] = 0;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']) {
	load_editor();
}
$msg->printErrors();
$savant->display('instructor/announcements/add_news.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>