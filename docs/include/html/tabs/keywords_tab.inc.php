	<tr>
		<td colspan="2" valign="top" align="left" class="row1">
		<?php print_popup_help(AT_HELP_KEYWORDS); ?>
		<b><label for="keywords"><?php echo _AT('keywords'); ?>:</label></b><br />
		<?php if ($_POST['keywords']) { ?>
			<p><textarea name="keywords" class="formfield" cols="73" rows="2" id="keywords"><?php echo ContentManager::cleanOutput($_POST['keywords']); ?></textarea></p>
			<br />
		<?php } else {  ?>
			<p><textarea name="keywords" class="formfield" cols="73" rows="2" id="keywords"><?php echo ContentManager::cleanOutput($row['keywords']); ?></textarea></p>
			<br />
		<?php } ?>
		</td>
	</tr>