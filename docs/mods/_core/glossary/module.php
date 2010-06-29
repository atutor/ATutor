<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_GLOSSARY', $this->getPrivilege());

//side menu
$this->_stacks['glossary'] = array('title_var'=>'glossary', 'file'=>AT_INCLUDE_PATH.'../mods/_core/glossary/dropdown/glossary.inc.php');

// modules sub-content
$this->_list['glossary'] = array('title_var'=>'glossary','file'=>'mods/_core/glossary/sublinks.php');

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'mods/_core/glossary/index.php';

$this->_pages['mods/_core/glossary/tools/index.php']['title_var'] = 'glossary';
$this->_pages['mods/_core/glossary/tools/index.php']['parent']    = 'tools/index.php';
$this->_pages['mods/_core/glossary/tools/index.php']['children']  = array('mods/_core/glossary/tools/add.php');

	$this->_pages['mods/_core/glossary/tools/add.php']['title_var']  = 'add_glossary';
	$this->_pages['mods/_core/glossary/tools/add.php']['parent'] = 'mods/_core/glossary/tools/index.php';

	$this->_pages['mods/_core/glossary/tools/edit.php']['title_var']  = 'edit_glossary';
	$this->_pages['mods/_core/glossary/tools/edit.php']['parent'] = 'mods/_core/glossary/tools/index.php';

	$this->_pages['mods/_core/glossary/tools/delete.php']['title_var']  = 'delete_glossary';
	$this->_pages['mods/_core/glossary/tools/delete.php']['parent'] = 'mods/_core/glossary/tools/index.php';

//student pages
$this->_pages['mods/_core/glossary/index.php']['title_var'] = 'glossary';
$this->_pages['mods/_core/glossary/index.php']['img']       = 'images/home-glossary.png';
$this->_pages['mods/_core/glossary/index.php']['icon']      = 'images/home-glossary_sm.png';

?>