<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a Module obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

$path = 'mods/certify/';

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_CERTIFY',       $this->getPrivilege());
define('AT_ADMIN_PRIV_CERTIFY', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
// XXX: Not needed
//$this->_stacks['certify'] = array('title_var'=>'certify', 'file'=>$path.'side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('certify', array('title_var' => 'certify', 'file' => './side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_group_tool = $_student_tool = $path.'index.php';

/*******
 * add the admin pages when needed.
 */
// XXX: Not needed
/*
if (admin_authenticate(AT_ADMIN_PRIV_HELLO_WORLD, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array($path.'index_admin.php');
	$this->_pages[$path.'index_admin.php']['title_var'] = 'Certify';
	$this->_pages[$path.'index_admin.php']['parent']    = AT_NAV_ADMIN;
}
*/
/*******
 * instructor Manage section:
 */

// top page, under manage 
$this->_pages[$path.'index_instructor.php']['title_var'] = 'Certify';
$this->_pages[$path.'index_instructor.php']['parent']   = 'tools/index.php';

// children of top page
$this->_pages[$path.'index_instructor.php']['children']  = array($path.'certify_certificate.php', 
																		);

// sub pages with navigation
$this->_pages[$path.'certify_certificate.php']['title_var'] = 'certify_add_certificate';
$this->_pages[$path.'certify_certificate.php']['parent'] = $path.'index_instructor.php';
$this->_pages[$path.'certify_tests.php']['title_var'] = 'certify_tests';
$this->_pages[$path.'certify_tests.php']['parent'] = $path.'index_instructor.php';
$this->_pages[$path.'certify_student_status.php']['title_var'] = 'certify_student_status';
$this->_pages[$path.'certify_student_status.php']['parent'] = $path.'index_instructor.php';
$this->_pages[$path.'certify_delete.php']['title_var'] = 'certify_delete';
$this->_pages[$path.'certify_delete.php']['parent'] = $path.'index_instructor.php';

// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'certify';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages[$path.'index.php']['title_var'] = 'certify_certificates';
$this->_pages[$path.'index.php']['img']       = $path.'certify.gif';
$this->_pages[$path.'download_certificate.php']['title_var'] = 'download_certificate';
$this->_pages[$path.'download_certificate.php']['img']       = $path.'certify.gif';
$this->_pages[$path.'download_certificate.php']['parent']       = $path.'index.php';


/* public pages */
// XXX: Not needed
//$this->_pages[AT_NAV_PUBLIC] = array($path.'index_public.php');
//$this->_pages[$path.'index_public.php']['title_var'] = 'certify';
//$this->_pages[$path.'index_public.php']['parent'] = AT_NAV_PUBLIC;

/* my start page pages */
// XXX: Not needed
//$this->_pages[AT_NAV_START]  = array($path.'index_mystart.php');
//$this->_pages[$path.'index_mystart.php']['title_var'] = 'certify';
//$this->_pages[$path.'index_mystart.php']['parent'] = AT_NAV_START;

//function certify_get_group_url($group_id) {
//	return $path.'index.php';
//}
?>