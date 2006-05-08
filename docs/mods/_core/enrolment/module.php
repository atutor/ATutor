<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_ENROLLMENT', $this->getPrivilege());

$this->_stacks['users_online'] = array('title_var'=>'users_online', 'file'=>AT_INCLUDE_PATH.'html/dropdowns/users_online.inc.php');

$this->_pages['tools/enrollment/index.php']['title_var'] = 'enrolment';
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