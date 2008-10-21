<?php
/*==============================================================
  Photo Album
 ==============================================================
  Copyright (c) 2006 by Dylan Cheon & Kelvin Wong
  Institute for Assistive Technology / University of Victoria
  http://www.canassist.ca/                                    
                                                               
  This program is free software. You can redistribute it and/or
  modify it under the terms of the GNU General Public License  
  as published by the Free Software Foundation.                
 ==============================================================
 */
// $Id:

/**
 * @desc	This file installs the photo album module
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */
 
/**
 * @desc the line below safe-guards this file from being accessed directly from a web browser. It will only execute if required from within an ATutor script, in our case the Module::uninstall() method.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!defined('AT_MODULE_PATH')) { exit; }
//define('AT_MODULE_PATH', realpath(AT_INCLUDE_PATH.'../mods') . DIRECTORY_SEPARATOR);

/********
 * the following code is used for removing a module-specific directory created in module_install.php.
 * it generates appropriate error messages to aid in its creation.
 */
$pa_array[0]=AT_CONTENT_DIR.'photo_album';

// check if the directory exists
foreach ($pa_array as $directory){
	if (is_dir($directory)) {
		require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
	
		if (!clr_dir($directory))
			$msg->addError(array('MODULE_UNINSTALL', '<li>'.$directory.' can not be removed. Please manually remove it.</li>'));
	}
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

	/**
	 * the SQL file could be stored anywhere, and named anything, "module.sql" is simply
	 * a convention we're using.
	 */
	$sqlUtility->revertQueryFromFile(dirname(__FILE__) . '/module.sql', TABLE_PREFIX);
}

?>
