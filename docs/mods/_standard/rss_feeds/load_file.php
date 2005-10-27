<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $savant;

$feed_id = intval(basename(__FILE__));
$cache_file = AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss.cache';
$title_file = AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss_title.cache';

ob_start(); 

//if file doesn't exist or is more than 6 hours old (1 hour = 3600) 
if (!file_exists($cache_file) || ((time() - filemtime($cache_file)) > 21600) ) {
	make_cache_file($feed_id);
}

readfile($cache_file);

$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', file_get_contents($title_file));
$savant->display('include/box.tmpl.php');

?>