<?php

/*******
 * this function named [module_name]_delete is called whenever a course content is deleted
 * which includes when restoring a backup with override set, or when deleting an entire course.
 * the function must delete all module-specific material associated with this course.
 * $course is the ID of the course to delete.
 */

function adobe_connect_delete($course) {

    global $db;

    // commented, adobe_connect_tasks could be used as log
    $sql = "DELETE FROM ".TABLE_PREFIX."adobe_connect_tasks WHERE course_id=$course";
    // mysql_query($sql, $db);
}

?>
