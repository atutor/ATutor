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
// $Id: remove_uploaded_photo.php 9519 2010-03-18 15:38:11Z hwong $
// $Id: profile_picture.php 6850 2007-03-06 19:35:37Z joel $
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

$member_id = intval($_GET['member_id']);

require(AT_INCLUDE_PATH.'../mods/_standard/profile_picture/html/profile_picture.inc.php'); ?>