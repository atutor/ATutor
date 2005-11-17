<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_LINKS', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'links/index.php';

$_module_pages['tools/links/index.php']['title_var'] = 'links';
$_module_pages['tools/links/index.php']['parent']    = 'tools/index.php';
$_module_pages['tools/links/index.php']['children'] = array('tools/links/add.php', 'tools/links/categories.php', 'tools/links/categories_create.php');

	$_module_pages['tools/links/add.php']['title_var']  = 'add_link';
	$_module_pages['tools/links/add.php']['parent'] = 'tools/links/index.php';

	$_module_pages['tools/links/edit.php']['title_var']  = 'edit_link';
	$_module_pages['tools/links/edit.php']['parent'] = 'tools/links/index.php';

	$_module_pages['tools/links/delete.php']['title_var']  = 'delete_link';
	$_module_pages['tools/links/delete.php']['parent'] = 'tools/links/index.php';

	$_module_pages['tools/links/categories.php']['title_var']  = 'categories';
	$_module_pages['tools/links/categories.php']['parent'] = 'tools/links/index.php';

	$_module_pages['tools/links/categories_create.php']['title_var']  = 'create_category';
	$_module_pages['tools/links/categories_create.php']['parent'] = 'tools/links/index.php';

	$_module_pages['tools/links/categories_edit.php']['title_var']  = 'edit_category';
	$_module_pages['tools/links/categories_edit.php']['parent'] = 'tools/links/categories.php';

	$_module_pages['tools/links/categories_delete.php']['title_var']  = 'delete_category';
	$_module_pages['tools/links/categories_delete.php']['parent'] = 'tools/links/categories.php';

//student pages
$_module_pages['links/index.php']['title_var'] = 'links';
$_module_pages['links/index.php']['children']  = array('links/add.php');
$_module_pages['links/index.php']['img']       = 'images/home-links.gif';

	$_module_pages['links/add.php']['title_var'] = 'suggest_link';
	$_module_pages['links/add.php']['parent']    = 'links/index.php';


?>