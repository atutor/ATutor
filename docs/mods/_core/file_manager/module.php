<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_FILES', $this->getPrivilege());

$_module_pages['tools/filemanager/index.php']['title_var'] = 'file_manager';
$_module_pages['tools/filemanager/index.php']['parent']    = 'tools/index.php';
$_module_pages['tools/filemanager/index.php']['guide']     = 'instructor/?p=7.0.file_manager.php';
$_module_pages['tools/filemanager/index.php']['children']  = array('tools/filemanager/new.php');

	$_module_pages['tools/filemanager/new.php']['title_var'] = 'create_new_file';
	$_module_pages['tools/filemanager/new.php']['parent']    = 'tools/filemanager/index.php';

	$_module_pages['tools/filemanager/zip.php']['title_var'] = 'zip_file_manager';
	$_module_pages['tools/filemanager/zip.php']['parent']    = 'tools/filemanager/index.php';

	$_module_pages['tools/filemanager/rename.php']['title_var'] = 'rename';
	$_module_pages['tools/filemanager/rename.php']['parent']    = 'tools/filemanager/index.php';

	$_module_pages['tools/filemanager/move.php']['title_var'] = 'move';
	$_module_pages['tools/filemanager/move.php']['parent']    = 'tools/filemanager/index.php';

	$_module_pages['tools/filemanager/edit.php']['title_var'] = 'edit';
	$_module_pages['tools/filemanager/edit.php']['parent']    = 'tools/filemanager/index.php';

	$_module_pages['tools/filemanager/delete.php']['title_var'] = 'delete';
	$_module_pages['tools/filemanager/delete.php']['parent']    = 'tools/filemanager/index.php';

?>