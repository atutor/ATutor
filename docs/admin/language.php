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
$_include_path = '../include/';
require($_include_path.'vitals.inc.php');
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
require($_include_path.'admin_html/header.inc.php');


echo '<h2>'._AT('lang_manager').'</h2>';

require('translate.php');

require($_include_path.'cc_html/footer.inc.php');
?>