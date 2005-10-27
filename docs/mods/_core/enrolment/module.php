<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_ENROLLMENT', $this->getPrivilege());

$_module_stacks['users_online'] = array('title_var'=>'users_online', 'file'=>AT_INCLUDE_PATH.'html/dropdowns/users_online.inc.php');

$_module_pages['tools/enrollment/index.php']['title_var'] = 'enrolment';
$_module_pages['tools/enrollment/index.php']['parent']    = 'tools/index.php';
$_module_pages['tools/enrollment/index.php']['guide']     = 'instructor/?p=6.0.enrollment.php';
$_module_pages['tools/enrollment/index.php']['children'] = array('tools/enrollment/export_course_list.php', 'tools/enrollment/import_course_list.php', 'tools/enrollment/create_course_list.php');

	$_module_pages['tools/enrollment/export_course_list.php']['title_var'] = 'list_export_course_list';
	$_module_pages['tools/enrollment/export_course_list.php']['parent']    = 'tools/enrollment/index.php';

	$_module_pages['tools/enrollment/import_course_list.php']['title_var'] = 'list_import_course_list';
	$_module_pages['tools/enrollment/import_course_list.php']['parent']    = 'tools/enrollment/index.php';

	$_module_pages['tools/enrollment/create_course_list.php']['title_var'] = 'list_create_course_list';
	$_module_pages['tools/enrollment/create_course_list.php']['parent']    = 'tools/enrollment/index.php';

	$_module_pages['tools/enrollment/verify_list.php']['title_var']  = 'course_list';
	$_module_pages['tools/enrollment/verify_list.php']['parent'] = 'tools/enrollment/index.php';

	$_module_pages['tools/enrollment/privileges.php']['title_var']  = 'privileges';
	$_module_pages['tools/enrollment/privileges.php']['parent'] = 'tools/enrollment/index.php';
	$_module_pages['tools/enrollment/privileges.php']['guide']     = 'instructor/?p=6.1.privileges.php';
	
	$_module_pages['tools/enrollment/enroll_edit.php']['title_var']    = 'edit';
	$_module_pages['tools/enrollment/enroll_edit.php']['parent']   = 'tools/enrollment/index.php';

?>