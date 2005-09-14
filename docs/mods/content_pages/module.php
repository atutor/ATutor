<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_CONTENT', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_student_tools = array('export.php', 'my_stats.php');

$_module_pages['tools/content/index.php']['title_var'] = 'content';
$_module_pages['tools/content/index.php']['parent']    = 'tools/index.php';
$_module_pages['tools/content/index.php']['guide']     = 'instructor/?p=4.0.content.php';
$_module_pages['tools/content/index.php']['children'] = array('editor/add_content.php', 'tools/ims/index.php', 'tools/tracker/index.php');

	$_module_pages['editor/add_content.php']['title_var']    = 'add_content';
	$_module_pages['editor/add_content.php']['parent']   = 'tools/content/index.php';
	$_module_pages['editor/add_content.php']['guide']     = 'instructor/?p=4.1.creating_editing_content.php';

	$_module_pages['editor/edit_content.php']['title_var'] = 'edit_content';
	$_module_pages['editor/edit_content.php']['parent']    = 'tools/content/index.php';
	$_module_pages['editor/edit_content.php']['guide']     = 'instructor/?p=4.1.creating_editing_content.php';

	$_module_pages['editor/delete_content.php']['title_var'] = 'delete_content';
	$_module_pages['editor/delete_content.php']['parent']    = 'tools/content/index.php';

$_pages['export.php']['title_var'] = 'export_content';
$_pages['export.php']['img']       = 'images/home-export_content.gif';
$_pages['export.php']['guide']     = 'general/?p=6.1.export_content.php';

$_pages['my_stats.php']['title_var'] = 'my_tracker';
$_pages['my_stats.php']['img']       = 'images/home-tracker.gif';

$_pages['tools/tracker/index.php']['title_var'] = 'content_usage';
$_pages['tools/tracker/index.php']['parent']    = 'tools/content/index.php';
$_pages['tools/tracker/index.php']['children']  = array('tools/tracker/student_usage.php', 'tools/tracker/reset.php');
$_pages['tools/tracker/index.php']['guide']     = 'instructor/?p=4.3.content_usage.php';		

	$_pages['tools/tracker/student_usage.php']['title_var']  = 'member_stats';
	$_pages['tools/tracker/student_usage.php']['parent'] = 'tools/tracker/index.php';

	$_pages['tools/tracker/reset.php']['title_var']  = 'reset';
	$_pages['tools/tracker/reset.php']['parent'] = 'tools/tracker/index.php';
?>