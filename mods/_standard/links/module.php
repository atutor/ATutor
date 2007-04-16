<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_LINKS', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_group_tool = $_student_tool = 'links/index.php';

/*$this->_pages['tools/links/index.php']['title_var'] = 'links';
$this->_pages['tools/links/index.php']['parent']    = 'tools/index.php';
$this->_pages['tools/links/index.php']['children'] = array('tools/links/add.php', 'tools/links/categories.php', 'tools/links/categories_create.php');
$this->_pages['tools/links/index.php']['guide'] = 'instructor/?p=links.php';

	$this->_pages['tools/links/add.php']['title_var']  = 'add_link';
	$this->_pages['tools/links/add.php']['parent'] = 'tools/links/index.php';

	$this->_pages['tools/links/edit.php']['title_var']  = 'edit_link';
	$this->_pages['tools/links/edit.php']['parent'] = 'tools/links/index.php';

	$this->_pages['tools/links/delete.php']['title_var']  = 'delete_link';
	$this->_pages['tools/links/delete.php']['parent'] = 'tools/links/index.php';

	$this->_pages['tools/links/categories.php']['title_var']  = 'categories';
	$this->_pages['tools/links/categories.php']['parent'] = 'tools/links/index.php';

	$this->_pages['tools/links/categories_create.php']['title_var']  = 'create_category';
	$this->_pages['tools/links/categories_create.php']['parent'] = 'tools/links/index.php';

	$this->_pages['tools/links/categories_edit.php']['title_var']  = 'edit_category';
	$this->_pages['tools/links/categories_edit.php']['parent'] = 'tools/links/categories.php';

	$this->_pages['tools/links/categories_delete.php']['title_var']  = 'delete_category';
	$this->_pages['tools/links/categories_delete.php']['parent'] = 'tools/links/categories.php';
*/

//instructor & group pages
$this->_pages['tools/links/index.php']['title_var'] = 'manage_links';
$this->_pages['tools/links/index.php']['parent']    = 'links/index.php';
$this->_pages['tools/links/index.php']['children'] = array('tools/links/add.php', 'tools/links/categories.php', 'tools/links/categories_create.php');
$this->_pages['tools/links/index.php']['guide'] = 'instructor/?p=links.php';

	$this->_pages['tools/links/add.php']['title_var']  = 'add_link';
	$this->_pages['tools/links/add.php']['parent'] = 'tools/links/index.php';

	$this->_pages['tools/links/edit.php']['title_var']  = 'edit_link';
	$this->_pages['tools/links/edit.php']['parent'] = 'tools/links/index.php';

	$this->_pages['tools/links/delete.php']['title_var']  = 'delete_link';
	$this->_pages['tools/links/delete.php']['parent'] = 'tools/links/index.php';

	$this->_pages['tools/links/categories.php']['title_var']  = 'categories';
	$this->_pages['tools/links/categories.php']['parent'] = 'tools/links/index.php';

	$this->_pages['tools/links/categories_create.php']['title_var']  = 'create_category';
	$this->_pages['tools/links/categories_create.php']['parent'] = 'tools/links/index.php';

	$this->_pages['tools/links/categories_edit.php']['title_var']  = 'edit_category';
	$this->_pages['tools/links/categories_edit.php']['parent'] = 'tools/links/categories.php';

	$this->_pages['tools/links/categories_delete.php']['title_var']  = 'delete_category';
	$this->_pages['tools/links/categories_delete.php']['parent'] = 'tools/links/categories.php';

//student pages
$this->_pages['links/index.php']['title_var'] = 'links';
$this->_pages['links/index.php']['children']  = array('links/add.php', 'tools/links/index.php');
$this->_pages['links/index.php']['img']       = 'images/home-links.gif';

	$this->_pages['links/add.php']['title_var'] = 'suggest_link';
	$this->_pages['links/add.php']['parent']    = 'links/index.php';


function links_get_group_url($group_id) {
	global $db;
	$sql = "SELECT cat_id FROM ".TABLE_PREFIX."links_categories WHERE owner_id=$group_id and owner_type=".LINK_CAT_GROUP;
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		return 'links/index.php?cat_parent_id='.$row['cat_id'].'&search=&filter=Filter';
	} 

	return 'links/index.php';
}

?>