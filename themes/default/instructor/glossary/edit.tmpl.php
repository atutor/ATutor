<?php global $stripslashes; ?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="gid" value="<?php echo $this->gid; ?>" />

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('edit_glossary'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('glossary_term');  ?></label><br/ >
		<input type="text" name="word" size="40" id="title" value="<?php echo htmlentities_utf8($stripslashes($row['word'])); ?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="body"><?php echo _AT('glossary_definition'); ?></label><br />
		<textarea name="definition" cols="55" rows="7" id="body"><?php echo htmlentities_utf8($row['definition']); ?></textarea>
	</div>

	<div class="row">
		<?php echo _AT('glossary_related');  ?><br />
	<?php
		
		if ($row_g = mysql_fetch_array($this->result_related)) {
			echo '<select name="related_term">';
			echo '<option value="0"></option>';
			do {
				if ($row_g['word_id'] == $row['word_id']) {
					continue;
				}
		
				echo '<option value="'.$row_g['word_id'].'"';
			
				if ($row_g['word_id'] == $row['related_word_id']) {
					echo ' selected="selected" ';
				}
			
				echo '>'.htmlentities_utf8($row_g['word']).'</option>';
			} while ($row_g = mysql_fetch_array($result));
			
			echo '</select>';
		
		} else {
			echo  _AT('no_glossary_items');
		}
	?>
	</div>
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel');  ?>" />
	</div>
	</fieldset>
</div>
</form>