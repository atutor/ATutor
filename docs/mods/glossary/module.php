<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_GLOSSARY', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_modules[] = 'glossary/index.php';

$_pages['tools/glossary/index.php']['title_var'] = 'glossary';
$_pages['tools/glossary/index.php']['parent']    = 'tools/index.php';
$_pages['tools/glossary/index.php']['children']  = array('tools/glossary/add.php');

	$_pages['tools/glossary/add.php']['title_var']  = 'add_glossary';
	$_pages['tools/glossary/add.php']['parent'] = 'tools/glossary/index.php';

	$_pages['tools/glossary/edit.php']['title_var']  = 'edit_glossary';
	$_pages['tools/glossary/edit.php']['parent'] = 'tools/glossary/index.php';

	$_pages['tools/glossary/delete.php']['title_var']  = 'delete_glossary';
	$_pages['tools/glossary/delete.php']['parent'] = 'tools/glossary/index.php';

?>