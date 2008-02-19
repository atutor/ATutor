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
define('AT_PRIV_PATCHER',       $this->getPrivilege());
define('AT_ADMIN_PRIV_PATCHER', $this->getAdminPrivilege());

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_PATCHER, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/patcher/index_admin.php');
	$this->_pages['mods/patcher/index_admin.php']['title_var'] = 'patcher';
	$this->_pages['mods/patcher/index_admin.php']['parent']    = AT_NAV_ADMIN;
}

?>