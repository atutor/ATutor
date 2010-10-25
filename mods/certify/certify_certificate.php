<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CERTIFY);

$certify_id = '';
if (isset($_POST['certify_id'])) {
    $certify_id = $addslashes($_POST['certify_id']);
} else if (isset($_GET['certify_id'])) {
    $certify_id = $addslashes($_GET['certify_id']);
}

$templatefile = AT_CONTENT_DIR .'certify/template_'.$certify_id.'.pdf';
$templatepresent = file_exists($templatefile);

function let_to_num($v){ //This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
    $l = substr($v, -1);
    $ret = substr($v, 0, -1);
    switch(strtoupper($l)){
    case 'P':
        $ret *= 1024;
    case 'T':
        $ret *= 1024;
    case 'G':
        $ret *= 1024;
    case 'M':
        $ret *= 1024;
    case 'K':
        $ret *= 1024;
        break;
    }
    return $ret;
}
$max_upload_size = min(let_to_num(ini_get('post_max_size')), let_to_num(ini_get('upload_max_filesize')));

$certify_title = '';
$certify_description = '';

if (isset($_POST['submit'])) { // Incoming changes
	
	$certify_title = $addslashes($_POST['certify_title']);
	$certify_description = $addslashes($_POST['certify_description']);

	if (strlen($certify_id) > 0) {
		
		// COMMIT CHANGES

		$sql = "UPDATE ".TABLE_PREFIX."certify
				SET
					title = '$certify_title',
					description = '$certify_description'
				WHERE
					certify_id = $certify_id
				";

		$result = mysql_query($sql, $db) or die(mysql_error());

		if (file_exists($templatefile))
			unlink($templatefile);
		if ($_FILES['certify_file']['size'] > 0 && $_FILES['certify_file']['error'] == 0) {
			if (move_uploaded_file($_FILES['certify_file']['tmp_name'], $templatefile)) {
				// File ok
			} else {
				unlink($templatefile);
			}
		}
		$templatepresent = file_exists($templatefile);

		//write_to_log(AT_ADMIN_LOG_UPDATE, 'certify', mysql_affected_rows($db), $sql);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	
		header('Location: index_instructor.php');
		exit;

	} else {

		// COMMIT NEW
	
		$sql = "INSERT INTO ".TABLE_PREFIX."certify
				(course_id, 
				 title,
				 description) 
					VALUES (". $_SESSION['course_id'] .", 
							'". $certify_title ."',
							'". $certify_description ."')";
							
		$result = mysql_query($sql, $db) or die(mysql_error());

		if ($_FILES['certify_file']['size'] > 0 && $_FILES['certify_file']['error'] == 0) {
			if (move_uploaded_file($_FILES['certify_file']['tmp_name'], $templatefile)) {
				// File ok
			} else {
				unlink($templatefile);
			}
		}
		$templatepresent = file_exists($templatefile);

		$certify_id = mysql_insert_id($db);
		write_to_log(AT_ADMIN_LOG_INSERT, 'certify', mysql_affected_rows($db), $sql);
	
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	
		header('Location: index_instructor.php');
		exit;
		
	}


} else if (isset($_POST['cancel'])) { // Cancelled
	
	// CANCEL

	$msg->addFeedback('CANCELLED');
	header('Location: index_instructor.php');
	exit;

} else if (strlen($certify_id) > 0) {
	
	// EDIT EXISTING

	// Fetch basic data
	$sql = "SELECT * from ".TABLE_PREFIX."certify where certify_id=".$certify_id;
	$result = mysql_query($sql, $db) or die(mysql_error());
	$row = mysql_fetch_assoc($result);

	if (!$row)
		exit; // TODO: Invalid id - how to handle?
		
	$certify_title = $row['title'];
	$certify_description = $row['description'];

}

require(AT_INCLUDE_PATH.'header.inc.php'); 
$msg->printAll();

?>

<p>For instructor to add new certificate


<form enctype="multipart/form-data" name="certifydetails" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_upload_size; ?>"> <!-- We have no real need to restrict the upload here -->
<?php if (strlen($certify_id) > 0) { ?>
<input type="hidden" name="certify_id" value="<?php echo $certify_id; ?>">
<?php } ?>
<dl>
<dt><label for="certify_title"><?php echo _AT('certify_title'); ?></label></dt>
<dd><input type="text" name="certify_title" maxlength="60" value="<?php echo $certify_title; ?>"></dd>
<dt><label for="certify_description"><?php echo _AT('certify_description'); ?></label></dt>
<dd><textarea name="certify_description" cols="40" rows="5"><?php echo $certify_description; ?></textarea></dd>
<dt><label for="certify_file"><?php echo _AT('certify_file'); ?></label></dt>
<dd><input type="file" name="certify_file"></dd>
</dl>
<input type="submit" name="submit" value="<?php echo _AT('save'); ?>">
<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
</form>

<!-- TODO: Download link for existing template -->


<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>