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
define('AT_INCLUDE_PATH', '../../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_POLLS);


if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

if (isset($_GET['poll_id'])) {
	$poll_id = intval($_GET['poll_id']);
} else {
	$poll_id = intval($_POST['poll_id']);
}

if ($_POST['edit_poll']) {
	if (trim($_POST['question']) == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('question')));
	}

	if ((trim($_POST['c1']) == '') || (trim($_POST['c2']) == '')) {
		$msg->addError('POLL_QUESTION_MINIMUM');
	}

	if (!$msg->containsErrors()) {
		//$_POST['question'] = $addslashes($_POST['question']);
		//Check if the question has exceeded the words amount - 100, decided in the db
		$_POST['question'] = validate_length($_POST['question'], 100);

		for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++) {
			$trimmed_word = validate_length($_POST['c' . $i], 100);			
			//$trimmed_word = $addslashes($trimmed_word);
			$choices .= "choice$i = '" . $trimmed_word . "',";
		}
		$choices = substr($choices, 0, -1);

		$sql = "UPDATE %spolls SET question='%s', created_date=created_date, $choices WHERE poll_id=%d AND course_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $_POST['question'], $poll_id, $_SESSION['course_id']));
        
        if($result > 0){
		    $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		}
		header('Location: index.php');
		exit;
	}
	for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++) {
		$_POST['c' . $i] = $stripslashes($_POST['c' . $i]);
	}
	$_POST['question'] = $stripslashes($_POST['question']);
}

require(AT_INCLUDE_PATH.'header.inc.php');

	if ($poll_id == 0) {
		$msg->printErrors('ITEM_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$sql = "SELECT * FROM %spolls WHERE poll_id=%d AND course_id=%d";
	$row_poll = queryDB($sql,array(TABLE_PREFIX, $poll_id, $_SESSION['course_id']), TRUE);
	
	if(count($row_poll) == 0){
		$msg->printErrors('ITEM_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

$savant->assign('row', $row_poll);
$savant->display('instructor/polls/edit.tmpl.php');
require (AT_INCLUDE_PATH.'footer.inc.php'); ?>