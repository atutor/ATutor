<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_ENROLLMENT', $this->getPrivilege());
define('AT_ADMIN_PRIV_ENROLLMENT', $this->getAdminPrivilege());

$this->_stacks['users_online'] = array('title_var'=>'users_online', 'file'=>AT_INCLUDE_PATH.'html/dropdowns/users_online.inc.php');

if (admin_authenticate(AT_ADMIN_PRIV_ENROLLMENT, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/_core/enrolment/admin/index.php');
	$this->_pages['mods/_core/enrolment/admin/index.php']['parent'] = AT_NAV_ADMIN;

	$this->_pages['mods/_core/enrolment/admin/index.php']['title_var'] = 'enrollment';
	$this->_pages['mods/_core/enrolment/admin/index.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['mods/_core/enrolment/admin/index.php']['guide']     = 'admin/?p=enrollment.php';

	$this->_pages['mods/_core/enrolment/admin/enroll_edit.php']['title_var']    = 'enrollment';
	$this->_pages['mods/_core/enrolment/admin/enroll_edit.php']['parent']   = 'mods/_core/enrolment/admin/index.php';

	$this->_pages['mods/_core/enrolment/admin/privileges.php']['title_var'] = 'privileges';
	$this->_pages['mods/_core/enrolment/admin/privileges.php']['parent']    = 'mods/_core/enrolment/admin/index.php';
	$this->_pages['mods/_core/enrolment/admin/privileges.php']['guide']     = 'admin/?p=enrollment_privileges.php';

	// linked from users.php
	$this->_pages['admin/user_enrollment.php']['title_var'] = 'enrollment';
	$this->_pages['admin/user_enrollment.php']['parent']    = 'admin/users.php';
}

$this->_pages['mods/_core/enrolment/index.php']['title_var'] = 'enrollment';
$this->_pages['mods/_core/enrolment/index.php']['parent']    = 'tools/index.php';
$this->_pages['mods/_core/enrolment/index.php']['guide']     = 'instructor/?p=enrollment.php';
$this->_pages['mods/_core/enrolment/index.php']['children'] = array('mods/_core/enrolment/export_course_list.php', 'mods/_core/enrolment/import_course_list.php', 'mods/_core/enrolment/create_course_list.php');

	$this->_pages['mods/_core/enrolment/export_course_list.php']['title_var'] = 'list_export_course_list';
	$this->_pages['mods/_core/enrolment/export_course_list.php']['parent']    = 'mods/_core/enrolment/index.php';

	$this->_pages['mods/_core/enrolment/import_course_list.php']['title_var'] = 'list_import_course_list';
	$this->_pages['mods/_core/enrolment/import_course_list.php']['parent']    = 'mods/_core/enrolment/index.php';

	$this->_pages['mods/_core/enrolment/create_course_list.php']['title_var'] = 'list_create_course_list';
	$this->_pages['mods/_core/enrolment/create_course_list.php']['parent']    = 'mods/_core/enrolment/index.php';

	$this->_pages['mods/_core/enrolment/verify_list.php']['title_var']  = 'course_list';
	$this->_pages['mods/_core/enrolment/verify_list.php']['parent'] = 'mods/_core/enrolment/index.php';

	$this->_pages['mods/_core/enrolment/privileges.php']['title_var']  = 'privileges';
	$this->_pages['mods/_core/enrolment/privileges.php']['parent'] = 'mods/_core/enrolment/index.php';
	$this->_pages['mods/_core/enrolment/privileges.php']['guide']     = 'instructor/?p=enrollment_privileges.php';
	
	$this->_pages['mods/_core/enrolment/enroll_edit.php']['title_var']    = 'enrollment';
	$this->_pages['mods/_core/enrolment/enroll_edit.php']['parent']   = 'mods/_core/enrolment/index.php';

?>