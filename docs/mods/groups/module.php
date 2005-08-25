<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

//$_pages['tools/enrollment/index.php']['children'][] = 'tools/enrollment/groups.php';

$_pages['tools/enrollment/groups.php']['title_var'] = 'groups';
$_pages['tools/enrollment/groups.php']['parent']    = 'tools/enrollment/index.php';
$_pages['tools/enrollment/groups.php']['children']  = array('tools/enrollment/groups_manage.php');

	$_pages['tools/enrollment/groups_manage.php']['title_var'] = 'create_group';
	$_pages['tools/enrollment/groups_manage.php']['parent']    = 'tools/enrollment/groups.php';

	$_pages['tools/enrollment/groups_members.php']['title_var'] = 'group_members';
	$_pages['tools/enrollment/groups_members.php']['parent']    = 'tools/enrollment/groups.php';
?>