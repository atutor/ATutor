<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'moduleproxy'))) { exit(__FILE__ . ' is not a ModuleProxy'); }

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'sitemap.php';

$_module_pages['sitemap.php']['title_var'] = 'sitemap';
$_module_pages['sitemap.php']['parent']    = 'index.php';
$_module_pages['sitemap.php']['img']       = 'images/home-site_map.gif';

?>