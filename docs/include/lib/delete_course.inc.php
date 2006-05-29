<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay,Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$


function delete_course($course, $material) {
	global $db, $moduleFactory;

	//unset s_cid var
	if ($material == TRUE) {
		unset($_SESSION['s_cid']);
	}

	$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED | AT_MODULE_STATUS_DISABLED);
	$keys = array_keys($module_list);

	//loop through mods and call delete function
	foreach ($keys as $module_name) {
		if ($module_name == '_core/groups') {
			continue;
		}
		if ($module_name == '_core/enrolment') {
			continue;
		}
		$module =& $module_list[$module_name];

		if (($material === TRUE) || isset($material[$module_name])) {
			$module->delete($course);
		}
	}

	// groups and enrollment must be deleted last because that info is used by other modules

	if (($material === TRUE) || isset($material['_core/groups'])) {
		$module =& $moduleFactory->getModule('_core/groups');
		$module->delete($course);
	}
	if (($material === TRUE) || isset($material['_core/enrolment'])) {
		$module =& $moduleFactory->getModule('_core/enrolment');
		$module->delete($course);
	}

	if ($material === TRUE) {
		// delete actual course
		$sql = "DELETE FROM ".TABLE_PREFIX."courses WHERE course_id=$course";
		$result = mysql_query($sql, $db);
	}
}
?>