<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/



if (isset($_POST['rename_action'])) {
	$_POST['new_name'] = trim($_POST['new_name']);
	$_POST['new_name'] = str_replace(' ', '_', $_POST['new_name']);
	$_POST['new_name'] = str_replace(array(' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '\''), '', $_POST['new_name']);

	$_POST['old_name'] = trim($_POST['old_name']);
	$_POST['old_name'] = str_replace(' ', '_', $_POST['old_name']);
	$_POST['old_name'] = str_replace(array(' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '\''), '', $_POST['old_name']);

	$path_parts_new = pathinfo($_POST['new_name']);
	$ext_new = $path_parts_new['extension'];

	if (file_exists($current_path.$pathext.$_POST['new_name']) || !file_exists($current_path.$pathext.$_POST['old_name'])) {
		$msg->printErrors('CANNOT_RENAME');
	} 
	/* check if this file extension is allowed: */
	/* $IllegalExtentions is defined in ./include/config.inc.php */
	else if (in_array($ext_new, $IllegalExtentions)) {
			$errors = array('FILE_ILLEGAL', $ext_new);
			$msg->printErrors($errors);
	}
	else {
		@rename($current_path.$pathext.$_POST['old_name'], $current_path.$pathext.$_POST['new_name']);
		$msg->printFeedbacks('RENAMED');
	}

}


/* check that at least one checkbox checked */		
if (isset($_POST['rename'])) {

	if (!is_array($_POST['check'])) {
		// error: you must select a file/dir to rename
		$msg->addError('NO_FILE_SELECT');
	} else if (count($_POST['check']) != 1) {
		// error: you must select one file/dir to rename
		$msg->addError('SELECT_ONE_FILE');
	} else {

		$oldname = $_POST['check'][0];

		echo '<h3>'._AT('rename_file_dir').'</h3>';
		echo '<p></p><p></p><form name="rename" action="'.$_SERVER['PHP_SELF'].'" method="post"><p>'."\n";
		echo '<input type="hidden" name="pathext" value="'.$_POST['pathext'].'" />';
		echo '<input type="hidden" name="old_name" value="'.$oldname.'" />';

		echo '<strong>'.$_POST['pathext'].'</strong><input type="text" name="new_name" value="'.$oldname.'" class="formfield" size="30" /> ';
		echo '<input type="submit" name="rename_action" value="'._AT('rename').'" class="button" />';
		echo ' - <input type="submit" name="cancel" value="'._AT('cancel').'" class="button" />';
		echo '</p></form>';
		echo '<hr size="4" width="100%">';

	}
}

?>