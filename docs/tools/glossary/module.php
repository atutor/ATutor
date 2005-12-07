<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_GLOSSARY', $this->getPrivilege());

//side menu
$this->_stacks['glossary'] = array('title_var'=>'glossary', 'file'=>AT_INCLUDE_PATH.'html/dropdowns/glossary.inc.php');

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'glossary/index.php';

$this->_pages['tools/glossary/index.php']['title_var'] = 'glossary';
$this->_pages['tools/glossary/index.php']['parent']    = 'tools/index.php';
$this->_pages['tools/glossary/index.php']['children']  = array('tools/glossary/add.php');

	$this->_pages['tools/glossary/add.php']['title_var']  = 'add_glossary';
	$this->_pages['tools/glossary/add.php']['parent'] = 'tools/glossary/index.php';

	$this->_pages['tools/glossary/edit.php']['title_var']  = 'edit_glossary';
	$this->_pages['tools/glossary/edit.php']['parent'] = 'tools/glossary/index.php';

	$this->_pages['tools/glossary/delete.php']['title_var']  = 'delete_glossary';
	$this->_pages['tools/glossary/delete.php']['parent'] = 'tools/glossary/index.php';

//student pages
$this->_pages['glossary/index.php']['title_var'] = 'glossary';
$this->_pages['glossary/index.php']['img']       = 'images/home-glossary.gif';

?>