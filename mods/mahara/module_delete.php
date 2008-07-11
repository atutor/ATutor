<?php
/*******
 * This function is used for uninstalling Mahara
 * At the moment it is not being called from anywhere in the module.
 */

function delete_mahara() {
	global $db;

	// delete mahara entries (in case we don't have 'drop' priviledges)
	$sql = "DELETE FROM ".TABLE_PREFIX."mahara";
	if (!(mysql_query($sql, $db))) echo "Error deleting from ".TABLE_PREFIX."mahara. ";

	// drop mahara table
	$sql = "DROP TABLE IF EXISTS ".TABLE_PREFIX."mahara";
	if (!(mysql_query($sql, $db))) echo "Error dropping table, ".TABLE_PREFIX."mahara. ";

    // Also remove added language stuff
	$sql = "DELETE FROM ".TABLE_PREFIX."language_text WHERE "
        . "term='mahara' OR term='mahara_location' OR term='mahara_new_win' OR term='mahara_opened' "
        . "OR term='AT_ERROR_MAHARA_MINURL_ADD_EMPTY' OR term='AT_ERROR_MAHARA_ERROR_INSTALL' "
        . "OR term='AT_ERROR_MAHARA_ERROR_PATH' OR term='AT_FEEDBACK_MAHARA_LOGIN' OR term='AT_FEEDBACK_MAHARA_MINURL_ADD_SAVED'";
	if (!(mysql_query($sql, $db))) echo "Error delete rows from ".TABLE_PREFIX."language_text. ";


    // Remove mahara entry from config
	$sql = "DELETE FROM ".TABLE_PREFIX."config WHERE name='mahara'";
	if (!(mysql_query($sql, $db))) echo "Error deleting from ".TABLE_PREFIX."config. ";

    // Remove mahara from modules
	$sql = "DELETE FROM ".TABLE_PREFIX."modules WHERE dir_name='mahara'";
	if (!(mysql_query($sql, $db))) echo "Error deleting from ".TABLE_PREFIX."modules. ";
}

?>