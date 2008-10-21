<?php
/*******
 * module_uninstall.php performs reversion of module_install.php
 */

/*******
 * the line below safe-guards this file from being accessed directly from
 * a web browser. It will only execute if required from within an ATutor script,
 * in our case the Module::install() method.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/********
 * the following code is used for removing a module-specific directory created in module_install.php.
 * it generates appropriate error messages to aid in its creation.
 */
$directory = AT_CONTENT_DIR .'hello_world';

// check if the directory exists
if (is_dir($directory)) {
	require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

	if (!clr_dir($directory))
		$msg->addError(array('MODULE_UNINSTALL', '<li>'.$directory.' can not be removed. Please manually remove it.</li>'));
}

/******
 * the following code checks if there are any errors (generated previously)
 * then uses the SqlUtility to run reverted database queries of module.sql, 
 * ie. "create table" statement in module.sql is run as drop according table.
 */
if (!$msg->containsErrors() && file_exists(dirname(__FILE__) . '/module.sql')) {
	// deal with the SQL file:
	require(AT_INCLUDE_PATH . 'classes/sqlutility.class.php');
	$sqlUtility =& new SqlUtility();

	/*
	 * the SQL file could be stored anywhere, and named anything, "module.sql" is simply
	 * a convention we're using.
	 */
	$sqlUtility->revertQueryFromFile(dirname(__FILE__) . '/module.sql', TABLE_PREFIX);
}

?>