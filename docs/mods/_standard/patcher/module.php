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

	$this->_pages['mods/patcher/index_admin.php']['children'] = array('mods/patcher/myown_patches.php','mods/patcher/patch_create.php');
	$this->_pages['mods/patcher/myown_patches.php']['title_var'] = 'myown_patches';
	$this->_pages['mods/patcher/myown_patches.php']['parent']   = 'mods/patcher/index_admin.php';

	$this->_pages['mods/patcher/myown_patches.php']['children'] = array('mods/patcher/patch_create.php');
	$this->_pages['mods/patcher/patch_create.php']['title_var'] = 'create_patch';
	$this->_pages['mods/patcher/patch_create.php']['parent']   = 'mods/patcher/myown_patches.php';

	$this->_pages['mods/patcher/patch_edit.php']['title_var'] = 'edit_patch';
	$this->_pages['mods/patcher/patch_edit.php']['parent']   = 'mods/patcher/myown_patches.php';

	$this->_pages['mods/patcher/patch_delete.php']['title_var'] = 'delete_patch';
	$this->_pages['mods/patcher/patch_delete.php']['parent']   = 'mods/patcher/myown_patches.php';
}
$this->_pages['mods/patcher/index_admin.php']['guide']     = 'admin/?p=patcher.php';
$this->_pages['mods/patcher/patch_create.php']['guide']     = 'admin/?p=create_patches.php';

?>
