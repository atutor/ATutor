<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_CONTENT', $this->getPrivilege());

define('AT_PACKAGE_TYPES', 'scorm-1.2');
define('AT_PACKAGE_URL_BASE', AT_BASE_HREF . 'sco/'); 


// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'packages/index.php';

$this->_pages['mods/scorm_packages/index.php']['title_var'] = 'packages';
$this->_pages['mods/scorm_packages/index.php']['parent']    = 'tools/index.php';
$this->_pages['mods/scorm_packages/index.php']['children']  = array('mods/scorm_packages/import.php', 'mods/scorm_packages/delete.php');
$this->_pages['mods/scorm_packages/index.php']['guide']     = 'instructor/?p=scorm_packages.php';
	
	$this->_pages['mods/scorm_packages/import.php']['title_var'] = 'import_package';
	$this->_pages['mods/scorm_packages/import.php']['parent']    = 'mods/scorm_packages/index.php';
	
	$this->_pages['mods/scorm_packages/delete.php']['title_var'] = 'delete_package';
	$this->_pages['mods/scorm_packages/delete.php']['parent']    = 'mods/scorm_packages/index.php';
	
	$this->_pages['mods/scorm_packages/settings.php']['title_var'] = 'package_settings';
	$this->_pages['mods/scorm_packages/settings.php']['parent']    = 'mods/scorm_packages/index.php';

	$this->_pages['mods/scorm_packages/scorm-1.2/view.php']['parent']    = 'mods/scorm_packages/index.php';

$this->_pages['packages/index.php']['title_var'] = 'packages';
$this->_pages['packages/index.php']['img']       = 'images/content_pkg.gif';
$this->_pages['packages/index.php']['children']  = array ('mods/scorm_packages/preferences.php');
$this->_pages['packages/index.php']['guide']     = 'general/?p=6.2.packages.php';

	$this->_pages['packages/preferences.php']['title_var'] = 'preferences';
	$this->_pages['packages/preferences.php']['parent']    = 'mods/scorm_packages/index.php';

	$this->_pages['packages/cmidata.php']['title_var'] = 'cmi_data';
	$this->_pages['packages/cmidata.php']['parent']    = 'mods/scorm_packages/index.php';
?>
