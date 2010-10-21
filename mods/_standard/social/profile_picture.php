<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
$_user_location	= 'public';
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$member_id = $_SESSION['member_id'];
$_custom_css = $_base_path . AT_SOCIAL_BASENAME . 'module.css'; // use a custom stylesheet

function resize_image($src, $dest, $src_h, $src_w, $dest_h, $dest_w, $type, $src_x=0, $src_y=0) {
	$thumbnail_img = imagecreatetruecolor($dest_w, $dest_h);

	if ($type == 'gif') {
		$source = imagecreatefromgif($src);
	} else if ($type == 'jpg') {
		$source = imagecreatefromjpeg($src);
	} else {
		$source = imagecreatefrompng($src);
	}
	
	if ($src_x > 0 || $src_y > 0){
		imagecopyresized($thumbnail_img, $source, 0, 0, $src_x, $src_y, $dest_w, $dest_h, $src_w, $src_h);
	} else {
		imagecopyresampled($thumbnail_img, $source, $src_x, $src_y, 0, 0, $dest_w, $dest_h, $src_w, $src_h);
	}

	if ($type == 'gif') {
		imagegif($thumbnail_img, $dest);
	} else if ($type == 'jpg') {
		imagejpeg($thumbnail_img, $dest, 75);
	} else {
		imagepng($thumbnail_img, $dest, 7);
	}
}

// check if GD is installed
if (!extension_loaded('gd')) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('FEATURE_NOT_AVAILABLE');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

// check if folder exists, if not, create it
if (!is_dir(AT_CONTENT_DIR.'/profile_pictures/profile')) {
	mkdir(AT_CONTENT_DIR.'/profile_pictures/profile');
}

$gd_info = gd_info();
$supported_images = array();
if ($gd_info['GIF Create Support']) {
	$supported_images[] = 'gif';
}
if ($gd_info['JPG Support'] || $gd_info['JPEG Support']) {
	$supported_images[] = 'jpg';
}
if ($gd_info['PNG Support']) {
	$supported_images[] = 'png';
}

if (!$supported_images) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('FEATURE_NOT_AVAILABLE');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_SERVER['PHP_SELF'].'?member_id='.$member_id);
	exit;
} else if (isset($_POST['submit'])) {
	if (isset($_POST['delete']) && !$_FILES['file']['size']) {
		profile_image_delete($member_id);

		$msg->addFeedback('PROFILE_UPDATED');

		header('Location: '.$_SERVER['PHP_SELF'].'?member_id='.$member_id);
		exit;
	} else if ($_FILES['file']['error'] == UPLOAD_ERR_FORM_SIZE) {
		$msg->addError(array('FILE_MAX_SIZE', $_config['prof_pic_max_file_size'] . ' ' . _AT('bytes')));
		header('Location: '.$_SERVER['PHP_SELF'].'?member_id='.$member_id);
		exit;
	} else if (!$_FILES['file']['size']) {
		header('Location: '.$_SERVER['PHP_SELF'].'?member_id='.$member_id);
		exit;
	}

	// check if this is a supported file type
	$filename   = $stripslashes($_FILES['file']['name']);
	$path_parts = pathinfo($filename);
	$extension  = strtolower($path_parts['extension']);
	$image_attributes = getimagesize($_FILES['file']['tmp_name']);

	if ($extension == 'jpeg') {
		$extension = 'jpg';
	}

	if (!in_array($extension, $supported_images)) {
		$msg->addError(array('FILE_ILLEGAL', $extension));
		header('Location: '.$_SERVER['PHP_SELF'].'?member_id='.$member_id);
		exit;
	} else if ($image_attributes[2] > IMAGETYPE_PNG) {
		$msg->addError(array('FILE_ILLEGAL', $extension));
		header('Location: '.$_SERVER['PHP_SELF'].'?member_id='.$member_id);
		exit;
	}

	// make sure under max file size
	if ($_FILES['file']['size'] > $_config['prof_pic_max_file_size']) {
		$msg->addError('FILE_MAX_SIZE');
		header('Location: '.$_SERVER['PHP_SELF'].'?member_id='.$member_id);
		exit;
	}

	// delete the old images (if any)
	profile_image_delete($member_id);

	$new_filename   = $member_id . '.' . $extension;
	$original_img  = AT_CONTENT_DIR.'profile_pictures/originals/'. $new_filename;
	$profile_img   = AT_CONTENT_DIR.'profile_pictures/profile/'. $new_filename;
	$thumbnail_img = AT_CONTENT_DIR.'profile_pictures/thumbs/'. $new_filename;

	// save original
	if (!move_uploaded_file($_FILES['file']['tmp_name'], $original_img)) {
		$msg->addError('CANNOT_OVERWRITE_FILE');
		header('Location: '.$_SERVER['PHP_SELF'].'?member_id='.$member_id);
		exit;
	}

	// resize the original and save it at $thumbnail_file
	$width  = $image_attributes[0];
	$height = $image_attributes[1];

	$thumbnail_fixed_height = 60; 
	$thumbnail_fixed_width = 60; 

	if ($width > $height && $height > $thumbnail_fixed_height) {
		$thumbnail_height= $thumbnail_fixed_height;
		$thumbnail_width = intval($thumbnail_fixed_height * $width / $height);
		resize_image($original_img, $thumbnail_img, $height, $width, $thumbnail_height, $thumbnail_width, $extension);
		//cropping
		resize_image($thumbnail_img, $thumbnail_img, $thumbnail_fixed_height, $thumbnail_fixed_width, $thumbnail_fixed_height, $thumbnail_fixed_width, $extension, ($thumbnail_width-$thumbnail_fixed_width)/2);
	} else if ($width <= $height && $width>$thumbnail_fixed_width) {
		$thumbnail_height = intval($thumbnail_fixed_width * $height / $width);
		$thumbnail_width  = $thumbnail_fixed_width;
		resize_image($original_img, $thumbnail_img, $height, $width, $thumbnail_height, $thumbnail_width, $extension);
		//cropping
		resize_image($thumbnail_img, $thumbnail_img, $thumbnail_fixed_height, $thumbnail_fixed_width, $thumbnail_fixed_height, $thumbnail_fixed_width, $extension, 0, ($thumbnail_height-$thumbnail_fixed_height)/2);
	} else {
		// no resizing, just copy the image.
		// it's too small to resize.
		copy($original_img, $thumbnail_img);
	}

	// resize the original and save it to profile
	$profile_fixed_height = 320;
	$profile_fixed_width = 240;
	if ($width > $height && $height>$profile_fixed_height) {
		$profile_width = intval($profile_fixed_height * $width / $height);
		$profile_height  = $profile_fixed_height;
		resize_image($original_img, $profile_img, $height, $width, $profile_height, $profile_width, $extension);
		//cropping
		resize_image($profile_img, $profile_img, $profile_fixed_height, $profile_fixed_width, $profile_fixed_height, $profile_fixed_width, $extension, ($profile_width-$profile_fixed_width)/2);
	} else if ($width <= $height && $width > $profile_fixed_width) {
		$profile_width = $profile_fixed_width;
		$profile_height = intval($profile_fixed_width * $height / $width);
		resize_image($original_img, $profile_img, $height, $width, $profile_height, $profile_width, $extension);
		//cropping
		resize_image($profile_img, $profile_img, $profile_fixed_height, $profile_fixed_width, $profile_fixed_height, $profile_fixed_width, $extension, 0, ($profile_height-$profile_fixed_height)/2);
	} else {
		// no resizing, just copy the image.
		// it's too small to resize.
		copy($original_img, $profile_img);
	}

	$msg->addFeedback('PROFILE_UPDATED');

	header('Location: '.$_SERVER['PHP_SELF'].'?member_id='.$member_id);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('member_id', $member_id);
$savant->assign('supported_images', $supported_images);
$savant->display('social/profile_picture.html.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>