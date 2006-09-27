<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_CONTENT', $this->getPrivilege());

global $_base_href;

define('AT_PACKAGE_TYPES', 'scorm-1.2');
define('AT_PACKAGE_URL_BASE', $_base_href . 'sco/'); 


// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'packages/index.php';

$this->_pages['tools/packages/index.php']['title_var'] = 'packages';
$this->_pages['tools/packages/index.php']['parent']    = 'tools/index.php';
$this->_pages['tools/packages/index.php']['children']  = array('tools/packages/import.php', 'tools/packages/delete.php');
$this->_pages['tools/packages/index.php']['guide']     = 'instructor/?p=4.5.scorm_packages.php';
	
	$this->_pages['tools/packages/import.php']['title_var'] = 'import_package';
	$this->_pages['tools/packages/import.php']['parent']    = 'tools/packages/index.php';
	
	$this->_pages['tools/packages/delete.php']['title_var'] = 'delete_package';
	$this->_pages['tools/packages/delete.php']['parent']    = 'tools/packages/index.php';
	
	//$this->_pages['tools/packages/settings.php']['title_var'] = 'package_settings';
	//$this->_pages['tools/packages/settings.php']['parent']    = 'tools/packages/index.php';

	$this->_pages['tools/packages/scorm-1.2/view.php']['parent']    = 'tools/packages/index.php';

$this->_pages['packages/index.php']['title_var'] = 'packages';
$this->_pages['packages/index.php']['img']       = 'images/content_pkg.gif';
$this->_pages['packages/index.php']['children']  = array ('packages/preferences.php');
$this->_pages['packages/index.php']['guide']     = 'general/?p=6.2.packages.php';

	$this->_pages['packages/preferences.php']['title_var'] = 'preferences';
	$this->_pages['packages/preferences.php']['parent']    = 'packages/index.php';

	$this->_pages['packages/cmidata.php']['title_var'] = 'cmi_data';
	$this->_pages['packages/cmidata.php']['parent']    = 'packages/index.php';
?>