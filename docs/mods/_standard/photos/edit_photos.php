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

$aid = intval($_GET['aid']);
$pid = intval($_GET['pid']);
if(isset($_POST['aid'])){
	$aid = intval($_POST['aid']);
}
if(isset($_POST['pid'])){
	$pid = intval($_POST['pid']);
}

//initialization
$pa = new PhotoAlbum($aid);

//validation
if(!($pa->checkPhotoPriv($pid, $_SESSION['member_id']) || $pa->checkAlbumPriv($_SESSION['member_id']))){
	$msg->addError("ACCESS_DENIED");
		header('location: index.php');
		exit;
} 

//get details
if ($pid > 0){
	//get only 1 photo
	$photos = array($pa->getPhotoInfo($pid));
} else {
	$photos = $pa->getAlbumPhotos();
}
$album_info = $pa->getAlbumInfo();

//Set pages/submenu
$_pages[AT_PA_BASENAME.'albums.php?id='.$aid]['title']    = _AT('pa_albums') .' - '.$album_info['name'];
$_pages[AT_PA_BASENAME.'albums.php?id='.$aid]['parent']   = AT_PA_BASENAME.'index.php';
$_pages[AT_PA_BASENAME.'albums.php?id='.$aid]['children']  = array(
														AT_PA_BASENAME.'edit_photos.php',
													);
$_pages[AT_PA_BASENAME.'edit_photos.php']['parent'] = AT_PA_BASENAME.'albums.php?id='.$aid;


//handle organize
if(isset($_GET['org'])){
	$_custom_head .= '<script type="text/javascript" src="'.AT_PA_BASENAME.'include/imageReorderer.js"></script>';
	//reset pages/submenu
	$_pages[AT_PA_BASENAME.'edit_photos.php']['title'] = _AT('pa_organize_photos');

	if (isset($_POST['submit'])){
		foreach($photos as $index=>$photo_array){
			$ordering = $_POST['image_'.$photo_array['id']];
			if(isset($ordering)){
				$result = $pa->editPhotoOrder($photo_array['id'], $ordering);
				if (!$result){
					$msg->addError('PA_EDIT_PHOTO_FAILED');
				}
			}
		}
		exit;
	}
	include (AT_INCLUDE_PATH.'header.inc.php');
	$savant->assign('album_info', $album_info);
	$savant->assign('photos', $photos);
	$savant->display('pa_organize_photos.tmpl.php');
	include (AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

//handle Edit.
if (isset($_POST['submit'])){
	//update photo description
	foreach($photos as $index=>$photo_array){
		$alt_text = $_POST['alt_text_'.$photo_array['id']];
		$description = $_POST['description_'.$photo_array['id']];
		$deletion = $_POST['delete_'.$photo_array['id']];
		//don't have to update description if it's deleted
		if (isset($deletion)){
			$pa->deletePhoto($photo_array['id']);
		} elseif (isset($description)){
			$result = $pa->editPhoto($photo_array['id'], $description, $alt_text);
			if (!$result){
				//TODO: sql error
				$msg->addError('PA_EDIT_PHOTO_FAILED');
			}
		}
	}

	//update photo album.
	if (isset($_POST['album_cover'])){
		$result = $pa->editAlbumCover($_POST['album_cover']);
		if (!$result){
			//albumcover error.
			$msg->addError('PA_EDIT_PHOTO_FAILED');
		}
	}

	//if no errors
	if ($_POST['submit'] == 'ajax'){
		//ajax, return 200 status
		header('HTTP/1.1 200 OK');
		exit;
	}
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	if (admin_authenticate(AT_ADMIN_PRIV_PHOTO_ALBUM, true)) {
		//if admin
		header('Location: index_admin.php');
		exit;
	} 
	header('Location: albums.php?id='.$aid);
	exit;
} elseif (isset($_POST['cancel'])){
	$msg->addFeedback('CANCELLED');
	if (admin_authenticate(AT_ADMIN_PRIV_PHOTO_ALBUM, true)) {
		//if admin
		header('Location: index_admin.php');
		exit;
	}
	header('Location: albums.php?id='.$aid);
	exit;
}


include (AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('album_info', $album_info);
$savant->assign('photos', $photos);
$savant->display('pa_edit_photos.tmpl.php');
include (AT_INCLUDE_PATH.'footer.inc.php'); 
?>
