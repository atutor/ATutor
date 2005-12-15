<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['submit_yes'])) {

	$_POST['cid'] = intval($_POST['cid']);

	$result = $contentManager->deleteContent($_POST['cid']);

	unset($_SESSION['s_cid']);
	unset($_SESSION['from_cid']);
		
	$msg->addFeedback('CONTENT_DELETED');
	header('Location: '.$_base_href.'tools/content/index.php');
	exit;
} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'tools/content/index.php');
	exit;
}

$_GET['cid'] = intval($_REQUEST['cid']);

$path	= $contentManager->getContentPath($cid);
require(AT_INCLUDE_PATH.'header.inc.php');

if ($_GET['cid'] == 0) {
	$msg->printErrors('ID_ZERO');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$children = $contentManager->getContent($_GET['cid']);

$hidden_vars['cid'] = $_GET['cid'];

if (is_array($children) && (count($children)>0) ) {
	$msg->addConfirm('SUB_CONTENT_DELETE', $hidden_vars);
	$msg->addConfirm('GLOSSARY_REMAINS', $hidden_vars);
} else {
	$msg->addConfirm('GLOSSARY_REMAINS', $hidden_vars);
}
	
$msg->addConfirm('DELETE_CONTENT', $hidden_vars);
$msg->printConfirm();
	
require(AT_INCLUDE_PATH.'footer.inc.php');
?>