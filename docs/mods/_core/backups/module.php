<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_ADMIN_PRIV_BACKUPS', $this->getAdminPrivilege());

if (admin_authenticate(AT_ADMIN_PRIV_BACKUPS, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$_module_pages['admin/courses.php']['children'] = array('admin/backup/index.php');
		$_module_pages['admin/backup/index.php']['parent']    = 'admin/courses.php';
	} else {
		$_module_pages[AT_NAV_ADMIN] = array('admin/backup/index.php');
		$_module_pages['admin/backup/index.php']['parent'] = AT_NAV_ADMIN;
	}

	$_module_pages['admin/backup/index.php']['title_var'] = 'backups';
	$_module_pages['admin/backup/index.php']['guide']     = 'admin/?p=4.2.backups.php';
	$_module_pages['admin/backup/index.php']['children']  = array('admin/backup/create.php');

		$_module_pages['admin/backup/create.php']['title_var'] = 'create_backup';
		$_module_pages['admin/backup/create.php']['parent']    = 'admin/backup/index.php';
		$_module_pages['admin/backup/create.php']['guide']     = 'admin/?p=4.2.backups.php';

		// this item is a bit iffy:
		$_module_pages['admin/backup/restore.php']['title_var'] = 'restore';
		$_module_pages['admin/backup/restore.php']['parent']    = 'admin/backup/index.php';
		$_module_pages['admin/backup/restore.php']['guide']     = 'admin/?p=4.2.backups.php';

		$_module_pages['admin/backup/delete.php']['title_var'] = 'delete';
		$_module_pages['admin/backup/delete.php']['parent']    = 'admin/backup/index.php';

		$_module_pages['admin/backup/edit.php']['title_var'] = 'edit';
		$_module_pages['admin/backup/edit.php']['parent']    = 'admin/backup/index.php';
}
//instructor pages
$_module_pages['tools/backup/index.php']['title_var'] = 'backups';
$_module_pages['tools/backup/index.php']['guide']     = 'instructor/?p=2.0.backups.php';
$_module_pages['tools/backup/index.php']['parent']    = 'tools/index.php';
$_module_pages['tools/backup/index.php']['children']  = array('tools/backup/create.php', 'tools/backup/upload.php');

	$_module_pages['tools/backup/create.php']['title_var'] = 'create';
	$_module_pages['tools/backup/create.php']['parent']    = 'tools/backup/index.php';
	$_module_pages['tools/backup/create.php']['guide']     = 'instructor/?p=2.1.creating_restoring.php';

	$_module_pages['tools/backup/upload.php']['title_var']  = 'upload';
	$_module_pages['tools/backup/upload.php']['parent'] = 'tools/backup/index.php';
	$_module_pages['tools/backup/upload.php']['guide'] = 'instructor/?p=2.2.downloading_uploading.php';

	$_module_pages['tools/backup/restore.php']['title_var']  = 'restore';
	$_module_pages['tools/backup/restore.php']['parent'] = 'tools/backup/index.php';
	$_module_pages['tools/backup/restore.php']['guide'] = 'instructor/?p=2.1.creating_restoring.php';

	$_module_pages['tools/backup/edit.php']['title_var']  = 'edit';
	$_module_pages['tools/backup/edit.php']['parent'] = 'tools/backup/index.php';
	$_module_pages['tools/backup/edit.php']['guide'] = 'instructor/?p=2.3.editing_deleting.php';

	$_module_pages['tools/backup/delete.php']['title_var']  = 'delete';
	$_module_pages['tools/backup/delete.php']['parent'] = 'tools/backup/index.php';				
	$_module_pages['tools/backup/delete.php']['guide'] = 'instructor/?p=2.3.editing_deleting.php';

?>