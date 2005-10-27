<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_POLLS', $this->getPrivilege());

//side dropdown
$_module_stacks['poll'] = array('title_var'=>'poll','file'=>AT_INCLUDE_PATH.'html/dropdowns/poll.inc.php');

// if this module is to be made available to students on the Home or Main Navigation
$_student_tools = 'polls/index.php';

$_module_pages['polls/index.php']['title_var'] = 'polls';
$_module_pages['polls/index.php']['img']       = 'images/home-polls.gif';

$_module_pages['tools/polls/index.php']['title_var'] = 'polls';
$_module_pages['tools/polls/index.php']['parent']    = 'tools/index.php';
$_module_pages['tools/polls/index.php']['children']  = array('tools/polls/add.php');
$_module_pages['tools/polls/index.php']['guide']     = 'instructor/?p=11.0.polls.php';

	$_module_pages['tools/polls/add.php']['title_var'] = 'add_poll';
	$_module_pages['tools/polls/add.php']['parent']    = 'tools/polls/index.php';

	$_module_pages['tools/polls/edit.php']['title_var'] = 'edit_poll';
	$_module_pages['tools/polls/edit.php']['parent']    = 'tools/polls/index.php';

	$_module_pages['tools/polls/delete.php']['title_var'] = 'delete_poll';
	$_module_pages['tools/polls/delete.php']['parent']    = 'tools/polls/index.php';


?>