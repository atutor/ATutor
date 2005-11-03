<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_CONTENT', $this->getPrivilege());

//side menu dropdowns
$_module_stacks['menu_menu'] = array('title_var'=>'menu_menu', 'file'=>AT_INCLUDE_PATH.'html/dropdowns/menu_menu.inc.php');
$_module_stacks['related_topics'] = array('title_var'=>'related_topics', 'file'=>AT_INCLUDE_PATH.'html/dropdowns/related_topics.inc.php');
$_module_stacks['search'] = array('title_var'=>'search', 'file'=>AT_INCLUDE_PATH.'html/dropdowns/search.inc.php');


$_module_pages['search.php']['title_var']      = 'search';

$_module_pages['tools/content/index.php']['title_var'] = 'content';
$_module_pages['tools/content/index.php']['parent']    = 'tools/index.php';
$_module_pages['tools/content/index.php']['guide']     = 'instructor/?p=4.0.content.php';
$_module_pages['tools/content/index.php']['children']  = array('editor/add_content.php', 'tools/ims/index.php');

	$_module_pages['editor/add_content.php']['title_var']    = 'add_content';
	$_module_pages['editor/add_content.php']['parent']   = 'tools/content/index.php';
	$_module_pages['editor/add_content.php']['guide']     = 'instructor/?p=4.1.creating_editing_content.php';

	$_module_pages['editor/edit_content.php']['title_var'] = 'edit_content';
	$_module_pages['editor/edit_content.php']['parent']    = 'tools/content/index.php';
	$_module_pages['editor/edit_content.php']['guide']     = 'instructor/?p=4.1.creating_editing_content.php';

	$_module_pages['editor/delete_content.php']['title_var'] = 'delete_content';
	$_module_pages['editor/delete_content.php']['parent']    = 'tools/content/index.php';

?>