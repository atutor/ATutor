<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_FORUMS', $this->getPrivilege());

// adding this module to the main page as a basic student tool:
$_module_pages['mods/atalker/index.php']['title']    = 'ATalker';
$_module_pages['mods/atalker/index.php']['img']      = 'mods/atalker/images/atalker.gif';
$_module_pages['mods/atalker/index.php']['guide']     = 'atalker_docs/?p=1.1.atalker.php';

	$_module_pages['mods/atalker/admin/admin_index.php']['title']     = 'ATalker Administrator';
	$_module_pages['mods/atalker/admin/admin_index.php']['parent']    = 'admin/index.php';
	$_module_pages['mods/atalker/admin/index.php']['privilege'] = AT_PRIV_ADMIN;
	$_module_pages['mods/atalker/admin/admin_index.php']['guide']     = 'atalker_docs/?p=1.0.voices.php';

/*

// Add a link to the admin's Configuration submenu by including this line in include/lib/menu_page.php after the 
// initial admin/index.php array is created.

array_push($_pages['admin/index.php']['children'], 'mods/atalker/admin/admin_index.php');

*/
?>