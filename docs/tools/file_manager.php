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

if ($_GET['popup']) {
	header('Location: filemanager/filemanager_window.php?overwrite='.$_GET['overwrite']);
	exit;
} else {
	header('Location: filemanager/index.php?overwrite='.$_GET['overwrite']);
	exit;
}

?>