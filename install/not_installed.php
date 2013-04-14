<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INSTALLER_INCLUDE_PATH', 'include/');
define('AT_INCLUDE_PATH', '../include/');
error_reporting(E_ALL ^ E_NOTICE);

require(AT_INCLUDE_PATH . 'lib/constants.inc.php');

$new_version = VERSION;

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

require(AT_INSTALLER_INCLUDE_PATH.'header.php');
?>

<div id="feedback">
<p>ATutor is ready to be installed or upgraded. <a href="index.php">Continue on to Step 1 of the setup process</a>.</p>
</div>

<?php require(AT_INSTALLER_INCLUDE_PATH.'footer.php'); ?>