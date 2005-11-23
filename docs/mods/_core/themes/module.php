<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'moduleproxy'))) { exit(__FILE__ . ' is not a ModuleProxy'); }

define('AT_ADMIN_PRIV_THEMES', $this->getAdminPrivilege());

if (admin_authenticate(AT_ADMIN_PRIV_THEMES, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$_module_pages['admin/index.php']['children']      = array('admin/themes/index.php');
		$_module_pages['admin/themes/index.php']['parent'] = 'admin/index.php';
	} else {
		$_module_pages[AT_NAV_ADMIN] = array('admin/themes/index.php');
		$_module_pages['admin/themes/index.php']['parent'] = AT_NAV_ADMIN;
	}


	//admin
	$_module_pages['admin/themes/index.php']['title_var'] = 'themes';
	$_module_pages['admin/themes/index.php']['guide']     = 'admin/?p=2.4.themes.php';

	$_module_pages['admin/themes/delete.php']['title_var'] = 'delete';
	$_module_pages['admin/themes/delete.php']['parent']    = 'admin/themes/index.php';

}
?>