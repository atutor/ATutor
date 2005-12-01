<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_ADMIN_PRIV_BACKUPS', $this->getAdminPrivilege());

if (admin_authenticate(AT_ADMIN_PRIV_BACKUPS, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$this->_pages['admin/courses.php']['children'] = array('admin/backup/index.php');
		$this->_pages['admin/backup/index.php']['parent']    = 'admin/courses.php';
	} else {
		$this->_pages[AT_NAV_ADMIN] = array('admin/backup/index.php');
		$this->_pages['admin/backup/index.php']['parent'] = AT_NAV_ADMIN;
	}

	$this->_pages['admin/backup/index.php']['title_var'] = 'backups';
	$this->_pages['admin/backup/index.php']['guide']     = 'admin/?p=4.2.backups.php';
	$this->_pages['admin/backup/index.php']['children']  = array('admin/backup/create.php');

		$this->_pages['admin/backup/create.php']['title_var'] = 'create_backup';
		$this->_pages['admin/backup/create.php']['parent']    = 'admin/backup/index.php';
		$this->_pages['admin/backup/create.php']['guide']     = 'admin/?p=4.2.backups.php';

		// this item is a bit iffy:
		$this->_pages['admin/backup/restore.php']['title_var'] = 'restore';
		$this->_pages['admin/backup/restore.php']['parent']    = 'admin/backup/index.php';
		$this->_pages['admin/backup/restore.php']['guide']     = 'admin/?p=4.2.backups.php';

		$this->_pages['admin/backup/delete.php']['title_var'] = 'delete';
		$this->_pages['admin/backup/delete.php']['parent']    = 'admin/backup/index.php';

		$this->_pages['admin/backup/edit.php']['title_var'] = 'edit';
		$this->_pages['admin/backup/edit.php']['parent']    = 'admin/backup/index.php';
}
//instructor pages
$this->_pages['tools/backup/index.php']['title_var'] = 'backups';
$this->_pages['tools/backup/index.php']['guide']     = 'instructor/?p=2.0.backups.php';
$this->_pages['tools/backup/index.php']['parent']    = 'tools/index.php';
$this->_pages['tools/backup/index.php']['children']  = array('tools/backup/create.php', 'tools/backup/upload.php');

	$this->_pages['tools/backup/create.php']['title_var'] = 'create';
	$this->_pages['tools/backup/create.php']['parent']    = 'tools/backup/index.php';
	$this->_pages['tools/backup/create.php']['guide']     = 'instructor/?p=2.1.creating_restoring.php';

	$this->_pages['tools/backup/upload.php']['title_var']  = 'upload';
	$this->_pages['tools/backup/upload.php']['parent'] = 'tools/backup/index.php';
	$this->_pages['tools/backup/upload.php']['guide'] = 'instructor/?p=2.2.downloading_uploading.php';

	$this->_pages['tools/backup/restore.php']['title_var']  = 'restore';
	$this->_pages['tools/backup/restore.php']['parent'] = 'tools/backup/index.php';
	$this->_pages['tools/backup/restore.php']['guide'] = 'instructor/?p=2.1.creating_restoring.php';

	$this->_pages['tools/backup/edit.php']['title_var']  = 'edit';
	$this->_pages['tools/backup/edit.php']['parent'] = 'tools/backup/index.php';
	$this->_pages['tools/backup/edit.php']['guide'] = 'instructor/?p=2.3.editing_deleting.php';

	$this->_pages['tools/backup/delete.php']['title_var']  = 'delete';
	$this->_pages['tools/backup/delete.php']['parent'] = 'tools/backup/index.php';				
	$this->_pages['tools/backup/delete.php']['guide'] = 'instructor/?p=2.3.editing_deleting.php';

?>