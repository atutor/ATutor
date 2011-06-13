<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('create_backup'); ?></legend>
	<div class="row">
		<?php echo _AT('create_backup_about', AT_COURSE_BACKUPS); ?>
	</div>

	<?php if ($this->Backup->getNumAvailable() >= AT_COURSE_BACKUPS): ?>
		<div class="row">
			<p><strong><?php echo _AT('max_backups_reached'); ?></strong></p>
		</div>
	<?php else: ?>
		<div class="row">
			<label for="desc"><?php echo _AT('optional_description'); ?></label>
			<textarea cols="35" rows="2" id="desc" name="description"></textarea>
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('create'); ?>" accesskey="s"  class="button"/> 
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  class="button"/>
		</div>
	<?php endif; ?>
</div>
</form>