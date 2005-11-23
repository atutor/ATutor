<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'moduleproxy'))) { exit(__FILE__ . ' is not a ModuleProxy'); }

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'export.php';

//instructors
$_module_pages['tools/ims/index.php']['title_var'] = 'content_packaging';
$_module_pages['tools/ims/index.php']['parent']    = 'tools/content/index.php';
$_module_pages['tools/ims/index.php']['guide']     = 'instructor/?p=4.2.content_packages.php';

//students
$_module_pages['export.php']['title_var'] = 'export_content';
$_module_pages['export.php']['img']       = 'images/home-export_content.gif';
$_module_pages['export.php']['guide']     = 'general/?p=6.1.export_content.php';

?>