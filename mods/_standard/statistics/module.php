<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

$this->_pages['mods/_standard/statistics/course_stats.php']['title_var'] = 'statistics';
$this->_pages['mods/_standard/statistics/course_stats.php']['parent']    = 'tools/index.php';
$this->_pages['mods/_standard/statistics/course_stats.php']['guide']     = 'instructor/?p=statistics.php';

?>