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

$lid = explode('-', $_REQUEST['lid']);
$link_id = intval($lid[0]);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/links/tools/index.php');
	exit;
} else if (isset($_POST['submit_yes'])) {

	$row = get_cat_info(intval($_POST['cat_id']));

	if (!links_authenticate($row['owner_type'], $row['owner_id'])) {
		$msg->addError('ACCESS_DENIED');
		header('Location: '.AT_BASE_HREF.'mods/_standard/links/tools/index.php');
		exit;
	}

	$sql = "DELETE FROM ".TABLE_PREFIX."links WHERE link_id=$_POST[link_id]";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.AT_BASE_HREF.'mods/_standard/links/tools/index.php');
	exit;
}

$_section[0][0] = _AT('delete_link');

require(AT_INCLUDE_PATH.'header.inc.php');

	$sql = "SELECT LinkName, cat_id FROM ".TABLE_PREFIX."links WHERE link_id=$link_id";

	$result = mysql_query($sql,$db);
	if (mysql_num_rows($result) == 0) {
		$msg->printErrors('LINK_NOT_FOUND');
	} else {
		$row = mysql_fetch_assoc($result);

		$hidden_vars['delete_link']  = TRUE;
		$hidden_vars['link_id'] = $link_id;
		$hidden_vars['cat_id'] = $row['cat_id'];
		
		$confirm = array('DELETE_LINK', AT_print($row['LinkName'], 'resource_links.LinkName');
		$msg->addConfirm($confirm, $hidden_vars);
		
		$msg->printConfirm();
	}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>