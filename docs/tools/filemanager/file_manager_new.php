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



	if (isset($_POST['overwritenewfile'])) {
		if (($f = @fopen($current_path.$pathext.$_POST['filename'],'w')) && @fwrite($f,$_POST['body_text']) != false && @fclose($f)){
			$msg->addFeedback('FILE_OVERWRITE');
		} else {
			$msg->addError('CANNOT_OVERWRITE_FILE');
		}
		unset($_POST['newfile']);
	}

	if(isset($_POST['savenewfile'])) {
		if (isset($_POST['filename']) && ($_POST['filename'] != "")) {
			$filename = $_POST['filename'];
			$ext = explode('.',$filename);

			if ((in_array($ext[1],$IllegalExtentions)) || (($ext[1] != 'txt') && (!in_array($ext[1], array('html','htm'))))) {
				$msg->addError('BAD_FILE_TYPE');
			} else if (!@file_exists($current_path.$pathext.$filename)) {
				$content = str_replace("\r\n", "\n", $_POST['body_text']);

				if (($f = fopen($current_path.$pathext.$filename, 'w')) && (@fwrite($f, $content)!== false)  && (@fclose($f))) {
					$msg->addFeedback('FILE_SAVED');

				} else {
					$msg->addError('FILE_NOT_SAVED');
				}
			} else {
				$msg->printWarnings(array('FILE_EXISTS', $filename));
				echo '<form name="form1" action="'.$_SERVER['PHP_SELF'].'" method="post">'."\n";
				echo '<input type="hidden" name="pathext" value="'.$pathext.'" />'."\n";
				echo '<input type="hidden" name="filename" value="'.$filename.'" />'."\n";
				echo '<input type="hidden" name="body_text" value="'.$_POST['body_text'].'" />'."\n";
				echo '<input type="submit" name="overwritenewfile" value="'._AT('overwrite').'" />';
				echo '<input type="submit" name="cancel" value="'._AT('cancel').'"/></p>'."\n";
				echo '</form>';
				$_POST['newfile'] = "new";
			
			}
		} else {
			$msg->addError('NEED_FILENAME');
		}
	}



if ($_GET['action'] == 'new') {

	$msg->printWarnings();
	$msg->printErrors();
	$msg->printFeedbacks();


?>
	<br />
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
		<input type="hidden" name="pathext" value="<?php echo $pathext ?>" />
		<table cellspacing="1" cellpadding="0" width="90%" border="0" class="bodyline" align="center" summary="">
			<tr><th class="cyan"><?php echo _AT('file_manager_new'); ?></th></tr>
			<tr>
				<td class="row1" colspan="2"><strong><label for="ctitle"><?php echo _AT('file_name');  ?>:</label></strong>
				<input type="text" name="filename" size="40" class="formfield" <?php if (isset($_POST['filename'])) echo 'value="'.$_POST['filename'].'"'?> /><?php echo _AT('html_only') ?></td>
			</tr>
			<tr>
				<td colspan="2" valign="top" align="left" class="row1">
				<table cellspacing="0" cellpadding="0" width="100%" border="0" summary="">
				<tr><td class="row1" align="center">	
				<textarea name="body_text" id="body_text" rows="25" class="formfield" style="width: 98%;"><?php echo ContentManager::cleanOutput($_POST['body_text']); ?></textarea>
				</td></tr></table>
				</td>
			</tr>
			<tr><td height="1" class="row2" colspan="2"></td></tr>
			<tr>
				<td colspan="2" valign="top" align="center" class="row1">
					<input type="submit" name="savenewfile" value="<?php echo _AT('save'); ?> [alt-s]" class="button" accesskey="s" />
					<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" />
				</td>
			</tr>

			</table>

		</form>
<?php

	require($_footer_file);
	exit;
}
?>
