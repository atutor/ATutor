<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_ADMIN_PRIV_THEMES', $this->getAdminPrivilege());

$_module_pages['admin/index.php']['children']  = array('admin/themes/index.php');

//admin
$_module_pages['admin/themes/index.php']['title_var'] = 'themes';
$_module_pages['admin/themes/index.php']['parent']    = 'admin/index.php';
$_module_pages['admin/themes/index.php']['guide']     = 'admin/?p=2.4.themes.php';

$_module_pages['admin/themes/delete.php']['title_var'] = 'delete';
$_module_pages['admin/themes/delete.php']['parent']    = 'admin/themes/index.php';

?>