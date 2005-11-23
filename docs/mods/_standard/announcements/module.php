<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'moduleproxy'))) { exit(__FILE__ . ' is not a ModuleProxy'); }

define('AT_PRIV_ANNOUNCEMENTS', $this->getPrivilege());

$_module_pages['tools/news/index.php']['title_var'] = 'announcements';
$_module_pages['tools/news/index.php']['guide']     = 'instructor/?p=1.0.announcements.php';
$_module_pages['tools/news/index.php']['parent']    = 'tools/index.php';
$_module_pages['tools/news/index.php']['children']  = array('editor/add_news.php');

	$_module_pages['editor/add_news.php']['title_var']  = 'add_announcement';
	$_module_pages['editor/add_news.php']['parent'] = 'tools/news/index.php';

	$_module_pages['editor/edit_news.php']['title_var']  = 'edit_announcement';
	$_module_pages['editor/edit_news.php']['parent'] = 'tools/news/index.php';

	$_module_pages['editor/delete_news.php']['title_var']  = 'delete_announcement';
	$_module_pages['editor/delete_news.php']['parent'] = 'tools/news/index.php';

?>