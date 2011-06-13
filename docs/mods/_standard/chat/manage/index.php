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

define('AT_INCLUDE_PATH', '../../../../include/');

$CACHE_DEBUG=0;
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/chat/lib/chat.inc.php');

if (isset($_GET['view'], $_GET['file'])) {
	header("Location:view_transcript.php?t=".$_GET['file']);
	exit;
} else if ((isset($_GET['view']) || isset($_GET['delete'])) && !isset($_GET['file'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

$admin = getAdminSettings();

if (isset($_GET['delete'], $_GET['file'])) {

	if (($_GET['file'].'.html' == $admin['tranFile']) && ($admin['produceTran'])) {
		$msg->addError('TRANSCRIPT_ACTIVE');
	} else {
		header("Location:delete_transcript.php?m=".$_GET['file']);
		exit;
	}
}
require(AT_INCLUDE_PATH.'header.inc.php');

$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('name' => 1, 'date' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'date';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'date';
} else {
	// no order set
	$order = 'desc';
	$col   = 'date';
}

$tran_files = array();
if (!@opendir(AT_CONTENT_DIR . 'chat/')){
	mkdir(AT_CONTENT_DIR . 'chat/', 0777);
}

if(!file_exists(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings')){
	@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'], 0777);
	@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/', 0776);
	@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs/', 0776);
	@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users/', 0776);
	@copy('admin.settings.default', AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings');
	@chmod (AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings', 0777);

}
	
if ($dir = @opendir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/')) {
	while (($file = readdir($dir)) !== false) {
		if (substr($file, -strlen('.html')) == '.html') {
			$la	= stat(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$file);

			$file = str_replace('.html', '', $file);
			$tran_files[$file] = $la['ctime'];
		}
	}
}
$savant->assign('admin', $admin);
$savant->assign('orders', $orders);
$savant->assign('order', $order);
$savant->assign('col', $col);
$savant->assign('tran_files', $tran_files);
$savant->display('instructor/chat/index.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>