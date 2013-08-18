<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_GET['redirect_to'])) {
    $_SESSION['redirect_to'] = intval($_GET['redirect_to']);
} else {
    if (!isset($_POST['submit_yes']) && !isset($_POST['submit_no'])) {
        $_SESSION['redirect_to'] = 0;
    }
}
if (isset($_POST['submit_yes'])) {

	$_POST['cid'] = intval($_POST['cid']);

	$result = $contentManager->deleteContent($_POST['cid']);

	unset($_SESSION['s_cid']);
	unset($_SESSION['from_cid']);
		
	$msg->addFeedback('CONTENT_DELETED');
} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
}
if (isset($_POST['submit_yes']) || isset($_POST['submit_no'])) {
    $location = ($_SESSION['redirect_to'] == 1) ? 'mods/_standard/sitemap/sitemap.php' : 'mods/_core/content/index.php';
    header('Location: '.AT_BASE_HREF.$location);
    unset($_SESSION['redirect_to']);
    exit;
}

$_GET['cid'] = intval($_REQUEST['cid']);


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
	
$sql = "SELECT * from %scontent WHERE content_id = '%s'";
$rows_content = queryDB($sql, array(TABLE_PREFIX, $hidden_vars['cid']));

foreach($rows_content as $row){
	$title = $row['title'];
}

$msg->addConfirm(array('DELETE', $title),  $hidden_vars);
$msg->printConfirm();
	
require(AT_INCLUDE_PATH.'footer.inc.php');
?>