
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="edit_poll" value="true" />
<input type="hidden" name="poll_id" value="<?php echo $this->row['poll_id']; ?>" />

<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT('edit_poll'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php echo _AT('question'); ?>:</label><br />
		<textarea name="question" cols="55" rows="3" id="question"><?php if (isset ($_POST['question'])) { echo AT_print($_POST['question'], 'input.text'); } else { echo AT_print($this->row['question'], 'input.text'); } ?></textarea>
	</div>

<?php
	for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++): ?>
		<div class="row">
			<?php if (($i==1) || ($i==2)) { ?>
				<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
			<?php } ?>
			<label for="c<?php echo $i; ?>"><?php echo _AT('choice'); ?> <?php echo $i; ?>:</label><br />
			<input type="text" name="c<?php echo $i; ?>" id="c<?php echo $i; ?>" value="<?php if (isset ($_POST['c' . $i])) { echo AT_print($_POST['c' . $i], 'input.text'); } else { echo AT_print($this->row['choice' . $i], 'input.text'); }?>" size="40" />
		</div>

<?php endfor; ?>
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?> " />
	</div>
	</fieldset>
</div>
</form>