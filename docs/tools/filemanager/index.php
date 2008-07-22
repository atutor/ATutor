<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');


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


if (isset($_SESSION['flash']) && $_SESSION['flash'] == "yes") {
    // Add <script> tags to header to handle fluid
    $fluid_dir = 'jscripts/fluid-components/';
    $framed = intval($_GET['framed']);
    $popup = intval($_GET['popup']);
    $current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';
    $pathext = urldecode($_GET['pathext']);
    $_custom_head .= '
        <link href="'.$fluid_dir.'css/infusion-theme.css" rel="stylesheet" type="text/css" />
        <link href="'.$fluid_dir.'css/Uploader.css" rel="stylesheet" type="text/css" />

        <script src="'.$fluid_dir.'js/jquery/jquery-1.2.3.js" type="text/javascript"></script>
        <script src="'.$fluid_dir.'js/jquery/jquery.tabindex.js" type="text/javascript"></script>
        <script src="'.$fluid_dir.'js/jquery/jARIA.js" type="text/javascript"></script>
        <script src="'.$fluid_dir.'js/jquery/jquery.dimensions.js" type="text/javascript"></script>
        <script src="'.$fluid_dir.'js/jquery/ui.base.js" type="text/javascript"></script>
        <script src="'.$fluid_dir.'js/jquery/jquery.keyboard-a11y.js" type="text/javascript"></script>
        <script src="'.$fluid_dir.'js/swfupload/swfupload.js" type="text/javascript"></script>
        <script src="'.$fluid_dir.'js/fluid/Fluid.js" type="text/javascript"></script>
        <script src="'.$fluid_dir.'js/fluid/Uploader.js" type="text/javascript"></script>

        <script language="JavaScript" type="text/javascript">
            
            // set to empty to use demo upload js code instead actual server side upload handlers
            var uploadURL = "include/lib/upload.php?path='.urlencode($current_path.$pathext).'";  // relative to the swf file
            var flashURL = "jscripts/fluid-components/swfupload/swfupload_f9.swf";
            
            var settings =   {
                whenDone: "'.$_SERVER['PHP_SELF'].'?pathext=' . urlencode($pathext) . SEP . 'popup=' . $popup . SEP . 'framed=' . $framed . SEP . 'msg=FILEUPLOAD_DONE",
                whenCancel: "'.$_SERVER['PHP_SELF'].'?pathext=' . urlencode($pathext) . SEP . 'popup=' . $popup . SEP . 'framed=' . $framed . SEP . 'msg=FILEUPLOAD_DONE",
                continueAfterUpload: true,
                debug: false
            };
            
            var myUpload; // mostly used for testing
            $(document).ready(function() {
                myUpload = new fluid.Uploader("single-inline-fluid-uploader", uploadURL, flashURL, settings);
            });
        </script>
        <style type="text/css">
            .fluid-uploader {
                margin-top: 2em;
                padding: 1em 2em;
                display: block;
                clear: both;
            }
        </style>
    ';
}

global $msg;
if (isset($_GET['msg']))
	$msg -> addFeedback($_GET['msg']);

require('top.php');
$_SESSION['done'] = 1;

require(AT_INCLUDE_PATH.'html/filemanager_display.inc.php');

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