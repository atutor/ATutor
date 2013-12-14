<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (!$_SESSION['groups']) {
	$msg->addInfo('NOT_IN_ANY_GROUPS');
	$error = 1;

}

require(AT_INCLUDE_PATH.'header.inc.php');
if(isset($error)){
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$group_list = implode(',', $_SESSION['groups']);

$sql = "SELECT group_id, title, modules FROM %sgroups WHERE group_id IN (%s) ORDER BY title";
$rows_groups = queryDB($sql, array(TABLE_PREFIX, $group_list));

echo '<ol id="tools">';
foreach($rows_groups as $row){
	echo '<li class="top-tool">'.AT_print($row['title'], 'groups.title') . ' ';

	$modules = explode('|', $row['modules']);
	asort($modules);

	if ($modules) {
		echo '<ul class="child-top-tool">';
		foreach ($modules as $module_name) {
			$fn = basename($module_name) . '_get_group_url';
			$module =& $moduleFactory->getModule($module_name);
			if ($module->isEnabled() && function_exists($fn)) {
				echo '<li class="child-tool"><a href="'. url_rewrite($fn($row['group_id'])) .'">'._AT($_pages[$module->getGroupTool()]['title_var']).'</a></li>';
			}
		}
		echo '</ul>';
	}
	echo '</li>';
}
echo '</ol>';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>