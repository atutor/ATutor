<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_FAQ', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'faq/index.php';


// instructor Manage section:
$this->_pages['faq/index_instructor.php']['title_var'] = 'faq';
$this->_pages['faq/index_instructor.php']['parent']    = 'tools/index.php';
$this->_pages['faq/index_instructor.php']['children']  = array('faq/add_topic.php', 'faq/add_question.php');
$this->_pages['faq/index_instructor.php']['guide']     = 'instructor/?p=16.0.faq.php';


	$this->_pages['faq/add_topic.php']['title_var'] = 'add_topic';
	$this->_pages['faq/add_topic.php']['parent']    = 'faq/index_instructor.php';

	$this->_pages['faq/delete_topic.php']['title_var'] = 'delete';
	$this->_pages['faq/delete_topic.php']['parent']    = 'faq/index_instructor.php';

	$this->_pages['faq/edit_topic.php']['title_var'] = 'edit';
	$this->_pages['faq/edit_topic.php']['parent']    = 'faq/index_instructor.php';

	$this->_pages['faq/add_question.php']['title_var'] = 'add_question';
	$this->_pages['faq/add_question.php']['parent']    = 'faq/index_instructor.php';

	$this->_pages['faq/delete_question.php']['title_var'] = 'delete';
	$this->_pages['faq/delete_question.php']['parent']    = 'faq/index_instructor.php';

	$this->_pages['faq/edit_question.php']['title_var'] = 'edit';
	$this->_pages['faq/edit_question.php']['parent']    = 'faq/index_instructor.php';

// student page:
$this->_pages['faq/index.php']['title_var'] = 'faq';
$this->_pages['faq/index.php']['img']       = 'faq/icon.gif';

?>