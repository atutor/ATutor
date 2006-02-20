<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');

$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED, 0, TRUE);
$keys = array_keys($module_list);

echo '<ol id="tools">';
foreach ($keys as $module_name) {
	$module =& $module_list[$module_name];
	if ($module->getPrivilege() && authenticate($module->getPrivilege(), AT_PRIV_RETURN)) {
		if ($parent = $module->getChildPage('tools/index.php')) {
			echo '<li class="top-tool">  <a href="' . $parent . '">' . $module->getName() . '</a>  ';
			if ($_pages[$parent]['children']) {
				echo '<ul class="child-top-tool">';
				foreach ($_pages[$parent]['children'] as $child) {
					echo '<li class="child-tool"><a href="'.$child.'">'._AT($_pages[$child]['title_var']).'</a></li>';
				}
				echo '</ul>';
			}
			echo '</li>';
		}
	}
}
echo '</ol>';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>