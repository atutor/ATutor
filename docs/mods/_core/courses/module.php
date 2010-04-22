<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

if (!defined('AT_PRIV_ADMIN')) {
	define('AT_PRIV_ADMIN', $this->getPrivilege());
}
if (!defined('AT_ADMIN_PRIV_COURSES')) {
	define('AT_ADMIN_PRIV_COURSES', $this->getAdminPrivilege());
}


// for admin
if (admin_authenticate(AT_ADMIN_PRIV_COURSES, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {

	$this->_pages[AT_NAV_ADMIN] = array('mods/_core/courses/admin/courses.php');

	$this->_pages['mods/_core/courses/admin/courses.php']['title_var'] = 'courses';
	$this->_pages['mods/_core/courses/admin/courses.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['mods/_core/courses/admin/courses.php']['guide']     = 'mods/_core/courses/admin/?p=courses.php';
	$this->_pages['mods/_core/courses/admin/courses.php']['children']  = array('mods/_core/courses/admin/create_course.php','mods/_core/enrolment/admin/index.php', 'mods/_core/courses/admin/default_mods.php', 'mods/_core/courses/admin/default_side.php','mods/_core/courses/admin/auto_enroll.php');

		$this->_pages['mods/_core/courses/admin/instructor_login.php']['title_var'] = 'view';
		$this->_pages['mods/_core/courses/admin/instructor_login.php']['parent']    = 'mods/_core/courses/admin/courses.php';

		$this->_pages['mods/_core/courses/admin/create_course.php']['title_var'] = 'create_course';
		$this->_pages['mods/_core/courses/admin/create_course.php']['parent']    = 'mods/_core/courses/admin/courses.php';
		$this->_pages['mods/_core/courses/admin/create_course.php']['guide']     = 'mods/_core/courses/admin/?p=creating_courses.php';

		$this->_pages['mods/_core/courses/admin/default_mods.php']['title_var'] = 'default_modules';
		$this->_pages['mods/_core/courses/admin/default_mods.php']['parent']    = 'mods/_core/courses/admin/courses.php';
		$this->_pages['mods/_core/courses/admin/default_mods.php']['guide']     = 'mods/_core/courses/admin/?p=default_student_tools.php';

		$this->_pages['mods/_core/courses/admin/default_side.php']['title_var'] = 'default_side_menu';
		$this->_pages['mods/_core/courses/admin/default_side.php']['parent']    = 'mods/_core/courses/admin/courses.php';
		$this->_pages['mods/_core/courses/admin/default_side.php']['guide']     = 'mods/_core/courses/admin/?p=default_side_menu.php';


            $this->_pages['mods/_core/courses/admin/auto_enroll.php']['title_var'] = 'auto_enroll';
            $this->_pages['mods/_core/courses/admin/auto_enroll.php']['parent']    = 'mods/_core/courses/admin/courses.php';
            $this->_pages['mods/_core/courses/admin/auto_enroll.php']['guide']     = 'admin/?p=auto_enroll.php';
            $this->_pages['mods/_core/courses/admin/auto_enroll.php']['children']  = array_merge(array('mods/_core/courses/admin/auto_enroll_edit.php'));
            $this->_pages['admin/config_edit.php']['children']  = array_merge((array) $this->_pages['admin/config_edit.php']['children'], array('mods/_core/courses/admin/auto_enroll.php'));



            $this->_pages['mods/_core/courses/admin/auto_enroll_edit.php']['title_var'] = 'auto_enroll_edit';
            $this->_pages['mods/_core/courses/admin/auto_enroll_edit.php']['parent']    = 'mods/_core/courses/admin/auto_enroll.php';
            $this->_pages['mods/_core/courses/admin/auto_enroll_edit.php']['guide']     = 'admin/?p=auto_enroll.php';

            $this->_pages['mods/_core/courses/admin/auto_enroll_delete.php']['title_var'] = 'auto_enroll_delete';
            $this->_pages['mods/_core/courses/admin/auto_enroll_delete.php']['parent']    = 'mods/_core/courses/admin/auto_enroll.php';

}

?>