<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_FORUMS', $this->getPrivilege());

$_pages['tools/forums/index.php']['title_var'] = 'forums';
$_pages['tools/forums/index.php']['parent']    = 'tools/index.php';
$_pages['tools/forums/index.php']['guide']     = 'instructor/?p=3.0.forums.php';
$_pages['tools/forums/index.php']['children']  = array('editor/add_forum.php');

	$_pages['editor/add_forum.php']['title_var']  = 'create_forum';
	$_pages['editor/add_forum.php']['parent'] = 'tools/forums/index.php';

	$_pages['editor/delete_forum.php']['title_var']  = 'delete_forum';
	$_pages['editor/delete_forum.php']['parent'] = 'tools/forums/index.php';

	$_pages['editor/edit_forum.php']['title_var']  = 'edit_forum';
	$_pages['editor/edit_forum.php']['parent'] = 'tools/forums/index.php';

?>