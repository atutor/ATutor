<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

$section = 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

if($_REQUEST['t']){
	$_SESSION['lang']	 = $_REQUEST['t'];
	$_SESSION['charset'] = $langcharset[$thislang];
}

if ($_GET['file_missing']){
	$errors[]=AT_ERROR_LANG_MISSING;

}

if ($_GET['lang_exists']){
	$warnings[]=AT_WARNING_LANG_EXISTS;

}
require(AT_INCLUDE_PATH.'admin_html/header.inc.php');

echo '<h2>'._AT('server_configuration').'</h2>';
if (isset($_GET['f'])) { 
	$f = intval($_GET['f']);
	if ($f <= 0) {
		/* it's probably an array */
		$f = unserialize(urldecode($_GET['f']));
	}
	print_feedback($f);
}
if (isset($errors)) { print_errors($errors); }
if(isset($warnings)){ print_warnings($warnings); }

echo '<br /><p>ATutor'._AT('version').': '.VERSION;
//echo '<br />Default Language: '.DEFAULT_LANGUAGE;
echo '</p>';
?>

<?php
require(AT_INCLUDE_PATH.'cc_html/footer.inc.php');
?>