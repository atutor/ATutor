<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_ADMIN', $this->getPrivilege());
define('AT_ADMIN_PRIV_COURSES', $this->getAdminPrivilege());


// for admin
if (admin_authenticate(AT_ADMIN_PRIV_COURSES, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {

	$_module_pages[AT_NAV_ADMIN] = array('admin/courses.php');

	$_module_pages['admin/courses.php']['title_var'] = 'courses';
	$_module_pages['admin/courses.php']['parent']    = AT_NAV_ADMIN;
	$_module_pages['admin/courses.php']['guide']     = 'admin/?p=4.0.courses.php';
	$_module_pages['admin/courses.php']['children']  = array('admin/create_course.php', 'admin/modules/default_mods.php', 'admin/modules/default_side.php');

		$_module_pages['admin/instructor_login.php']['title_var'] = 'view';
		$_module_pages['admin/instructor_login.php']['parent']    = 'admin/courses.php';

		$_module_pages['admin/create_course.php']['title_var'] = 'create_course';
		$_module_pages['admin/create_course.php']['parent']    = 'admin/courses.php';

		$_pages['admin/modules/default_mods.php']['title_var'] = 'default_modules';
		$_pages['admin/modules/default_mods.php']['parent']    = 'admin/courses.php';
		$_pages['admin/modules/default_mods.php']['guide']     = 'admin/?p=2.2.1.default_student_tools.php';

		$_pages['admin/modules/default_side.php']['title_var'] = 'default_side_menu';
		$_pages['admin/modules/default_side.php']['parent']    = 'admin/courses.php';
		$_pages['admin/modules/default_side.php']['guide']     = 'admin/?p=2.2.2.default_side_menu.php';

}
//what about delete course (in properties) ?

?>