<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

$_pages['tools/filemanager/index.php']['title_var'] = 'file_manager';
$_pages['tools/filemanager/index.php']['privilege'] = AT_PRIV_FILES;
$_pages['tools/filemanager/index.php']['parent']    = 'tools/index.php';
$_pages['tools/filemanager/index.php']['guide']     = 'instructor/?p=7.0.file_manager.php';
$_pages['tools/filemanager/index.php']['children']  = array('tools/filemanager/new.php');

	$_pages['tools/filemanager/new.php']['title_var'] = 'create_new_file';
	$_pages['tools/filemanager/new.php']['parent']    = 'tools/filemanager/index.php';

	$_pages['tools/filemanager/zip.php']['title_var'] = 'zip_file_manager';
	$_pages['tools/filemanager/zip.php']['parent']    = 'tools/filemanager/index.php';

	$_pages['tools/filemanager/rename.php']['title_var'] = 'rename';
	$_pages['tools/filemanager/rename.php']['parent']    = 'tools/filemanager/index.php';

	$_pages['tools/filemanager/move.php']['title_var'] = 'move';
	$_pages['tools/filemanager/move.php']['parent']    = 'tools/filemanager/index.php';

	$_pages['tools/filemanager/edit.php']['title_var'] = 'edit';
	$_pages['tools/filemanager/edit.php']['parent']    = 'tools/filemanager/index.php';

	$_pages['tools/filemanager/delete.php']['title_var'] = 'delete';
	$_pages['tools/filemanager/delete.php']['parent']    = 'tools/filemanager/index.php';

?>