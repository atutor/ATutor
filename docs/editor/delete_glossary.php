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
	
	if ($_POST['submit_no']) {
		$msg->addFeedback('CANCELLED');
		header('Location: '.$_base_href.'glossary/index.php');
		exit;
	}

	if ($_POST['submit_yes']) {

		$_POST['gid'] = intval($_POST['gid']);

		$sql = "DELETE FROM ".TABLE_PREFIX."glossary WHERE word_id=$_POST[gid] AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);

		$sql = "UPDATE ".TABLE_PREFIX."glossary SET related_word_id=0 WHERE related_word_id=$_POST[gid] AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);

		$msg->addFeedback('GLOSSARY_DELETE2');
		header('Location: ../glossary/index.php');
		exit;
	} else if ($_POST['submit_no']) {

		$msg->addFeedback('CANCELLED');
		header('Location: ../glossary/index.php');
		exit;
	}

	$_section[0][0] = _AT('delete_this_term1');

	require(AT_INCLUDE_PATH.'header.inc.php');

	echo '<h3>'._AT('delete_this_term1').'</h3>';

	$_GET['gid'] = intval($_GET['gid']);

	if ($_GET['gid'] == 0) {
		$msg->printErrors('GLOS_ID_MISSING');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$hidden_vars['word'] = $_GET['t'];
	$hidden_vars['gid']  = $_GET['gid'];
			
	$msg->addConfirm('GLOSSARY_REMAINS2', $hidden_vars);
	$msg->addConfirm('GLOSSARY_DELETE',  $hidden_vars);
		
	$msg->printConfirm();

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>