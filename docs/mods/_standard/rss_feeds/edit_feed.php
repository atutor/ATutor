<?php	
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH . 'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_RSS);

$feed_id = intval($_GET['fid']);
$title_file = AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss_title.cache';

if (isset($_GET['submit'])) {
	$missing_fields = array();
	//check both fields are not empty
	if (trim($_REQUEST['title']) == '') {
		$missing_fields[] = _AT('title');
	}



	if (trim($_REQUEST['url']) == '') {
		$missing_fields[] = _AT('url');
	}
	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) {
		$_GET['url'] = $addslashes($_GET['url']);
		
		$sql	= "REPLACE INTO ".TABLE_PREFIX."feeds VALUES(".$feed_id.", '".$_GET['url']."')";
		$result = mysql_query($sql, $db);

		//update language
		if ($f = @fopen($title_file, 'w')) {
			fwrite($f, $_GET['title'], strlen($_GET['title']));
			fclose($f);
		}

		//delete old cache file
		@unlink(AT_CONTENT_DIR.'/feeds/'.$feed_id.'_rss.cache');

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: index.php');
		exit;
	} 

} else if (isset($_GET['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header("Location:index.php");
	exit;
}

if ($feed_id != '') {

	$sql	= "SELECT * FROM ".TABLE_PREFIX."feeds WHERE feed_id=".$feed_id;
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	
	if (file_exists($title_file)) {
		$_GET['title'] = file_get_contents($title_file);
	}
	$_GET['url'] = $row['url'];
} 

$onload = 'document.form.title.focus();';

require (AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="form">
	<input type="hidden" name="fid" value="<?php echo $feed_id; ?>" />
	<div class="input-form" style="width:95%">
		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
			<input id="title" name="title" type="text" size="60" maxlength="255" value="<?php echo $stripslashes(htmlspecialchars($_GET['title'])); ?>" /><br />
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="url"><?php echo _AT('url'); ?></label><br />
			<input id="url" name="url" type="text" size="60" maxlength="255" value="<?php echo $stripslashes(htmlspecialchars($_GET['url'])); ?>" /><br />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value=" <?php echo _AT('save'); ?> " accesskey="s" />
			<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " />
		</div>
	</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>