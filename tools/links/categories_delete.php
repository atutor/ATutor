<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'lib/links.inc.php');

if (!manage_links()) {
	$msg->addError('ACCESS_DENIED');
	header('Location: '.AT_BASE_HREF.'links/index.php');
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
		header('Location: '.AT_BASE_HREF.'tools/links/categories.php');
		exit;
	}

	//check if there are sub cats within this cat, or links
	$sql = "SELECT C.cat_id, L.link_id FROM ".TABLE_PREFIX."links_categories C, ".TABLE_PREFIX."links L WHERE C.parent_id=$cat_id OR L.cat_id=$cat_id";
	$result = mysql_query($sql, $db);
	if (mysql_num_rows($result) == 0) {
		$sql = "DELETE FROM ".TABLE_PREFIX."links_categories WHERE owner_id=$owner_id AND owner_type=$owner_type AND cat_id=$cat_id";
		$result = mysql_query($sql, $db);
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