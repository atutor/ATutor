<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
$page = 'file_manager';

if ((isset($_REQUEST['popup']))  && ($_REQUEST['popup'] == TRUE) && 
	(!isset($_REQUEST['framed']) || $_REQUEST['framed'] == FALSE)) {
	$_header_file = AT_INCLUDE_PATH.'fm_header.php';
	$_footer_file = AT_INCLUDE_PATH.'fm_footer.php';
	$popup = TRUE;
	$framed = FALSE;
} else if ((isset($_REQUEST['framed'])) && ($_REQUEST['framed'] == TRUE)) {
	$_header_file = AT_INCLUDE_PATH.'fm_header.php';
	$_footer_file = AT_INCLUDE_PATH.'fm_footer.php';
	$popup = TRUE;
	$framed = TRUE;
}
else {
	$_header_file = AT_INCLUDE_PATH.'header.inc.php';
	$_footer_file = AT_INCLUDE_PATH.'footer.inc.php';
	$popup = FALSE;
	$framed = FALSE;
}

require('top.php');

/*
if (!$popup && !$framed) {
	$msg->addHelp('FILEMANAGER2');
	$msg->addHelp('FILEMANAGER3');
	$msg->addHelp('FILEMANAGER4');
}
*/

require('filemanager.php');

closedir($dir);


?>
<script type="text/javascript">
<!--
function Checkall(form){ 
  for (var i = 0; i < form.elements.length; i++){    
    eval("form.elements[" + i + "].checked = form.checkall.checked");  
  } 
}
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
-->
</script>
<?php require($_footer_file); ?>