<div class="input-form">
	<form action="" method="post" >
		<div class="row">
			<label for="pa_max_memory"><?php echo _AT('pa_max_memory'); ?></label><br/>
			<input type="text" id="pa_max_memory" name="pa_max_memory" value="<?php echo $this->max_memory; ?>"/> <?php echo _AT('mb'); ?>
		</div>

		<div class="row">
			<input class="button" type="submit" name="submit" value="<?php echo _AT('save'); ?>" />
		</div>
	</form>
</div>