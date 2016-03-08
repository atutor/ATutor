<?php	
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH . 'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_RSS);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header("Location: index.php");
	exit;
} else if (isset($_POST['submit'])) {
	$missing_fields = array();

	if (trim($_POST['title']) == '') {
		$missing_fields[] = _AT('title');
	}
	if (trim($_POST['url']) == '') {
		$missing_fields[] = _AT('url');
	}
	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) {
		$output = make_cache_file(0);
		if (!isset($output) || empty($output)) {
			$msg->addError('FEED_NO_CONTENT');
		}
	}

	if ($msg->containsErrors()) {
		unset($_POST['confirm']);
	}

	$hidden_vars['new'] = '1';
    $hidden_vars['title'] = $_POST['title'];
    $hidden_vars['url'] = $_POST['url'];
    $hidden_vars['output'] = $output;
    
	require (AT_INCLUDE_PATH.'header.inc.php'); 
	$msg->addConfirm('ADD_FEED', $hidden_vars); 
    $msg->printConfirm();
    echo $output;
    require (AT_INCLUDE_PATH.'footer.inc.php');
    exit;

} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	require (AT_INCLUDE_PATH.'header.inc.php'); 
	$savant->assign('title', $_POST['title']);
    $savant->assign('url', $_POST['url']);
    $savant->display('admin/system_preferences/add_feed.tmpl.php');
    require (AT_INCLUDE_PATH.'footer.inc.php'); 
    exit;

} else if (isset($_POST['submit_yes'])) {
	$_POST['url'] = $addslashes($_POST['url']);

	$sql	= "INSERT INTO %sfeeds VALUES (NULL, '%s')";
	$result = queryDB($sql, array(TABLE_PREFIX, $_POST['url']));

	$feed_id = at_insert_id();
	//copy load file
	copy('load_file.php', AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss.inc.php');

	//add language
	$title_file = AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss_title.cache';
	if ($f = @fopen($title_file, 'w')) {
		fwrite ($f, $_POST['title'], strlen($_POST['title']));
		fclose($f);
	}
	//add url
	$output = $_POST['output']; //make_cache_file($feed_id);
	$output_file = AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss.cache';
	if ($fc = @fopen($output_file, 'w')) {
		fwrite ($fc,  $output);
		fclose($fc);
	}
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php');
	exit;
} 

$onload = 'document.form.title.focus();';
//$hidden_vars['new'] = '1';
//$hidden_vars['title'] = $_POST['title'];
//$hidden_vars['url'] = $_POST['url'];

//$msg->addConfirm(array('ADD_FEED', $hidden_vars));
require (AT_INCLUDE_PATH.'header.inc.php');
$msg->printConfirm();
//$savant->assign('msg', $msg);
$savant->assign('output', $output);
$savant->assign('title_file', $title_file);
$savant->display('admin/system_preferences/add_feed.tmpl.php');
require (AT_INCLUDE_PATH.'footer.inc.php'); ?>