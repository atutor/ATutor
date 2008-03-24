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
define('AT_PRIV_CERTIFICATE',       $this->getPrivilege());
define('AT_ADMIN_PRIV_CERTIFICATE', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
//$this->_stacks['certificate'] = array('title_var'=>'certificate', 'file'=>'mods/certificate/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('certificate', array('title_var' => 'certificate', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_group_tool = $_student_tool = 'mods/certificate/index.php';

/*******
 * add the admin pages when needed.
 */
//if (admin_authenticate(AT_ADMIN_PRIV_CERTIFICATE, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
//	$this->_pages[AT_NAV_ADMIN] = array('mods/certificate/index_admin.php');
//	$this->_pages['mods/certificate/index_admin.php']['title_var'] = 'certificate';
//	$this->_pages['mods/certificate/index_admin.php']['parent']    = AT_NAV_ADMIN;
//}

/*******
 * instructor Manage section:
 */
$this->_pages['tools/tests/index.php']['children'] = array('mods/certificate/index_instructor.php');
$this->_pages['mods/certificate/index_instructor.php']['title_var'] = 'certificate';
$this->_pages['mods/certificate/index_instructor.php']['parent']   = 'tools/tests/index.php';

$this->_pages['mods/certificate/index_instructor.php']['children'] = array('mods/certificate/certificate_create.php');
$this->_pages['mods/certificate/certificate_create.php']['title_var'] = 'create_certificate';
$this->_pages['mods/certificate/certificate_create.php']['parent']   = 'mods/certificate/index_instructor.php';

$this->_pages['mods/certificate/certificate_delete.php']['title_var'] = 'delete_certificate';
$this->_pages['mods/certificate/certificate_delete.php']['parent']   = 'mods/certificate/index_instructor.php';

$this->_pages['mods/certificate/certificate_edit.php']['title_var'] = 'edit_certificate';
$this->_pages['mods/certificate/certificate_edit.php']['parent']   = 'mods/certificate/index_instructor.php';

// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'certificate';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';

/*******
 * student page.
 */
//$this->_pages[AT_NAV_COURSE] = array('mods/certificate/index_public.php');
$this->_pages['mods/certificate/index.php']['title_var'] = 'certificate';
$this->_pages['mods/certificate/index.php']['img']       = 'mods/certificate/certificate.gif';
//
//
///* public pages */
//$this->_pages[AT_NAV_PUBLIC] = array('mods/certificate/index_public.php');
//$this->_pages['mods/certificate/index_public.php']['title_var'] = 'certificate';
//$this->_pages['mods/certificate/index_public.php']['parent'] = AT_NAV_PUBLIC;
//
///* my start page pages */
//$this->_pages[AT_NAV_START]  = array('mods/certificate/index_mystart.php');
//$this->_pages['mods/certificate/index_mystart.php']['title_var'] = 'certificate';
//$this->_pages['mods/certificate/index_mystart.php']['parent'] = AT_NAV_START;

function certificate_get_group_url($group_id) {
	return 'mods/certificate/index.php';
}
?>