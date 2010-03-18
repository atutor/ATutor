<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institution  */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
include (AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');	//clr_dir()
//include (AT_INCLUDE_PATH.'lib/filemanager.inc.php');	//clr_dir()
include (AT_PA_INCLUDE.'lib.inc.php');	//album_filepath
include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');

//validates if this is me/have the privilege to delete.
$pid = intval($_GET['pid']);
$aid = intval($_GET['aid']);

//init
$pa = new PhotoAlbum($aid);

if ($pid<1 || $aid <1){
	$msg->addError('PA_PHOTO_NOT_FOUND');	//no such picture
	header('Location: index.php');
	exit;
} elseif (!$pa->checkPhotoPriv($pid, $_SESSION['member_id'])){
	$msg->addError('ACCESS_DENIED');	
	header('Location: albums.php?id='.$aid);
	exit;
} 

if ($pa->deletePhoto($pid)){
	header('HTTP/1.1 200 OK');
} else {
	header('HTTP/1.1 500 Internal Server Error');
}
exit;
?>
