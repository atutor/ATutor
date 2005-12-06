<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

$this->_pages['admin/index.php']['children'] = array('mods/_standard/google_search/admin/module_prefs.php');

	$this->_pages['mods/_standard/google_search/admin/module_prefs.php']['title_var'] = 'google_key';
	$this->_pages['mods/_standard/google_search/admin/module_prefs.php']['parent']    = 'admin/index.php';

$_student_tool = 'mods/_standard/google_search/index.php';

//side menu
$this->_stacks['google_search'] = array('title_var'=>'google_search', 'file'=>dirname(__FILE__).'/side_menu.inc.php');


$this->_pages['mods/_standard/google_search/index.php']['title_var'] = 'google_search';
$this->_pages['mods/_standard/google_search/index.php']['img']       = 'mods/_standard/google_search/google.gif';

?>