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

if (isset($_POST['save'])) {
	$content = str_replace("\r\n", "\n", $_POST['body_text']);
	$file = $_POST['file'];
	if (($f = @fopen($current_path.$pathext.$file, 'w')) && @fwrite($f, $content) !== false && @fclose($f)) {
		$msg->addFeedback('FILE_SAVED');
		
	} else {
		$msg->addError('FILE_NOT_SAVED');
	}
}

if (isset($_POST['editfile'])) {
	if (!is_array($_POST['check'])) {
		// error: you must select a file/dir 
		$msg->addError('NO_FILE_SELECT');
	} else if (count($_POST['check']) != 1) {
		// error: select only one file
		$msg->addError('SELECT_ONE_FILE');
	} else {
		$file = $file[0];
		$filedata = stat($current_path.$pathext.$file);
		$path_parts = pathinfo($current_path.$pathext.$file);
		$ext = $path_parts['extension'];
		
		// open file to edit 
		if (is_dir($current_path.$pathext.$file)) {
			// error: cannot edit folder
			$msg->addError('BAD_FILE_TYPE');
		} else if ($ext == 'txt') {
			$_POST['body_text'] = file_get_contents($current_path.$pathext.$file);
		} else if (in_array($ext, array('html', 'htm'))){
			$_POST['body_text'] = file_get_contents($current_path.$pathext.$file);
			$_POST['body_text'] = get_html_body($_POST['body_text']); 
		} else {
			//error: bad file type
			$msg->addError('BAD_FILE_TYPE');
		}
		if (($ext == 'txt') || (in_array($ext, array('html', 'htm')))) {
			echo "\n\n".'<p align="center"><strong>'.$file."</strong></p>\n\n";
?>

			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" >
<?php	
				echo '<input type="hidden" name="pathext" value="'.$pathext.'" />'."\n";
				echo '<input type="hidden" name="file" value="'.$file.'" />'."\n";
?>
				<table cellspacing="1" cellpadding="0" width="98%" border="0" class="bodyline" summary="">
				<tr>
					<td colspan="2" valign="top" align="left" class="row1">
					<table cellspacing="0" cellpadding="0" width="100%" border="0" summary="">
					<tr><td class="row1">	
					<textarea  name="body_text" id="body_text" rows="25" class="formfield" style="width: 100%;"><?php echo ContentManager::cleanOutput($_POST['body_text']); ?></textarea>
					</td></tr></table>
					</td>
				</tr>
				<tr><td height="1" class="row2" colspan="2"></td></tr>
				<tr>
					<td colspan="2" valign="top" align="center" class="row1">
						<input type="reset" value="<?php echo _AT('reset'); ?>" class="button" />
						<input type="submit" name="save" value="<?php echo _AT('save'); ?>" class="button" />
						<input type="submit" name="cancel" value="<?php echo _AT('back'); ?>" class="button" />
					</td>
				</tr>

				</table>

			</form>
<?php
		
		require($_footer_file);
		exit;
		}
	}
}
?>
