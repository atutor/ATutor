<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_COURSE_TOOLS', $this->getPrivilege());

$_module_pages['tools/modules.php']['title_var'] = 'student_tools';
$_module_pages['tools/modules.php']['parent']    = 'tools/index.php';
$_module_pages['tools/modules.php']['children']  = array('tools/side_menu.php');
$_module_pages['tools/modules.php']['guide']     = 'instructor/?p=14.0.student_tools.php';

	$_module_pages['tools/side_menu.php']['title_var'] = 'side_menu';
	$_module_pages['tools/side_menu.php']['parent']    = 'tools/modules.php';
	$_module_pages['tools/side_menu.php']['guide']     = 'instructor/?p=14.1.side_menu.php';

?>