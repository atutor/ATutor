<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'my_stats.php';

$_pages['my_stats.php']['title_var'] = 'my_tracker';
$_pages['my_stats.php']['img']       = 'images/home-tracker.gif';

$_pages['tools/content/index.php']['children'][]  = 'tools/tracker/index.php';

$_pages['tools/tracker/index.php']['title_var'] = 'content_usage';
$_pages['tools/tracker/index.php']['parent']    = 'tools/content/index.php';
$_pages['tools/tracker/index.php']['children']  = array('tools/tracker/student_usage.php', 'tools/tracker/reset.php');
$_pages['tools/tracker/index.php']['guide']     = 'instructor/?p=4.3.content_usage.php';		

	$_pages['tools/tracker/student_usage.php']['title_var']  = 'member_stats';
	$_pages['tools/tracker/student_usage.php']['parent'] = 'tools/tracker/index.php';

	$_pages['tools/tracker/reset.php']['title_var']  = 'reset';
	$_pages['tools/tracker/reset.php']['parent'] = 'tools/tracker/index.php';
?>