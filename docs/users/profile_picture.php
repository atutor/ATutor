<?php
define('AT_INCLUDE_PATH', '../include/');
$_user_location	= 'users';
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	Header('Location: profile.php');
	exit;
} else if (isset($_POST['submit'])) {
	$filename = stripslashes($_FILES['upload_file']['name']);

	//make sure extension is jpg, jpeg, gif, or png
	$exe = explode('.', $filename);
	$exe = $exe[1];

	if ($exe!='jpg' && $exe!='jpeg' && $exe!='png' && $exe!='gif') {
		$msg->addError('FILE_ILLEGAL', $exe);
		header('Location: profile_picture.php');
		exit;
	}

	//make sure under max file size
	if ($_FILES['size'] > $_config['prof_pic_max_file_size']) {
		$msg->addError('FILE_MAX_SIZE');
		header('Location: profile_picture.php');
		exit;
	}

	$new_filename = $_SESSION['member_id'] .'.'. $exe;
	$save_orig = AT_CONTENT_DIR.'profile_pictures/originals/'. $new_filename;
	$save_100 = AT_CONTENT_DIR.'profile_pictures/thumbs/'. $new_filename;

	//save original
	if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $save_orig)) {
		$msg->addFeedback('FILE_SAVED');
	} else {
		$msg->addError('CANNOT_OVERWRITE_FILE');
	}	


	//resize
	list($width, $height) = getimagesize($save_orig);
	if ($width >= $height && $width>100) {
		$new_height = 100;
		$new_width= ($width * 100) / $height;
	} else {
		$new_width = 100;
		$new_height= ($height * 100) / $width;
	}

	$thumb_100 = imagecreatetruecolor($new_width, $new_height);
	$source = imagecreatefromjpeg($save_orig);
	
	imagecopyresized($thumb_100, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

	if ($exe=='jpeg' || $exe=='jpg') {
		//imagejpeg($thumb_100);
	} else if ($exe=='gif') {
		//imagegif($thumb_100);
	} else if($exe=='png') {
		//imagepng($thumb_100);
	}

	//save thumb
	if (($f = @fopen($save_100,'w')) && @fwrite($f, $thumb_100) !== FALSE && @fclose($f)){
		$msg->addFeedback('FILE_SAVED');	
	} else {
		$msg->addError('CANNOT_OVERWRITE_FILE');
	}	


	header('Location: profile.php');
	exit;
}

require (AT_INCLUDE_PATH.'header.inc.php');
?>

<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<?php global $_config; ?>
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $_config['prof_pic_max_file_size']; ?>" />
<div class="input-form">

	<div class="row">
		<label for="login"><?php echo _AT('current_picture'); ?></label><br />

		<img src="" alt="" />
		<input type="checkbox" name="delete" value="1" /> <?php echo _AT('delete'); ?>
	</div>
</div>

<div class="input-form">

	<div class="row">
		<label for="login"><?php echo _AT('upload_new_picture'); ?></label><br />
		<input type="file" name="upload_file" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value=" <?php echo _AT('save'); ?> " accesskey="s" />
		<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " />
	</div>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>