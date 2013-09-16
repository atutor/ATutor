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

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	Header('Location: index_instructor.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$_POST['id'] = intval($_POST['id']);

	// check that this topic_id belongs to this course:
	$sql = "DELETE FROM %sfaq_topics WHERE topic_id=%d AND course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $_POST['id'], $_SESSION['course_id']));
	
	if($result > 0){

		$sql = "DELETE FROM %sfaq_entries WHERE topic_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $_POST['id']));
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index_instructor.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');


$_GET['id'] = intval($_GET['id']); 

$sql = "SELECT name, topic_id FROM ".TABLE_PREFIX."faq_topics WHERE topic_id=$_GET[id]";
$row = queryDB($sql,array(TABLE_PREFIX, $_GET['id']));

if(count($row) > 0){

	$hidden_vars['id'] = $_GET['id'];

	$confirm = array('DELETE_FAQ_TOPIC', AT_print($row['name'], 'faqs.topic'));
	$msg->addConfirm($confirm, $hidden_vars);
	$msg->printConfirm();
} else {
	$msg->addError('ITEM_NOT_FOUND');
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>