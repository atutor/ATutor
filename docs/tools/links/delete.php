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
	header('Location: '.$_base_href.'tools/links/index.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$_POST['form_link_id'] = intval($_POST['form_link_id']);

	$sql = "DELETE FROM ".TABLE_PREFIX."resource_links WHERE LinkID=$_POST[form_link_id]";
	$result = mysql_query($sql, $db);
	
	$msg->addFeedback('LINK_DELETED');
	header('Location: '.$_base_href.'tools/links/index.php');
	exit;
}

$_section[0][0] = _AT('delete_link');

require(AT_INCLUDE_PATH.'header.inc.php');

	$_GET['lid'] = intval($_GET['lid']); 

	$sql = "SELECT * FROM ".TABLE_PREFIX."resource_links WHERE LinkID=$_GET[lid]";

	$result = mysql_query($sql,$db);
	if (mysql_num_rows($result) == 0) {
		$msg->printErrors('LINK_NOT_FOUND');
	} else {
		$row = mysql_fetch_assoc($result);

		$hidden_vars['delete_link']  = TRUE;
		$hidden_vars['form_link_id'] = $row['LinkID'];
		
		$confirm = array('DELETE_LINK', AT_print($row['LinkName'], 'resource_links.LinkName'));
		$msg->addConfirm($confirm, $hidden_vars);
		
		$msg->printConfirm();
	}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>