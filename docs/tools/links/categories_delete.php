<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
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
authenticate(AT_PRIV_LINKS);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: categories.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	/* delete has been confirmed, delete this category */
	$cat_id	= intval($_POST['cat_id']);

	if (!is_array($categories[$cat_id]['children'])) {
		$sql = "DELETE FROM ".TABLE_PREFIX."resource_categories WHERE course_id=$_SESSION[course_id] AND CatID=$cat_id";
		$result = mysql_query($sql, $db);

		$msg->addFeedback('CAT_DELETED');
		header('Location: categories.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

	$_GET['cat_id'] = intval($_GET['cat_id']); 

	$sql = "SELECT * FROM ".TABLE_PREFIX."resource_categories WHERE course_id=$_SESSION[course_id] AND CatID=$_GET[cat_id]";
	$result = mysql_query($sql,$db);

	if (mysql_num_rows($result) == 0) {
		$msg->printErrors('CAT_NOT_FOUND');
	} else {
		$row = mysql_fetch_assoc($result);
		
		$hidden_vars['cat_name']= $row['CatName'];
		$hidden_vars['cat_id']	= $row['CatID'];

		$confirm = array('DELETE_CATEGORY', AT_print($row['CatName'], 'resource_categories.cat_name'));
		$msg->addConfirm($confirm, $hidden_vars);
		
		$msg->printConfirm();
	}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>