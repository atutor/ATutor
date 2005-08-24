<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

// if this module is to be made available to students on the Home or Main Navigation
$_modules[] = 'mods/atalker/index.php';

// adding this module to the main page as a basic student tool:
$_pages['mods/atalker/index.php']['title']    = 'ATalker';
$_pages['mods/atalker/index.php']['img']      = 'mods/atalker/images/atalker.gif';
//$_pages['mods/atalker/index.php']['children'] = array('mods/hello_world/subpage.php');

// adding this modeul's instructor page to the course Manage page:
//$_pages['mods/atalker/instructor/index.php']['title']     = 'ATalker Manager';
//$_pages['mods/atalker/instructor/index.php']['parent']    = 'tools/index.php';
//$_pages['mods/atalker/instructor/index.php']['privilege'] = AT_PRIV_ADMIN;


	$_pages['mods/atalker/admin/admin_index.php']['title']     = 'ATalker Administrator';
	$_pages['mods/atalker/admin/admin_index.php']['parent']    = 'admin/index.php';
	$_pages['mods/atalker/admin/index.php']['privilege'] = AT_PRIV_ADMIN;
?>