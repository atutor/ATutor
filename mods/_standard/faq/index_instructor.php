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

if (isset($_GET['edit'], $_GET['item'])) {
	$item = intval($_GET['item']);
	if (substr($_GET['item'], -1) == 'q') {
		header('Location: edit_question.php?id=' . $item);
	} else {
		header('Location: edit_topic.php?id=' . $item);
	}
	exit;
} else if (isset($_GET['delete'], $_GET['item'])) {
	$item = intval($_GET['item']);

	if (substr($_GET['item'], -1) == 'q') {
		header('Location: delete_question.php?id=' . $item);
	} else {
		header('Location: delete_topic.php?id=' . $item);
	}
	exit;
} else if (!empty($_GET)) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$counter = 1;

$sql	 = "SELECT name, topic_id FROM %sfaq_topics WHERE course_id=%d ORDER BY name";
$rows_topics  = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
$faq_topics = array(); 
foreach($rows_topics as $row){
	
	$faq_topics[$row['topic_id']] = $row;

	$entry_sql = "SELECT * FROM %sfaq_entries WHERE topic_id=%d ORDER BY question";
	$entries = queryDB($entry_sql,  array(TABLE_PREFIX, $row['topic_id']));	
	
	foreach($entries as $entry_result)
	{
		$faq_topics[$row['topic_id']]['entry_rows'][] = $entry_result;
	}
}


//NOT SURE WHY THIS IF IS HERE
//if ($_SESSION['id'] > 0){
//	$savant->assign('faq_topics', $faq_topics);	
//}
//else {
	$savant->assign('faq_topics', $faq_topics);	
	$savant->display('instructor/faq/index_instructor.tmpl.php');
//}

require(AT_INCLUDE_PATH.'footer.inc.php');  ?>