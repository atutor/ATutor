<?php	
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: $

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH . 'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_RSS);

$feed_id = intval($_GET['fid']);
$title_file = AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss_title.cache';

if (isset($_GET['submit'])) {

	//check both fields are not empty
	if (trim($_GET['title']) == '') {
		$msg->addError('TITLE_EMPTY');
	}
	if (trim($_GET['url']) == '') {
		$msg->addError('URL_EMPTY');
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

		$msg->addFeedback('FEED_SAVED');
		header("Location:index_admin.php");
		exit;
	} 

} else if (isset($_GET['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header("Location:index_admin.php");
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

require (AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
	<input type="hidden" name="fid" value="<?php echo $feed_id; ?>" />
	<div class="input-form" style="max-width: 525px">
		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="title"><?php echo _AT('title'); ?></label><br />
			<input id="title" name="title" type="text" size="60" maxlength="255" value="<?php echo stripslashes(htmlspecialchars($_GET['title'])); ?>" /><br />
		</div>

		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="url"><?php echo _AT('url'); ?></label><br />
			<input id="url" name="url" type="text" size="60" maxlength="255" value="<?php echo stripslashes(htmlspecialchars($_GET['url'])); ?>" /><br />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value=" <?php echo _AT('save'); ?> " accesskey="s" />
			<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " />
		</div>
	</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>