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
if (!defined('AT_INCLUDE_PATH')) { exit; }

?>
		<tr>
			<td align="right" class="row1" valign="top"><?php
				print_popup_help(AT_HELP_PASTE_FILE);
				?><strong><?php echo _AT('paste_file'); ?>:</strong></td>
			<td class="row1" valign="top"><input type="file" name="uploadedfile" class="formfield" size="20" /> <input type="submit" name="submit_file" value="<?php echo _AT('upload'); ?>" class="button" /><br />
				<small class="spacer">&middot;<?php echo _AT('html_only'); ?><br />
				&middot;<?php echo _AT('edit_after_upload'); ?></small>
			</td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td align="center" class="row1" colspan="2"><strong><?php echo _AT('or'); ?></strong></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1" colspan="2"><br /><strong><label for="ctitle"><?php echo _AT('title');  ?>:</label></strong>
			<input type="text" name="title" size="40" class="formfield" value="<?php echo ContentManager::cleanOutput($_POST['title']); ?>" id="ctitle" /></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<?php
			if ($content_row['content_path']) {
				echo '<tr>';
				echo '<td colspan="2" class="row1"><strong>'._AT('packaged_in').': <a href="'.$_base_href.'tools/file_manager.php?pathext='.urlencode($content_row['content_path'].'/').'">'.$content_row['content_path'].'</a></strong></td>';
				echo '</tr>';
				echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
			}
		?>
<tr><td colspan="2" valign="top" align="left" class="row1">
			<?php print_popup_help(AT_HELP_FORMATTING); ?>
			<b><?php echo _AT('formatting'); ?>:</b> <input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] == 0 && $_POST['visual'] == 0) { echo 'checked="checked"'; } ?> /><label for="text"><?php echo _AT('plain_text'); ?></label>, <input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] == 1 || $_POST['visual'] == 1) { echo 'checked="checked"'; } ?> onclick="document.form.visual.disabled=false;"/><label for="html"><?php echo _AT('html'); ?></label> (<?php
	// Option to use Visual Editor
	if ($_POST['visual'] || $_POST['formatting'] == "html") {
		echo '<input type="checkbox" onclick="javascript: myFunction(); document.form.formatting.html.checked=\'true\';" value="1" name="visual" id="visual" checked="checked" /><label for="visual">'._AT('enable visual').'</label>';
	}
	else{
		$_POST['visual'] = 0;
		echo '<input type="checkbox" onclick="javascript: myFunction(); document.form.formatting.html.checked=\'true\';" value="1" name="visual" id="visual" disabled="disabled" /><label for="visual">'._AT('enable visual').'</label>';
	}  	
	?>)
</td></tr>


		<tr><td height="1" class="row2" colspan="2"></td></tr>

		<tr>
			<td colspan="2" valign="top" align="left" class="row1"><?php print_popup_help(AT_HELP_BODY); ?><strong><label for="body_text"><?php echo _AT('body');  ?>:</label></strong>

			<br /><p>

			<textarea  name="body_text" id="body_text" cols="73" rows="20" class="formfield"><?php echo ContentManager::cleanOutput($_POST['body_text']); ?></textarea></p>
				</td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1" colspan="2"><?php require(AT_INCLUDE_PATH.'html/editor_tabs/content_code_picker.inc.php'); ?></td>
		</tr>
