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
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_ANNOUNCEMENTS);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/announcements/index.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$_POST['form_news_id'] = intval($_POST['form_news_id']);

	$sql = "DELETE FROM %snews WHERE news_id=%d AND course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $_POST['form_news_id'], $_SESSION['course_id']));
	
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

$_section[0][0] = _AT('delete_announcement');

require(AT_INCLUDE_PATH.'header.inc.php');

	$_GET['aid'] = intval($_GET['aid']); 

	$sql = "SELECT * FROM %snews WHERE news_id=%d AND course_id=%d";
	$row_news = queryDB($sql, array(TABLE_PREFIX, $_GET['aid'], $_SESSION['course_id']), TRUE);
	
	if(count($row_news) == 0){
		$msg->printErrors('ITEM_NOT_FOUND');
	} else {
        $row = $row_news;
		$hidden_vars['delete_news']  = TRUE;
		$hidden_vars['form_news_id'] = $row['news_id'];
		
		$confirm = array('DELETE_NEWS', AT_print($row['title'], 'news.title'));
		$msg->addConfirm($confirm, $hidden_vars);
		
		$msg->printConfirm();
	}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>