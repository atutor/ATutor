<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

// if this module is to be made available to students on the Home or Main Navigation
$_modules[] = 'links/index.php';

$_pages['tools/links/index.php']['title_var'] = 'links';
$_pages['tools/links/index.php']['privilege'] = AT_PRIV_LINKS;
$_pages['tools/links/index.php']['parent']    = 'tools/index.php';
$_pages['tools/links/index.php']['children'] = array('tools/links/add.php', 'tools/links/categories.php', 'tools/links/categories_create.php');

	$_pages['tools/links/add.php']['title_var']  = 'add_link';
	$_pages['tools/links/add.php']['parent'] = 'tools/links/index.php';

	$_pages['tools/links/edit.php']['title_var']  = 'edit_link';
	$_pages['tools/links/edit.php']['parent'] = 'tools/links/index.php';

	$_pages['tools/links/delete.php']['title_var']  = 'delete_link';
	$_pages['tools/links/delete.php']['parent'] = 'tools/links/index.php';

	$_pages['tools/links/categories.php']['title_var']  = 'categories';
	$_pages['tools/links/categories.php']['parent'] = 'tools/links/index.php';

	$_pages['tools/links/categories_create.php']['title_var']  = 'create_category';
	$_pages['tools/links/categories_create.php']['parent'] = 'tools/links/index.php';

	$_pages['tools/links/categories_edit.php']['title_var']  = 'edit_category';
	$_pages['tools/links/categories_edit.php']['parent'] = 'tools/links/categories.php';

	$_pages['tools/links/categories_delete.php']['title_var']  = 'delete_category';
	$_pages['tools/links/categories_delete.php']['parent'] = 'tools/links/categories.php';


?>