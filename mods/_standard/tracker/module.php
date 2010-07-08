<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'mods/_standard/tracker/my_stats.php';

// module sublinks
$this->_list['my_tracker'] = array('title_var'=>'my_tracker','file'=>'mods/_standard/tracker/sublinks.php');

$_pages['mods/_standard/tracker/my_stats.php']['title_var'] = 'my_tracker';
$_pages['mods/_standard/tracker/my_stats.php']['img']       = 'images/home-tracker.png';
$_pages['mods/_standard/tracker/my_stats.php']['icon']      = 'images/home-tracker_sm.png';

$_pages['mods/_core/content/index.php']['children'][]  = 'mods/_standard/tracker/tools/index.php';

$_pages['mods/_standard/tracker/tools/index.php']['title_var'] = 'content_usage';
$_pages['mods/_standard/tracker/tools/index.php']['parent']    = 'mods/_core/content/index.php';
$_pages['mods/_standard/tracker/tools/index.php']['children']  = array('mods/_standard/tracker/tools/student_usage.php', 'mods/_standard/tracker/tools/reset.php');
$_pages['mods/_standard/tracker/tools/index.php']['guide']     = 'instructor/?p=content_usage.php';		

	$_pages['mods/_standard/tracker/tools/student_usage.php']['title_var']  = 'member_stats';
	$_pages['mods/_standard/tracker/tools/student_usage.php']['parent'] = 'mods/_standard/tracker/tools/index.php';

	$_pages['mods/_standard/tracker/tools/reset.php']['title_var']  = 'reset';
	$_pages['mods/_standard/tracker/tools/reset.php']['parent'] = 'mods/_standard/tracker/tools/index.php';
?>