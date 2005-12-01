<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

$_module_pages['tools/enrollment/index.php']['children'] = array('tools/enrollment/groups.php');

$_module_pages['tools/enrollment/groups.php']['title_var'] = 'groups';
$_module_pages['tools/enrollment/groups.php']['parent']    = 'tools/enrollment/index.php';
$_module_pages['tools/enrollment/groups.php']['children']  = array('tools/enrollment/groups_manage.php');

	$_module_pages['tools/enrollment/groups_manage.php']['title_var'] = 'create_group';
	$_module_pages['tools/enrollment/groups_manage.php']['parent']    = 'tools/enrollment/groups.php';

	$_module_pages['tools/enrollment/groups_members.php']['title_var'] = 'group_members';
	$_module_pages['tools/enrollment/groups_members.php']['parent']    = 'tools/enrollment/groups.php';
?>