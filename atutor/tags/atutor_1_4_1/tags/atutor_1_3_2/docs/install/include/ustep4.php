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
ignore_user_abort(true); 
@set_time_limit(0); 

if (!defined('AT_INCLUDE_PATH')) { exit; }

print_progress($step);

/* try copying the content over from the old dir to the new one */

require('../include/lib/filemanager.inc.php');

if (is_dir('../../'.$_POST['step1']['old_path'].'/content/')) {
	$courses = scandir('../../'.$_POST['step1']['old_path'].'/content/');

	foreach ($courses as $course) {
		if (is_numeric($course)) {
			copys('../../'.$_POST['step1']['old_path'].'/content/'.$course, '../content/'.$course);
			$progress[] = 'Course content directory <b>'.$course.'</b> copied successfully.';
		} 
	}
}

if (is_dir('../../'.$_POST['step1']['old_path'].'/content/chat/')) {
	$courses = scandir('../../'.$_POST['step1']['old_path'].'/content/chat/');

	foreach ($courses as $course) {
		if (is_numeric($course)) {
			copys('../../'.$_POST['step1']['old_path'].'/content/chat/'.$course, '../content/chat/'.$course);
		} 
	}
	$progress[] = 'Course chat directories copied successfully.';
}

if (isset($progress)) {
	print_feedback($progress);
}

if ($_POST['step3']['cache_dir'] != '') {
	define('CACHE_DIR', urldecode($_POST['step3']['cache_dir']));
	define('CACHE_ON', 1);
	require('../include/phpCache/phpCache.inc.php');
	cache_gc(NULL, 1, true);
}
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="step" value="6" />
<?php
	print_hidden(4);
?>

<br /><br /><p align="center"><input type="submit" class="button" value=" Next »" name="submit4" /></p>
</form>