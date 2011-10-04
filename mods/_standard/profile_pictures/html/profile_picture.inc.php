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
// $Id$
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }
require(AT_INCLUDE_PATH.'header.inc.php');

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
};
?>

<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>?member_id=<?php echo $member_id; ?>" name="form">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $_config['prof_pic_max_file_size']; ?>" />
<div class="input-form">
<?php if (profile_image_exists($member_id)): ?>
	<div class="row">
		<a href="get_profile_img.php?id=<?php echo $member_id.SEP.'size=o'; ?>"><img src="get_profile_img.php?id=<?php echo $member_id; ?>" alt="" /></a>
		<input type="checkbox" name="delete" value="1" id="del"/><label for="del"><?php echo _AT('delete'); ?></label>
	</div>
<?php endif; ?>
	<div class="row">
		<h3><?php echo _AT('upload_new_picture'); ?></h3>
		<input type="file" name="file" /> (<?php echo implode(', ', $supported_images); ?>)</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>