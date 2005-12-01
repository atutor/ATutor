<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

$_module_pages['admin/index.php']['children'] = array('mods/_standard/google_search/admin/module_prefs.php');

	$_module_pages['mods/_standard/google_search/admin/module_prefs.php']['title_var'] = 'google_key';
	$_module_pages['mods/_standard/google_search/admin/module_prefs.php']['parent']    = 'admin/index.php';

$_student_tool = 'mods/_standard/google_search/index.php';

//side menu
$_module_stacks['google_search'] = array('title_var'=>'google_search', 'file'=>dirname(__FILE__).'\side_menu.inc.php');

$_module_pages['mods/_standard/google_search/index.php']['title_var'] = 'google_search';
$_module_pages['mods/_standard/google_search/index.php']['img']       = 'mods/_standard/google_search/google.gif';

?>