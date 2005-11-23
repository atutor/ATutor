<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'moduleproxy'))) { exit(__FILE__ . ' is not a ModuleProxy'); }

define('AT_ADMIN_PRIV_CATEGORIES', $this->getAdminPrivilege());

if (admin_authenticate(AT_ADMIN_PRIV_CATEGORIES, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$_module_pages['admin/courses.php']['children'] = array('admin/course_categories.php');
		$_module_pages['admin/course_categories.php']['parent']    = 'admin/courses.php';
	} else {
		$_module_pages[AT_NAV_ADMIN] = array('admin/course_categories.php');
		$_module_pages['admin/course_categories.php']['parent'] = AT_NAV_ADMIN;
	}

$_module_pages['admin/course_categories.php']['title_var'] = 'cats_categories';
$_module_pages['admin/course_categories.php']['guide']     = 'admin/?p=4.4.categories.php';
$_module_pages['admin/course_categories.php']['children']  = array('admin/create_category.php');

	$_module_pages['admin/create_category.php']['title_var'] = 'create_category';
	$_module_pages['admin/create_category.php']['parent']    = 'admin/course_categories.php';

	$_module_pages['admin/edit_category.php']['title_var'] = 'edit_category';
	$_module_pages['admin/edit_category.php']['parent']    = 'admin/course_categories.php';

	$_module_pages['admin/delete_category.php']['title_var'] = 'delete_category';
	$_module_pages['admin/delete_category.php']['parent']    = 'admin/course_categories.php';

}
?>