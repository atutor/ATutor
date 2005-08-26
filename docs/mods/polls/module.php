<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_POLLS', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_modules[] = 'polls/index.php';

$_pages['polls/index.php']['title_var'] = 'polls';
$_pages['polls/index.php']['img']       = 'images/home-polls.gif';

$_pages['tools/polls/index.php']['title_var'] = 'polls';
$_pages['tools/polls/index.php']['parent']    = 'tools/index.php';
$_pages['tools/polls/index.php']['children']  = array('tools/polls/add.php');
$_pages['tools/polls/index.php']['guide']     = 'instructor/?p=11.0.polls.php';

	$_pages['tools/polls/add.php']['title_var'] = 'add_poll';
	$_pages['tools/polls/add.php']['parent']    = 'tools/polls/index.php';

	$_pages['tools/polls/edit.php']['title_var'] = 'edit_poll';
	$_pages['tools/polls/edit.php']['parent']    = 'tools/polls/index.php';

	$_pages['tools/polls/delete.php']['title_var'] = 'delete_poll';
	$_pages['tools/polls/delete.php']['parent']    = 'tools/polls/index.php';


?>