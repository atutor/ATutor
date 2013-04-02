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

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');
$_custom_css = $_base_path . AT_SOCIAL_BASENAME . 'module.css'; // use a custom stylesheet

// Get social group class
$social_groups = new SocialGroups();

//validate if this script is being run by the group admin
//validate the group_admin is indeed a group member
//TODO

if (isset($_POST['create'])){
	//handles group logo
	if ($_FILES['logo']['name']!=''){
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

		// check if this is a supported file type
		$filename   = $stripslashes($_FILES['logo']['name']);
		$path_parts = pathinfo($filename);
		$extension  = strtolower($path_parts['extension']);
		$image_attributes = getimagesize($_FILES['logo']['tmp_name']);

		if ($extension == 'jpeg') {
			$extension = 'jpg';
		}

		if (!in_array($extension, $supported_images)) {
			$msg->addError(array('FILE_ILLEGAL', $extension));
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		} else if ($image_attributes[2] > IMAGETYPE_PNG) {
			$msg->addError(array('FILE_ILLEGAL', $extension));
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}

		// make sure under max file size
		if ($_FILES['logo']['size'] > $_config['prof_pic_max_file_size']) {
			$msg->addError('FILE_MAX_SIZE');
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}

		// delete the old images (if any)
		foreach ($supported_images as $ext) {
			if (file_exists(AT_CONTENT_DIR.'social/'. $id.'.'.$ext)) {
				unlink(AT_CONTENT_DIR.'social/'. $id.'.'.$ext);
			}
		}

		$new_filename = 'no_id'. '.' . $extension;
		$original_img = AT_CONTENT_DIR.'social/temp_'. $new_filename;
		$thumbnail_img= AT_CONTENT_DIR.'social/'. $new_filename;

		// only want the resized logo. (for now)
		if (!move_uploaded_file($_FILES['logo']['tmp_name'], $original_img)) {
			$msg->addError('CANNOT_OVERWRITE_FILE');
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
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
		// clean the original
		unlink($original_img);
	}

	//check if fields are empty
	if ($_POST['group_name']==''){
		$missing_fields[] = _AT('group_name');
	} elseif (intval($_POST['group_type'])<=0){
		$missing_fields[] = _('group_type');
	}
	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	} else {
		$group_id = $social_groups->addGroup($_POST['group_type'], $_POST['group_name'], $_POST['description'], $_POST['group_privacy']);
		if($group_id){
			//Add the logo in now that we have the group id. And rename the old one.
			if ($thumbnail_img!=''){			
				$new_group = new SocialGroup($group_id);
				$new_group->updateGroupLogo($group_id . '.' . $extension);
				$new_location = AT_CONTENT_DIR.'social/'. $group_id . '.' . $extension;
				copy($thumbnail_img, $new_location);
				unlink($thumbnail_img);
			}
			$msg->addFeedback('GROUP_CREATED');
			header('Location: index.php');
			exit;
		} else {
			//Something went bad in the backend, contact admin?
			$msg->addError('GROUP_CREATION_FAILED');
		}
	}
}

//Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('social/pubmenu.tmpl.php');
$savant->assign('group_types', $social_groups->getAllGroupType());
$savant->display('social/sgroup_edit.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>