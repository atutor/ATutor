<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: index.php 1905 2004-10-15 13:49:11Z shozubq $

$page = 'themes';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/themes.inc.php');

if (isset($_POST['export'])) {
	export_theme($_POST['theme_name']);
} else if(isset($_POST['delete'])) {
	header('Location: delete.php?theme_code='.$_POST['theme_name']);
	exit;
} else if(isset($_POST['default'])) {
	set_theme_as_default ($_POST['theme_name']);
	$feedback = array('THEME_DEFAULT', $_POST['theme_name']);
	$msg->addFeedback($feedback);
} else if(isset($_POST['enable'])) {
	$version = get_version($_POST['theme_name']);
	if ($version != VERSION) {
		$str = $_POST['theme_name'] . ' - version: ' . $version;
		$warnings = array('THEME_VERSION_DIFF', $str);
		$msg->addWarning($warnings);
	}

	$feedback = array('THEME_ENABLED', $_POST['theme_name']);
	$msg->addFeedback($feedback);
	enable_theme($_POST['theme_name']);
} else if(isset($_POST['disable'])) {
	$feedback = array('THEME_DISABLED', $_POST['theme_name']);
	$msg->addFeedback($feedback);
	disable_theme($_POST['theme_name']);
} else if(isset($_POST['import'])) {
	import_theme();
}

require(AT_INCLUDE_PATH.'header.inc.php');

//if themes directory is not writeable
if (!is_writable('../../themes/')) {
	//Attempt to make the Themes directory writeable
	@chmod('../../themes/', 0557);

	//if attempt successfull continue
	if (is_writable('../../themes/')) {
		//do nothing
	}
	else {
		//if not successfull display warning message with instruction on how to make the directory writeable
		$msg->addWarning('THEMES_NOT_WRITEABLE');
	}
}
?>

<form name="importForm" method="post" action="admin/themes/import.php" enctype="multipart/form-data">
<div class="input-form">
	<div class="row">
		<h3><?php echo _AT('import_theme'); ?></h3>
	</div>

	<div class="row">
		<label for="file"><?php echo _AT('upload_theme_package'); ?></label><br />
		<input type="file" name="file" size="40" id="file" />
	</div>

	<div class="row">
		<label for="url"><?php echo _AT('specify_url_to_theme_package'); ?></label><br />
		<input type="text" name="url" value="http://" size="40" id="url" />
	</div>
	
	<div class="row buttons">
	<input type= "submit" name="import" value="<?php echo _AT('import_theme'); ?>" />
	</div>
</div>
</form>

<?php

$themes = get_all_themes();

foreach ($themes as $theme):
	if ($theme == 'Atutor') {
		$ss = $_base_href . 'themes/default/screenshot.jpg';
	} else {
		$src = get_folder($theme);
		$ss = $_base_href . 'themes/' . $src . '/screenshot.jpg';
	}

	$info = get_themes_info($theme);
?>


<form name="themes" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" value="<?php echo $t; ?>" name="theme_name" />

<div class="input-form">
	<div class="row">
		<h3><?php echo $theme; ?></h3>
	</div>

	<img src="<?php echo $ss; ?>" width="185" height="126" border="0" alt="" style="float: right; margin-right: 10px;"/>

	<div class="row">
		<p><?php echo AT_print($info['extra_info'], 'themes.extra_info'); ?></p>
	</div>

	<div class="row">
		<?php echo _AT('version'); ?><br />
		<?php echo AT_print($info['version'], 'themes.version'); ?>
	</div>

	<div class="row">
		<?php echo _AT('updated'); ?><br />
		<?php echo AT_print($info['last_updated'], 'themes.last_updated'); ?>
	</div>

	<div class="row buttons">
		<input type="submit" name="export" value="<?php echo _AT('export'); ?>" />
		<?php if (intval(check_status($theme)) == 0) : ?>
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
			<input type="submit" name="enable" value="<?php echo _AT('enable'); ?>" />
			<input type="submit" name="default" value="<?php echo _AT('set_default'); ?>" />
		<?php elseif (intval(check_status($theme)) == 1) : ?>
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
			<input type="submit" name="disable" value="<?php echo _AT('disable'); ?>" />
			<input type="submit" name="default" value="<?php echo _AT('set_default'); ?>" />
		<?php else: ?>
			<em><?php echo _AT('current_default_theme'); ?></em>
		<?php endif; ?>
	</div>
</div>

</form>
<?php endforeach; ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>