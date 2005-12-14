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

if ((isset($_REQUEST['popup']) && $_REQUEST['popup']) && 
	(!isset($_REQUEST['framed']) || !$_REQUEST['framed'])) {
	$popup = TRUE;
	$framed = FALSE;
} else if (isset($_REQUEST['framed']) && $_REQUEST['framed'] && isset($_REQUEST['popup']) && $_REQUEST['popup']) {
	$popup = TRUE;
	$framed = TRUE;
} else {
	$popup = FALSE;
	$framed = FALSE;
}

require('top.php');

require(AT_INCLUDE_PATH.'html/filemanager_display.inc.php');

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
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>