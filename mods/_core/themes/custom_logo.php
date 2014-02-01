<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: 

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../../../include/');

require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_THEMES);
global $msg;
require (AT_INCLUDE_PATH.'header.inc.php');
if(isset($_POST['submit'])) {
    upload_custom_logo();
}

function upload_custom_logo()
{
    global $msg;
    global $_config;
    global $stripslashes;
    
    //error in the file
    if ($_FILES['file']['error'] == UPLOAD_ERR_FORM_SIZE){
		// Check if filesize is too large for a POST
		$msg->addError(array('FILE_MAX_SIZE', $_config['prof_pic_max_file_size'] . ' ' . _AT('bytes')));
    }
    
    //If file has no name
	if (!$_FILES['file']['name']) {
		$msg->addError('FILE_NOT_SELECTED');
	}

	//check if file size is ZERO	
	if ($_FILES['file']['size'] == 0) {
		$msg->addError('IMPORTFILE_EMPTY');
	}
    
    if ($_FILES['file']['error'] || !is_uploaded_file($_FILES['file']['tmp_name'])) {
        $msg->addError('FILE_NOT_SAVED');
    }
    
    if (!$msg->containsErrors()) {
        if (defined('AT_FORCE_GET_FILE')) {
            $path = AT_CONTENT_DIR.'logos/';
        } else {
            $path = 'content/logos/';
        }
        
        if (!is_dir($path)) {
            @mkdir($path);
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

		// check if this is a supported file type
		$filename   = $stripslashes($_FILES['file']['name']);
		$path_parts = pathinfo($filename);
		$extension  = strtolower($path_parts['extension']);
		$image_attributes = getimagesize($_FILES['file']['tmp_name']);

		if ($extension == 'jpeg') {
			$extension = 'jpg';
		}

		// resize the original but don't backup a copy.
		$width  = $image_attributes[0];
		$height = $image_attributes[1];
		$original_img	= $_FILES['file']['tmp_name'];
		$thumbnail_img	= $path . "custom_logo.". $extension;
            
        $_FILES['file']['name'] = addslashes($_FILES['file']['name']);
        
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
        $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
    }
    else {
        $msg->addError('FILE_NOT_SAVED');
    }
        
    header('Location:custom_logo.php');
}

?>
<form name="customlogoForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
	<div class="input-form" style="width:95%;">
		<div class="row">
			<h3><?php echo _AT('add_custom_logo'); ?></h3>
		</div>

		<div class="row">
			<label for="file"><?php echo _AT('upload_custom_logo'); ?></label><br />
			<input type="file" name="file" size="40" id="file" />
		</div>
        <div class="row buttons">
			<input type= "submit" name="submit" value="<?php echo _AT('upload'); ?>" />
		</div>
	</div>
</form>
<br />
<?php
require (AT_INCLUDE_PATH.'footer.inc.php'); 
?>