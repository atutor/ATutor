<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');

if (!$_SESSION['groups']) {
	$msg->printErrors('NOT_IN_ANY_GROUPS');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$group_list = implode(',', $_SESSION['groups']);
$sql = "SELECT group_id, title, modules FROM ".TABLE_PREFIX."groups WHERE group_id IN ($group_list) ORDER BY title";
$result = mysql_query($sql, $db);

echo '<ol id="tools">';

while ($row = mysql_fetch_assoc($result)) {
	echo '<li class="top-tool">'.$row['title'] . ' ';

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