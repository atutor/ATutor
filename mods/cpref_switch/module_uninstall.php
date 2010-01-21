<?php
/*******
 * module_uninstall.php performs reversion of module_install.php
 */

/*******
 * the line below safe-guards this file from being accessed directly from
 * a web browser. It will only execute if required from within an ATutor script,
 * in our case the Module::uninstall() method.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/********
 * the following code is used for removing a module-specific directory created in module_install.php.
 * it generates appropriate error messages to aid in its creation.
 */
$directory = AT_CONTENT_DIR .'cpref_switch';

// check if the directory exists
if (is_dir($directory)) {
	require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

	if (!clr_dir($directory))
		$msg->addError(array('MODULE_UNINSTALL', '<li>'.$directory.' can not be removed. Please manually remove it.</li>'));
}

?>