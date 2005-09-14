<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay,Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$


function delete_course($course) {
	global $db, $moduleFactory;

	$module_list = $moduleFactory->getModules(AT_MODULE_ENABLED | AT_MODULE_CORE);
	$keys = array_keys($module_list);

	//loop through mods and call delete function
	foreach ($keys as $module_name) {
		$module =& $module_list[$module_name];
		$module->delete($course);
	}

	// delete actual course
	$sql = "DELETE FROM ".TABLE_PREFIX."courses WHERE course_id=$course";
	$result = mysql_query($sql, $db);

}
?>