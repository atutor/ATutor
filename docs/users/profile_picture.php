<?php
define('AT_INCLUDE_PATH', '../include/');
$_user_location	= 'users';
require (AT_INCLUDE_PATH.'vitals.inc.php');


function profile_image_delete($id) {
	$extensions = array('gif', 'jpg', 'png');

	foreach ($extensions as $extension) {
		if (file_exists(AT_CONTENT_DIR.'profile_pictures/originals/'. $id.'.'.$extension)) {
			unlink(AT_CONTENT_DIR.'profile_pictures/originals/'. $id.'.'.$extension);
		}
		if (file_exists(AT_CONTENT_DIR.'profile_pictures/thumbs/'. $id.'.'.$extension)) {
			unlink(AT_CONTENT_DIR.'profile_pictures/thumbs/'. $id.'.'.$extension);
		}
	}
}

function profile_image_exists($id) {
	$extensions = array('gif', 'jpg', 'png');

	foreach ($extensions as $extension) {
		if (file_exists(AT_CONTENT_DIR.'profile_pictures/originals/'. $id.'.'.$extension)) {
			return true;
		}
	}
}

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
		imagepng($thumbnail_img, $dest, 75);
	}
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	Header('Location: profile.php');
	exit;
} else if (isset($_POST['submit'])) {
	if (isset($_POST['delete']) && !$_FILES['file']['size']) {
		profile_image_delete($_SESSION['member_id']);

		$msg->addFeedback('PROFILE_UPDATED');

		header('Location: profile_picture.php');
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

	if (!in_array($extension, array('jpg', 'gif', 'png'))) {
		$msg->addError('FILE_ILLEGAL');
		header('Location: profile_picture.php');
		exit;
	} else if ($image_attributes[2] > IMAGETYPE_PNG) {
		$msg->addError('FILE_ILLEGAL');
		header('Location: profile_picture.php');
		exit;
	}

	// make sure under max file size
	if ($_FILES['file']['size'] > $_config['prof_pic_max_file_size']) {
		$msg->addError('FILE_MAX_SIZE');
		header('Location: profile_picture.php');
		exit;
	}

	// delete the old images (if any)
	profile_image_delete($_SESSION['member_id']);

	$new_filename   = $_SESSION['member_id'] . '.' . $extension;
	$original_img  = AT_CONTENT_DIR.'profile_pictures/originals/'. $new_filename;
	$thumbnail_img = AT_CONTENT_DIR.'profile_pictures/thumbs/'. $new_filename;

	// save original
	if (!move_uploaded_file($_FILES['file']['tmp_name'], $original_img)) {
		$msg->addError('CANNOT_OVERWRITE_FILE');
		header('Location: profile_picture.php');
		exit;
	}

	// resize the original and save it at $thumbnail_file
	$width  = $image_attributes[0];
	$height = $image_attributes[1];

	if ($width > $height && $width>100) {
		$thumbnail_height = intval(100 * $height / $width);
		$thumbnail_width  = 100;

		resize_image($original_img, $thumbnail_img, $height, $width, $thumbnail_height, $thumbnail_width, $extension);
	} else if ($width < $height && $height > 100) {
		$thumbnail_height= 100;
		$thumbnail_width = intval(100 * $width / $height);
		resize_image($original_img, $thumbnail_img, $height, $width, $thumbnail_height, $thumbnail_width, $extension);
	} else {
		// no resizing, just copy the image.
		// it's too small to resize.
		copy($original_img, $thumbnail_img);
	}

	$msg->addFeedback('PROFILE_UPDATED');

	header('Location: profile_picture.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

// check if GD is installed
if (!extension_loaded('gd')) {
	$msg->printInfos('FEATURE_NOT_AVAILABLE');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$gd_info = gd_info();
?>

<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $_config['prof_pic_max_file_size']; ?>" />
<div class="input-form">
<?php if (profile_image_exists($_SESSION['member_id'])): ?>
	<div class="row">
		<img src="get_profile_img.php?id=<?php echo $_SESSION['member_id']; ?>" alt="" />
		<input type="checkbox" name="delete" value="1" id="del"/><label for="del"><?php echo _AT('delete'); ?></label>
	</div>
<?php endif; ?>

	<div class="row">
		<h3><?php echo _AT('upload_new_picture'); ?></h3>
		<input type="file" name="file" />
		(
		<?php if ($gd_info['GIF Create Support']): ?>
			GIF
		<?php endif; ?>
		<?php if ($gd_info['JPG Support']): ?>
			JPG
		<?php endif; ?>
		<?php if ($gd_info['PNG Support']): ?>
			PNG
		<?php endif; ?>
		)
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>