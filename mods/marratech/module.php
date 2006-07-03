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
define('AT_ADMIN_PRIV_MARRATECH', $this->getAdminPrivilege());

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_student_tool = 'mods/marratech/index.php';

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_MARRATECH, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/marratech/marratech.php');
	$this->_pages['mods/marratech/marratech.php']['title_var'] = 'marratech';
	$this->_pages['mods/marratech/marratech.php']['parent']    = AT_NAV_ADMIN;
}

/*******
 * student page.
 */
$this->_pages['mods/marratech/index.php']['title_var'] = 'marratech';
$this->_pages['mods/marratech/index.php']['img']       = 'mods/marratech/marratech_logo.jpg';


?>