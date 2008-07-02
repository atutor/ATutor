<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

global $next_prev_links;
global $_base_path, $_my_uri;
global $_stacks, $db;
global $system_courses;

$side_menu = array();
$stack_files = array();

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

$theme_img  = $_base_path . 'themes/'. $_SESSION['prefs']['PREF_THEME'] . '/images/';
$savant->assign('img', $theme_img);

if (isset($err)) {
	$err->showErrors(); // print all the errors caught on this page
}
$savant->assign('side_menu', $stack_files);

// this js is indep of the theme used:
?>
<script language="javascript" type="text/javascript">
//<!--
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

if ($framed || $popup) {
	$savant->display('include/fm_footer.tmpl.php');
} else {
	$savant->display('include/footer.tmpl.php');
}

//Harris Timer
  $mtime = microtime(); 
  $mtime = explode(" ", $mtime); 
  $mtime = $mtime[1] + $mtime[0]; 
  $endtime = $mtime; 
  $totaltime = ($endtime - $starttime); 
  debug ($totaltime. ' seconds.', "TIME USED"); 
//Harris Timer Ends

if (defined('AT_DEVEL') && AT_DEVEL) {
	debug(TABLE_PREFIX, 'TABLE_PREFIX');
	debug(DB_NAME, 'DB_NAME');
	debug(VERSION, 'VERSION');
	debug($_SESSION);
}

?>