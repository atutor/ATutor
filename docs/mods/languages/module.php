<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_ADMIN_PRIV_LANGUAGES', $this->getAdminPrivilege());

$_module_pages['admin/courses.php']['children']  = array('admin/language.php');

//admin
$_module_pages['admin/language.php']['title_var'] = 'languages';
$_module_pages['admin/language.php']['parent']    = 'admin/index.php';
$_module_pages['admin/language.php']['guide']     = 'admin/?p=2.3.languages.php';

	$_module_pages['admin/language_add.php']['title_var'] = 'add_language';
	$_module_pages['admin/language_add.php']['parent']    = 'admin/language.php';

	$_module_pages['admin/language_edit.php']['title_var'] = 'edit_language';
	$_module_pages['admin/language_edit.php']['parent']    = 'admin/language.php';

	$_module_pages['admin/language_delete.php']['title_var'] = 'delete_language';
	$_module_pages['admin/language_delete.php']['parent']    = 'admin/language.php';

?>