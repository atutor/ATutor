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


print_progress($step);

/* try copying the content over from the old dir to the new one */

require('../include/lib/filemanager.inc.php');

$courses = scandir('../../'.$_POST['step1']['old_path'].'/content/');

foreach ($courses as $course) {
	if (is_numeric($course)) {
		copys('../../'.$_POST['step1']['old_path'].'/content/'.$course, '../content/'.$course);
		$progress[] = 'Course content directory <b>'.$course.'</b> copied successfully.';
	}
}

if (isset($progress)) {
	print_feedback($progress);
}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="step" value="5" />
<?php
	print_hidden(4);
?>

<br /><br /><p align="center"><input type="submit" class="button" value=" Next »" name="submit4" /></p>
</form>