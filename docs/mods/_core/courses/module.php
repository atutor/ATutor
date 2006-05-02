<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_ADMIN', $this->getPrivilege());
define('AT_ADMIN_PRIV_COURSES', $this->getAdminPrivilege());


// for admin
if (admin_authenticate(AT_ADMIN_PRIV_COURSES, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {

	$this->_pages[AT_NAV_ADMIN] = array('admin/courses.php');

	$this->_pages['admin/courses.php']['title_var'] = 'courses';
	$this->_pages['admin/courses.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['admin/courses.php']['guide']     = 'admin/?p=courses.php';
	$this->_pages['admin/courses.php']['children']  = array('admin/create_course.php', 'admin/modules/default_mods.php', 'admin/modules/default_side.php');

		$this->_pages['admin/instructor_login.php']['title_var'] = 'view';
		$this->_pages['admin/instructor_login.php']['parent']    = 'admin/courses.php';

		$this->_pages['admin/create_course.php']['title_var'] = 'create_course';
		$this->_pages['admin/create_course.php']['parent']    = 'admin/courses.php';

		$_pages['admin/modules/default_mods.php']['title_var'] = 'default_modules';
		$_pages['admin/modules/default_mods.php']['parent']    = 'admin/courses.php';
		$_pages['admin/modules/default_mods.php']['guide']     = 'admin/?p=default_student_tools.php';

		$_pages['admin/modules/default_side.php']['title_var'] = 'default_side_menu';
		$_pages['admin/modules/default_side.php']['parent']    = 'admin/courses.php';
		$_pages['admin/modules/default_side.php']['guide']     = 'admin/?p=default_side_menu.php';

}
//what about delete course (in properties) ?

?>