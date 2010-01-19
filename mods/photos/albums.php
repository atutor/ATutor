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

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');
include (AT_PA_INCLUDE.'classes/SimpleImage.class.php');
include (AT_PA_INCLUDE.'lib.inc.php');
$_custom_css = $_base_path . AT_PA_BASENAME . 'module.css'; // use a custom stylesheet

$id = intval($_REQUEST['id']);

$pa = new PhotoAlbum($id);
$info = $pa->getAlbumInfo();

//TODO: handle add_photo
if(isset($_POST['upload'])){
	debug($_FILES);
	//check file size, filename, and extension
	$_FILES['photo'] = checkPhoto($_FILES['photo']);

	//computer album folder name and photo filename, if exist, shift bits
	//goal: generate a random yet computable file structure to disallow
	//		users to browse through others' photos through URLs.	
	$album_file_path = getAlbumFilePath($id, $info['created_date']);
	$album_file_path_tn = $album_file_path.'_tn'.DIRECTORY_SEPARATOR;
	$album_file_path .= DIRECTORY_SEPARATOR;

	if (!is_dir(AT_PA_CONTENT_DIR.$album_file_path)){
		mkdir(AT_PA_CONTENT_DIR.$album_file_path);		
	}
	if (!is_dir(AT_PA_CONTENT_DIR.$album_file_path_tn)){
		mkdir(AT_PA_CONTENT_DIR.$album_file_path_tn);		
	}

	//add the photo
	$result = $pa->addPhoto($_FILES['photo']['name'], $_POST['photo_comment'], $_SESSION['member_id']);
	if ($result===FALSE){
		//TODO: sql error
		$msg->addError();
	}
	//get photo filepath
	$added_photo_id = mysql_insert_id();
	$photo_info = $pa->getPhotoInfo($added_photo_id);
	$photo_file_path = getPhotoFilePath($added_photo_id, $_FILES['photo']['name'], $photo_info['created_date']);

	//resize images to a specific size, and its thumbnail
	$si = new SimpleImage();
	$si->load($_FILES['photo']['tmp_name']);
	$image_w = $si->getWidth();
	$image_h = $si->getHeight();

	//picture is horizontal
	if($image_w > $image_h){		
		$si->resizeToWidth(604);
		$si->save(AT_PA_CONTENT_DIR.$album_file_path.$photo_file_path);
		$si->resizeToWidth(130);
		$si->save(AT_PA_CONTENT_DIR.$album_file_path_tn.$photo_file_path);
	} else {
		$si->resizeToHeight(604);
		$si->save(AT_PA_CONTENT_DIR.$album_file_path.$photo_file_path);
		$si->resizeToHeight(130);
		$si->save(AT_PA_CONTENT_DIR.$album_file_path_tn.$photo_file_path);
	}

	header('location: albums.php?id='.$id);
	exit;
}

//paginator settings
$page = intval($_GET['p']);
$photos_count = sizeof($pa->getAlbumPhotos());
$last_page = ceil($photos_count/AT_PA_PHOTO_PERS_PAGE);

if (!$page || $page < 0) {
	$page = 1;
} elseif ($page > $last_page){
	$page = $last_page;
}

$count  = (($page-1) * AT_PA_PHOTO_PERS_PAGE) + 1;
$offset = ($page-1) * AT_PA_PHOTO_PERS_PAGE;

//get details
$photos = $pa->getAlbumPhotos($offset);
$comments = $pa->getComments($id, false);

include (AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('album_info', $info);
$savant->assign('photos', $photos);
$savant->assign('comments', $comments);
$savant->assign('page', $page);
$savant->assign('num_rows', $photos_count);
$savant->assign('action_permission', $pa->checkAlbumPriv($_SESSION['member_id']));
$savant->display('pa_albums.tmpl.php');
include (AT_INCLUDE_PATH.'footer.inc.php'); 
?>