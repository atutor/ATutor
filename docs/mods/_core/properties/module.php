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

if (!defined('AT_PRIV_ADMIN')) {
	define('AT_PRIV_ADMIN', $this->getPrivilege());
}

//admin pages
$this->_pages['mods/_core/properties/admin/edit_course.php']['title_var'] = 'course_properties';
$this->_pages['mods/_core/properties/admin/edit_course.php']['parent']    = 'admin/courses.php';

$this->_pages['mods/_core/properties/admin/delete_course.php']['title_var'] = 'delete_course';
$this->_pages['mods/_core/properties/admin/delete_course.php']['parent']    = 'admin/courses.php';


//instructor pages
$this->_pages['mods/_core/properties/course_properties.php']['title_var'] = 'properties';
$this->_pages['mods/_core/properties/course_properties.php']['parent']    = 'tools/index.php';
$this->_pages['mods/_core/properties/course_properties.php']['children']  = array('mods/_core/properties/delete_course.php', 'mods/_core/properties/access.php');
$this->_pages['mods/_core/properties/course_properties.php']['guide']     = 'instructor/?p=properties.php';

	$this->_pages['mods/_core/properties/delete_course.php']['title_var'] = 'delete_course';
	$this->_pages['mods/_core/properties/delete_course.php']['parent']    = 'mods/_core/properties/course_properties.php';

	$this->_pages['mods/_core/properties/access.php']['title_var'] = 'authenticated_access';
	$this->_pages['mods/_core/properties/access.php']['parent']    = 'mods/_core/properties/course_properties.php';
	$this->_pages['mods/_core/properties/access.php']['guide']     = 'instructor/?p=authenticated_access.php';

?>