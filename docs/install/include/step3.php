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

if(isset($_POST['submit']) && ($_POST['action'] == 'process')) {
	unset($errors);

	$_POST['admin_username'] = trim($_POST['admin_username']);
	$_POST['admin_password'] = trim($_POST['admin_password']);
	$_POST['admin_email']    = trim($_POST['admin_email']);
	$_POST['site_name']      = trim($_POST['site_name']);
	$_POST['header_img']     = trim($_POST['header_img']);
	$_POST['header_logo']    = trim($_POST['header_logo']);
	$_POST['home_url']	     = trim($_POST['home_url']);

	$_POST['cache_dir']      = trim($_POST['cache_dir']);

	$_POST['max_file_size']    = intval($_POST['max_file_size']);
	$_POST['max_course_size']  = intval($_POST['max_course_size']);
	$_POST['max_course_float'] = intval($_POST['max_course_float']);


	/* login name check */
	if ($_POST['admin_username'] == ''){
		$errors[] = 'Administrator username cannot be empty.';
	} else {
		/* check for special characters */
		if (!(eregi("^[a-zA-Z0-9_]([a-zA-Z0-9_])*$", $_POST['admin_username']))){
			$errors[] = 'Administrator username is not valid.';
		}
	}

	if ($_POST['admin_password'] == '') {
		$errors[] = 'Administrator password cannot be empty.';
	}

	if ($_POST['admin_email'] == '') {
		$errors[] = 'Administrator email cannot be empty.';
	}

	//check that all values have been set	
	if ($_POST['max_file_size'] == '') {
		$errors[] = 'Maximum File Size cannot be empty.';
	}
	if ($_POST['max_course_size'] == '') {
		$errors[] = 'Maximum Course Size cannot be empty.';
	}
	if ($_POST['max_course_float'] == '') {
		$errors[] = 'Maximum Course Size cannot be empty.';
	}
	if ($_POST['site_name'] == '') {
		$errors[] = 'Site name cannot be empty.';
	}

	if ($_POST['cache_dir'] != '') {
		if (!is_dir($_POST['cache_dir'])) {
			$errors[] = 'The Cache Directory chosen does not exist.';
		} else if (!is_writable($_POST['cache_dir'])){
			$errors[] = 'The Cache Directory is not writable.';
		}
	}

	if (!isset($errors)) {
		//put quotes around each extension
		$exts= explode(",",$_POST['ill_ext']);
		$_POST['ill_ext'] = "";
		foreach ($exts as $ext) {
			$_POST['ill_ext'] .= "'".trim($ext)."', ";
		}

		unset($errors);
		unset($_POST['submit']);
		unset($action);
		store_steps($step);
		$step++;
		return;
	}
}	

print_progress($step);

if (isset($errors)) {
	print_errors($errors);
}

if (isset($_POST['step1']['old_version']) && $_POST['upgrade_action']) {

	$defaults['admin_username'] = urldecode($_POST['step1']['admin_username']);
	$defaults['admin_password'] = urldecode($_POST['step1']['admin_password']);
	$defaults['admin_email'] = urldecode($_POST['step1']['admin_email']);

	$defaults['site_name'] = urldecode($_POST['step1']['site_name']);
	$defaults['header_img'] = urldecode($_POST['step1']['header_img']);
	$defaults['header_logo'] = urldecode($_POST['step1']['header_logo']);
	$defaults['home_url'] = urldecode($_POST['step1']['home_url']);

	$defaults['email_notification'] = $_POST['step1']['email_notification'];
	$defaults['allow_instructor_requests'] = $_POST['step1']['allow_instructor_requests'];
	$defaults['auto_approve_instructors'] = $_POST['step1']['auto_approve_instructors'];

	$defaults['max_file_size'] = $_POST['step1']['max_file_size'];
	$defaults['max_course_size'] = $_POST['step1']['max_course_size'];
	$defaults['max_course_float'] = $_POST['step1']['max_course_float'];
	$defaults['ill_ext'] = urldecode($_POST['step1']['ill_ext']);
	$defaults['theme_categories'] = $_POST['step1']['theme_categories'];
	$defaults['cache_dir'] = urldecode($_POST['step1']['cache_dir']);

	if (version_compare($_POST['step1']['old_version'], '1.3.1', '<')) {
		$blurb = '<tr><td colspan="2" class="row1"><small><span style="color: red; font-weight: bold;">Note: Since version 1.3.1 the administrator account can be customized using any username and password.</span></small></td></tr>';
	}

	$_POST['email_notification'] = $defaults['email_notification'];
	$_POST['allow_instructor_requests'] = $defaults['allow_instructor_requests'];
	$_POST['auto_approve_instructors'] = $defaults['auto_approve_instructors'];

} else {
	$defaults = $_defaults;
	$blurb = '';
}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="action" value="process" />
	<input type="hidden" name="step" value="<?php echo $step; ?>" />
	<?php print_hidden($step); ?>

	<?php
		/* detect mail settings. if sendmail_path is empty then use SMTP. */
		if (@ini_get('sendmail_path') == '') { 
			echo '<input type="hidden" name="smtp" value="true" />';
		} else {
			echo '<input type="hidden" name="smtp" value="false" />';
		}
	?>

	<h4>Administrator Account</h4>
	<p>The Administrator account is used for managing ATutor user accounts and courses. There can be only one Administrator account.</p>
	<p>Keep the administrator username and password in a secure location. If at any time you wish to change the password or email, edit the <kbd>config.inc.php</kbd> file found in the <kbd>./include/</kbd> directory.</p>

	<table width="70%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
	<?php echo $blurb; ?>
	<tr>
		<td class="row1"><small><b><label for="username">Administrator Username:</label></b><br />
		May contain only letters, numbers, or underscores. 20 character maximum. A username other than <kbd>admin</kbd> is recommended.</small></td>
		<td class="row1"><input type="text" name="admin_username" id="username" maxlength="20" size="20" value="<?php if (!empty($_POST['admin_username'])) { echo stripslashes(htmlspecialchars($_POST['admin_username'])); } else { echo $defaults['admin_username']; } ?>" class="formfield" /></td>
	</tr>
	<tr>
		<td class="row1"><small><b><label for="password">Password:</label></b><br />
		Use a combination of letters, numbers and symbols.<br />15 character maximum.</small></td>
		<td class="row1"><input type="text" name="admin_password" id="password" maxlength="15" size="15" value="<?php if (!empty($_POST['admin_password'])) { echo stripslashes(htmlspecialchars($_POST['admin_password'])); } else { echo $defaults['admin_password']; } ?>" class="formfield" /></td>
	</tr>
	<tr>
		<td class="row1"><small><b><label for="email">Email:</label></b><br />
		The email that will be used as the return email when needed and when instructor account requests are made.</small></td>
		<td class="row1"><input type="text" name="admin_email" id="email" size="30" value="<?php if (!empty($_POST['admin_email'])) { echo stripslashes(htmlspecialchars($_POST['admin_email'])); } else { echo $defaults['admin_email']; } ?>" class="formfield" /></td>
	</tr>
	</table>

	<br />
	<h4>System Preferences</h4>
	<p>These preferences affect hosted courses and the general operation of the course server.</p>
	<table width="80%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
	<tr>
		<td class="row1"><small><b><label for="sitename">Site Name:</b><br />
		The name of your course server website.<br />Default: <kbd><?php echo $defaults['site_name']; ?></kbd></small></td>
		<td class="row1"><input type="text" name="site_name" size="28" maxlength="60" id="sitename" value="<?php if (!empty($_POST['site_name'])) { echo stripslashes(htmlspecialchars($_POST['site_name'])); } else { echo $defaults['site_name']; } ?>" class="formfield" /></td>
	</tr>
	<tr>
		<td class="row1"><small><b><label for="headerimg">Public Area - Header Image:</b><br />
		This image will appear in the top left corner of the public area header.  Dimensions are approximately w:230 x h:90 pixels.  Enter the URL or path to this image.<br />Default: <kbd><?php echo $defaults['header_img'];?></kbd></small></td>
		<td class="row1"><input type="text" name="header_img" size="28" maxlength="60" id="headerimg" value="<?php if (!empty($_POST['header_img'])) { echo stripslashes(htmlspecialchars($_POST['header_img'])); } else { echo $defaults['header_img']; } ?>" class="formfield" /></td>
	</tr>
	<tr>
		<td class="row1"><small><b><label for="headerlogo">Public Area - Logo:</b><br />
		This image will appear in the top right corner of the public area header. Enter the URL or path to this image.<br />Default: <kbd><?php echo $defaults['header_logo'];?></kbd></small></td>
		<td class="row1"><input type="text" name="header_logo" size="28" maxlength="60" id="headerlogo" value="<?php if (!empty($_POST['header_logo'])) { echo stripslashes(htmlspecialchars($_POST['header_logo'])); } else { echo $defaults['header_logo']; } ?>" class="formfield" /></td>
	</tr>
	<tr>
		<td class="row1"><small><b><label for="home_url">Public Area - 'Home' URL:</b><br />
		This will be the URL for the 'Home' link in the Public Area.  Leave empty to have this link not appear. <br /></small></td>
		<td class="row1"><input type="text" name="home_url" size="28" maxlength="60" id="home_url" value="<?php if (!empty($_POST['home_url'])) { echo stripslashes(htmlspecialchars($_POST['home_url'])); } else { echo $defaults['home_url']; } ?>" class="formfield" /></td>
	</tr>
	<tr>
		<td class="row1"><small><b>Email Notification:</b><br />
		Do you want to be emailed when a user requests an instructor account?<br />
		Default: <kbd>Yes</kbd></small></td>
		<td class="row1"><input type="radio" name="email_notification" value="TRUE" id="en_y" <?php if ($_POST['email_notification']=='TRUE' || empty($_POST['email_notification'])) { echo 'checked="checked"'; }?>/><label for="en_y">Yes</label>, <input type="radio" name="email_notification" value="FALSE" id="en_n" <?php if($_POST['email_notification']=='FALSE') { echo 'checked="checked"'; }?> /><label for="en_n">No</label></td>
	</tr>
	<tr>
		<td class="row1"><small><b>Allow Instructor Requests:</b><br />
		Allow users to request instructor accounts?<br />
		Default: <kbd>Yes</kbd></small></td>
		<td class="row1"><input type="radio" name="allow_instructor_requests" value="TRUE" id="air_y" <?php if($_POST['allow_instructor_requests']=='TRUE' || empty($_POST['allow_instructor_requests'])) { echo 'checked="checked"'; }?>/><label for="air_y">Yes</label>, <input type="radio" name="allow_instructor_requests" value="FALSE" id="air_n" <?php if($_POST['allow_instructor_requests']=='FALSE') { echo 'checked="checked"'; }?>/><label for="air_n">No</label></td>
	</tr>
	<tr>
		<td class="row1"><small><b>Auto Approve Instructors:</b><br />
		If you answered yes to Allow Instructor Requests, then do you want the requests to be approved instantly, bypassing the approval process?<br />Default: <kbd>No</kbd></small></td>
		<td class="row1"><input type="radio" name="auto_approve_instructors" value="TRUE" id="aai_y" <?php if($_POST['auto_approve_instructors']=='TRUE') { echo 'checked="checked"'; }?>/><label for="aai_y">Yes</label>, <input type="radio" name="auto_approve_instructors" value="FALSE" id="aai_n" <?php if($_POST['auto_approve_instructors']=='FALSE' || empty($_POST['auto_approve_instructors'])) { echo 'checked="checked"'; }?>/><label for="aai_n">No</label></td>
	</tr>
	<tr>
		<td class="row1"><small><b><label for="maxfile">Maximum File Size:</label></b><br />
		Maximum allowable file size in Bytes to upload. This does not override the value set for <kbd>upload_max_filesize</kbd> in <kbd>php.ini</kbd>.<br />Default: <kbd><?php echo $_defaults['max_file_size']; ?></kbd> bytes</small></td>
		<td class="row1"><input type="text" size="10" name="max_file_size" id="maxfile" value="<?php if (!empty($_POST['max_file_size'])) { echo stripslashes(htmlspecialchars($_POST['max_file_size'])); } else { echo $defaults['max_file_size']; } ?>" class="formfieldR" /></td>
	</tr>
	<tr>
		<td class="row1"><small><b><label for="maxcourse">Maximum Course Size:</label></b><br />
		Total maximum allowable course size in Bytes. The total of all the uploaded files.<br />Default: <kbd><?php echo $_defaults['max_course_size']; ?></kbd> bytes</small></td>
		<td class="row1"><input type="text" size="10" name="max_course_size" id="maxcourse" value="<?php if (!empty($_POST['max_course_size'])) { echo stripslashes(htmlspecialchars($_POST['max_course_size'])); } else { echo $defaults['max_course_size']; } ?>" class="formfieldR" /></td>
	</tr>
	<tr>
		<td class="row1"><small><b><label for="float">Maximum Course Float:</label></b><br />
		How much a course can be over its limit while still allowing the file to upload or import to continue. Makes the actual course limit to be <kbd>Max Course Size + Max Course Float</kbd>, but when Max Course Float is reached, no more uploads will be allowed for that course.<br />Default: <kbd><?php echo $_defaults['max_course_float']; ?></kbd> bytes</small></td>
		<td class="row1"><input type="text" size="10" name="max_course_float" id="float" value="<?php if (!empty($_POST['max_course_float'])) { echo stripslashes(htmlspecialchars($_POST['max_course_float'])); } else { echo $defaults['max_course_float']; } ?>" class="formfieldR" /></td>
	</tr>
	<tr>
		<td class="row1"><small><b><label for="ext">Illegal Extensions:</label></b><br />
		Illegal file types, by extension. Any extensions to disallow for uploading or importing. (Just the extention without the leading dot.)<br />Default: <kbd><?php echo $_defaults['ill_ext']; ?></kbd></small></td>
		<td class="row1"><textarea name="ill_ext" cols="24" id="ext" rows="5" class="formfield"><?php if (!empty($_POST['ill_ext'])) { echo $_POST['ill_ext']; } else { echo $defaults['ill_ext']; } ?></textarea></td>
	</tr>
	<tr>
		<td class="row1"><small><b><label for="cache">Cache Directory:</label></b><br />
		Where the cache directory should be created. On a Windows machine the path should look like <kbd>C:\Windows\temp\</kbd>, on Unix <kbd>/tmp/cache/</kbd>. Leave empty to disable caching.</small></td>
		<td class="row1"><input type="text" name="cache_dir" id="cache" value="<?php if (!empty($_POST['cache_dir'])) { echo stripslashes(htmlspecialchars($_POST['cache_dir'])); } else { echo $defaults['cache_dir']; } ?>" class="formfield" /></td>
	</tr>
	<tr>
		<td class="row1"><small><b>Enable Theme Specific Categories:</b><br />
		Theme specific categories allows you to associate themes with categories. Courses belonging to a specific category will always be viewed using that category's theme. Caution: This option also disables the personalised theme preference.<br />Default: <kbd>No</kbd></small></td>
		<td class="row1"><input type="radio" name="theme_categories" value="TRUE" id="tc_y" <?php if($_POST['theme_categories']=='TRUE') { echo 'checked="checked"'; }?>/><label for="tc_y">Yes</label>, <input type="radio" name="theme_categories" value="FALSE" id="tc_n" <?php if($_POST['theme_categories']=='FALSE' || empty($_POST['theme_categories'])) { echo 'checked="checked"'; }?>/><label for="tc_n">No</label></td>
	</tr>
	<tr>
		<td class="row1"><small><b><label for="course_backups">Course Backups:</label></b><br />
		Maximum number of course backups that can be stored per course.<br />Default: <kbd><?php echo $_defaults['course_backups']; ?></kbd></small></td>
		<td class="row1"><input type="text" size="2" name="course_backups" id="course_backups" value="<?php if (!empty($_POST['course_backups'])) { echo stripslashes(htmlspecialchars($_POST['course_backups'])); } else { echo $defaults['course_backups']; } ?>" class="formfieldR" /></td>
	</tr>
	</table>

	<br /><br /><p align="center"><input type="submit" class="button" value=" Next »" name="submit" /></p>
</form>