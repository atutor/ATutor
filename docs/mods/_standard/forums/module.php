<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'moduleproxy'))) { exit(__FILE__ . ' is not a ModuleProxy'); }

define('AT_PRIV_FORUMS',       $this->getPrivilege() );
define('AT_ADMIN_PRIV_FORUMS', $this->getAdminPrivilege() );


// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'forum/list.php';

//side dropdown
$_module_stacks['posts'] = array('title_var'=>'posts','file'=>AT_INCLUDE_PATH.'html/dropdowns/posts.inc.php');

//instructor pages
$_module_pages['tools/forums/index.php']['title_var'] = 'forums';
$_module_pages['tools/forums/index.php']['parent']    = 'tools/index.php';
$_module_pages['tools/forums/index.php']['guide']     = 'instructor/?p=3.0.forums.php';
$_module_pages['tools/forums/index.php']['children']  = array('editor/add_forum.php');

	$_module_pages['editor/add_forum.php']['title_var']  = 'create_forum';
	$_module_pages['editor/add_forum.php']['parent'] = 'tools/forums/index.php';

	$_module_pages['editor/delete_forum.php']['title_var']  = 'delete_forum';
	$_module_pages['editor/delete_forum.php']['parent'] = 'tools/forums/index.php';

	$_module_pages['editor/edit_forum.php']['title_var']  = 'edit_forum';
	$_module_pages['editor/edit_forum.php']['parent'] = 'tools/forums/index.php';

//student pages
$_module_pages['forum/list.php']['title_var']  = 'forums';
$_module_pages['forum/list.php']['img']        = 'images/home-forums.gif';

// for admin
if (admin_authenticate(AT_ADMIN_PRIV_FORUMS, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$_module_pages['admin/courses.php']['children'] = array('admin/forums.php');
		$_module_pages['admin/forums.php']['parent']    = 'admin/courses.php';
	} else {
		$_module_pages[AT_NAV_ADMIN] = array('admin/forums.php');
		$_module_pages['admin/forums.php']['parent'] = AT_NAV_ADMIN;
	}

	$_module_pages['admin/forums.php']['title_var'] = 'forums';
	$_module_pages['admin/forums.php']['guide']     = 'admin/?p=4.3.forums.php';
	$_module_pages['admin/forums.php']['children']  = array('admin/forum_add.php');

		$_module_pages['admin/forum_add.php']['title_var'] = 'create_forum';
		$_module_pages['admin/forum_add.php']['parent']    = 'admin/forums.php';

		$_module_pages['admin/forum_edit.php']['title_var'] = 'edit_forum';
		$_module_pages['admin/forum_edit.php']['parent']    = 'admin/forums.php';

		$_module_pages['admin/forum_delete.php']['title_var'] = 'delete_forum';
		$_module_pages['admin/forum_delete.php']['parent']    = 'admin/forums.php';
}
?>