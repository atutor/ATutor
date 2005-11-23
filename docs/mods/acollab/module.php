<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_ACOLLAB', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'acollab/index.php';

$_module_pages['acollab/index.php']['parent']    = 'tools/index.php';
$_module_pages['acollab/index.php']['title_var'] = 'acollab';
$_module_pages['acollab/index.php']['img']       = 'images/home-acollab.gif';

	$_module_pages['acollab/bounce.php']['title_var'] = 'acollab';

	$_module_pages['acollab/integrate.php']['title_var'] = 'integrate';


//enter values for these entries 
define('AC_PATH',			'http://142.150.154.185/acollab/');
define('AC_TABLE_PREFIX',	'AC_');
?>