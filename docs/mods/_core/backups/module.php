<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_ADMIN_PRIV_BACKUPS', $this->getAdminPrivilege());

if (admin_authenticate(AT_ADMIN_PRIV_BACKUPS, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$this->_pages['mods/_core/backups/admin/courses.php']['children'] = array('mods/_core/backups/admin/index.php');
		$this->_pages['mods/_core/backups/admin/index.php']['parent']    = 'mods/_core/backups/admin/courses.php';
	} else {
		$this->_pages[AT_NAV_ADMIN] = array('mods/_core/backups/admin/index.php');
		$this->_pages['mods/_core/backups/admin/index.php']['parent'] = AT_NAV_ADMIN;
	}

	$this->_pages['mods/_core/backups/admin/index.php']['title_var'] = 'backups';
	$this->_pages['mods/_core/backups/admin/index.php']['guide']     = 'mods/_core/backups/admin/?p=backups.php';
	$this->_pages['mods/_core/backups/admin/index.php']['children']  = array('mods/_core/backups/admin/create.php');

	$this->_pages['mods/_core/backups/admin/create.php']['title_var'] = 'create_backup';
	$this->_pages['mods/_core/backups/admin/create.php']['parent']    = 'mods/_core/backups/admin/index.php';
	$this->_pages['mods/_core/backups/admin/create.php']['guide']     = 'mods/_core/backups/admin/?p=backups.php';

	// this item is a bit iffy:
	$this->_pages['mods/_core/backups/admin/restore.php']['title_var'] = 'restore';
	$this->_pages['mods/_core/backups/admin/restore.php']['parent']    = 'mods/_core/backups/admin/index.php';
	$this->_pages['mods/_core/backups/admin/restore.php']['guide']     = 'mods/_core/backups/admin/?p=backups.php';

	$this->_pages['mods/_core/backups/admin/delete.php']['title_var'] = 'delete';
	$this->_pages['mods/_core/backups/admin/delete.php']['parent']    = 'mods/_core/backups/admin/index.php';

	$this->_pages['mods/_core/backups/admin/edit.php']['title_var'] = 'edit';
	$this->_pages['mods/_core/backups/admin/edit.php']['parent']    = 'mods/_core/backups/admin/index.php';
}
	//instructor pages
	$this->_pages['mods/_core/backups/index.php']['title_var'] = 'backups';
	$this->_pages['mods/_core/backups/index.php']['guide']     = 'instructor/?p=backups.php';
	$this->_pages['mods/_core/backups/index.php']['parent']    = 'tools/index.php';
	$this->_pages['mods/_core/backups/index.php']['children']  = array('mods/_core/backups/create.php', 'mods/_core/backups/upload.php');

	$this->_pages['mods/_core/backups/create.php']['title_var'] = 'create';
	$this->_pages['mods/_core/backups/create.php']['parent']    = 'mods/_core/backups/index.php';
	$this->_pages['mods/_core/backups/create.php']['guide']     = 'instructor/?p=creating_restoring.php';

	$this->_pages['mods/_core/backups/upload.php']['title_var']  = 'upload';
	$this->_pages['mods/_core/backups/upload.php']['parent'] = 'mods/_core/backups/index.php';
	$this->_pages['mods/_core/backups/upload.php']['guide'] = 'instructor/?p=downloading_uploading.php';

	$this->_pages['mods/_core/backups/restore.php']['title_var']  = 'restore';
	$this->_pages['mods/_core/backups/restore.php']['parent'] = 'mods/_core/backups/index.php';
	$this->_pages['mods/_core/backups/restore.php']['guide'] = 'instructor/?p=creating_restoring.php';

	$this->_pages['mods/_core/backups/edit.php']['title_var']  = 'edit';
	$this->_pages['mods/_core/backups/edit.php']['parent'] = 'mods/_core/backups/index.php';
	$this->_pages['mods/_core/backups/edit.php']['guide'] = 'instructor/?p=editing_deleting.php';

	$this->_pages['mods/_core/backups/delete.php']['title_var']  = 'delete';
	$this->_pages['mods/_core/backups/delete.php']['parent'] = 'mods/_core/backups/index.php';				
	$this->_pages['mods/_core/backups/delete.php']['guide'] = 'instructor/?p=editing_deleting.php';

?>