<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_FORUMS', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_modules[] = 'forum/list.php';

$_module_pages['tools/forums/index.php']['title_var'] = 'forums';
$_module_pages['tools/forums/index.php']['parent']    = 'tools/index.php';
$_module_pages['tools/forums/index.php']['guide']     = 'instructor/?p=3.0.forums.php';
$_module_pages['tools/forums/index.php']['children']  = array('editor/add_forum.php');

	$_module_pages['editor/add_forum.php']['title_var']  = 'create_forum';
	$_module_pages['editor/add_forum.php']['parent'] = 'tools/forums/index.php';

	$_module_pages['editor/delete_forum.php']['title_var']  = 'delete_forum';
	$_module_pages['editor/delete_forum.php']['parent'] = 'tools/forums/index.php';

	$_module_pages['editor/edit_forum.php']['title_var']  = 'edit_forum';
	$_module_pages['editor/edit_forum.php']['parent'] = 'tools/forums/index.php';

//student pages
$_module_pages['forum/list.php']['title_var']  = 'forums';
$_module_pages['forum/list.php']['img']        = 'images/home-forums.gif';

?>