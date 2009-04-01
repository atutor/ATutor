<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');
$_custom_css = $_base_path . 'mods/social/module.css'; // use a custom stylesheet

// Get social group class
$social_groups = new SocialGroups();

//validate if this script is being run by the group admin
//validate the group_admin is indeed a group member
//TODO
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

if (isset($_POST['create'])){
	//handles group logo
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

	$new_filename = $id . '.' . $extension;
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
		$isSucceded = $social_groups->addGroup($_POST['group_type'], $_POST['group_name'], $_POST['description'], $new_filename);

		if($isSucceded){
			$msg->addFeedback('GROUP_CREATED');
			header('Location: index.php');
		} else {
			//Something went bad in the backend, contact admin?
			$msg->addError('GROUP_CREATION_FAILED');
		}
	}
}

//Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('group_types', $social_groups->getAllGroupType());
$savant->display('sgroup_edit.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>