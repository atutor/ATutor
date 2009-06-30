<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_CHAT', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'chat/index.php';

// module sublinks
$this->_list['chat'] = array('title_var'=>'chat','file'=>'mods/_standard/chat/sublinks.php');

$this->_pages['tools/chat/index.php']['title_var'] = 'chat';
$this->_pages['tools/chat/index.php']['parent']    = 'tools/index.php';
$this->_pages['tools/chat/index.php']['children']  = array('tools/chat/start_transcript.php');
$this->_pages['tools/chat/index.php']['guide']     = 'instructor/?p=chat.php';

	$this->_pages['tools/chat/start_transcript.php']['title_var']  = 'chat_start_transcript';
	$this->_pages['tools/chat/start_transcript.php']['parent'] = 'tools/chat/index.php';

	$this->_pages['tools/chat/delete_transcript.php']['title_var']  = 'chat_delete_transcript';
	$this->_pages['tools/chat/delete_transcript.php']['parent'] = 'tools/chat/index.php';

	$this->_pages['tools/chat/view_transcript.php']['title_var']  = 'chat_transcript';
	$this->_pages['tools/chat/view_transcript.php']['parent'] = 'tools/chat/index.php';

$this->_pages['chat/index.php']['title_var'] = 'chat';
$this->_pages['chat/index.php']['img']       = 'images/home-chat.png';
$this->_pages['chat/index.php']['icon']      = 'images/home-chat_sm.png';

	$this->_pages['chat/chat_frame.php']['title_var'] = 'chat';
	$this->_pages['chat/chat_frame.php']['parent']    = 'chat/index.php';

	$this->_pages['chat/view_transcript.php']['title_var'] = 'chat_transcript';
	$this->_pages['chat/view_transcript']['parent']        = 'chat/index.php';

$this->_pages['chat/chat.php']['title_var'] = 'chat';


?>