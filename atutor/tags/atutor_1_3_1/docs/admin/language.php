<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$section = 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if (!$_SESSION['s_is_super_admin']) {
	exit;
}


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

echo '<h2>'._AT('language').'</h2>';
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

require('translate.php');

require(AT_INCLUDE_PATH.'cc_html/footer.inc.php');
?>