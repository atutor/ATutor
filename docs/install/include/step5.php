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
	unset($errors);

	if(!realpath($_POST['content_dir'])) {
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
		} 
		if (!is_writable($_POST['content_dir'].'/import')){
			$errors[] = '<strong>'.$_POST['content_dir'].'/import</strong> directory is not writable.';
		} 

		if (!is_dir($_POST['content_dir'].'/chat')) {
			if (!@mkdir($_POST['content_dir'].'/chat')) {
				$errors[] = '<strong>'.$_POST['content_dir'].'/chat</strong> directory does not exist and cannot be created.';  
			}
		} 
		if (!is_writable($_POST['content_dir'].'/chat')){
			$errors[] = '<strong>'.$_POST['content_dir'].'/chat</strong> directory is not writable.';
		} 		
	}

	if (!isset($errors)) {
		unset($errors);
		unset($_POST['submit']);
		unset($action);
		$_POST['content_dir'] .= DIRECTORY_SEPARATOR;
		store_steps($step);
		$step++;
		return;
	}
}	

print_progress($step);

if (isset($errors)) {
	print_errors($errors);
}

$old_path = realpath('../../').DIRECTORY_SEPARATOR.$_POST['step1']['old_path'];

if (isset($_POST['step1']['old_version'])) {
	//get real path to old content

	if (is_dir(urldecode($_POST['step1']['content_dir'])) ) {
		$copy_from = '';
	} else {
		$this_dir = substr(realpath('../'), strlen(realpath('../../')));
		$end = substr(urldecode($_POST['step1']['content_dir']), strlen(realpath('../../').$this_dir));
		$copy_from = $old_path.$end.DIRECTORY_SEPARATOR;
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

<?php if (!$copy_from) { ?>
	<input type="hidden" name="content_dir" value="<?php echo urldecode($_POST['step1']['content_dir']); ?>" />
	<table width="80%" class="tableborder" cellspacing="0" cellpadding="1" align="center">	
	<tr>
		<td class="row1">The content directory at <strong><?php echo urldecode($_POST['step1']['content_dir']); ?> </strong> will be used for this installation's content.  No content files will be copied.</td>
	</tr>
	</table>
<?php
} else {
?>
	<table width="80%" class="tableborder" cellspacing="0" cellpadding="1" align="center">	
	<tr>
		<td class="row1" colspan="2"><small><b>bal bal</small></td>
	</tr>
	<tr>
		<td class="row1"><small><b><label for="contentdir">Content Directory:</label></b><br />
		Where the content directory, which holds all course file manager files and imported content, is located. As a security measure, you may now move the content directory outside of your ATutor installation (for example, to a non-web-accessible location).  On a Windows machine, the path should look like <kbd>C:\htdocs\ATutor\content</kbd>, on Unix <kbd>htdocs/ATutor/content</kbd>.  </small></td>
		<td class="row1"><input type="text" name="content_dir" id="contentdir" value="<?php if (!empty($_POST['content_dir'])) { echo stripslashes($addslashes($_POST['content_dir'])); } else { echo $_defaults['content_dir']; } ?>" class="formfield" size="45" /></td>
	</tr>
	</table>
<?php
} 

?>
	<br /><br /><p align="center"><input type="submit" class="button" value=" Next »" name="submit" /></p>
</form>
