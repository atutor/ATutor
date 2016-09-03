<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_FORUMS',       $this->getPrivilege() );
define('AT_ADMIN_PRIV_FORUMS', $this->getAdminPrivilege() );

// if this module is to be made available to students on the Home or Main Navigation
$_group_tool = $_student_tool = 'mods/_standard/forums/forum/list.php';

//side dropdown
$this->_stacks['posts'] = array('title_var'=>'posts','file'=>AT_INCLUDE_PATH.'../mods/_standard/forums/dropdown/posts.inc.php');

//modules sub-content
$this->_list['forums'] = array('title_var'=>'forums','file'=>'mods/_standard/forums/sublinks.php');

//tool manager
$this->_tool['forums'] = array('title_var'=>'forums','file'=>'mods/_core/tool_manager/forums_tool.php');

//instructor pages
$this->_pages['mods/_standard/forums/index.php']['title_var'] = 'forums';
$this->_pages['mods/_standard/forums/index.php']['parent']    = 'tools/index.php';
$this->_pages['mods/_standard/forums/index.php']['guide']     = 'instructor/?p=forums.php';
$this->_pages['mods/_standard/forums/index.php']['img']        = 'images/home-forums.png';
$this->_pages['mods/_standard/forums/index.php']['children']  = array('mods/_standard/forums/add_forum.php');

$this->_pages['mods/_standard/forums/add_forum.php']['title_var']  = 'create_forum';
$this->_pages['mods/_standard/forums/add_forum.php']['parent'] = 'mods/_standard/forums/index.php';
$this->_pages['mods/_standard/forums/add_forum.php']['children']  = array('mods/_standard/forums/forum/list.php','search.php?search_within=forums');

$this->_pages['mods/_standard/forums/delete_forum.php']['title_var']  = 'delete_forum';
$this->_pages['mods/_standard/forums/delete_forum.php']['parent'] = 'mods/_standard/forums/forums/index.php';

$this->_pages['mods/_standard/forums/edit_forum.php']['title_var']  = 'edit_forum';
$this->_pages['mods/_standard/forums/edit_forum.php']['parent'] = 'mods/_standard/forums/index.php';

$this->_pages['mods/_standard/forums/add_forum.php']['title_var']  = 'create_forum';
$this->_pages['mods/_standard/forums/add_forum.php']['parent'] = 'mods/_standard/forums/forum/list.php';

//student pages
$this->_pages['mods/_standard/forums/forum/list.php']['title_var']  = 'forums';
$this->_pages['mods/_standard/forums/forum/list.php']['img']        = 'images/home-forums.png';
$this->_pages['mods/_standard/forums/forum/list.php']['icon']		  = 'images/pin.png';		//added favicon
//$this->_pages['forum/list.php']['text']		  = 'Sezione Forum';				//added text

// The instructor course admin tools
if($_SESSION['is_admin'] > 0 || authenticate(AT_PRIV_FORUMS, TRUE)){	
$fid = intval($_GET['fid']);

$this->_pages_i['mods/_standard/forums/add_forum.php']['title_var']  = 'create_forum';
$this->_pages_i['mods/_standard/forums/add_forum.php']['other_parent'] = 'mods/_standard/forums/forum/list.php';
$this->_pages_i['mods/_standard/forums/forum/list.php']['children']        = array('mods/_standard/forums/add_forum.php');
$this->_pages_i['mods/_standard/forums/edit_forum.php']['title_var'] = 'edit_forum';
$this->_pages_i['mods/_standard/forums/edit_forum.php']['other_parent']    = 'mods/_standard/forums/forum/index.php';
$this->_pages_i['mods/_standard/forums/forum/index.php']['children'] = array('mods/_standard/forums/edit_forum.php');

}
$this->_pages['mods/_standard/forums/forum/list.php']['children']        = array('search.php?search_within=forums');

	//list.php's children
	$this->_pages['search.php?search_within=forums']['title_var'] = 'search';
	$this->_pages['search.php?search_within=forums']['parent']    = 'mods/_standard/forums/index.php';

// for admin
if (admin_authenticate(AT_ADMIN_PRIV_FORUMS, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$this->_pages['mods/_core/courses/admin/courses.php']['children'] = array('mods/_standard/forums/admin/forums.php');
		$this->_pages['mods/_standard/forums/admin/forums.php']['parent']    = 'mods/_core/courses/admin/courses.php';
	} else {
		$this->_pages[AT_NAV_ADMIN] = array('mods/_standard/forums/admin/forums.php');
		$this->_pages['mods/_standard/forums/admin/forums.php']['parent'] = AT_NAV_ADMIN;
	}

	$this->_pages['mods/_standard/forums/admin/forums.php']['title_var'] = 'forums';
	$this->_pages['mods/_standard/forums/admin/forums.php']['guide']     = 'mods/_standard/forums/admin/?p=forums.php';
	$this->_pages['mods/_standard/forums/admin/forums.php']['children']  = array('mods/_standard/forums/admin/forum_add.php');

		$this->_pages['mods/_standard/forums/admin/forum_add.php']['title_var'] = 'create_forum';
		$this->_pages['mods/_standard/forums/admin/forum_add.php']['parent']    = 'mods/_standard/forums/admin/forums.php';

		$this->_pages['mods/_standard/forums/admin/forum_edit.php']['title_var'] = 'edit_forum';
		$this->_pages['mods/_standard/forums/admin/forum_edit.php']['parent']    = 'mods/_standard/forums/admin/forums.php';

		$this->_pages['mods/_standard/forums/admin/forum_delete.php']['title_var'] = 'delete_forum';
		$this->_pages['mods/_standard/forums/admin/forum_delete.php']['parent']    = 'mods/_standard/forums/admin/forums.php';
}

function forums_get_group_url($group_id) {
	$sql = "SELECT forum_id FROM %sforums_groups WHERE group_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $group_id), TRUE);
	return 'mods/_standard/forums/forum/index.php?fid='.$row['forum_id'];
}
?>