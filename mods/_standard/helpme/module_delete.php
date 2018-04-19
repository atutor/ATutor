<?php
/*******
 * this function named [module_name]_delete is called whenever a course content is deleted
 * which includes when restoring a backup with override set, or when deleting an entire course.
 * the function must delete all module-specific material associated with this course.
 * $course is the ID of the course to delete.
 */

function helpme_delete($course) {
    //  global $db;

    // delete hello_world course table entries
    //$sql = "DELETE FROM ".TABLE_PREFIX."helpme WHERE course_id=$course";
    //mysql_query($sql, $db);

    // delete hello_world course files
    //$path = AT_CONTENT_DIR .'hello_world/' . $course .'/';
    //clr_dir($path);
}

?>