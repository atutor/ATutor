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
require(AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'../mods/_standard/links/lib/links.inc.php');

if (!manage_links()) {
	$msg->addError('ACCESS_DENIED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/links/index.php');
	exit;
}

$cat_id = intval($_REQUEST['cat_id']);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: categories.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	/* delete has been confirmed, delete this category */
	$owner_type	= intval($_POST['owner_type']);
	$owner_id	= intval($_POST['owner_id']);
	//OR get_cat_info() again incase data has ben tampered?

	if (!links_authenticate($owner_type, $owner_id)) {
		$msg->addError('ACCESS_DENIED');
		header('Location: '.AT_BASE_HREF.'mods/_standard/links/tools/categories.php');
		exit;
	}

	//check if there are sub cats within this cat, or links
	$result = queryDB('SELECT C.cat_id, L.link_id FROM %slinks_categories C, %slinks L WHERE C.parent_id=%d OR L.cat_id=%d', array(TABLE_PREFIX, TABLE_PREFIX, $cat_id, $cat_id));
	
	if (empty($result)) {
		queryDB("DELETE FROM %slinks_categories WHERE owner_id=%d AND owner_type=%d AND cat_id=%d", array(TABLE_PREFIX, $owner_id, $owner_type, $cat_id));
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	} else {
		$msg->addError('LINK_CAT_NOT_EMPTY');
	}

	header('Location: categories.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

	$row = get_cat_info($cat_id);

	if (empty($row)) {
		$msg->printErrors('ITEM_NOT_FOUND');
	} else {
		$hidden_vars['cat_name']= $row['name'];
		$hidden_vars['cat_id']	= $row['cat_id'];
		$hidden_vars['owner_type']	= $row['owner_type'];
		$hidden_vars['owner_id']	= $row['owner_id'];

		$confirm = array('DELETE_CATEGORY', AT_print($row['name'], 'links_categories.name'));
		$msg->addConfirm($confirm, $hidden_vars);
		
		$msg->printConfirm();
	}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>