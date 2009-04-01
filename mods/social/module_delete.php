<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

/*******
 * this function named [module_name]_delete is called whenever a course content is deleted
 * which includes when restoring a backup with override set, or when deleting an entire course.
 * the function must delete all module-specific material associated with this course.
 * $course is the ID of the course to delete.
 */

function hello_world_delete($course) {
	global $db;

	// delete hello_world course table entries
	$sql = "DELETE FROM ".TABLE_PREFIX."hello_world WHERE course_id=$course";
	mysql_query($sql, $db);

	// delete hello_world course files
	$path = AT_CONTENT_DIR .'hello_world/' . $course .'/';
	clr_dir($path);
}

?>