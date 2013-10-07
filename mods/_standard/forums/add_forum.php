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
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FORUMS);

tool_origin();

if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
		exit;
}

if ($_POST['add_forum'] && (authenticate(AT_PRIV_FORUMS, AT_PRIV_RETURN))) {
	if ($_POST['title'] == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('title')));
	} else {
		$_POST['title'] = validate_length($_POST['title'], 60);
	}

	if (!$msg->containsErrors()) {
		require(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');
		add_forum($_POST);
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		//header('Location: '.AT_BASE_HREF.'mods/_standard/forums/index.php');
		//header('Location: '.AT_BASE_HREF.'mods/_standard/forums/forum/list.php');
		$return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
		exit;
		exit;
	}
}

$onload = 'document.form.title.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

$savant->display('instructor/forums/add_forum.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>