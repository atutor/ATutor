<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a Module obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_GRADEBOOK',       $this->getPrivilege());
define('AT_ADMIN_PRIV_GRADEBOOK', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
//$this->_stacks['gradebook'] = array('title_var'=>'gradebook', 'file'=>'mods/_standard/gradebook/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('gradebook', array('title_var' => 'gradebook', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_student_tool = 'mods/_standard/gradebook/my_gradebook.php';

/*******
 * add the admin pages when needed.
 */
//if (admin_authenticate(AT_ADMIN_PRIV_GRADEBOOK, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
//	$this->_pages[AT_NAV_ADMIN] = array('mods/_standard/gradebook/index_admin.php');
//	$this->_pages['mods/_standard/gradebook/index_admin.php']['title_var'] = 'gradebook';
//	$this->_pages['mods/_standard/gradebook/index_admin.php']['parent']    = AT_NAV_ADMIN;
//}

/*******
 * instructor Manage section:
 */
$this->_pages['mods/_standard/gradebook/gradebook_tests.php']['title_var'] = 'gradebook';
$this->_pages['mods/_standard/gradebook/gradebook_tests.php']['parent']    = 'tools/index.php';
$this->_pages['mods/_standard/gradebook/gradebook_tests.php']['guide']     = 'instructor/?p=gradebook.php';
$this->_pages['mods/_standard/gradebook/gradebook_tests.php']['children']  = array('mods/_standard/gradebook/gradebook_add_tests.php', 'mods/_standard/gradebook/update_gradebook.php', 'mods/_standard/gradebook/import_export_external_marks.php', 'mods/_standard/gradebook/edit_marks.php', 'mods/_standard/gradebook/grade_scale.php');

$this->_pages['mods/_standard/gradebook/gradebook_add_tests.php']['title_var'] = 'add_tests';
$this->_pages['mods/_standard/gradebook/gradebook_add_tests.php']['parent']    = 'mods/_standard/gradebook/gradebook_tests.php';
$this->_pages['mods/_standard/gradebook/gradebook_add_tests.php']['guide']     = 'instructor/?p=gradebook_add.php';

$this->_pages['mods/_standard/gradebook/gradebook_edit_tests.php']['title_var'] = 'edit_tests';
$this->_pages['mods/_standard/gradebook/gradebook_edit_tests.php']['parent']    = 'mods/_standard/gradebook/gradebook_tests.php';

$this->_pages['mods/_standard/gradebook/gradebook_delete_tests.php']['title_var'] = 'delete_test';
$this->_pages['mods/_standard/gradebook/gradebook_delete_tests.php']['parent']    = 'mods/_standard/gradebook/gradebook_tests.php';

$this->_pages['mods/_standard/gradebook/update_gradebook.php']['title_var'] = 'update_gradebook';
$this->_pages['mods/_standard/gradebook/update_gradebook.php']['parent']    = 'mods/_standard/gradebook/gradebook_tests.php';
$this->_pages['mods/_standard/gradebook/update_gradebook.php']['guide']     = 'instructor/?p=gradebook_update.php';

$this->_pages['mods/_standard/gradebook/verify_tests.php']['title_var'] = 'update_list';
$this->_pages['mods/_standard/gradebook/verify_tests.php']['parent']    = 'mods/_standard/gradebook/update_gradebook.php';

$this->_pages['mods/_standard/gradebook/import_export_external_marks.php']['title_var'] = 'import_export_external_marks';
$this->_pages['mods/_standard/gradebook/import_export_external_marks.php']['parent']    = 'mods/_standard/gradebook/gradebook_tests.php';
$this->_pages['mods/_standard/gradebook/import_export_external_marks.php']['guide']     = 'instructor/?p=gradebook_external_marks.php';

$this->_pages['mods/_standard/gradebook/verify_list.php']['title_var'] = 'update_list';
$this->_pages['mods/_standard/gradebook/verify_list.php']['parent']    = 'mods/_standard/gradebook/import_export_external_marks.php';

$this->_pages['mods/_standard/gradebook/edit_marks.php']['title_var'] = 'edit_marks';
$this->_pages['mods/_standard/gradebook/edit_marks.php']['parent']    = 'mods/_standard/gradebook/gradebook_tests.php';
$this->_pages['mods/_standard/gradebook/edit_marks.php']['guide']     = 'instructor/?p=gradebook_edit_marks.php';

$this->_pages['mods/_standard/gradebook/grade_scale.php']['title_var'] = 'grade_scale';
$this->_pages['mods/_standard/gradebook/grade_scale.php']['parent']    = 'mods/_standard/gradebook/gradebook_tests.php';
$this->_pages['mods/_standard/gradebook/grade_scale.php']['guide']     = 'instructor/?p=gradebook_scales.php';
$this->_pages['mods/_standard/gradebook/grade_scale.php']['children']  = array('mods/_standard/gradebook/grade_scale_add.php');

$this->_pages['mods/_standard/gradebook/grade_scale_add.php']['title_var'] = 'add_grade_scale';
$this->_pages['mods/_standard/gradebook/grade_scale_add.php']['parent']    = 'mods/_standard/gradebook/grade_scale.php';
$this->_pages['mods/_standard/gradebook/grade_scale_add.php']['guide']     = 'instructor/?p=gradebook_scales.php';

$this->_pages['mods/_standard/gradebook/grade_scale_edit.php']['title_var'] = 'edit_grade_scale';
$this->_pages['mods/_standard/gradebook/grade_scale_edit.php']['parent']    = 'mods/_standard/gradebook/grade_scale.php';

$this->_pages['mods/_standard/gradebook/grade_scale_delete.php']['title_var'] = 'delete_grade_scale';
$this->_pages['mods/_standard/gradebook/grade_scale_delete.php']['parent']    = 'mods/_standard/gradebook/grade_scale.php';

/*******
 * student page.
 */
$this->_pages['mods/_standard/gradebook/my_gradebook.php']['title_var'] = 'gradebook';
$this->_pages['mods/_standard/gradebook/my_gradebook.php']['img']       = 'mods/_standard/gradebook/gradebook.png';


function gradebook_get_group_url($group_id) {
	return 'mods/_standard/gradebook/index.php';
}
?>