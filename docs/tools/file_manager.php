<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$page = 'file_manager';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if ($_GET['popup']) {
	header('Location: filemanager/filemanager_window.php?overwrite='.urlencode($_GET['overwrite']).SEP.'pathext='.urlencode($_GET['pathext']));
	exit;
} else {
	header('Location: filemanager/index.php?overwrite='.urlencode($_GET['overwrite']).SEP.'pathext='.urlencode($_GET['pathext']));
	exit;
}

?>