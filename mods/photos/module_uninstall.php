<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institution  */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$

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
$directory = AT_PA_CONTENT_DIR;

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