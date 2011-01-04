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
// $Id: set_profile_picture.php 10055 2010-06-29 20:30:24Z cindy $

define('AT_INCLUDE_PATH', '../../../include/');
$_user_location	= 'public';
require (AT_INCLUDE_PATH.'vitals.inc.php');
require ('../profile_pictures/save_profile_picture.php');	//handle POST request

//tweak.  disallow redirect to profile album, if the refererer is from profile picture.php
if (isset($_SESSION['course_id']) && $_SESSION['course_id']>0){
	header('Location: profile_album.php');
} else {
	header('Location: ../profile_pictures/profile_picture.php');
}
exit;
?>