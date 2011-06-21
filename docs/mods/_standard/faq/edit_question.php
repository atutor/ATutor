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

authenticate(AT_PRIV_FAQ);


if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index_instructor.php');
	exit;
} 

if (isset($_GET['id'])) {
	$id = intval($_GET['id']);
} else {
	$id = intval($_POST['id']);
}

if (isset($_POST['submit'])) {
	$_POST['question'] = trim($_POST['question']);
	$_POST['answer'] = trim($_POST['answer']);

	$missing_fields = array();
	
	if (!$_POST['question']) {
		$missing_fields[] = _AT('question');
	}

	if (!$_POST['answer']) {
		$missing_fields[] = _AT('answer');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) {
		$_POST['question'] = $addslashes($_POST['question']);
		$_POST['answer'] = $addslashes($_POST['answer']);
		$_POST['topic_id'] = intval($_POST['topic_id']);
		//These will truncate the content of the length to 240 as defined in the db.
		$_POST['question'] = validate_length($_POST['question'], 250);
		$_POST['answer'] = validate_length($_POST['answer'], 250);

		$sql = "UPDATE ".TABLE_PREFIX."faq_entries SET question='$_POST[question]', answer='$_POST[answer]', topic_id=$_POST[topic_id] WHERE entry_id=$id";
		$result = mysql_query($sql,$db);

		$msg->addFeedback('QUESTION_UPDATED');
		header('Location: index_instructor.php');
		exit;
	}
}
$onload = 'document.form.topic.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

if ($id == 0) {
	$msg->printErrors('ITEM_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$sql = "SELECT * FROM ".TABLE_PREFIX."faq_entries WHERE entry_id=$id";
$result = mysql_query($sql,$db);
if (!($row = mysql_fetch_assoc($result))) {
	$msg->printErrors('ITEM_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}


$sql	= "SELECT name, topic_id FROM ".TABLE_PREFIX."faq_topics WHERE course_id=$_SESSION[course_id] ORDER BY name";
$result = mysql_query($sql, $db);
$num_topics = mysql_num_rows($result);
if (!$num_topics) {
	$msg->printErrorS('NO_FAQ_TOPICS');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$sql	= "SELECT name, topic_id FROM ".TABLE_PREFIX."faq_topics WHERE course_id=$_SESSION[course_id] ORDER BY name";
$result = mysql_query($sql, $db);
$faq_topics = array();
while ($topic_row = mysql_fetch_assoc($result)){
	$faq_topics[] = $topic_row;
}
				


$savant->assign('row', $row);
$savant->assign('result', $result);
$savant->assign('faq_topics', $faq_topics);
$savant->display('instructor/faq/edit_question.tmpl.php');
require (AT_INCLUDE_PATH.'footer.inc.php'); ?>