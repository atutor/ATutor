<?php
/** snippit to use when extending Modules
class PropertiesModule extends Module {

	function PropertiesModule($row) {
		parent::Module($row);

		define('AT_PRIV_ADMIN', $row['privilege']);
	}

	function delete() {

	}
}
return;
**/

if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_ADMIN', $this->getPrivilege());

//admin pages
$this->_pages['admin/edit_course.php']['title_var'] = 'course_properties';
$this->_pages['admin/edit_course.php']['parent']    = 'admin/courses.php';

$this->_pages['admin/delete_course.php']['title_var'] = 'delete_course';
$this->_pages['admin/delete_course.php']['parent']    = 'admin/courses.php';


//instructor pages
$this->_pages['tools/course_properties.php']['title_var'] = 'properties';
$this->_pages['tools/course_properties.php']['parent']    = 'tools/index.php';
$this->_pages['tools/course_properties.php']['children']  = array('tools/delete_course.php');
$this->_pages['tools/course_properties.php']['guide']     = 'instructor/?p=properties.php';

	$this->_pages['tools/delete_course.php']['title_var'] = 'delete_course';
	$this->_pages['tools/delete_course.php']['parent']    = 'tools/course_properties.php';

?>