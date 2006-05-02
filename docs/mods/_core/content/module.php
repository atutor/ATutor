<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_CONTENT', $this->getPrivilege());

//side menu dropdowns
$this->_stacks['menu_menu'] = array('title_var'=>'menu_menu', 'file'=>AT_INCLUDE_PATH.'html/dropdowns/menu_menu.inc.php');
$this->_stacks['related_topics'] = array('title_var'=>'related_topics', 'file'=>AT_INCLUDE_PATH.'html/dropdowns/related_topics.inc.php');
$this->_stacks['search'] = array('title_var'=>'search', 'file'=>AT_INCLUDE_PATH.'html/dropdowns/search.inc.php');


$this->_pages['search.php']['title_var']      = 'search';

$this->_pages['tools/content/index.php']['title_var'] = 'content';
$this->_pages['tools/content/index.php']['parent']    = 'tools/index.php';
$this->_pages['tools/content/index.php']['guide']     = 'instructor/?p=content.php';
$this->_pages['tools/content/index.php']['children']  = array('editor/add_content.php', 'tools/ims/index.php');

	$this->_pages['editor/add_content.php']['title_var']    = 'add_content';
	$this->_pages['editor/add_content.php']['parent']   = 'tools/content/index.php';
	$this->_pages['editor/add_content.php']['guide']     = 'instructor/?p=creating_editing_content.php';

	$this->_pages['editor/edit_content.php']['title_var'] = 'edit_content';
	$this->_pages['editor/edit_content.php']['parent']    = 'tools/content/index.php';
	$this->_pages['editor/edit_content.php']['guide']     = 'instructor/?p=creating_editing_content.php';

	$this->_pages['editor/delete_content.php']['title_var'] = 'delete_content';
	$this->_pages['editor/delete_content.php']['parent']    = 'tools/content/index.php';

?>