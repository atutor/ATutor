<?php	
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH . 'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_RSS);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header("Location: index_admin.php");
	exit;
} else if (isset($_POST['submit'])) {
	$missing_fields = array();

	if (trim($_POST['title']) == '') {
		$missing_fields[] = _AT('title');
	}
	if (trim($_POST['url']) == '') {
		$missing_fields[] = _AT('url');
	}
	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) {
		$output = make_cache_file(0);
		if (!isset($output) || empty($output)) {
			$msg->addError('FEED_NO_CONTENT');
		}
	}

	if ($msg->containsErrors()) {
		unset($_POST['confirm']);
	}

} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');

} else if (isset($_POST['submit_yes'])) {
	$_POST['url'] = $addslashes($_POST['url']);

	$sql	= "INSERT INTO ".TABLE_PREFIX."feeds VALUES(0, '".$_POST['url']."')";
	$result = mysql_query($sql, $db);

	$feed_id = mysql_insert_id($db);
	
	//copy load file
	copy('../../mods/_standard/rss_feeds/load_file.php', AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss.inc.php');

	//add language
	$title_file = AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss_title.cache';
	if ($f = @fopen($title_file, 'w')) {
		fwrite ($f, $_POST['title'], strlen($_POST['title']));
		fclose($f);
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php');
	exit;
} 

$onload = 'document.form.title.focus();';

require (AT_INCLUDE_PATH.'header.inc.php');

if (!isset($_POST['confirm'])) {
?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
		<input type="hidden" name="confirm" value="1" />

		<div class="input-form" style="max-width: 525px">
			<div class="row">
				<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="title"><?php echo _AT('title'); ?></label><br />
				<input id="title" name="title" type="text" size="60" maxlength="255" value="<?php echo $stripslashes(htmlspecialchars($_POST['title'])); ?>" /><br />
			</div>

			<div class="row">
				<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="url"><?php echo _AT('url'); ?></label><br />
				<input id="url" name="url" type="text" size="60" maxlength="255" value="<?php echo $stripslashes(htmlspecialchars($_POST['url'])); ?>" /><br />
			</div>

			<div class="row buttons">
				<input type="submit" name="submit" value=" <?php echo _AT('save'); ?> " accesskey="s" />
				<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " />
			</div>
		</div>
	</form>
<?php 
} else { ?>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="new" value="<?php echo $_POST['new']; ?>" />

	<div class="input-form">
		<div class="row">
			<h3><?php if (file_exists($title_file)) { 
					readfile($title_file); 
				} else {
					echo $_POST['title'];
				}?>
			</h3>
		</div>

		<div class="row">
			<?php echo $output; ?>
		</div>
	</div>
	</form>

	<?php
		$hidden_vars['new'] = '1';
		$hidden_vars['title'] = $_POST['title'];
		$hidden_vars['url'] = $_POST['url'];

		$msg->addConfirm('ADD_FEED', $hidden_vars);
		$msg->printConfirm();
	?>

<?php 
}
?>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>