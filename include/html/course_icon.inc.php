<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: course_icon.inc.php 7603 2008-06-11 14:59:33Z hwong $
if (!defined('AT_INCLUDE_PATH')) { exit; }


// check if GD is installed
if (!extension_loaded('gd')) {
	$msg->addInfo('FEATURE_NOT_AVAILABLE');
}else{

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
		$msg->addInfo('FEATURE_NOT_AVAILABLE');
	}
}
?>
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $_config['prof_pic_max_file_size']; ?>" />

    <div style="float:right;width:40%;">
		<h3 style="padding-top:0;"><?php echo _AT('upload_icon'); ?></h3>
		<?php
			if(!extension_loaded('gd')){
				$msg->printInfos();
			}else{

		?>
		<input type="file"  name="customicon" id="customicon" /> (<?php echo implode(', ', $supported_images); ?>)
		<?php } ?>
	</div>
