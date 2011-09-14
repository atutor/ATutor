<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_FILES', $this->getPrivilege());

$this->_pages['mods/_core/file_manager/index.php']['title_var'] = 'file_manager';
$this->_pages['mods/_core/file_manager/index.php']['parent']    = 'tools/index.php';
$this->_pages['mods/_core/file_manager/index.php']['guide']     = 'instructor/?p=file_manager.php';
$this->_pages['mods/_core/file_manager/index.php']['children']  = array('mods/_core/file_manager/new.php');

	$this->_pages['mods/_core/file_manager/new.php']['title_var'] = 'create_new_file';
	$this->_pages['mods/_core/file_manager/new.php']['parent']    = 'mods/_core/file_manager/index.php';

	$this->_pages['mods/_core/file_manager/zip.php']['title_var'] = 'zip_file_manager';
	$this->_pages['mods/_core/file_manager/zip.php']['parent']    = 'mods/_core/file_manager/index.php';

	$this->_pages['mods/_core/file_manager/rename.php']['title_var'] = 'rename';
	$this->_pages['mods/_core/file_manager/rename.php']['parent']    = 'mods/_core/file_manager/index.php';

	$this->_pages['mods/_core/file_manager/move.php']['title_var'] = 'move';
	$this->_pages['mods/_core/file_manager/move.php']['parent']    = 'mods/_core/file_manager/index.php';

	$this->_pages['mods/_core/file_manager/edit.php']['title_var'] = 'edit';
	$this->_pages['mods/_core/file_manager/edit.php']['parent']    = 'mods/_core/file_manager/index.php';

	$this->_pages['mods/_core/file_manager/delete.php']['title_var'] = 'delete';
	$this->_pages['mods/_core/file_manager/delete.php']['parent']    = 'mods/_core/file_manager/index.php';

?>