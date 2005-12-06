<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_POLLS', $this->getPrivilege());

//side dropdown
$this->_stacks['poll'] = array('title_var'=>'poll','file'=>AT_INCLUDE_PATH.'html/dropdowns/poll.inc.php');

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'polls/index.php';

$this->_pages['polls/index.php']['title_var'] = 'polls';
$this->_pages['polls/index.php']['img']       = 'images/home-polls.gif';

$this->_pages['tools/polls/index.php']['title_var'] = 'polls';
$this->_pages['tools/polls/index.php']['parent']    = 'tools/index.php';
$this->_pages['tools/polls/index.php']['children']  = array('tools/polls/add.php');
$this->_pages['tools/polls/index.php']['guide']     = 'instructor/?p=11.0.polls.php';

	$this->_pages['tools/polls/add.php']['title_var'] = 'add_poll';
	$this->_pages['tools/polls/add.php']['parent']    = 'tools/polls/index.php';

	$this->_pages['tools/polls/edit.php']['title_var'] = 'edit_poll';
	$this->_pages['tools/polls/edit.php']['parent']    = 'tools/polls/index.php';

	$this->_pages['tools/polls/delete.php']['title_var'] = 'delete_poll';
	$this->_pages['tools/polls/delete.php']['parent']    = 'tools/polls/index.php';

?>