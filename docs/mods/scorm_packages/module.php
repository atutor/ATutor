<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_CONTENT', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'packages/index.php';

$_module_pages['tools/packages/index.php']['title_var'] = 'packages';
$_module_pages['tools/packages/index.php']['parent']    = 'tools/index.php';
$_module_pages['tools/packages/index.php']['children']  = array('tools/packages/import.php', 'tools/packages/delete.php', 'tools/packages/settings.php');
$_module_pages['tools/packages/index.php']['guide']     = 'instructor/?p=4.5.scorm_packages.php';
	
	$_module_pages['tools/packages/import.php']['title_var'] = 'import_package';
	$_module_pages['tools/packages/import.php']['parent']    = 'tools/packages/index.php';
	
	$_module_pages['tools/packages/delete.php']['title_var'] = 'delete_package';
	$_module_pages['tools/packages/delete.php']['parent']    = 'tools/packages/index.php';
	
	$_module_pages['tools/packages/settings.php']['title_var'] = 'package_settings';
	$_module_pages['tools/packages/settings.php']['parent']    = 'tools/packages/index.php';

	$_module_pages['tools/packages/scorm-1.2/view.php']['parent']    = 'tools/packages/index.php';

$_module_pages['packages/index.php']['title_var'] = 'packages';
$_module_pages['packages/index.php']['img']       = 'images/content_pkg.gif';
$_module_pages['packages/index.php']['children']  = array ('packages/preferences.php');
$_module_pages['packages/index.php']['guide']     = 'general/?p=6.2.packages.php';

	$_module_pages['packages/preferences.php']['title_var'] = 'package_preferences';
	$_module_pages['packages/preferences.php']['parent']    = 'packages/index.php';

	$_module_pages['packages/cmidata.php']['title_var'] = 'cmi_data';
	$_module_pages['packages/cmidata.php']['parent']    = 'packages/index.php';
?>