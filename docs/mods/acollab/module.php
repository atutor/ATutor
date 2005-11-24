<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'moduleproxy'))) { exit(__FILE__ . ' is not a ModuleProxy'); }


define('AT_PRIV_ACOLLAB', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'acollab/index.php';

$_module_pages['acollab/index.php']['parent']    = 'tools/index.php';
$_module_pages['acollab/index.php']['title_var'] = 'acollab';
$_module_pages['acollab/index.php']['img']       = 'images/home-acollab.gif';

	$_module_pages['acollab/bounce.php']['title_var'] = 'acollab';

	$_module_pages['acollab/integrate.php']['title_var'] = 'integrate';


//enter values for these entries 
//define('AC_PATH',			'');
//define('AC_TABLE_PREFIX',	'');
?>