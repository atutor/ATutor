<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_RSS);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$feed_id = intval($_POST['fid']);

	//delete feed
	$sql	= "DELETE FROM %sfeeds WHERE feed_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $feed_id));
	//delete feed title from lang
    ///// THESE QUERIES DO NOT APPEAR TO BE USED, commented in 2.2
	//$sql	= "DELETE FROM %slanguage_text WHERE term='%d_rss_title'";
	//$result = queryDB($sql, array(TABLE_PREFIX, $feed_id));
	
	//delete files
	@unlink(AT_CONTENT_DIR.'/feeds/'.$feed_id.'_rss.cache');
	@unlink(AT_CONTENT_DIR.'/feeds/'.$feed_id.'_rss_title.cache');
	@unlink(AT_CONTENT_DIR.'/feeds/'.$feed_id.'_rss.inc.php');

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

	$feed_id = intval($_GET['fid']);

	$sql	= "SELECT * FROM %sfeeds WHERE feed_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $feed_id), TRUE);

	if (count($row) == 0) {
		$msg->addError('FEED_NOT_FOUND');
		$msg->printErrors();
	} else {
		$hidden_vars['delete_feed'] = TRUE;
		$hidden_vars['fid'] = $feed_id;

		$title = file_get_contents(AT_CONTENT_DIR.'/feeds/'.$feed_id.'_rss_title.cache');
		$msg->addConfirm(array('DELETE_FEED', $title), $hidden_vars);
		$msg->printConfirm();
	}

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>