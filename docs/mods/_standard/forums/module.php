<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_FORUMS',       $this->getPrivilege() );
define('AT_ADMIN_PRIV_FORUMS', $this->getAdminPrivilege() );


// if this module is to be made available to students on the Home or Main Navigation
$_group_tool = $_student_tool = 'forum/list.php';

//side dropdown
$this->_stacks['posts'] = array('title_var'=>'posts','file'=>AT_INCLUDE_PATH.'html/dropdowns/posts.inc.php');

//instructor pages
$this->_pages['tools/forums/index.php']['title_var'] = 'forums';
$this->_pages['tools/forums/index.php']['parent']    = 'tools/index.php';
$this->_pages['tools/forums/index.php']['guide']     = 'instructor/?p=3.0.forums.php';
$this->_pages['tools/forums/index.php']['children']  = array('editor/add_forum.php');

	$this->_pages['editor/add_forum.php']['title_var']  = 'create_forum';
	$this->_pages['editor/add_forum.php']['parent'] = 'tools/forums/index.php';

	$this->_pages['editor/delete_forum.php']['title_var']  = 'delete_forum';
	$this->_pages['editor/delete_forum.php']['parent'] = 'tools/forums/index.php';

	$this->_pages['editor/edit_forum.php']['title_var']  = 'edit_forum';
	$this->_pages['editor/edit_forum.php']['parent'] = 'tools/forums/index.php';

//student pages
$this->_pages['forum/list.php']['title_var']  = 'forums';
$this->_pages['forum/list.php']['img']        = 'images/home-forums.gif';

// for admin
if (admin_authenticate(AT_ADMIN_PRIV_FORUMS, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$this->_pages['admin/courses.php']['children'] = array('admin/forums.php');
		$this->_pages['admin/forums.php']['parent']    = 'admin/courses.php';
	} else {
		$this->_pages[AT_NAV_ADMIN] = array('admin/forums.php');
		$this->_pages['admin/forums.php']['parent'] = AT_NAV_ADMIN;
	}

	$this->_pages['admin/forums.php']['title_var'] = 'forums';
	$this->_pages['admin/forums.php']['guide']     = 'admin/?p=4.3.forums.php';
	$this->_pages['admin/forums.php']['children']  = array('admin/forum_add.php');

		$this->_pages['admin/forum_add.php']['title_var'] = 'create_forum';
		$this->_pages['admin/forum_add.php']['parent']    = 'admin/forums.php';

		$this->_pages['admin/forum_edit.php']['title_var'] = 'edit_forum';
		$this->_pages['admin/forum_edit.php']['parent']    = 'admin/forums.php';

		$this->_pages['admin/forum_delete.php']['title_var'] = 'delete_forum';
		$this->_pages['admin/forum_delete.php']['parent']    = 'admin/forums.php';
}
?>