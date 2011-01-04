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
// $Id: profile_picture.php 10142 2010-08-17 19:17:26Z hwong $

define('AT_INCLUDE_PATH', '../../../include/');
$_user_location	= 'users';
require (AT_INCLUDE_PATH.'vitals.inc.php');
require ('save_profile_picture.php');

if (in_array('mods/_standard/photos/index.php', $_modules)){
	require(AT_PA_INCLUDE.'profile_album.inc.php');
} else {
	require(AT_INCLUDE_PATH.'../mods/_standard/profile_pictures/html/profile_picture.inc.php'); 
}
?>
