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


	if ($_POST['submit']) {

		$_POST['cid'] = intval($_POST['cid']);

		$result = $contentManager->deleteContent($_POST['cid']);

		unset($_SESSION['s_cid']);
		unset($_SESSION['from_cid']);
		
		$msg->addFeedback('CONTENT_DELETED');
		Header('Location: ../index.php');
		exit;
	} else if ($_POST['submit_no']) {
		$msg->addFeedback('CANCELLED');
		Header('Location: ../index.php?cid='.$_POST['cid']);
		exit;
	}

	$_section[0][0] = _AT('delete_content');

	$_GET['cid'] = intval($_REQUEST['cid']);

	$path	= $contentManager->getContentPath($cid);
	require(AT_INCLUDE_PATH.'header.inc.php');

	echo '<h3>'._AT('delete_content').'</h3>';

	if ($_GET['cid'] == 0) {
		$msg->printErrors('ID_ZERO');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$children = $contentManager->getContent($_GET['cid']);

	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	echo '<input type="hidden" name="cid" value="'.$_GET['cid'].'">';
	echo '<p>';

	if (is_array($children) && (count($children)>0) ) {
		$msg->addWarning('SUB_CONTENT_DELETE');
		$msg->addWarning('GLOSSARY_REMAINS');
	} else {
		$msg->addWarning('GLOSSARY_REMAINS');
	}
	
	$msg->add('DELETE_CONTENT');
	$msg->printWarnings();
	
	echo '<input type="submit" name="submit" value="'._AT('yes_delete').'" class="button">';
	echo ' - <input type="submit" name="submit_no" value="'._AT('no_cancel').'" class="button">';

	echo '</form>';
	echo '</p>';


	require(AT_INCLUDE_PATH.'footer.inc.php');
?>