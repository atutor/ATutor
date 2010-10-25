<?php
/*******
 * this function named [module_name]_delete is called whenever a course content is deleted
 * which includes when restoring a backup with override set, or when deleting an entire course.
 * the function must delete all module-specific material associated with this course.
 * $course is the ID of the course to delete.
 */

function cerify_delete($course) {
	global $db;

/*
	// delete hello_world course table entries
	$sql =  "DELETE FROM ".TABLE_PREFIX."certify, ".TABLE_PREFIX."certify_members, ".TABLE_PREFIX."certify_tests ";
	$sql .= "USING ".TABLE_PREFIX."certify INNER JOIN ".TABLE_PREFIX."certify_members INNER JOIN ".TABLE_PREFIX."certify_tests ";
	$sql .= "WHERE ".TABLE_PREFIX."certify.course = $course ";
	$sql .= "AND ".TABLE_PREFIX."certify.certify_id = ".TABLE_PREFIX."certify_members.certify_id ";
	$sql .= "AND ".TABLE_PREFIX."certify.certify_id = ".TABLE_PREFIX."certify_tests.certify_id";
	mysql_query($sql, $db);
*/

//	$sql = 'DELETE members FROM '.TABLE_PREFIX.'certify AS certify INNER JOIN '.TABLE_PREFIX.'certify_members AS members WHERE certify.course='.$course.' AND certify.certify_id=members.certify_id';
//	mysql_query($sql, $db);
	$sql = 'DELETE tests FROM '.TABLE_PREFIX.'certify AS certify INNER JOIN '.TABLE_PREFIX.'certify_tests AS tests WHERE certify.course='.$course.' AND certify.certify_id=tests.certify_id';
	mysql_query($sql, $db);
	$sql = 'DELETE FROM '.TABLE_PREFIX.'certify AS certify WHERE certify.course='.$course;
	mysql_query($sql, $db);


}

?>