<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/


if(isset($_POST['submit']) && $_POST['action']=="process") {	
	
	//check that all values have been set	
	if ($_POST['max_file_size']=='' || $_POST['max_course_size']=='' || $_POST['max_course_float']==''
		|| $_POST['site_name']=='' || $_POST['cache_dir']=='') {
		$errors[] = 'Empty fields.';
	} else {
		
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
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="action" value="process" />
<input type="hidden" name="step" value="3" />
<?php print_hidden($step); ?>

<center><table width="65%" class="tableborder" cellspacing="0" cellpadding="1">
<tr>
	<td class="row1"><small><b>Email Notification:</b><br />
	Do you (the admin) want to be emailed when users request upgrades to Instructor accounts.  Default: <code>Yes</code></small></td>
	<td class="row1"><input type="radio" name="email_notification" value="TRUE" id="en_y" <?php if($_POST['$email_notification']=='TRUE' || empty($_POST['$email_notification'])) { echo "checked"; }?>/><label for="en_y">Yes</label>, <input type="radio" name="email_notification" value="FALSE" id="en_n" <?php if($_POST['$email_notification']=='FALSE') { echo "checked"; }?>/><label for="en_n">No</label></td>
</tr>
<tr>
	<td class="row1"><small><b>Allow Instructor Requests:</b><br />
	Allow regular users to request that their account be upgraded to instructor status.
	Default: <code>Yes</code></small></td>
	<td class="row1"><input type="radio" name="allow_instructor_requests" value="TRUE" id="air_y" <?php if($_POST['$allow_instructor_requests']=='TRUE' || empty($_POST['$allow_instructor_requests'])) { echo "checked"; }?>/><label for="air_y">Yes</label>, <input type="radio" name="allow_instructor_requests" value="FALSE" id="air_n" <?php if($_POST['$allow_instructor_requests']=='FALSE') { echo "checked"; }?>/><label for="air_n">No</label></td>
</tr>
<tr>
	<td class="row1"><small><b>Auto Approve Instructors:</b><br />
	If you said yes to Allow Instructor Requests, then you can have the requests approved instantly/automatically, otherwise each request will have to be approved manually by the admin.  Default: <code>No</code></small></td>
	<td class="row1"><input type="radio" name="auto_approve_instructors" value="TRUE" id="aai_y" <?php if($_POST['$auto_approve_instructors']=='TRUE') { echo "checked"; }?>/><label for="aai_y">Yes</label>, <input type="radio" name="auto_approve_instructors" value="FALSE" id="aai_n" <?php if($_POST['$auto_approve_instructors']=='FALSE' || empty($_POST['$auto_approve_instructors'])) { echo "checked"; }?>/><label for="aai_n">No</label></td>
</tr>
<tr>
	<td class="row1"><small><b>Maximum File Size:</b><br />
	Maximum allowable file size in Bytes to upload. This does not override the value set for upload_max_filesize in php.ini.  Default: <code>1048576</code> bytes</small></td>
	<td class="row1"><input type="text" name="max_file_size" value="<?php if (!empty($_POST['max_file_size'])) { echo $_POST['max_file_size']; } else { echo '1048576'; } ?>" class="formfield" /></td>
</tr>
<tr>
	<td class="row1"><small><b>Maximum Course Size:</b><br />
	Total maximum allowable course size in Bytes. The total of all the uploaded files.  Default: <code>10485760</code> bytes</small></td>
	<td class="row1"><input type="text" name="max_course_size" value="<?php if (!empty($_POST['max_course_size'])) { echo $_POST['max_course_size']; } else { echo '10485760'; } ?>" class="formfield" /></td>
</tr>
<tr>
	<td class="row1"><small><b>Maximum Course Float:</b><br />
	How much a course can be over its limit while still allowing the file or import to continue. Makes the actual course limit to be Max Course Size + Max Course Float, but when Max Course Float is reached, no more uploads will be allowed for that course. Default: <code>2097152</code> bytes</small></td>
	<td class="row1"><input type="text" name="max_course_float" value="<?php if (!empty($_POST['max_course_float'])) { echo $_POST['max_course_float']; } else { echo '2097152'; } ?>" class="formfield" /></td>
</tr>
<tr>
	<td class="row1"><small><b>Illegal Extensions:</b><br />
	Illegal file types, by extension. Any extensions to disallow for uploading. (Just the extention without the leading dot.)  Default: <code>exe, asp, php, bat, cgi, pl</code></small></td>
	<td class="row1"><textarea name="ill_ext" class="formfield"><?php if (!empty($_POST['ill_ext'])) { echo $_POST['ill_ext']; } else { echo 'exe, asp, php, bat, cgi, pl'; } ?></textarea></td>
</tr>
<tr>
	<td class="row1"><small><b>Allow Import Content Files:</b><br />
	Allow instructors to import the course content directory from the zip exported file. This option to disable the importing of the content directory was added as a security concern prior to ATutor 1.1. Default: <code>Yes</code></small></td>
	<td class="row1"><input type="radio" name="allow_import_content_files" value="TRUE" id="aicf_y" <?php if($_POST['$allow_import_content_files']=='TRUE' || empty($_POST['$allow_import_content_files'])) { echo "checked"; }?>/><label for="aicf_y">Yes</label>, <input type="radio" name="allow_import_content_files" value="FALSE" id="aicf_n" <?php if($_POST['$allow_import_content_files']=='FALSE') { echo "checked"; }?>/><label for="aicf_n">No</label></td></tr>
<tr>
	<td class="row1"><small><b>Site Name:</b><br />
	The name of your course server website. Default: <code>Course Server</code></small></td>
	<td class="row1"><input type="text" name="site_name" value="<?php if (!empty($_POST['site_name'])) { echo $_POST['site_name']; } else { echo 'Course Server'; } ?>" class="formfield" /></td>
</tr>
<tr>
	<td class="row1"><small><b>Cache Directory:</b><br />
	Where the cache directory should be created. On a Windows machine the path should look like C:\Windows\temp\. Default: <code>/tmp/cache</code></small></td>
	<td class="row1"><input type="text" name="cache_dir" value="<?php if (!empty($_POST['cache_dir'])) { echo $_POST['cache_dir']; } else { echo '/tmp/cache'; } ?>" class="formfield" /></td>
</tr>
</table></center>
</center>

<br /><br /><p align="center"><input type="submit" class="button" value=" Next »" name="submit" /></p>
</form>