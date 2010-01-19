<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

if (!defined('AT_PRIV_CONTENT')) {
	define('AT_PRIV_CONTENT', $this->getPrivilege());
}

global $_custom_css;
$_custom_css = AT_BASE_HREF."jscripts/infusion/components/inlineEdit/css/InlineEdit.css";

//side menu dropdowns
$this->_stacks['menu_menu'] = array('title_var'=>'menu_menu', 'file'=>AT_INCLUDE_PATH.'html/dropdowns/menu_menu.inc.php');
$this->_stacks['related_topics'] = array('title_var'=>'related_topics', 'file'=>AT_INCLUDE_PATH.'html/dropdowns/related_topics.inc.php');
$this->_stacks['search'] = array('title_var'=>'search', 'file'=>AT_INCLUDE_PATH.'html/dropdowns/search.inc.php');


$this->_pages['search.php']['title_var']      = 'search';

$this->_pages['tools/content/index.php']['title_var'] = 'content';
$this->_pages['tools/content/index.php']['parent']    = 'tools/index.php';
$this->_pages['tools/content/index.php']['guide']     = 'instructor/?p=content.php';
$this->_pages['tools/content/index.php']['children']  = array('mods/_core/editor/add_content.php', 'mods/_core/editor/arrange_content.php', 'mods/_core/imscp/index.php');

$this->_pages['mods/_core/editor/add_content.php']['title_var']    = 'add_content';
$this->_pages['mods/_core/editor/add_content.php']['parent']   = 'tools/content/index.php';
$this->_pages['mods/_core/editor/add_content.php']['guide']     = 'instructor/?p=creating_editing_content.php';

$this->_pages['mods/_core/editor/arrange_content.php']['title_var']    = 'arrange_content';
$this->_pages['mods/_core/editor/arrange_content.php']['parent']   = 'tools/content/index.php';
$this->_pages['mods/_core/editor/arrange_content.php']['guide']     = 'instructor/?p=arrange_content.php';

$this->_pages['mods/_core/editor/edit_content.php']['title_var'] = 'edit_content';
$this->_pages['mods/_core/editor/edit_content.php']['parent']    = 'tools/content/index.php';
$this->_pages['mods/_core/editor/edit_content.php']['guide']     = 'instructor/?p=creating_editing_content.php';

if (!isset($_GET['cid']) && !isset($_POST['cid']))
	$this->_pages['mods/_core/editor/edit_content_folder.php']['title_var'] = 'add_content_folder';
else
	$this->_pages['mods/_core/editor/edit_content_folder.php']['title_var'] = 'edit_content_folder';
$this->_pages['mods/_core/editor/edit_content_folder.php']['parent']    = 'tools/content/index.php';
$this->_pages['mods/_core/editor/edit_content_folder.php']['guide']     = 'instructor/?p=creating_editing_content_folder.php';

$this->_pages['mods/_core/editor/delete_content.php']['title_var'] = 'delete_content';
$this->_pages['mods/_core/editor/delete_content.php']['parent']    = 'tools/content/index.php';

?>