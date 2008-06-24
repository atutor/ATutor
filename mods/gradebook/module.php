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
//$this->_stacks['gradebook'] = array('title_var'=>'gradebook', 'file'=>'mods/gradebook/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('gradebook', array('title_var' => 'gradebook', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_group_tool = $_student_tool = 'mods/gradebook/my_gradebook.php';

/*******
 * add the admin pages when needed.
 */
//if (admin_authenticate(AT_ADMIN_PRIV_GRADEBOOK, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
//	$this->_pages[AT_NAV_ADMIN] = array('mods/gradebook/index_admin.php');
//	$this->_pages['mods/gradebook/index_admin.php']['title_var'] = 'gradebook';
//	$this->_pages['mods/gradebook/index_admin.php']['parent']    = AT_NAV_ADMIN;
//}

/*******
 * instructor Manage section:
 */
$this->_pages['mods/gradebook/gradebook_tests.php']['title_var'] = 'gradebook';
$this->_pages['mods/gradebook/gradebook_tests.php']['parent']    = 'tools/index.php';
$this->_pages['mods/gradebook/gradebook_tests.php']['guide']     = 'instructor/?p=grade_scale.php';
$this->_pages['mods/gradebook/gradebook_tests.php']['children']  = array('mods/gradebook/gradebook_add_tests.php', 'mods/gradebook/update_gradebook.php', 'mods/gradebook/import_export_external_marks.php', 'mods/gradebook/edit_marks.php', 'mods/gradebook/grade_scale.php');

$this->_pages['mods/gradebook/gradebook_add_tests.php']['title_var'] = 'add_tests';
$this->_pages['mods/gradebook/gradebook_add_tests.php']['parent']    = 'mods/gradebook/gradebook_tests.php';
$this->_pages['mods/gradebook/gradebook_add_tests.php']['guide']     = 'instructor/?p=gradebook_add_tests.php';

$this->_pages['mods/gradebook/gradebook_edit_tests.php']['title_var'] = 'edit_tests';
$this->_pages['mods/gradebook/gradebook_edit_tests.php']['parent']    = 'mods/gradebook/gradebook_tests.php';

$this->_pages['mods/gradebook/gradebook_delete_tests.php']['title_var'] = 'delete_test';
$this->_pages['mods/gradebook/gradebook_delete_tests.php']['parent']    = 'mods/gradebook/gradebook_tests.php';

$this->_pages['mods/gradebook/update_gradebook.php']['title_var'] = 'update_gradebook';
$this->_pages['mods/gradebook/update_gradebook.php']['parent']    = 'mods/gradebook/gradebook_tests.php';
$this->_pages['mods/gradebook/update_gradebook.php']['guide']     = 'instructor/?p=update_gradebook.php';

$this->_pages['mods/gradebook/verify_tests.php']['title_var'] = 'update_list';
$this->_pages['mods/gradebook/verify_tests.php']['parent']    = 'mods/gradebook/update_gradebook.php';

$this->_pages['mods/gradebook/import_export_external_marks.php']['title_var'] = 'import_export_external_marks';
$this->_pages['mods/gradebook/import_export_external_marks.php']['parent']    = 'mods/gradebook/gradebook_tests.php';
$this->_pages['mods/gradebook/import_export_external_marks.php']['guide']     = 'instructor/?p=import_export_external_marks.php';

$this->_pages['mods/gradebook/verify_list.php']['title_var'] = 'update_list';
$this->_pages['mods/gradebook/verify_list.php']['parent']    = 'mods/gradebook/import_export_external_marks.php';

$this->_pages['mods/gradebook/edit_marks.php']['title_var'] = 'edit_marks';
$this->_pages['mods/gradebook/edit_marks.php']['parent']    = 'mods/gradebook/gradebook_tests.php';

$this->_pages['mods/gradebook/grade_scale.php']['title_var'] = 'grade_scale';
$this->_pages['mods/gradebook/grade_scale.php']['parent']    = 'mods/gradebook/gradebook_tests.php';
$this->_pages['mods/gradebook/grade_scale.php']['guide']     = 'instructor/?p=grade_scale.php';
$this->_pages['mods/gradebook/grade_scale.php']['children']  = array('mods/gradebook/grade_scale_add.php');

$this->_pages['mods/gradebook/grade_scale_add.php']['title_var'] = 'add_grade_scale';
$this->_pages['mods/gradebook/grade_scale_add.php']['parent']    = 'mods/gradebook/grade_scale.php';
$this->_pages['mods/gradebook/grade_scale_add.php']['guide']     = 'instructor/?p=grade_scale_add.php';

$this->_pages['mods/gradebook/grade_scale_edit.php']['title_var'] = 'edit_grade_scale';
$this->_pages['mods/gradebook/grade_scale_edit.php']['parent']    = 'mods/gradebook/grade_scale.php';

$this->_pages['mods/gradebook/grade_scale_delete.php']['title_var'] = 'delete_grade_scale';
$this->_pages['mods/gradebook/grade_scale_delete.php']['parent']    = 'mods/gradebook/grade_scale.php';

/*******
 * student page.
 */
$this->_pages['mods/gradebook/my_gradebook.php']['title_var'] = 'gradebook';
$this->_pages['mods/gradebook/my_gradebook.php']['img']       = 'mods/gradebook/gradebook.png';


function gradebook_get_group_url($group_id) {
	return 'mods/gradebook/index.php';
}
?>