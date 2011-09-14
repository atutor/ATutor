<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_LINKS', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_group_tool = $_student_tool = 'mods/_standard/links/index.php';

//modules sub-content
$this->_list['links'] = array('title_var'=>'links','file'=>'mods/_standard/links/sublinks.php');

//tool manager
//$this->_tool['links'] = array('title_var'=>'links','file'=>'links_tool.php');

/*$this->_pages['mods/_standard/links/tools/index.php']['title_var'] = 'links';
$this->_pages['mods/_standard/links/tools/index.php']['parent']    = 'tools/index.php';
$this->_pages['mods/_standard/links/tools/index.php']['children'] = array('mods/_standard/links/tools/add.php', 'mods/_standard/links/tools/categories.php', 'mods/_standard/links/tools/categories_create.php');
$this->_pages['mods/_standard/links/tools/index.php']['guide'] = 'instructor/?p=links.php';

	$this->_pages['mods/_standard/links/tools/add.php']['title_var']  = 'add_link';
	$this->_pages['mods/_standard/links/tools/add.php']['parent'] = 'mods/_standard/links/tools/index.php';

	$this->_pages['mods/_standard/links/tools/edit.php']['title_var']  = 'edit_link';
	$this->_pages['mods/_standard/links/tools/edit.php']['parent'] = 'mods/_standard/links/tools/index.php';

	$this->_pages['mods/_standard/links/tools/delete.php']['title_var']  = 'delete_link';
	$this->_pages['mods/_standard/links/tools/delete.php']['parent'] = 'mods/_standard/links/tools/index.php';

	$this->_pages['mods/_standard/links/tools/categories.php']['title_var']  = 'categories';
	$this->_pages['mods/_standard/links/tools/categories.php']['parent'] = 'mods/_standard/links/tools/index.php';

	$this->_pages['mods/_standard/links/tools/categories_create.php']['title_var']  = 'create_category';
	$this->_pages['mods/_standard/links/tools/categories_create.php']['parent'] = 'mods/_standard/links/tools/index.php';

	$this->_pages['mods/_standard/links/tools/categories_edit.php']['title_var']  = 'edit_category';
	$this->_pages['mods/_standard/links/tools/categories_edit.php']['parent'] = 'mods/_standard/links/tools/categories.php';

	$this->_pages['mods/_standard/links/tools/categories_delete.php']['title_var']  = 'delete_category';
	$this->_pages['mods/_standard/links/tools/categories_delete.php']['parent'] = 'mods/_standard/links/tools/categories.php';
*/

//instructor & group pages
$this->_pages['mods/_standard/links/tools/index.php']['title_var'] = 'manage_links';
$this->_pages['mods/_standard/links/tools/index.php']['parent']    = 'mods/_standard/links/index.php';
$this->_pages['mods/_standard/links/tools/index.php']['children'] = array('mods/_standard/links/tools/add.php', 'mods/_standard/links/tools/categories.php', 'mods/_standard/links/tools/categories_create.php');
$this->_pages['mods/_standard/links/tools/index.php']['guide'] = 'instructor/?p=links.php';

	$this->_pages['mods/_standard/links/tools/add.php']['title_var']  = 'add_link';
	$this->_pages['mods/_standard/links/tools/add.php']['parent'] = 'mods/_standard/links/tools/index.php';

	$this->_pages['mods/_standard/links/tools/edit.php']['title_var']  = 'edit_link';
	$this->_pages['mods/_standard/links/tools/edit.php']['parent'] = 'mods/_standard/links/tools/index.php';

	$this->_pages['mods/_standard/links/tools/delete.php']['title_var']  = 'delete_link';
	$this->_pages['mods/_standard/links/tools/delete.php']['parent'] = 'mods/_standard/links/tools/index.php';

	$this->_pages['mods/_standard/links/tools/categories.php']['title_var']  = 'categories';
	$this->_pages['mods/_standard/links/tools/categories.php']['parent'] = 'mods/_standard/links/tools/index.php';

	$this->_pages['mods/_standard/links/tools/categories_create.php']['title_var']  = 'create_category';
	$this->_pages['mods/_standard/links/tools/categories_create.php']['parent'] = 'mods/_standard/links/tools/index.php';

	$this->_pages['mods/_standard/links/tools/categories_edit.php']['title_var']  = 'edit_category';
	$this->_pages['mods/_standard/links/tools/categories_edit.php']['parent'] = 'mods/_standard/links/tools/categories.php';

	$this->_pages['mods/_standard/links/tools/categories_delete.php']['title_var']  = 'delete_category';
	$this->_pages['mods/_standard/links/tools/categories_delete.php']['parent'] = 'mods/_standard/links/tools/categories.php';

//student pages
$this->_pages['mods/_standard/links/index.php']['title_var'] = 'links';
$this->_pages['mods/_standard/links/index.php']['children']  = array('mods/_standard/links/add.php', 'mods/_standard/links/tools/index.php');
$this->_pages['mods/_standard/links/index.php']['img']       = 'images/home-links.png';
$this->_pages['mods/_standard/links/index.php']['icon']       = 'images/home-links_sm.png';

	$this->_pages['mods/_standard/links/add.php']['title_var'] = 'suggest_link';
	$this->_pages['mods/_standard/links/add.php']['parent']    = 'mods/_standard/links/index.php';


function links_get_group_url($group_id) {
	global $db;
	$sql = "SELECT cat_id FROM ".TABLE_PREFIX."links_categories WHERE owner_id=$group_id and owner_type=".LINK_CAT_GROUP;
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		return 'mods/_standard/links/index.php?cat_parent_id='.$row['cat_id'].'&search=&filter=Filter';
	} 

	return 'mods/_standard/links/index.php';
}

?>