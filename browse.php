<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$

$_user_location = 'public';

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

unset($_SESSION['course_id']);

if($_config['allow_browse']){
	unset($_SESSION['course_id']);

if($_config['allow_browse']){
	require(AT_INCLUDE_PATH.'html/browse.inc.php');
} else {
 	header("Location:".$_base_href );
}
} else {
 	header("Location:".$_base_href );
}
?>