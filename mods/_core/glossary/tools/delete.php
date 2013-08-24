<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_GLOSSARY);

if ($_POST['submit_no']) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

if ($_POST['submit_yes']) {

	$_POST['gid'] = intval($_POST['gid']);

	$sql = "DELETE FROM %sglossary WHERE word_id=%d AND course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $_POST['gid'], $_SESSION['course_id']));

	$sql = "UPDATE %sglossary SET related_word_id=0 WHERE related_word_id=%d AND course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $_POST['gid'], $_SESSION['course_id']));
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php');
	exit;
} else if ($_POST['submit_no']) {

	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['gid'] = intval($_GET['gid']);

if ($_GET['gid'] == 0) {
	$msg->printErrors('ITEM_NOT_FOUND');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$hidden_vars['word'] = $_GET['t'];
$hidden_vars['gid']  = $_GET['gid'];

$sql = "SELECT * from %sglossary WHERE word_id = %d";
$rows_g = queryDB($sql, array(TABLE_PREFIX, $hidden_vars['gid']));

foreach($rows_g as $row){
	$title = $row['word'];
}
		
$msg->addConfirm(array('DELETE', htmlentities_utf8($title)),  $hidden_vars);
$msg->addConfirm('GLOSSARY_REMAINS', $hidden_vars);
	
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>