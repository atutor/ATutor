<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_ADMIN', $this->getPrivilege());
define('AT_ADMIN_PRIV_COURSES', $this->getAdminPrivilege());


//admin pages
$_module_pages[AT_NAV_ADMIN] = array('admin/courses.php');

$_module_pages['admin/courses.php']['title_var'] = 'courses';
$_module_pages['admin/courses.php']['parent']    = AT_NAV_ADMIN;
$_module_pages['admin/courses.php']['guide']     = 'admin/?p=4.0.courses.php';
$_module_pages['admin/courses.php']['children']  = array('admin/create_course.php');

	$_module_pages['admin/instructor_login.php']['title_var'] = 'view';
	$_module_pages['admin/instructor_login.php']['parent']    = 'admin/courses.php';

	$_module_pages['admin/create_course.php']['title_var'] = 'create_course';
	$_module_pages['admin/create_course.php']['parent']    = 'admin/courses.php';


//what about delete course (in properties) ?

?>