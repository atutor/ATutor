<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

$_pages['tools/news/index.php']['title_var'] = 'announcements';
$_pages['tools/news/index.php']['guide']     = 'instructor/?p=1.0.announcements.php';
$_pages['tools/news/index.php']['privilege'] = AT_PRIV_ANNOUNCEMENTS;
$_pages['tools/news/index.php']['parent']    = 'tools/index.php';
$_pages['tools/news/index.php']['children']  = array('editor/add_news.php');

	$_pages['editor/add_news.php']['title_var']  = 'add_announcement';
	$_pages['editor/add_news.php']['parent'] = 'tools/news/index.php';

	$_pages['editor/edit_news.php']['title_var']  = 'edit_announcement';
	$_pages['editor/edit_news.php']['parent'] = 'tools/news/index.php';

	$_pages['editor/delete_news.php']['title_var']  = 'delete_announcement';
	$_pages['editor/delete_news.php']['parent'] = 'tools/news/index.php';

?>