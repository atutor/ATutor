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

if (isset($_POST['back'])) {
	header('Location: index.php');
	exit;
}

require (AT_INCLUDE_PATH.'header.inc.php');

$feed_id    = intval($_GET['fid']);
$cache_file = AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss.cache';
$title_file = AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss_title.cache';

if (!file_exists($cache_file) || ((time() - filemtime($cache_file)) > 21600) ) {
	make_cache_file($feed_id);
}
?>


<?php 
$savant->assign('cache_file', $cache_file);
$savant->assign('title_file', $title_file);
$savant->display('admin/system_preferences/preview.tmpl.php');
require (AT_INCLUDE_PATH.'footer.inc.php'); ?>