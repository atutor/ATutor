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

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/tinymce.inc.php');

authenticate(AT_PRIV_ANNOUNCEMENTS);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/announcements/index.php');
	exit;
} else if ($_POST['edit_news']) {
	$_POST['title'] = trim($_POST['title']);
	$_POST['body_text']  = trim($_POST['body_text']);
	$_POST['aid']	= intval($_POST['aid']);
	$_POST['formatting']	= intval($_POST['formatting']);

	if (($_POST['title'] == '') && ($_POST['body_text'] == '')) {
		$msg->addError('ANN_BOTH_EMPTY');
	}

	if (!$msg->containsErrors() && isset($_POST['submit'])) {

		//Check if the title has exceeded the DB length, 100
		$_POST['title'] = validate_length($_POST['title'], 100);

		$sql = "UPDATE %snews SET title='%s', body='%s', formatting=%d, date=date WHERE news_id=%d AND course_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $_POST['title'], $_POST['body_text'], $_POST['formatting'], $_POST['aid'], $_SESSION['course_id']));

		/* update announcement RSS: */
		if (file_exists(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS1.0.xml')) {
			@unlink(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS1.0.xml');
		}
		if (file_exists(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS2.0.xml')) {
			@unlink(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS2.0.xml');
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
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

if ((!$_POST['setvisual'] && $_POST['settext']) || !$_GET['setvisual']){
	$onload = 'document.form.title.focus();';
}

require(AT_INCLUDE_PATH.'header.inc.php');

if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']) {
	load_editor();
}

if (isset($_GET['aid'])) {
	$aid = intval($_GET['aid']);
} else {
	$aid = intval($_POST['aid']);
}

if ($aid == 0) {
	$msg->printErrors('ITEM_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$sql = "SELECT * FROM %snews WHERE news_id=%d AND course_id=%d";
$row = queryDB($sql,array(TABLE_PREFIX, $aid, $_SESSION['course_id']), TRUE);

if(count($row) == 0){
	$msg->printErrors('ITEM_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$_POST['formatting'] = intval($row['formatting']);
$savant->assign('row', $row);
$savant->display('instructor/announcements/edit_news.tmpl.php');
require (AT_INCLUDE_PATH.'footer.inc.php'); ?>