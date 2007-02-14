<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_ENROLLMENT', $this->getPrivilege());
define('AT_ADMIN_PRIV_ENROLLMENT', $this->getAdminPrivilege());

$this->_stacks['users_online'] = array('title_var'=>'users_online', 'file'=>AT_INCLUDE_PATH.'html/dropdowns/users_online.inc.php');

if (admin_authenticate(AT_ADMIN_PRIV_ENROLLMENT, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('admin/enrollment/index.php');
	$this->_pages['admin/enrollment/index.php']['parent'] = AT_NAV_ADMIN;

	$this->_pages['admin/enrollment/index.php']['title_var'] = 'enrollment';
	$this->_pages['admin/enrollment/index.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['admin/enrollment/index.php']['guide']     = 'admin/?p=enrollment.php';

	$this->_pages['admin/enrollment/enroll_edit.php']['title_var']    = 'edit';
	$this->_pages['admin/enrollment/enroll_edit.php']['parent']   = 'admin/enrollment/index.php';

	$this->_pages['admin/enrollment/privileges.php']['title_var'] = 'privileges';
	$this->_pages['admin/enrollment/privileges.php']['parent']    = 'admin/enrollment/index.php';
	$this->_pages['admin/enrollment/privileges.php']['guide']     = 'admin/?p=enrollment_privileges.php';
}

$this->_pages['tools/enrollment/index.php']['title_var'] = 'enrollment';
$this->_pages['tools/enrollment/index.php']['parent']    = 'tools/index.php';
$this->_pages['tools/enrollment/index.php']['guide']     = 'instructor/?p=enrollment.php';
$this->_pages['tools/enrollment/index.php']['children'] = array('tools/enrollment/export_course_list.php', 'tools/enrollment/import_course_list.php', 'tools/enrollment/create_course_list.php');

	$this->_pages['tools/enrollment/export_course_list.php']['title_var'] = 'list_export_course_list';
	$this->_pages['tools/enrollment/export_course_list.php']['parent']    = 'tools/enrollment/index.php';

	$this->_pages['tools/enrollment/import_course_list.php']['title_var'] = 'list_import_course_list';
	$this->_pages['tools/enrollment/import_course_list.php']['parent']    = 'tools/enrollment/index.php';

	$this->_pages['tools/enrollment/create_course_list.php']['title_var'] = 'list_create_course_list';
	$this->_pages['tools/enrollment/create_course_list.php']['parent']    = 'tools/enrollment/index.php';

	$this->_pages['tools/enrollment/verify_list.php']['title_var']  = 'course_list';
	$this->_pages['tools/enrollment/verify_list.php']['parent'] = 'tools/enrollment/index.php';

	$this->_pages['tools/enrollment/privileges.php']['title_var']  = 'privileges';
	$this->_pages['tools/enrollment/privileges.php']['parent'] = 'tools/enrollment/index.php';
	$this->_pages['tools/enrollment/privileges.php']['guide']     = 'instructor/?p=enrollment_privileges.php';
	
	$this->_pages['tools/enrollment/enroll_edit.php']['title_var']    = 'edit';
	$this->_pages['tools/enrollment/enroll_edit.php']['parent']   = 'tools/enrollment/index.php';

?>