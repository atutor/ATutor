<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'moduleproxy'))) { exit(__FILE__ . ' is not a ModuleProxy'); }

$_module_pages['admin/index.php']['children'] = array('mods/_standard/google_search/admin/module_prefs.php');

	$_module_pages['mods/_standard/google_search/admin/module_prefs.php']['title_var'] = 'google_key';
	$_module_pages['mods/_standard/google_search/admin/module_prefs.php']['parent']    = 'admin/index.php';

global $_base_path, $msg, $_config;

if (isset($_config['gsearch']) && $_config['gsearch'] != '') {

	$_student_tool = 'mods/_standard/google_search/index.php';

	//side menu
	$_module_stacks['google_search'] = array('title_var'=>'google_search', 'file'=>dirname(__FILE__).'\side_menu.inc.php');

	$_module_pages['mods/_standard/google_search/index.php']['title_var'] = 'google_search';
	$_module_pages['mods/_standard/google_search/index.php']['img']       = 'mods/_standard/google_search/google.gif';
} else if ($_SERVER['PHP_SELF'] == $_base_path.'admin/modules/index.php') {
	$msg->addError('GOOGLE_KEY_MISSING');
} else {
	$msg->deleteError('GOOGLE_KEY_MISSING');
}


function google_search_disable() {
	global $msg;
	$msg->deleteError('GOOGLE_KEY_MISSING');
}

?>