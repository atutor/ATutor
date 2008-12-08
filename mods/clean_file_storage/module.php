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
//define('AT_PRIV_CLEAN_FILE_STORAGE',       $this->getPrivilege());
define('AT_ADMIN_PRIV_CLEAN_FILE_STORAGE', $this->getAdminPrivilege());

if (admin_authenticate(AT_ADMIN_PRIV_CLEAN_FILE_STORAGE, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages['admin/users.php']['children']  = array('mods/clean_file_storage/index_admin.php');
	$this->_pages['mods/clean_file_storage/index_admin.php']['title_var'] = 'clean_file_storage';
	$this->_pages['mods/clean_file_storage/index_admin.php']['parent'] = 'admin/users.php';
}

?>