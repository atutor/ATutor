<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'moduleproxy'))) { exit(__FILE__ . ' is not a ModuleProxy'); }

$_module_pages['tools/course_stats.php']['title_var'] = 'statistics';
$_module_pages['tools/course_stats.php']['parent']    = 'tools/index.php';
$_module_pages['tools/course_stats.php']['guide']     = 'instructor/?p=13.0.statistics.php';

?>