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
$_user_location = 'public';
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');
include (AT_PA_INCLUDE.'classes/SimpleImage.class.php');
include (AT_PA_INCLUDE.'lib.inc.php');
include (AT_PA_INCLUDE.'classes/AjaxMessage.class.php');
$_custom_css = $_base_path . AT_PA_BASENAME . 'module.css'; // use a custom stylesheet
$_custom_head .= '<script src="'.$_base_path . AT_PA_BASENAME . 'include/ajaxupload.js" type="text/javascript"></script>';

$id = intval($_REQUEST['id']);
$pa = new PhotoAlbum($id);
$info = $pa->getAlbumInfo();
$action_permission = $pa->checkAlbumPriv($_SESSION['member_id']);

//TODO: Validate users, using permission and course album control.
if ($info['member_id'] != $_SESSION['member_id']){
	$visible_albums = $pa->getAlbums($_SESSION['member_id'], $info['type_id']);
	if(!isset($visible_albums[$id]) && $info['permission']==AT_PA_PRIVATE_ALBUM){
		//TODO msg;
		$msg->addError("ACCESS_DENIED");
		header('location: index.php');
		exit;
	}
}

//Set pages/submenu
$_pages[AT_PA_BASENAME.'index.php']['children'] = array(AT_PA_BASENAME.'albums.php');

$_pages[AT_PA_BASENAME.'albums.php']['title']    = _AT('pa_albums') .' - '.$info['name'];
if ($info['type_id']==AT_PA_TYPE_MY_ALBUM){
	$_pages[AT_PA_BASENAME.'albums.php']['parent']	  = AT_PA_BASENAME.'index.php';
} elseif ($info['type_id']==AT_PA_TYPE_COURSE_ALBUM){
	$_pages[AT_PA_BASENAME.'albums.php']['parent']	  = AT_PA_BASENAME.'course_albums.php';
} 
//if this member has the permission to edit the album, show the edit/organize menu
if ($action_permission){
	$_pages[AT_PA_BASENAME.'albums.php']['children']  = array(
															AT_PA_BASENAME.'edit_photos.php?aid='.$id,
															AT_PA_BASENAME.'edit_photos.php?aid='.$id.SEP.'org=1',
														);
	$_pages[AT_PA_BASENAME.'edit_photos.php?aid='.$id]['title_var'] = 'pa_edit_photos';
	$_pages[AT_PA_BASENAME.'edit_photos.php?aid='.$id]['parent'] = AT_PA_BASENAME.'albums.php';
	$_pages[AT_PA_BASENAME.'edit_photos.php?aid='.$id.SEP.'org=1']['title_var'] = 'pa_organize_photos';
	$_pages[AT_PA_BASENAME.'edit_photos.php?aid='.$id.SEP.'org=1']['parent'] = AT_PA_BASENAME.'albums.php';
}

//TODO: handle add_photo
if(isset($_POST['upload'])){
	//check file size, filename, and extension
	$_FILES['photo'] = checkPhoto($_FILES['photo']);
	if ($_FILES['photo']===false || (!$action_permission && $info['type_id']!=AT_PA_TYPE_COURSE_ALBUM)){
		//owner and course members can upload pictures.  Not edit though. 
		echo json_encode(array(
						'aid'=>$id,
						'pid'=>-1,
						'msg'=>htmlentities($msg->printErrors()),
						'error'=>true));
		exit;
	}

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
		$msg->addError('PA_ADD_PHOTO_FAILED');
	}

	if (!$msg->containsErrors()){
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
			//don't stretch images
			if ($image_w > AT_PA_IMAGE){
				$si->resizeToWidth(AT_PA_IMAGE);
				$si->save(AT_PA_CONTENT_DIR.$album_file_path.$photo_file_path);
			} else {
				move_uploaded_file($_FILES['photo']['tmp_name'], AT_PA_CONTENT_DIR.$album_file_path.$photo_file_path);
			}
			$si->resizeToWidth(AT_PA_IMAGE_THUMB);
			$si->save(AT_PA_CONTENT_DIR.$album_file_path_tn.$photo_file_path);
		} else {
			if ($image_h > AT_PA_IMAGE){
				$si->resizeToHeight(AT_PA_IMAGE);
				$si->save(AT_PA_CONTENT_DIR.$album_file_path.$photo_file_path);
			} else {
				move_uploaded_file($_FILES['photo']['tmp_name'], AT_PA_CONTENT_DIR.$album_file_path.$photo_file_path);
			}
			$si->resizeToHeight(AT_PA_IMAGE_THUMB);
			$si->save(AT_PA_CONTENT_DIR.$album_file_path_tn.$photo_file_path);
		}
		if ($_POST['upload'] == 'ajax'){
			$photo_file_hash = getPhotoFilePath($added_photo_id, '', $photo_info['created_date']);
			//return JSON, relying on jQuery to convert entries to html entities.
			echo json_encode(array(
						'aid'=>$id,
						'pid'=>$added_photo_id,
						'ph'=>$photo_file_hash,
						'size'=>number_format(filesize(AT_PA_CONTENT_DIR.$album_file_path.$photo_file_path)/1024, 2),
						'title'=>$photo_info['title'],
						'alt'=>$photo_info['alt']));
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			exit;
		}
		//if this is profile picture upload, sets it to the default profile
		if ($info['type_id']==AT_PA_TYPE_PERSONAL){
		     header('Location: ../profile_pictures/profile_picture.php?pid='.$added_photo_id.SEP.'aid='.$id);
		     exit;
        }
	} //if msg contain error
	header('location: albums.php?id='.$id);
	exit;
}

//If this is a profile album, 403 it to the profile album page
if ($info['type_id'] == AT_PA_TYPE_PERSONAL){
	header('Location: profile_album.php');
	exit;
}

//paginator settings
$page = intval($_GET['p']);
$photos_count = sizeof($pa->getAlbumPhotos());
$last_page = ceil($photos_count/AT_PA_PHOTOS_PER_PAGE);

if (!$page || $page < 0) {
	$page = 1;
} elseif ($page > $last_page){
	$page = $last_page;
}

$count  = (($page-1) * AT_PA_PHOTOS_PER_PAGE) + 1;
$offset = ($page-1) * AT_PA_PHOTOS_PER_PAGE;

//get details
$photos = $pa->getAlbumPhotos($offset);
$comments = $pa->getComments($id, false);
//TODO: Can improve performance by adding this to a session variable
$memory_usage = memoryUsage($_SESSION['member_id']);	

include (AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('album_info', $info);
$savant->assign('photos', $photos);
$savant->assign('comments', $comments);
$savant->assign('page', $page);
$savant->assign('num_rows', $photos_count);
$savant->assign('memory_usage', $memory_usage/(1024*1024));	//mb
$savant->assign('allowable_memory_usage', $_config['pa_max_memory_per_member']);	//mb
$savant->assign('action_permission', $action_permission);
$savant->display('pa_albums.tmpl.php');
include (AT_INCLUDE_PATH.'footer.inc.php'); 
?>
