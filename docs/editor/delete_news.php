<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

authenticate(AT_PRIV_ANNOUNCEMENTS);

	if ($_POST['submit_no']) {
		$msg->addFeedback('CANCELLED');
		Header('Location: ../index.php');
		exit;
	}

if ($_POST['submit_yes']) {
	$_POST['form_news_id'] = intval($_POST['form_news_id']);

	$sql = "DELETE FROM ".TABLE_PREFIX."news WHERE news_id=$_POST[form_news_id] AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);
		
	$msg->addFeedback('NEWS_DELETED');
	header('Location: ../index.php');
	exit;
}

$_section[0][0] = _AT('delete_announcement');

require(AT_INCLUDE_PATH.'header.inc.php');
?>
<h2><?php echo _AT('delete_announcement'); ?></h2>
<?php

	$_GET['aid'] = intval($_GET['aid']); 

	$sql = "SELECT * FROM ".TABLE_PREFIX."news WHERE news_id=$_GET[aid] AND course_id=$_SESSION[course_id]";

	$result = mysql_query($sql,$db);
	if (mysql_num_rows($result) == 0) {
		$msg->printErrors('ANN_NOT_FOUND');
	} else {
		$row = mysql_fetch_assoc($result);

		$hidden_vars['delete_news']  = TRUE;
		$hidden_vars['form_news_id'] = $row['news_id'];
		
		$confirm = array('DELETE_NEWS', AT_print($row['title'], 'news.title'));
		$msg->addConfirm($confirm, $hidden_vars);
		
		$msg->printConfirm();
	}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>