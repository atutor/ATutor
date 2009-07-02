<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

$_student_tool = 'google_search/index.php';

if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages['admin/config_edit.php']['children'] = array('google_search/admin/module_prefs.php');

	$this->_pages['google_search/admin/module_prefs.php']['title_var'] = 'google_key';
	$this->_pages['google_search/admin/module_prefs.php']['parent']    = 'admin/config_edit.php';
	$this->_pages['google_search/admin/module_prefs.php']['guide']     = 'admin/?p=google_key.php';

}

$this->_pages['google_search/index.php']['text']      = _AT('google_search_text');

//side menu
$this->_stacks['google_search'] = array('title_var'=>'google_search', 'file'=>dirname(__FILE__).'/side_menu.inc.php');


$this->_pages['google_search/index.php']['title_var'] = 'google_search';
$this->_pages['google_search/index.php']['img']       = 'google_search/google.gif';

?>