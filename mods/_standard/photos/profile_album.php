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
// $Id: profile_album.php 10424 2010-11-30 00:42:01Z hwong $
$_user_location = 'public';
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

//quit if this is not a member
if(!(isset($_SESSION['member_id']) && $_SESSION['member_id'] > 0)){
	$msg->addError('ACCESS_DENIED');
	header('Location: index.php');
	exit;
}

include (AT_PA_INCLUDE.'profile_album.inc.php');
exit;
?>
