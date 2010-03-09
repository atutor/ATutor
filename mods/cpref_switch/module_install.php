<?php
/*******
 * the line below safe-guards this file from being accessed directly from
 * a web browser. It will only execute if required from within an ATutor script,
 * in our case the Module::install() method.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/*******
 * Note: the many options for these variables are used to decrease confusion.
 *       TRUE | FALSE | 1 will be the convention.
 *
 * $_course_privilege
 *     specifies the type of instructor privilege this module uses.
 *     set to empty | FALSE | 0   to disable any privileges.
 *     set to 1 | AT_PRIV_ADMIN   to use the instructor only privilege.
 *     set to TRUE | 'new'        to create a privilege specifically for this module:
 *                                will make this module available as a student privilege.
 *
 * $_admin_privilege
 *    specifies the type of ATutor administrator privilege this module uses.
 *    set to FALSE | AT_ADMIN_PRIV_ADMIN   to use the super administrator only privilege.
 *    set to TRUE | 'new'                  to create a privilege specifically for this module:
 *                                         will make this module available as an administrator privilege.
 *
 *
 * $_cron_interval
 *    if non-zero specifies in minutes how often the module's cron job should be run.
 *    set to 0 or not set to disable.
 */
$_course_privilege = TRUE; // possible values: FALSE | AT_PRIV_ADMIN | TRUE


/********
 * the following code is used for creating a module-specific directory.
 * it generates appropriate error messages to aid in its creation.
 */
$directory = AT_CONTENT_DIR .'cpref_switch';

// check if the directory is writeable
if (!is_dir($directory) && !@mkdir($directory)) {
	$msg->addError(array('MODULE_INSTALL', '<li>'.$directory.' does not exist. Please create it.</li>'));
} else if (!is_writable($directory) && @chmod($directory, 0666)) {
	$msg->addError(array('MODULE_INSTALL', '<li>'.$directory.' is not writeable. On Unix issue the command <kbd>chmod a+rw</kbd>.</li>'));
}

/******
 * the following code checks if there are any errors (generated previously)
 * then uses the SqlUtility to run any database queries it needs, ie. to create
 * its own tables.
 */
if (!$msg->containsErrors() && file_exists(dirname(__FILE__) . '/module.sql')) {
	// deal with the SQL file:
	require(AT_INCLUDE_PATH . 'classes/sqlutility.class.php');
	$sqlUtility =& new SqlUtility();

	/*
	 * the SQL file could be stored anywhere, and named anything, "module.sql" is simply
	 * a convention we're using.
	 */
	$sqlUtility->queryFromFile(dirname(__FILE__) . '/module.sql', TABLE_PREFIX);
}

?>