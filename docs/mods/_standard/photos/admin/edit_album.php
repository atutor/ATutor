<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institute	   */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id: preferences.php 9606 2010-03-26 14:36:54Z hwong $
define('AT_INCLUDE_PATH', '../../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_PHOTO_ALBUM);

$isadmin   = TRUE;

//printer header iff this is not a POST request 
//a hack to avoid 'header already sent...' error.
if (!isset($_POST['submit']) && !isset($_POST['cancel'])){
	$_custom_css = $_base_path . AT_PA_BASENAME . 'module.css'; // use a custom stylesheet
	require(AT_INCLUDE_PATH.'header.inc.php'); 
	$msg->printAll();
}
require(AT_PA_INCLUDE.'edit_album.inc.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>