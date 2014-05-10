<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_GROUPS', $this->getPrivilege());

//modules sub-content
$this->_list['groups'] = array('title_var'=>'groups','file'=>'mods/_core/groups/sublinks.php');

$_student_tool = 'mods/_core/groups/groups.php';

$this->_pages['mods/_core/groups/index.php']['title_var'] = 'groups';
$this->_pages['mods/_core/groups/index.php']['parent']    = 'tools/index.php';
$this->_pages['mods/_core/groups/index.php']['img']       = 'images/home-acollab.png';
$this->_pages['mods/_core/groups/index.php']['children']  = array('mods/_core/groups/create.php');
$this->_pages['mods/_core/groups/index.php']['guide']  = 'instructor/?p=groups.php';
$this->_pages['mods/_core/groups/index.php']['avail_in_mobile']   = false;

	$this->_pages['mods/_core/groups/edit_group.php']['title_var'] = 'edit';
	$this->_pages['mods/_core/groups/edit_group.php']['parent']    = 'mods/_core/groups/index.php';

	$this->_pages['mods/_core/groups/delete_group.php']['title_var'] = 'delete';
	$this->_pages['mods/_core/groups/delete_group.php']['parent']    = 'mods/_core/groups/index.php';

	$this->_pages['mods/_core/groups/edit_type.php']['title_var'] = 'edit';
	$this->_pages['mods/_core/groups/edit_type.php']['parent']    = 'mods/_core/groups/index.php';

	$this->_pages['mods/_core/groups/delete_type.php']['title_var'] = 'delete';
	$this->_pages['mods/_core/groups/delete_type.php']['parent']    = 'mods/_core/groups/index.php';

	$this->_pages['mods/_core/groups/create.php']['title_var'] = 'create_groups';
	$this->_pages['mods/_core/groups/create.php']['parent']    = 'mods/_core/groups/index.php';

		$this->_pages['mods/_core/groups/create_manual.php']['title_var'] = 'groups_create_manual';
		$this->_pages['mods/_core/groups/create_manual.php']['parent']    = 'mods/_core/groups/create.php';

		$this->_pages['mods/_core/groups/create_automatic.php']['title_var'] = 'groups_create_automatic';
		$this->_pages['mods/_core/groups/create_automatic.php']['parent']    = 'mods/_core/groups/create.php';

	$this->_pages['mods/_core/groups/members.php']['title_var'] = 'group_members';
	$this->_pages['mods/_core/groups/members.php']['parent']    = 'mods/_core/groups/index.php';
if($_SESSION['is_admin'] > 0 || authenticate(AT_PRIV_GROUPS, TRUE)){	
	$this->_pages_i['mods/_core/groups/create.php']['title_var'] = 'create_groups';
	$this->_pages_i['mods/_core/groups/create.php']['parent']    = 'mods/_core/groups/index.php';
    $this->_pages_i['mods/_core/groups/groups.php']['children']  = array('mods/_core/groups/create.php');
 $this->_pages['mods/_core/groups/groups.php']['children']  = array();
}
// student stuff
$this->_pages['mods/_core/groups/groups.php']['title_var'] = 'groups';
$this->_pages['mods/_core/groups/groups.php']['img']       = 'images/home-acollab.png';
$this->_pages['mods/_core/groups/groups.php']['text']      = _AT('groups_text');

?>