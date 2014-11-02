<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

if (!defined('AT_PRIV_ADMIN')) {
	define('AT_PRIV_ADMIN', $this->getPrivilege());
}
if (!defined('AT_ADMIN_PRIV_COURSES')) {
	define('AT_ADMIN_PRIV_COURSES', $this->getAdminPrivilege());
}
global $_config, $db;

// for admin
if (admin_authenticate(AT_ADMIN_PRIV_COURSES, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		//$sql = "SELECT * from ".TABLE_PREFIX."modules WHERE dir_name = '_core/services' && status ='2'";
		$sql = "SELECT * from %smodules WHERE dir_name = '_core/services' && status ='2'";
		$result = queryDB($sql, array(TABLE_PREFIX), TRUE);
		if(count($result) > 0){
		    $service_installed = count($result);
		}

		$this->_pages[AT_NAV_ADMIN] = array('mods/_core/courses/admin/courses.php');
		$this->_pages['mods/_core/courses/admin/courses.php']['title_var'] = 'courses';
		$this->_pages['mods/_core/courses/admin/courses.php']['parent']    = AT_NAV_ADMIN;
		$this->_pages['mods/_core/courses/admin/courses.php']['guide']     = 'admin/?p=courses.php';
	if(!$service_installed){	
		$this->_pages['mods/_core/courses/admin/courses.php']['children']  = array('mods/_core/enrolment/admin/index.php', 'mods/_core/courses/admin/default_mods.php', 'mods/_core/courses/admin/default_side.php','mods/_core/courses/admin/auto_enroll.php', 'mods/_core/courses/admin/create_course.php');

	} else{
		$this->_pages['mods/_core/courses/admin/courses.php']['children']  = array('mods/_core/enrolment/admin/index.php', 'mods/_core/courses/admin/default_mods.php', 'mods/_core/courses/admin/default_side.php','mods/_core/courses/admin/auto_enroll.php');

	}
		$this->_pages['mods/_core/courses/admin/instructor_login.php']['title_var'] = 'view';
		$this->_pages['mods/_core/courses/admin/instructor_login.php']['parent']    = 'mods/_core/courses/admin/courses.php';
	if(!$service_installed){
			// if the service module is installed, disable create course when
			// the course limit is exceeded
		$this->_pages['mods/_core/courses/admin/create_course.php']['title_var'] = 'create_course';
		$this->_pages['mods/_core/courses/admin/create_course.php']['parent']    = 'mods/_core/courses/admin/courses.php';
		$this->_pages['mods/_core/courses/admin/create_course.php']['guide']     = 'admin/?p=creating_courses.php';
	}
		$this->_pages['mods/_core/courses/admin/default_mods.php']['title_var'] = 'default_modules';
		$this->_pages['mods/_core/courses/admin/default_mods.php']['parent']    = 'mods/_core/courses/admin/courses.php';
		$this->_pages['mods/_core/courses/admin/default_mods.php']['guide']     = 'admin/?p=default_student_tools.php';

		$this->_pages['mods/_core/courses/admin/default_side.php']['title_var'] = 'default_side_menu';
		$this->_pages['mods/_core/courses/admin/default_side.php']['parent']    = 'mods/_core/courses/admin/courses.php';
		$this->_pages['mods/_core/courses/admin/default_side.php']['guide']     = 'admin/?p=default_side_menu.php';


            $this->_pages['mods/_core/courses/admin/auto_enroll.php']['title_var'] = 'auto_enroll';
            $this->_pages['mods/_core/courses/admin/auto_enroll.php']['parent']    = 'mods/_core/courses/admin/courses.php';
            $this->_pages['mods/_core/courses/admin/auto_enroll.php']['guide']     = 'admin/?p=auto_enroll.php';
            $this->_pages['mods/_core/courses/admin/auto_enroll.php']['children']  = array_merge(array('mods/_core/courses/admin/auto_enroll_edit.php'));
            $this->_pages['admin/config_edit.php']['children']  = array_merge((array) $this->_pages['admin/config_edit.php']['children']);
			$this->_pages['mods/_core/courses/admin/auto_enroll.php']['avail_in_mobile']   = false;


            $this->_pages['mods/_core/courses/admin/auto_enroll_edit.php']['title_var'] = 'auto_enroll_edit';
            $this->_pages['mods/_core/courses/admin/auto_enroll_edit.php']['parent']    = 'mods/_core/courses/admin/auto_enroll.php';
            $this->_pages['mods/_core/courses/admin/auto_enroll_edit.php']['guide']     = 'admin/?p=auto_enroll.php';

            $this->_pages['mods/_core/courses/admin/auto_enroll_delete.php']['title_var'] = 'auto_enroll_delete';
            $this->_pages['mods/_core/courses/admin/auto_enroll_delete.php']['parent']    = 'mods/_core/courses/admin/auto_enroll.php';

}

//echo $sql;
//if(!defined('DISABLE_CREATE_COURSE')){

$sql = "SELECT * FROM %smodules WHERE dir_name ='_core/services' && status ='2'";
$row = queryDB($sql, array(TABLE_PREFIX), TRUE);

if($row['dir_name']){
    //This is a Service site 
    $service_site = 1;
}
if($_config['disable_create'] != "1" && !isset($service_site)){
	if (isset($_SESSION['member_id']) && get_instructor_status() === TRUE)	
	{
	$this->_pages['mods/_core/courses/users/create_course.php']['title_var'] = 'create_course';
	$this->_pages['mods/_core/courses/users/create_course.php']['parent']    = 'users/index.php';
	$this->_pages['mods/_core/courses/users/create_course.php']['guide']    = 'instructor/?p=creating_courses.php';
	$this->_pages['users/index.php']['children']  = array_merge(array('mods/_core/courses/users/create_course.php'), isset($this->_pages['users/index.php']['children']) ? $this->_pages['users/index.php']['children'] : array());
	
	}
	else if (isset($_SESSION['member_id']) && ALLOW_INSTRUCTOR_REQUESTS)
	{
	$this->_pages['mods/_core/courses/users/create_course.php']['title_var'] = 'request_instructor_priv';
	$this->_pages['mods/_core/courses/users/create_course.php']['parent']    = 'users/index.php';
	$this->_pages['mods/_core/courses/users/create_course.php']['guide']    = 'instructor/?p=creating_courses.php';
	$this->_pages['users/index.php']['children']  = array_merge(array('mods/_core/courses/users/create_course.php'), isset($this->_pages['users/index.php']['children']) ? $this->_pages['users/index.php']['children'] : array());
	
	}
}


?>