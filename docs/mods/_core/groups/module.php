<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

$this->_pages['tools/enrollment/index.php']['children'] = array('tools/enrollment/groups.php');

$this->_pages['tools/enrollment/groups.php']['title_var'] = 'groups';
$this->_pages['tools/enrollment/groups.php']['parent']    = 'tools/enrollment/index.php';
$this->_pages['tools/enrollment/groups.php']['children']  = array('tools/enrollment/groups_manage.php');

	$this->_pages['tools/enrollment/groups_manage.php']['title_var'] = 'create_group';
	$this->_pages['tools/enrollment/groups_manage.php']['parent']    = 'tools/enrollment/groups.php';

	$this->_pages['tools/enrollment/groups_members.php']['title_var'] = 'group_members';
	$this->_pages['tools/enrollment/groups_members.php']['parent']    = 'tools/enrollment/groups.php';
?>