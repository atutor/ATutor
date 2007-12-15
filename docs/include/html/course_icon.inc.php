<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

?>

<?php
/************************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: get_profile_img.php 6979 2007-06-20 17:35:02Z greg $

function resize_image($src, $dest, $src_h, $src_w, $dest_h, $dest_w, $type) {
	$thumbnail_img = imagecreatetruecolor($dest_w, $dest_h);

	if ($type == 'gif') {
		$source = imagecreatefromgif($src);
	} else if ($type == 'jpg') {
		$source = imagecreatefromjpeg($src);
	} else {
		$source = imagecreatefrompng($src);
	}
	
	imagecopyresampled($thumbnail_img, $source, 0, 0, 0, 0, $dest_w, $dest_h, $src_w, $src_h);

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

$gd_info = gd_info();
$supported_images = array();
if ($gd_info['GIF Create Support']) {
	$supported_images[] = 'gif';
}
if ($gd_info['JPG Support']) {
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
	header('Location: '.$_SERVER['PHP_SELF'].'?course_id='.$course_id);
	exit;
} else if (isset($_POST['submit'])) {
	if (isset($_POST['delete']) && !$_FILES['file']['size']) {
		profile_image_delete($course_id);

		$msg->addFeedback('PROFILE_UPDATED');

		header('Location: '.$_SERVER['PHP_SELF'].'?course_id='.$course_id);
		exit;
	} else if ($_FILES['file']['error'] == UPLOAD_ERR_FORM_SIZE) {
		$msg->addError(array('FILE_MAX_SIZE', $_config['prof_pic_max_file_size'] . ' ' . _AT('bytes')));
		header('Location: '.$_SERVER['PHP_SELF'].'?course_id='.$course_id);
		exit;
	} else if (!$_FILES['file']['size']) {
		header('Location: '.$_SERVER['PHP_SELF'].'?course_id='.$course_id);
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
		header('Location: '.$_SERVER['PHP_SELF'].'?course_id='.$course_id);
		exit;
	} else if ($image_attributes[2] > IMAGETYPE_PNG) {
		$msg->addError(array('FILE_ILLEGAL', $extension));
		header('Location: '.$_SERVER['PHP_SELF'].'?course_id='.$course_id);
		exit;
	}

	// make sure under max file size
	if ($_FILES['customicon']['size'] > $_config['prof_pic_max_file_size']) {
		$msg->addError('FILE_MAX_SIZE');
		header('Location: '.$_SERVER['PHP_SELF'].'?course_id='.$course_id);
		exit;
	}

	// delete the old images (if any)
	//profile_image_delete($course_id);

	//$new_filename   = $course_id . '.' . $extension;
	//$original_img  = AT_CONTENT_DIR.'custom_icons/originals/'. $new_filename;
	//$thumbnail_img = AT_CONTENT_DIR.'custom_icons/thumbs/'. $new_filename;

	// save original
	if (!move_uploaded_file($_FILES['customicon']['tmp_name'], $original_img)) {
		$msg->addError('CANNOT_OVERWRITE_FILE');
		header('Location: '.$_SERVER['PHP_SELF'].'?course_id='.$course_id);
		exit;
	}

	// resize the original and save it at $thumbnail_file
	$width  = $image_attributes[0];
	$height = $image_attributes[1];

	if ($width > $height && $width>100) {
		$thumbnail_height = intval(100 * $height / $width);
		$thumbnail_width  = 100;

		resize_image($original_img, $thumbnail_img, $height, $width, $thumbnail_height, $thumbnail_width, $extension);
	} else if ($width <= $height && $height > 100) {
		$thumbnail_height= 100;
		$thumbnail_width = intval(100 * $width / $height);
		resize_image($original_img, $thumbnail_img, $height, $width, $thumbnail_height, $thumbnail_width, $extension);
	} else {
		// no resizing, just copy the image.
		// it's too small to resize.
		copy($original_img, $thumbnail_img);
	}

	$msg->addFeedback('PROFILE_UPDATED');

	header('Location: '.$_SERVER['PHP_SELF'].'?course_id='.$course_id);
	exit;
}
?>
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $_config['prof_pic_max_file_size']; ?>" />

            <div class="row" style="float:right;width:40%;">
		<h3><?php echo _AT('upload_icon'); ?></h3>
		<input type="file"  name="customicon" id="customicon" /> (<?php echo implode(', ', $supported_images); ?>)
	</div>


<?php  //require(AT_INCLUDE_PATH.'footer.inc.php'); ?>