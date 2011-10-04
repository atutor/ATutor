<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="add_poll" value="true" />

<div class="input-form">	
<fieldset class="group_form"><legend class="group_form"><?php echo _AT('add_poll'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php  echo _AT('question'); ?></label><br />
		<textarea name="question" cols="45" rows="3" id="question" style="width:90%;"><?php if (isset ($_POST['question'])) echo htmlspecialchars($_POST['question']);  ?></textarea>
	</div>

<?php for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++): ?>
	<div class="row">
		<?php if (($i==1) || ($i==2)) { ?>
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
		<?php } ?>
		<label for="c<?php echo $i; ?>"><?php echo _AT('choice'); ?> <?php echo $i; ?></label><br />
		<input type="text" name="c<?php echo $i; ?>" value="<?php if (isset($_POST['c' . $i])) echo htmlspecialchars($_POST['c' . $i]);  ?>" size="50" id="c<?php echo $i; ?>" />
	</div>
<?php endfor; ?>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
	</fieldset>
</div>
</form>