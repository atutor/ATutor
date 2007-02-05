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

ignore_user_abort(true); 
@set_time_limit(0); 

if (!defined('AT_INCLUDE_PATH')) { exit; }

if (isset($_POST['submit'])) {
	if (!isset($errors)) {
		unset($_POST['submit']);
		unset($action);
		store_steps($step);
		$step++;
		return;
	}
}

print_progress($step);

/* try copying the content over from the old dir to the new one */
require('../include/lib/filemanager.inc.php'); // for copys()

$content_dir = urldecode(trim($_POST['step4']['content_dir']));
$_POST['step4']['copy_from'] = urldecode(trim($_POST['step4']['copy_from'])) . DIRECTORY_SEPARATOR;

//copy if copy_from is not empty

if ($_POST['step4']['copy_from'] && ($_POST['step4']['copy_from'] != DIRECTORY_SEPARATOR)) {
	if (is_dir($_POST['step4']['copy_from'])) {
		$files = scandir($_POST['step4']['copy_from']);

		foreach ($files as $file) {
			if ($file == '.' || $file == '..') { continue; }

			if (is_dir($file)) {
				copys($_POST['step4']['copy_from'].$file, $content_dir.$file);
				if (is_dir($content_dir.$course)) {			
					$progress[] = 'Course content directory <b>'.$file.'</b> copied successfully.';
				} else {
					$errors[] = 'Course content directory <b>'.$file.'</b> <strong>NOT</strong> copied.';
				}
			} else {
				// a regular file
				copy($_POST['step4']['copy_from'].$file, $content_dir.$file);
			}
		}
	}

} else {
	$progress[] = 'Using existing content directory <strong>'.$content_dir.'</strong>.';
}

echo '<br />';
if (isset($progress)) {
	print_feedback($progress);
}
if (isset($errors)) {
	print_errors($errors);
}

if ($_POST['step1']['cache_dir'] != '') {
	define('CACHE_DIR', urldecode($_POST['step1']['cache_dir']));
	define('CACHE_ON', 1);
	require('../include/phpCache/phpCache.inc.php');
	cache_gc(NULL, 1, true);
}

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="step" value="<?php echo $step;?>" />
<?php print_hidden($step); ?>

<br /><br /><p align="center"><input type="submit" class="button" value=" Next &raquo;" name="submit" /></p>
</form>