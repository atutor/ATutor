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
$sql	 = "SELECT name, topic_id FROM ".TABLE_PREFIX."faq_topics WHERE course_id=$_SESSION[course_id] ORDER BY name";
$result  = mysql_query($sql, $db);

$faq_topics = array(); 
while ($row = mysql_fetch_assoc($result)) { 
	
	$faq_topics[$row['topic_id']] = $row;
	$entry_sql = "SELECT * FROM ".TABLE_PREFIX."faq_entries WHERE topic_id=$row[topic_id] ORDER BY question";
	$entries = mysql_query($entry_sql, $db);
	
	while ($entry_result = mysql_fetch_assoc($entries))
	{
		$faq_topics[$row['topic_id']]['entry_rows'][] = $entry_result;
	}
}

$savant->assign('faq_topics', $faq_topics);
$savant->display('instructor/faq/index_instructor.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php');  ?>