<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_GROUPS', $this->getPrivilege());

$_student_tool = 'groups.php';


$this->_pages['tools/groups/index.php']['title_var'] = 'groups';
$this->_pages['tools/groups/index.php']['parent']    = 'tools/index.php';
$this->_pages['tools/groups/index.php']['children']  = array('tools/groups/create.php');
$this->_pages['tools/groups/index.php']['guide']  = 'instructor/?p=groups.php';

	$this->_pages['tools/groups/edit_group.php']['title_var'] = 'edit';
	$this->_pages['tools/groups/edit_group.php']['parent']    = 'tools/groups/index.php';

	$this->_pages['tools/groups/delete_group.php']['title_var'] = 'delete';
	$this->_pages['tools/groups/delete_group.php']['parent']    = 'tools/groups/index.php';

	$this->_pages['tools/groups/edit_type.php']['title_var'] = 'edit';
	$this->_pages['tools/groups/edit_type.php']['parent']    = 'tools/groups/index.php';

	$this->_pages['tools/groups/delete_type.php']['title_var'] = 'delete';
	$this->_pages['tools/groups/delete_type.php']['parent']    = 'tools/groups/index.php';

	$this->_pages['tools/groups/create.php']['title_var'] = 'create_groups';
	$this->_pages['tools/groups/create.php']['parent']    = 'tools/groups/index.php';

		$this->_pages['tools/groups/create_manual.php']['title_var'] = 'groups_create_manual';
		$this->_pages['tools/groups/create_manual.php']['parent']    = 'tools/groups/create.php';

		$this->_pages['tools/groups/create_automatic.php']['title_var'] = 'groups_create_automatic';
		$this->_pages['tools/groups/create_automatic.php']['parent']    = 'tools/groups/create.php';

	$this->_pages['tools/groups/members.php']['title_var'] = 'group_members';
	$this->_pages['tools/groups/members.php']['parent']    = 'tools/groups/index.php';

// student stuff
$this->_pages['groups.php']['title_var'] = 'groups';
$this->_pages['groups.php']['img']       = 'images/home-acollab.gif';

?>