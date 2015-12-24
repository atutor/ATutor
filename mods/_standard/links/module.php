<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_LINKS', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_group_tool = $_student_tool = 'mods/_standard/links/index.php';

//modules sub-content
$this->_list['links'] = array('title_var'=>'links','file'=>'mods/_standard/links/sublinks.php');

//instructor & group pages
$this->_pages['mods/_standard/links/tools/index.php']['title_var'] = 'manage_links';
$this->_pages['mods/_standard/links/tools/index.php']['parent'] = 'mods/_standard/links/index.php';
$this->_pages['mods/_standard/links/tools/index.php']['children'] = array('mods/_standard/links/tools/add.php', 'mods/_standard/links/tools/categories.php', 'mods/_standard/links/tools/categories_create.php');
$this->_pages['mods/_standard/links/tools/index.php']['guide'] = 'instructor/?p=links.php';

$this->_pages['mods/_standard/links/tools/add.php']['title_var'] = 'add_link';
$this->_pages['mods/_standard/links/tools/add.php']['parent'] = 'mods/_standard/links/tools/index.php';

$this->_pages['mods/_standard/links/tools/edit.php']['title_var'] = 'edit_link';
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
$this->_pages['mods/_standard/links/index.php']['children'] = array('mods/_standard/links/add.php');
$this->_pages['mods/_standard/links/index.php']['img'] = 'images/home-links.png';
$this->_pages['mods/_standard/links/index.php']['icon'] = 'images/home-links_sm.png';

$this->_pages['mods/_standard/links/add.php']['title_var'] = 'suggest_link';
$this->_pages['mods/_standard/links/add.php']['parent'] = 'mods/_standard/links/index.php';

if($_SESSION['is_admin'] > 0 || authenticate(AT_PRIV_LINKS, TRUE)){	
$this->_pages_i['mods/_standard/links/tools/index.php']['title_var'] = 'manage_links';
$this->_pages_i['mods/_standard/links/tools/index.php']['other_parent'] = 'mods/_standard/links/index.php';
$this->_pages_i['mods/_standard/links/tools/add.php']['title_var'] = 'add_link';
$this->_pages_i['mods/_standard/links/tools/add.php']['other_parent'] = 'mods/_standard/links/tools/index.php';
$this->_pages_i['mods/_standard/links/tools/categories_create.php']['title_var']  = 'create_category';
$this->_pages_i['mods/_standard/links/tools/categories_create.php']['other_parent'] = 'mods/_standard/links/tools/index.php';
$this->_pages_i['mods/_standard/links/tools/categories.php']['title_var']  = 'categories';
$this->_pages_i['mods/_standard/links/tools/categories.php']['parent'] = 'mods/_standard/links/tools/index.php';


$this->_pages_i['mods/_standard/links/index.php']['children'] = array('mods/_standard/links/tools/add.php', 'mods/_standard/links/tools/categories.php', 'mods/_standard/links/tools/categories_create.php', 'mods/_standard/links/tools/index.php');


    //$this->_pages['mods/_standard/chat/index.php']['children']  = array();
}


function links_get_group_url($group_id) {
    // Adding queryDB to what might be a broken SQL query. This query might return multiple rows and only the first one is selected.
    $result = queryDB("SELECT cat_id FROM %slinks_categories WHERE owner_id=%d and owner_type=%d", array(TABLE_PREFIX, $group_id, LINK_CAT_GROUP));
    $row = (is_array($result) && count($result) > 0) ? $result[0] : null;
    if ($row) {
        return 'mods/_standard/links/index.php?cat_parent_id='.$row['cat_id'].'&search=&filter=Filter';
    }

    return 'mods/_standard/links/index.php';
}

?>