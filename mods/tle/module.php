<?php
global $url;
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

$_student_tool = 'mods/tle/index.php';

if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages['admin/config_edit.php']['children'] = array('mods/tle/index_admin.php');
	$this->_pages['mods/tle/index_admin.php']['title_var'] = 'tle';
	$this->_pages['mods/tle/index_admin.php']['parent']    = 'admin/config_edit.php';
}

$this->_pages['mods/tle/index.php']['title_var'] = 'tle';
$this->_pages['mods/tle/index.php']['parent']    = 'tools/index.php';
$this->_pages['mods/tle/index.php']['children'] = array('mods/tle/assembler.php');

$this->_pages['mods/tle/assembler.php']['title_var'] = 'tle_assembler';
$this->_pages['mods/tle/assembler.php']['parent']    = 'mods/tle/index.php';

$this->_pages['mods/tle/import.php']['title_var'] = '';

$this->_pages['tools/content/index.php']['title_var'] = 'tle';
$this->_pages['tools/content/index.php']['children'] = array('mods/tle/index.php');

$this->_pages['mods/tle/import.php']['title_var'] = '';
$this->_pages['mods/tle/index.php']['img']       = 'images/home-tile_search.gif';

?>