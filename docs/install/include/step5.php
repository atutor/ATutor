<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

if(isset($_POST['submit'])) {

	$_POST['content_dir'] = stripslashes($addslashes($_POST['content_dir']));

	unset($errors);

	if(!file_exists($_POST['content_dir']) || !realpath($_POST['content_dir'])) {
		$errors[] = '<strong>Content Directory</strong> entered does not exist.';
	} else if (!is_dir($_POST['content_dir'])) {
		$errors[] = '<strong>Content Directory</strong> is not a directory.';
	} else if (!is_writable($_POST['content_dir'])){
		$errors[] = 'The Content Directory is not writable.';
	} else {

		$_POST['content_dir'] = realpath(urldecode($_POST['content_dir']));

		if (!is_dir($_POST['content_dir'].'/import')) {
			if (!@mkdir($_POST['content_dir'].'/import')) {
				$errors[] = '<strong>'.$_POST['content_dir'].'/import</strong> directory does not exist and cannot be created.';  
			}
		} else if (!is_writable($_POST['content_dir'].'/import')){
			$errors[] = '<strong>'.$_POST['content_dir'].'/import</strong> directory is not writable.';
		} 

		if (!is_dir($_POST['content_dir'].'/chat')) {
			if (!@mkdir($_POST['content_dir'].'/chat')) {
				$errors[] = '<strong>'.$_POST['content_dir'].'/chat</strong> directory does not exist and cannot be created.';  
			}
		} else if (!is_writable($_POST['content_dir'].'/chat')){
			$errors[] = '<strong>'.$_POST['content_dir'].'/chat</strong> directory is not writable.';
		}

		if (!is_dir($_POST['content_dir'].'/backups')) {
			if (!@mkdir($_POST['content_dir'].'/backups')) {
				$errors[] = '<strong>'.$_POST['content_dir'].'/backups</strong> directory does not exist and cannot be created.';  
			}
		} else if (!is_writable($_POST['content_dir'].'/backups')){
			$errors[] = '<strong>'.$_POST['content_dir'].'/backups</strong> directory is not writable.';
		}

		// save blank index.html pages to those directories
		@copy('../images/index.html', $_POST['content_dir'] . '/import/index.html');
		@copy('../images/index.html', $_POST['content_dir'] . '/chat/index.html');
		@copy('../images/index.html', $_POST['content_dir'] . '/backups/index.html');
		@copy('../images/index.html', $_POST['content_dir'] . '/index.html');
	}

	if (!isset($errors)) {
		unset($errors);
		unset($_POST['submit']);
		unset($action);

		$_POST['content_dir'] .= DIRECTORY_SEPARATOR;

		// kludge to fix the missing slashes when magic_quotes_gpc is On
		if ($addslashes != 'addslashes') {
			$_POST['content_dir'] = addslashes($_POST['content_dir']);
		}

		store_steps($step);
		$step++;
		return;
	}
}	

print_progress($step);

if (isset($errors)) {
	print_errors($errors);
}

if (isset($_POST['step1']['old_version'])) {
	//get real path to old content

	/*
	if (is_dir(urldecode($_POST['step1']['content_dir'])) ) {
		$copy_from = '';
	} else {
		$old_path = realpath('../../') . DIRECTORY_SEPARATOR . $_POST['step1']['old_path'];

		$this_dir = substr(realpath('../'), strlen(realpath('../../')));
		$end = substr(urldecode($_POST['step1']['content_dir']), strlen(realpath('../../').$this_dir));
		$copy_from = $old_path . $end . DIRECTORY_SEPARATOR;
	}

	$_defaults['content_dir'] = urldecode($_POST['step1']['content_dir']);
	*/

	$old_atutor_path = realpath('../../') . DIRECTORY_SEPARATOR . $_POST['step1']['old_path'];
	$old_content_dir = urldecode($_POST['step1']['content_dir']);

	if ($old_atutor_path . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR != $old_content_dir) {
		$copy_from = $old_atutor_path . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR;
	}

	$_defaults['content_dir'] = urldecode($_POST['step1']['content_dir']);
} else {
	$defaults = $_defaults;
	$blurb = '';
}


?>
<br />
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="step" value="<?php echo $step; ?>" />
	<input type="hidden" name="copy_from" value="<?php echo $copy_from; ?>" />
	<?php print_hidden($step); ?>

<?php if (!$copy_from && isset($_POST['step1']['old_version'])) : ?>
	<input type="hidden" name="content_dir" value="<?php echo urldecode($_POST['step1']['content_dir']); ?>" />
	<table width="80%" class="tableborder" cellspacing="0" cellpadding="1" align="center">	
	<tr>
		<td class="row1">The content directory at <strong><?php echo urldecode($_POST['step1']['content_dir']); ?> </strong> will be used for this installation's content.  No content files will be copied.</td>
	</tr>
	</table>
<?php elseif ($_POST['step3']['get_file'] == 'FALSE') : ?>
	<input type="hidden" name="content_dir" value="<?php if (!empty($_POST['content_dir'])) { echo stripslashes($addslashes($_POST['content_dir'])); } else { echo $_defaults['content_dir']; } ?>" />

	<table width="80%" class="tableborder" cellspacing="0" cellpadding="1" align="center">	
	<tr>
		<td class="row1"><small><b><label for="contentdir">Content Directory:</label></b><br />
		It has been detected that your server does not support the protected content directory feature. The content directory stores all the courses' files.<br /><br />Due to that restriction your content directory must exist within your ATutor installation directory and cannot be moved. Its path is specified below:</small>
		<br /><br />
		<input type="text" name="content_dir_disabled" id="contentdir" value="<?php if (!empty($_POST['content_dir'])) { echo stripslashes($addslashes($_POST['content_dir'])); } else { echo $_defaults['content_dir']; } ?>" class="formfield" size="70" disabled="disabled" /></td>
	</tr>
	</table>
<?php else: ?>
	<table width="80%" class="tableborder" cellspacing="0" cellpadding="1" align="center">	
	<tr>
		<td class="row1"><small><b><label for="contentdir">Content Directory:</label></b><br />
		Please specify where the content directory should be. The content directory stores all the courses' files. As a security measure, the content directory should be placed outside of your ATutor installation (for example, to a non-web-accessible location that is not publically available). On a Windows machine, the path should look like <kbd>C:\content</kbd>, while on Unix it should look like <kbd>/var/content</kbd>. The directory you specify must be created if it does not already exist and be writeable by the webserver. On Unix machines issue the command <kbd>chmod a+rwx content</kbd>, additionally the path may not contain any symbolic links.</small>
		<br /><br />
		<input type="text" name="content_dir" id="contentdir" value="<?php if (!empty($_POST['content_dir'])) { echo stripslashes($addslashes($_POST['content_dir'])); } else { echo $_defaults['content_dir']; } ?>" class="formfield" size="70" /></td>
	</tr>
	</table>
<?php endif; ?>
	<br /><br /><p align="center"><input type="submit" class="button" value=" Next »" name="submit" /></p>
</form>