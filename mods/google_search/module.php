<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }


// if this module is to be made available to students on the Home or Main Navigation
$_student_tools = 'mods/google_search/g_search.php';

$_module_pages['mods/google_search/g_search.php']['title_var'] = 'google_search';
$_module_pages['mods/google_search/g_search.php']['img']       = 'mods/google_search/google.gif';

$_module_pages['mods/google_search/admin/module_prefs.php']['title_var'] = 'google_key';
$_module_pages['mods/google_search/admin/module_prefs.php']['parent'] = 'admin/config_edit.php';

$_module_pages['admin/config_edit.php']['children'] = array('mods/google_search/admin/module_prefs.php');

?>