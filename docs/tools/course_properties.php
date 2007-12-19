<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/tinymce.inc.php');
require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');
require(AT_INCLUDE_PATH.'lib/file_storage.inc.php');

authenticate(AT_PRIV_ADMIN);

/**
 * To resize course_icon images
 * @param	uploaded image source path 
 * @param	uploaded image path to be saved as
 * @param	uploaded image's height
 * @param	uploaded image width
 * @param	save file with this height
 * @param	save file with this width
 * @param	file extension type
 * @return	true if successful, false otherwise
 */
function resize_image($src, $dest, $src_h, $src_w, $dest_h, $dest_w, $type) {
	$thumbnail_img = imagecreatetruecolor($dest_w, $dest_h);
	if ($type == 'gif') {
		$source = imagecreatefromgif($src);
	} else if ($type == 'jpg') {
		$source = imagecreatefromjpeg($src);
	} else {
		$source = imagecreatefrompng($src);
	}
	
	$result = imagecopyresampled($thumbnail_img, $source, 0, 0, 0, 0, $dest_w, $dest_h, $src_w, $src_h);

	if ($type == 'gif') {
		$result &= imagegif($thumbnail_img, $dest);
	} else if ($type == 'jpg') {
		$result &= imagejpeg($thumbnail_img, $dest, 75);
	} else {
		$result &= imagepng($thumbnail_img, $dest, 7);
	}
	return $result;
}

$course = $_SESSION['course_id'];
$isadmin   = FALSE;

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;


}else if($_POST['submit']){

    // added by Martin - for custom course icons

    if($_FILES['customicon']['tmp_name'] != ''){
        $_POST['comments'] = trim($_POST['comments']);

        $owner_id = $_SESSION['course_id'];
        $owner_type = "1";
            
        if ($_FILES['customicon']['error'] == UPLOAD_ERR_INI_SIZE) {
            $msg->addError(array('FILE_TOO_BIG', get_human_size(megabytes_to_bytes(substr(ini_get('upload_max_filesize'), 0, -1)))));

        } else if (!isset($_FILES['customicon']['name']) || ($_FILES['customicon']['error'] == UPLOAD_ERR_NO_FILE) || ($_FILES['customicon']['size'] == 0)) {
            $msg->addError('FILE_NOT_SELECTED');

        } else if ($_FILES['customicon']['error'] || !is_uploaded_file($_FILES['customicon']['tmp_name'])) {
            $msg->addError('FILE_NOT_SAVED');
        }
        
        if (!$msg->containsErrors()) {
            $_POST['description'] = $addslashes(trim($_POST['description']));
            $_FILES['customicon']['name'] = addslashes($_FILES['customicon']['name']);

            if ($_POST['comments']) {
                $num_comments = 1;
            } else {
                $num_comments = 0;
            }
            
            $path = AT_CONTENT_DIR.$owner_id."/custom_icons/";
		
            if (!is_dir($path)) {
                @mkdir($path);
            }
			
			// if we can upload custom course icon, it means GD is enabled, no need to check extension again.
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

			// check if this is a supported file type
			$filename   = $stripslashes($_FILES['customicon']['name']);
			$path_parts = pathinfo($filename);
			$extension  = strtolower($path_parts['extension']);
			$image_attributes = getimagesize($_FILES['customicon']['tmp_name']);

			if ($extension == 'jpeg') {
				$extension = 'jpg';
			}

			// resize the original but don't backup a copy.
			$width  = $image_attributes[0];
			$height = $image_attributes[1];
			$original_img	= $_FILES['customicon']['tmp_name'];
			$thumbnail_img	= $path . $_FILES['customicon']['name'];

			if ($width > $height && $width>79) {
				$thumbnail_height = intval(79 * $height / $width);
				$thumbnail_width  = 79;
				if (!resize_image($original_img, $thumbnail_img, $height, $width, $thumbnail_height, $thumbnail_width, $extension)){
					$msg->addError('FILE_NOT_SAVED');
				}
			} else if ($width <= $height && $height > 79) {
				$thumbnail_height= 100;
				$thumbnail_width = intval(100 * $width / $height);
				if (!resize_image($original_img, $thumbnail_img, $height, $width, $thumbnail_height, $thumbnail_width, $extension)){
					$msg->addError('FILE_NOT_SAVED');
				}
			} else {
				// no resizing, just copy the image.
				// it's too small to resize.
				copy($original_img, $thumbnail_img);
			}

        } else {
            $msg->addError('FILE_NOT_SAVED');
            
        }
        $_POST['icon'] = $_FILES['customicon']['name'];
        //header('Location: index.php'.$owner_arg_prefix.'folder='.$parent_folder_id);
        //exit;
    }

    //----------------------------------------

	require(AT_INCLUDE_PATH.'lib/course.inc.php');
	$_POST['instructor'] = $_SESSION['member_id'];

	$errors = add_update_course($_POST);

	if (is_numeric($errors)) {
		$msg->addFeedback('COURSE_PROPERTIES');
		header('Location: '.AT_BASE_HREF.'tools/index.php');	
		exit;
	}
		
//}else if(($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual'])){
} else if (($_POST['setvisual'] || $_POST['settext'])){
		//header('Location: '.$_SESSION['PHP_SELF'].'');	
		//exit;
} else if (isset($_POST['course'])) {
	require(AT_INCLUDE_PATH.'lib/course.inc.php');
	$_POST['instructor'] = $_SESSION['member_id'];

	$errors = add_update_course($_POST);

	if (is_numeric($errors)) {
		$msg->addFeedback('COURSE_PROPERTIES');
		header('Location: '.AT_BASE_HREF.'tools/index.php');	
		exit;
	}
}

$onload = 'document.course_form.title.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

require(AT_INCLUDE_PATH.'html/course_properties.inc.php');

require(AT_INCLUDE_PATH.'footer.inc.php');


?>