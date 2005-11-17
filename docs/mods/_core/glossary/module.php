<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_GLOSSARY', $this->getPrivilege());

//side menu
$_module_stacks['glossary'] = array('title_var'=>'glossary', 'file'=>AT_INCLUDE_PATH.'html/dropdowns/glossary.inc.php');

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'glossary/index.php';

$_module_pages['tools/glossary/index.php']['title_var'] = 'glossary';
$_module_pages['tools/glossary/index.php']['parent']    = 'tools/index.php';
$_module_pages['tools/glossary/index.php']['children']  = array('tools/glossary/add.php');

	$_module_pages['tools/glossary/add.php']['title_var']  = 'add_glossary';
	$_module_pages['tools/glossary/add.php']['parent'] = 'tools/glossary/index.php';

	$_module_pages['tools/glossary/edit.php']['title_var']  = 'edit_glossary';
	$_module_pages['tools/glossary/edit.php']['parent'] = 'tools/glossary/index.php';

	$_module_pages['tools/glossary/delete.php']['title_var']  = 'delete_glossary';
	$_module_pages['tools/glossary/delete.php']['parent'] = 'tools/glossary/index.php';

//student pages
$_module_pages['glossary/index.php']['title_var'] = 'glossary';
$_module_pages['glossary/index.php']['img']       = 'images/home-glossary.gif';

?>