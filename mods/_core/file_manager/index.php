<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_href.'jscripts/infusion/components/uploader/css/Uploader.css';

require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');
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

// If Flash is detected, call the necessary css and js, and configure settings to use the Fluid Uploader
if (isset($_SESSION['flash']) && $_SESSION['flash'] == "yes") {
    /* Provide the option of switching between Fluid Uploader and simple single file uploader
       and save the user preference as a cookie */
    if (!isset($_COOKIE["fluid_on"]))
        ATutor.setcookie("fluid_on", "yes", time()+1200); 

    $fluid_dir = 'jscripts/infusion/';
    $framed = intval($_GET['framed']);
    $popup = intval($_GET['popup']);
    $current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

    if ($_GET['pathext'] != '') {
        $pathext = urldecode($_GET['pathext']);
    } else if ($_POST['pathext'] != '') {
        $pathext = $_POST['pathext'];
    }

    if($_GET['back'] == 1) {
        $pathext  = substr($pathext, 0, -1);
        $slashpos = strrpos($pathext, '/');
        if($slashpos == 0) {
            $pathext = '';
        } else {
            $pathext = substr($pathext, 0, ($slashpos+1));
        }

    }
}

global $msg;
if (isset($_GET['msg']))
	$msg -> addFeedback($_GET['msg']);

require('top.php');
$_SESSION['done'] = 1;

require(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager_display.inc.php');

closedir($dir);

?>
<script type="text/javascript">
//<!--
function Checkall(form){ 
  for (var i = 0; i < form.elements.length; i++){    
    eval("form.elements[" + i + "].checked = form.checkall.checked");  
  } 
}
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
//-->
</script>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
