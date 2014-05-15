<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

global $next_prev_links;
global $_base_path, $_my_uri;
global $_stacks, $db;
global $system_courses;
global $_config_defaults;
global $_config;

$side_menu = array();
$stack_files = array();

function get_custom_logo() {
    global $_config;
    $path_gif = AT_CONTENT_DIR.'logos/custom_logo.gif';
    $path_jpg = AT_CONTENT_DIR.'logos/custom_logo.jpg';
    $path_png = AT_CONTENT_DIR.'logos/custom_logo.png';
    
    if(file_exists($path_gif))
        $ext = 'gif';
    else if(file_exists($path_jpg))
        $ext = 'jpg';
    else if(file_exists($path_png))
        $ext = 'png';
    
    if($_config['custom_logo_enabled'] && isset($ext) && ($ext=='gif' || $ext=='jpg' || $ext=='png')) {
        if (defined('AT_FORCE_GET_FILE')) {
            if(isset($_REQUEST['cid']) && $_REQUEST['cid']>0) {
                $file = 'custom_logo.'.$ext;
            } else if(!isset($_REQUEST['cid']) && isset($_SESSION['course_id']) && $_SESSION['course_id']>0) {
                $file = 'get.php/custom_logo.'.$ext;
            } else {
                $file = 'get_custom_logo.php';
            }
        } else {
            $dir = 'content/logos/custom_logo.';
            $file = $dir.$ext;
        }
        $path_to_logo = $file;
        
    } else {
        if($_SESSION['prefs']['PREF_THEME']=='atspaces') {
            $path_to_logo = AT_BASE_HREF."themes/atspaces/images/atspaces_logo49.jpg";
        } else {
            $path_to_logo = AT_BASE_HREF."images/AT_Logo_1_sm.png";
        }
    }
        
    return $path_to_logo;
}
 
$custom_logo_url = $_config_defaults['custom_logo_url'];
$custom_logo_alt_text = $_config_defaults['custom_logo_alt_text'];
if($_SESSION['prefs']['PREF_THEME'] == 'atspaces') {
    $custom_logo_alt_text = 'ATutorSpaces Logo';
}
if($_config['custom_logo_enabled']) {
    $custom_logo_url = $_config['custom_logo_url'];
    $custom_logo_alt_text = $_config['custom_logo_alt_text'];
}

$savant->assign('custom_logo_url', $custom_logo_url);
$savant->assign('custom_logo_alt_text', $custom_logo_alt_text);

if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0) {
	$savant->assign('my_uri', $_my_uri);

	$savant->assign('right_menu_open', TRUE);
	$savant->assign('popup_help', 'MAIN_MENU');
	$savant->assign('menu_url', '<a name="menu"></a>');
	$savant->assign('close_menu_url', htmlspecialchars($_my_uri).'disable=PREF_MAIN_MENU');
	$savant->assign('close_menus', _AT('close_menus'));

	//copyright can be found in include/html/copyright.inc.php

	$side_menu = explode('|', $system_courses[$_SESSION['course_id']]['side_menu']);

	foreach ($side_menu as $side) {
		if (isset($_stacks[$side])) {
			$stack_files[] = $_stacks[$side]['file'];
		}
	}
}

$theme_img  = AT_print($_base_path, 'url.base') . 'themes/'. $_SESSION['prefs']['PREF_THEME'] . '/images/';
$savant->assign('img', $theme_img);

if (isset($err)) {
	$err->showErrors(); // print all the errors caught on this page
}
$savant->assign('side_menu', $stack_files);

// this js is indep of the theme used:
?>
<script type="text/javascript">
//<!--z
var selected;
function rowselect(obj) {
	obj.className = 'selected';
	if (selected && selected != obj.id)
		document.getElementById(selected).className = '';
	selected = obj.id;
}
function rowselectbox(obj, checked, handler) {
	var functionDemo = new Function(handler + ";");
	functionDemo();

	if (checked)
		obj.className = 'selected';
	else
		obj.className = '';
}
//-->
</script>
<?php

//TODO******************BOLOGNA***************REMOVE ME ***********************/
if(isset($_GET['popup'])){
	$popup = intval($_GET['popup']);
}
$footerName = 'footer';
if ($framed || $popup) {
    $footerName = (isset($tool_flag) && ($tool_flag)) ? 'tm_footer' : 'fm_footer';
}

//Harris Timer
  $mtime = microtime(); 
  $mtime = explode(" ", $mtime); 
  $mtime = $mtime[1] + $mtime[0]; 
  $endtime = $mtime; 
  $totaltime = ($endtime - $starttime); 

//Harris Timer Ends

if (defined('AT_DEVEL') && AT_DEVEL) {
    echo '<br /><hr style="clear:both;">';
    debug ($totaltime. ' seconds.', "TIME USED"); 
	debug(TABLE_PREFIX, 'TABLE_PREFIX');
	debug(DB_NAME, 'DB_NAME');
	debug(VERSION, 'VERSION');
	debug($_SESSION, 'SESSION:');
	debug($_config, 'CONFIGURATION');
}

$savant->display(sprintf('include/%s.tmpl.php', $footerName));

?>