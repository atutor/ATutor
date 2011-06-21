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
} else if (isset($_POST['submit'])) {
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
		$_POST['answer']   = $addslashes($_POST['answer']);
		$_POST['topic_id'] = intval($_POST['topic_id']);
		//These will truncate the content of the length to 240 as defined in the db.
		$_POST['question'] = validate_length($_POST['question'], 250);
		$_POST['answer'] = validate_length($_POST['answer'], 250);

		// check that this topic_id belongs to this course:
		$sql    = "SELECT topic_id FROM ".TABLE_PREFIX."faq_topics WHERE topic_id=$_POST[topic_id] AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);
		if ($row = mysql_fetch_assoc($result)) {
			$sql	= "INSERT INTO ".TABLE_PREFIX."faq_entries VALUES (NULL, $_POST[topic_id], NOW(), 1, '$_POST[question]', '$_POST[answer]')";
			$result = mysql_query($sql,$db);
		}
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: index_instructor.php');
		exit;
	}
}

$onload = 'document.form.topic.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

	$sql	= "SELECT name, topic_id FROM ".TABLE_PREFIX."faq_topics WHERE course_id=$_SESSION[course_id] ORDER BY name";
	$result = mysql_query($sql, $db);
	$num_topics = mysql_num_rows($result);
	if (!$num_topics) {
		$msg->printErrors('NO_FAQ_TOPICS');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
$savant->assign('result', $result);
$savant->display('instructor/faq/add_question.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>