<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_FILES', $this->getPrivilege());

$this->_pages['tools/filemanager/index.php']['title_var'] = 'file_manager';
$this->_pages['tools/filemanager/index.php']['parent']    = 'tools/index.php';
$this->_pages['tools/filemanager/index.php']['guide']     = 'instructor/?p=file_manager.php';
$this->_pages['tools/filemanager/index.php']['children']  = array('tools/filemanager/new.php');

	$this->_pages['tools/filemanager/new.php']['title_var'] = 'create_new_file';
	$this->_pages['tools/filemanager/new.php']['parent']    = 'tools/filemanager/index.php';

	$this->_pages['tools/filemanager/zip.php']['title_var'] = 'zip_file_manager';
	$this->_pages['tools/filemanager/zip.php']['parent']    = 'tools/filemanager/index.php';

	$this->_pages['tools/filemanager/rename.php']['title_var'] = 'rename';
	$this->_pages['tools/filemanager/rename.php']['parent']    = 'tools/filemanager/index.php';

	$this->_pages['tools/filemanager/move.php']['title_var'] = 'move';
	$this->_pages['tools/filemanager/move.php']['parent']    = 'tools/filemanager/index.php';

	$this->_pages['tools/filemanager/edit.php']['title_var'] = 'edit';
	$this->_pages['tools/filemanager/edit.php']['parent']    = 'tools/filemanager/index.php';

	$this->_pages['tools/filemanager/delete.php']['title_var'] = 'delete';
	$this->_pages['tools/filemanager/delete.php']['parent']    = 'tools/filemanager/index.php';

?>