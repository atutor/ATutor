<?php
$top_level = $contentManager->getContent($row['content_parent_id']);
if($_POST['title']){
?>
		<tr>
			<td class="row1" colspan="2"><br /><b><label for="title"><?php echo _AT('title');  ?>:</label></b>
			<input type="text" name="title" size="40" class="formfield" value="<?php echo ContentManager::cleanOutput($_POST['title']); ?>" id="title" /></td>
		</tr>
		<?php }else{ ?>
		
		<tr>
			<td class="row1" colspan="2"><br /><b><label for="title"><?php echo _AT('title');  ?>:</label></b> <input type="text" name="title" size="40" id="title" class="formfield" value="<?php echo ContentManager::cleanOutput($row['title']); ?>" /></td>
		</tr>
		
		<?php } ?>
		<tr><td height="1" class="row2" colspan="2"></td></tr>

		<tr>
			<td colspan="2" class="row1" valign="top"><?php
				$errors[]=AT_ERROR_BAD_DATE;
				//$def = 'text<b>as</b>';   //whats this? untranslated
				print_popup_help(AT_HELP_PASTE_FILE1);
				?>
			<b><?php echo _AT('paste_file'); ?>:</b> 
			<input type="file" name="uploadedfile" class="formfield" size="20" /> <input type="submit" name="submit_file" value=" <?php echo _AT('upload'); ?>" class="button" /><?php
			?><br />
			<small class="spacer"><?php echo _AT('html_only') ?><br />
			<?php echo _AT('edit_after_upload'); ?></small>

			</td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td align="center" class="row1" colspan="2"><b><?php echo _AT('or');?></b></td>
		</tr>	
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<?php
			if ($row['content_path']) {
				echo '<tr>';
				echo '<td colspan="2" class="row1"><b>'._AT('packaged_in').': '.$row['content_path'].'</b></td>';
				echo '</tr>';
				echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
			}
		?>
		<tr>
			<td colspan="2" valign="top" align="left" class="row1"><?php print_popup_help(AT_HELP_BODY); ?><b><label for="body"><?php echo _AT('body');  ?>:</label></b><br />

			<?php if (isset($_POST['text'])) { ?>
				<p><textarea name="text" class="formfield" cols="73" rows="20" id="body"><?php echo ContentManager::cleanOutput($_POST['text']); ?></textarea></p>
			<?php } else {  ?>
				<p><textarea name="text" class="formfield" cols="73" rows="20" id="body"><?php echo ContentManager::cleanOutput($row['text']); ?></textarea></p>
			<?php } ?>
		<?php print_popup_help(AT_HELP_FORMATTING); ?>
			<b><?php echo _AT('formatting'); ?>:</b> <input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] === 0) { echo 'checked="checked"'; } ?> /><label for="text"><?php echo _AT('plain_text'); ?></label>, <input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] !== 0) { echo 'checked="checked"'; } ?> /><label for="html"><?php echo _AT('html'); ?></label> <?php
			?>
				</td>
		</tr>	
		<tr>
			<td class="row1" colspan="2"><a href="<?php echo substr($_my_uri, 0, strlen($_my_uri)-1); ?>#jumpcodes" title="<?php echo _AT('jump_codes') ?>"><?php print_popup_help(AT_HELP_ADD_CODES); ?><img src="images/clr.gif" height="1" width="1" alt="<?php echo _AT('jump_codes') ?>" border="0" /></a><?php require(AT_INCLUDE_PATH.'html/code_picker.inc.php'); ?></td>
		</tr>	