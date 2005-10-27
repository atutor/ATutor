<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }


// if this module is to be made available to students on the Home or Main Navigation
$_student_tools = 'mods/_standard/google_search/index.php';

//side menu
$_module_stacks['google_search'] = array('title_var'=>'google_search', 'file'=>dirname(__FILE__).'\side_menu.inc.php');

$_module_pages['mods/_standard/google_search/index.php']['title_var'] = 'google_search';
$_module_pages['mods/_standard/google_search/index.php']['img']       = 'mods/_standard/google_search/google.gif';

$_module_pages['mods/_standard/google_search/admin/module_prefs.php']['title_var'] = 'google_key';
$_module_pages['mods/_standard/google_search/admin/module_prefs.php']['parent']    = 'admin/config_edit.php';

$_module_pages['admin/config_edit.php']['children'] = array('mods/_standard/google_search/admin/module_prefs.php');

?>