<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH . '../mods/_core/modules/classes/ModuleUtility.class.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: courses.php');
	exit;
}

ModuleUtility::set_default_tools();
$main_defaults[] = ModuleUtility::get_main_defaults();
$home_defaults[] = ModuleUtility::get_home_defaults();

require(AT_INCLUDE_PATH.'header.inc.php');

$main_defaults = explode('|', $_config['main_defaults']);
$home_defaults = explode('|', $_config['home_defaults']);

$main_defaults = array_filter($main_defaults); // remove empties
$home_defaults = array_filter($home_defaults); // remove empties
?>
<?php 
$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED);
$keys = array_keys($module_list);

foreach ($keys as $dir_name) {
	$module =& $module_list[$dir_name]; 

	if ($module->getStudentTools()) {
		$student_tools[] = $module->getStudentTools();
	}
}

$count = 0;

//main mods
$_current_modules = $main_defaults;
$num_main    = count($_current_modules);
//main and home merged
$_current_modules = array_merge($_current_modules, array_diff($home_defaults, $main_defaults));
$num_modules = count($_current_modules);
//all other mods
$_current_modules = array_merge($_current_modules, array_diff($student_tools, $_current_modules));


?>

<?php 
$savant->assign('current_modules', $_current_modules);
$savant->assign('home_defaults', $home_defaults);
$savant->assign('main_defaults', $main_defaults);
$savant->assign('num_modules', $num_modules);
$savant->assign('num_main', $num_main);
$savant->assign('pages', $_pages);
$savant->display('admin/courses/default_mods.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>