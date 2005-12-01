<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_ADMIN_PRIV_CATEGORIES', $this->getAdminPrivilege());

if (admin_authenticate(AT_ADMIN_PRIV_CATEGORIES, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$this->_pages['admin/courses.php']['children'] = array('admin/course_categories.php');
		$this->_pages['admin/course_categories.php']['parent']    = 'admin/courses.php';
	} else {
		$this->_pages[AT_NAV_ADMIN] = array('admin/course_categories.php');
		$this->_pages['admin/course_categories.php']['parent'] = AT_NAV_ADMIN;
	}

$this->_pages['admin/course_categories.php']['title_var'] = 'cats_categories';
$this->_pages['admin/course_categories.php']['guide']     = 'admin/?p=4.4.categories.php';
$this->_pages['admin/course_categories.php']['children']  = array('admin/create_category.php');

	$this->_pages['admin/create_category.php']['title_var'] = 'create_category';
	$this->_pages['admin/create_category.php']['parent']    = 'admin/course_categories.php';

	$this->_pages['admin/edit_category.php']['title_var'] = 'edit_category';
	$this->_pages['admin/edit_category.php']['parent']    = 'admin/course_categories.php';

	$this->_pages['admin/delete_category.php']['title_var'] = 'delete_category';
	$this->_pages['admin/delete_category.php']['parent']    = 'admin/course_categories.php';

}
?>