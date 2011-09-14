<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_POLLS', $this->getPrivilege());

//side dropdown
$this->_stacks['poll'] = array('title_var'=>'poll','file'=>AT_INCLUDE_PATH.'../mods/_standard/polls/dropdown/poll.inc.php');

//modules sub-content
$this->_list['polls'] = array('title_var'=>'polls','file'=>'mods/_standard/polls/sublinks.php');

//tool manager
//$this->_tool['polls'] = array('title_var'=>'polls','file'=>'polls_tool.php');

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'mods/_standard/polls/index.php';

$this->_pages['mods/_standard/polls/index.php']['title_var'] = 'polls';
$this->_pages['mods/_standard/polls/index.php']['img']       = 'images/home-polls.png';
$this->_pages['mods/_standard/polls/index.php']['icon']       = 'images/home-polls_sm.png';

$this->_pages['mods/_standard/polls/tools/index.php']['title_var'] = 'polls';
$this->_pages['mods/_standard/polls/tools/index.php']['parent']    = 'tools/index.php';
$this->_pages['mods/_standard/polls/tools/index.php']['children']  = array('mods/_standard/polls/tools/add.php');
$this->_pages['mods/_standard/polls/tools/index.php']['guide']     = 'instructor/?p=polls.php';

$this->_pages['mods/_standard/polls/tools/add.php']['title_var'] = 'add_poll';
$this->_pages['mods/_standard/polls/tools/add.php']['parent']    = 'mods/_standard/polls/tools/index.php';

$this->_pages['mods/_standard/polls/tools/edit.php']['title_var'] = 'edit_poll';
$this->_pages['mods/_standard/polls/tools/edit.php']['parent']    = 'mods/_standard/polls/tools/index.php';

$this->_pages['mods/_standard/polls/tools/delete.php']['title_var'] = 'delete_poll';
$this->_pages['mods/_standard/polls/tools/delete.php']['parent']    = 'mods/_standard/polls/tools/index.php';


?>