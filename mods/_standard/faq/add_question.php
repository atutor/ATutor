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
tool_origin();
if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
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
		//$_POST['answer'] = validate_length($_POST['answer'], 250);

		// check that this topic_id belongs to this course:

		$sql    = "SELECT topic_id FROM %sfaq_topics WHERE topic_id=%d AND course_id=%d";
		$rows_topics = queryDB($sql, array(TABLE_PREFIX, $_POST['topic_id'], $_SESSION['course_id']), TRUE);
		
	    if(count($rows_topics) > 0){

			$sql	= "INSERT INTO %sfaq_entries VALUES (NULL, %d, NOW(), 1, '%s', '%s')";
			$result = queryDB($sql,array(TABLE_PREFIX, $_POST['topic_id'], $_POST['question'], $_POST['answer']));
		}
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
		exit;
	}
}

$onload = 'document.form.topic.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

	$sql	= "SELECT name, topic_id FROM %sfaq_topics WHERE course_id=%d ORDER BY name";
	$rows_topics = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));

    if(count($rows_topics) == 0){
		$msg->printErrors('NO_FAQ_TOPICS');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

$savant->assign('rows_topics', $rows_topics);
$savant->display('instructor/faq/add_question.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>