<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
	
global $db;

// check if both constants defined
if (!defined('AC_PATH') || !defined('AC_TABLE_PREFIX')) {	
	$msg->addError(array('MODULE_INSTALL', '<li>You must define the AC_PATH and AC_TABLE_PREFIX variables at the bottom of the mod/acollab/module.php file.</li>'));

} else {

	// check if file exists at path location
	if (!@file_get_contents(AC_PATH)) {
		$msg->addError(array('MODULE_INSTALL', '<li>AC_PATH is defined incorrectly. Cannot find ACollab installation.</li>'));
	}

	//check if can select an acollab table w/ prefix
	$sql = "SELECT * FROM ".AC_TABLE_PREFIX."files_revisions WHERE 1";
	$result = @mysql_query($sql, $db);
	if (empty($result)) {	
		$msg->addError(array('MODULE_INSTALL', '<li>AC_TABLE_PREFIX is defined incorrectly. Cannot select ACollab table.</li>'));
	}

}

?>