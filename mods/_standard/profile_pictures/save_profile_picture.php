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

if (!isset($member_id) || $member_id == 0) $member_id = $_SESSION['member_id'];

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

// check if this is a request from the photo album
$aid = intval($_GET['aid']);
$pid = intval($_GET['pid']);
if ($pid>0 && $aid>0){
	$photo_set_profile = true;
} else {
	$photo_set_profile = false;
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
} else if (isset($_POST['submit']) || $photo_set_profile) {
	if (isset($_POST['delete']) && !$_FILES['file']['size']) {
		profile_image_delete($member_id);

		$msg->addFeedback('PROFILE_UPDATED');

		header('Location: '.$_SERVER['PHP_SELF'].'?member_id='.$member_id);
		exit;
	} else if ($_FILES['file']['error'] == UPLOAD_ERR_FORM_SIZE) {
		$msg->addError(array('FILE_MAX_SIZE', $_config['prof_pic_max_file_size'] . ' ' . _AT('bytes')));
		header('Location: '.$_SERVER['PHP_SELF'].'?member_id='.$member_id);
		exit;
	} else if (!$_FILES['file']['size'] && !$photo_set_profile) {
		header('Location: '.$_SERVER['PHP_SELF'].'?member_id='.$member_id);
		exit;
	}

	// if this is a picture from the photo album
	if ($photo_set_profile) {
		include (AT_PA_INCLUDE.'lib.inc.php');
		include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');
        //run a check to see if any personal album exists, if not, create one.
        $sql = 'SELECT * FROM '.TABLE_PREFIX.'pa_albums WHERE member_id='.$_SESSION['member_id'].' AND type_id='.AT_PA_TYPE_PERSONAL;
        $result = mysql_query($sql, $db);
        if ($result){
            //precondition: Profile Album always exists.
	        $row = mysql_fetch_assoc($result);	//album info.
	        $profile_aid = $row['id'];  //current profile album id
        }
        $pa_profile = new PhotoAlbum($profile_aid);
		
		// album id of the GET requests (via set profile picture link)
		$pa = new PhotoAlbum($aid);
		$album_info = $pa->getAlbumInfo();
		$photo_info = $pa->getPhotoInfo($pid);

		//Validate users, using permission and course album control.
		$visible_albums = $pa->getAlbums($_SESSION['member_id'], $photo_info['type_id']);
		if(!isset($visible_albums[$aid]) && $album_info['permission']==AT_PA_PRIVATE_ALBUM){
			//TODO msg;
			$msg->addError("ACCESS_DENIED");
			header('location: index.php');
			exit;
		}
        
        // get the current photo info, and paths
		$album_file_path = getAlbumFilePath($album_info['id'], $album_info['created_date']);
		$album_file_path_tn = $album_file_path.'_tn'.DIRECTORY_SEPARATOR;
    	$album_file_path .= DIRECTORY_SEPARATOR;
		$photo_file_path = getPhotoFilePath($photo_info['id'], $photo_info['name'], $photo_info['created_date']);
		$photo_location = AT_PA_CONTENT_DIR . $album_file_path . $photo_file_path;
		$photo_tn_location = AT_PA_CONTENT_DIR . $album_file_path_tn . $photo_file_path;
		
		if ($aid!=$profile_aid){
		    // now, get the new photo info, and path
		    $pa_profile->addPhoto($photo_info['name'], $photo_info['description'], $_SESSION['member_id']);
		    $album_info_new = $pa_profile->getAlbumInfo();
		    $album_file_path_new = getAlbumFilePath($album_info_new['id'], $album_info_new['created_date']);
		    $album_file_path_tn_new = $album_file_path_new.'_tn'.DIRECTORY_SEPARATOR;
        	$album_file_path_new .= DIRECTORY_SEPARATOR;    	
		    $added_photo_id = mysql_insert_id();		
		    $photo_info_new = $pa->getPhotoInfo($added_photo_id);
		    $photo_file_path_new = getPhotoFilePath($added_photo_id, $photo_info_new['name'], $photo_info_new['created_date']);
		    $photo_location_new = AT_PA_CONTENT_DIR . $album_file_path_new . $photo_file_path_new;
		    $photo_tn_location_new = AT_PA_CONTENT_DIR . $album_file_path_tn_new . $photo_file_path_new;
		
		    // if directory does not exist, create it. 
		    if (!is_dir(AT_PA_CONTENT_DIR.$album_file_path_new)){
	        	mkdir(AT_PA_CONTENT_DIR.$album_file_path_new);		
	        }
	        if (!is_dir(AT_PA_CONTENT_DIR.$album_file_path_tn_new)){
		        mkdir(AT_PA_CONTENT_DIR.$album_file_path_tn_new);
	        }
	        
	        // copy both original and thumbnail over to the profile album
		    copy($photo_location, $photo_location_new);
		    copy($photo_tn_location, $photo_tn_location_new);
		}
	    
		$filename = $photo_info['name'];
		$image_attributes = getimagesize($photo_location);
	} else {
		// check if this is a supported file type
		$filename   = $stripslashes($_FILES['file']['name']);
		$image_attributes = getimagesize($_FILES['file']['tmp_name']);
	}
	$path_parts = pathinfo($filename);
	$extension  = strtolower($path_parts['extension']);	

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
	if ($photo_set_profile){
		copy($photo_location, $original_img);		
	} else {
		if (!move_uploaded_file($_FILES['file']['tmp_name'], $original_img)) {
			$msg->addError('CANNOT_OVERWRITE_FILE');
			header('Location: '.$_SERVER['PHP_SELF'].'?member_id='.$member_id);
			exit;
		}		
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
?>