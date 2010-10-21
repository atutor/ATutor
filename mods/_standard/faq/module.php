<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_FAQ', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'mods/_standard/faq/index.php';

//modules sub-content
$this->_list['faq'] = array('title_var'=>'faq','file'=>'mods/_standard/faq/sublinks.php');

// instructor Manage section:
$this->_pages['mods/_standard/faq/index_instructor.php']['title_var'] = 'faq';
$this->_pages['mods/_standard/faq/index_instructor.php']['parent']    = 'tools/index.php';
$this->_pages['mods/_standard/faq/index_instructor.php']['children']  = array('mods/_standard/faq/add_topic.php', 'mods/_standard/faq/add_question.php');
$this->_pages['mods/_standard/faq/index_instructor.php']['guide']     = 'instructor/?p=faq.php';


	$this->_pages['mods/_standard/faq/add_topic.php']['title_var'] = 'add_topic';
	$this->_pages['mods/_standard/faq/add_topic.php']['parent']    = 'mods/_standard/faq/index_instructor.php';

	$this->_pages['mods/_standard/faq/delete_topic.php']['title_var'] = 'delete';
	$this->_pages['mods/_standard/faq/delete_topic.php']['parent']    = 'mods/_standard/faq/index_instructor.php';

	$this->_pages['mods/_standard/faq/edit_topic.php']['title_var'] = 'edit';
	$this->_pages['mods/_standard/faq/edit_topic.php']['parent']    = 'mods/_standard/faq/index_instructor.php';

	$this->_pages['mods/_standard/faq/add_question.php']['title_var'] = 'add_question';
	$this->_pages['mods/_standard/faq/add_question.php']['parent']    = 'mods/_standard/faq/index_instructor.php';

	$this->_pages['mods/_standard/faq/delete_question.php']['title_var'] = 'delete';
	$this->_pages['mods/_standard/faq/delete_question.php']['parent']    = 'mods/_standard/faq/index_instructor.php';

	$this->_pages['mods/_standard/faq/edit_question.php']['title_var'] = 'edit';
	$this->_pages['mods/_standard/faq/edit_question.php']['parent']    = 'mods/_standard/faq/index_instructor.php';

// student page:
$this->_pages['mods/_standard/faq/index.php']['title_var'] = 'faq';
$this->_pages['mods/_standard/faq/index.php']['img']       = 'images/home-faq.png';
$this->_pages['mods/_standard/faq/index.php']['icon']       = 'images/home-faq_sm.png';

?>