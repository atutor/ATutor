<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
	
$_course_privilege = TRUE; // possible values: FALSE | AT_PRIV_ADMIN | TRUE

// check if both constants defined
if (!defined('AC_PATH') || !defined('AC_TABLE_PREFIX')) {	
	$msg->addError(array('MODULE_INSTALL', '<li>You must uncomment and define the AC_PATH and AC_TABLE_PREFIX variables at the bottom of the ./mod/acollab/module.php file.</li>'));

} else {

	// check if file exists at path location
	if (!@file_get_contents(AC_PATH)) {
		$msg->addError(array('MODULE_INSTALL', '<li>AC_PATH is not defined correctly. Cannot find ACollab installation. Edit <kbd>mods/acollab/module.php</kbd> and specify the path to ACollab.</li>'));
	} else {
		global $db;
		//check if can select an acollab table w/ prefix
		$sql = "SELECT * FROM ".AC_TABLE_PREFIX."files_revisions WHERE 1 LIMIT 1";
		$result = @mysql_query($sql, $db);
		if (!$result) {	
			$msg->addError(array('MODULE_INSTALL', '<li>AC_TABLE_PREFIX is not defined correctly. Cannot select ACollab table.</li>'));
		}
	}
}

?>