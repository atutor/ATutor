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
// $Id$
$_user_location = 'public';
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');
include (AT_PA_INCLUDE.'lib.inc.php');
$_custom_css = $_base_path . AT_PA_BASENAME . 'module.css'; // use a custom stylesheet

$aid = intval($_REQUEST['id']);
$pa = new PhotoAlbum($aid);

if (!$pa->checkAlbumPriv($_SESSION['member_id'])){
	$msg->addError('ACCESS_DENIED');
	header('location: index.php');
	exit;
}

$album_info = $pa->getAlbumInfo();

//handle Edit album info.
if(isset($_POST['submit'])){
	$pa = new PhotoAlbum($_POST['aid']);	//new object
	if (isset($_POST['album_type'])){
		$album_type	= (intval($_POST['album_type'])==AT_PA_TYPE_MY_ALBUM)?AT_PA_TYPE_MY_ALBUM:AT_PA_TYPE_COURSE_ALBUM;
	} else {
		//default is "my album" 'cause normally user can't create course album.
		$album_type	= AT_PA_TYPE_MY_ALBUM;
	}

	//private or shared album?
	if (isset($_POST['album_permission'])){
		$album_permission = ($_POST['album_permission']==AT_PA_SHARED_ALBUM)?AT_PA_SHARED_ALBUM:AT_PA_PRIVATE_ALBUM;
	} else {
		$album_permission = AT_PA_PRIVATE_ALBUM;
	}

	if (isset($_POST['album_name']) && $_POST['album_name']!=''){
		//TODO: photo_id = 0, should default to use the first one after multi-file uploader works
		$result = $pa->editAlbum($_POST['album_name'], $_POST['album_location'], $_POST['album_description'], $album_type, $album_permission);

		if (!$result){
			$msg->addError('PA_EDIT_ALBUM_FAILED');
		}
	} else {
		//album name can't be empty
		$msg->addError('EMPTY_ALBUM_NAME');

	}
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	if (admin_authenticate(AT_ADMIN_PRIV_PHOTO_ALBUM, true)) {
		//if admin
		header('Location: index_admin.php');
		exit;
	} 
	//header('Location: albums.php?id='.intval($_POST['aid']));
	header('Location: index.php');
	exit;
} elseif (isset($_POST['cancel'])){
	$msg->addFeedback('CANCELLED');
	if (admin_authenticate(AT_ADMIN_PRIV_PHOTO_ALBUM, true)) {
		//if admin
		header('Location: index_admin.php');
		exit;
	}
	header('Location: '.AT_PA_BASE);
	exit;
}


include (AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('album_info', $album_info);
$savant->display('pa_edit_album.tmpl.php');
include (AT_INCLUDE_PATH.'footer.inc.php'); 
?>