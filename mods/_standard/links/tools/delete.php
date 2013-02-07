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

define('AT_INCLUDE_PATH', '../../../../include/');
require_once(AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'../mods/_standard/links/lib/links.inc.php');

$linkIndexHeader = sprintf('Location: %smods/_standard/links/tools/index.php', AT_BASE_HREF);

if (!manage_links()) {
	$msg->addError('ACCESS_DENIED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/links/index.php');
	exit;
}

$lid = explode('-', $_REQUEST['lid']);
$link_id = intval($lid[0]);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header($linkIndexHeader);
	exit;
} else if (isset($_POST['submit_yes'])) {

	$row = get_cat_info(intval($_POST['cat_id']));

	if (!links_authenticate($row['owner_type'], $row['owner_id'])) {
		$msg->addError('ACCESS_DENIED');
		header($linkIndexHeader);
		exit;
	}

	queryDB('DELETE FROM %slinks WHERE link_id=%d', array(TABLE_PREFIX, $_POST[link_id]));
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header($linkIndexHeader);
	exit;
}

$_section[0][0] = _AT('delete_link');

require_once(AT_INCLUDE_PATH.'header.inc.php');

	$row = queryDB('SELECT LinkName, cat_id FROM %slinks WHERE link_id=%d', array(TABLE_PREFIX, $link_id), true);
	
	if (empty($row)) {
		$msg->printErrors('LINK_NOT_FOUND');
	} else {
		$hidden_vars['delete_link']  = TRUE;
		$hidden_vars['link_id'] = $link_id;
		$hidden_vars['cat_id'] = $row['cat_id'];
		
		$confirm = array('DELETE_LINK', AT_print($row['LinkName'], 'resource_links.LinkName'));
		$msg->addConfirm($confirm, $hidden_vars);
		$msg->printConfirm();
	}

require_once(AT_INCLUDE_PATH.'footer.inc.php');
?>