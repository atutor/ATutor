<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'moduleproxy'))) { exit(__FILE__ . ' is not a ModuleProxy'); }

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'directory.php';

$_module_pages['directory.php']['title_var'] = 'directory';
$_module_pages['directory.php']['img']       = 'images/home-directory.gif';

?>