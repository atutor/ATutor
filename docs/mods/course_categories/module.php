<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_ADMIN_PRIV_CATEGORIES', $this->getAdminPrivilege());

$_module_pages['admin/courses.php']['children']  = array('admin/course_categories.php');

$_module_pages['admin/course_categories.php']['title_var'] = 'cats_categories';
$_module_pages['admin/course_categories.php']['parent']    = 'admin/courses.php';
$_module_pages['admin/course_categories.php']['guide']     = 'admin/?p=4.4.categories.php';
$_module_pages['admin/course_categories.php']['children']  = array('admin/create_category.php');

	$_module_pages['admin/create_category.php']['title_var'] = 'create_category';
	$_module_pages['admin/create_category.php']['parent']    = 'admin/course_categories.php';

	$_module_pages['admin/edit_category.php']['title_var'] = 'edit_category';
	$_module_pages['admin/edit_category.php']['parent']    = 'admin/course_categories.php';

	$_module_pages['admin/delete_category.php']['title_var'] = 'delete_category';
	$_module_pages['admin/delete_category.php']['parent']    = 'admin/course_categories.php';


?>