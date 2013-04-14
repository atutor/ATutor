<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" >
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('upload'); ?></legend>
	<div class="row">
		<p><?php echo _AT('restore_upload'); ?></p>
	</div>

	<?php if ($this->Backup->getNumAvailable() >= AT_COURSE_BACKUPS): ?>
		<div class="row">
			<p><strong><?php echo _AT('max_backups_reached'); ?></strong></p>
		</div>
	<?php else: ?>
		<div class="row">
			<label for="descrip"><?php echo _AT('optional_description'); ?></label><br />
			<textarea id="descrip" cols="30" rows="2" name="description"></textarea>
		</div>
		
		<div class="row">
			<label for="file"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo _AT('file'); ?></label><br />
			<input type="file" name="file" id="file" />
		</div>

		<div class="row buttons">
		<input type="submit" name="upload" value="<?php echo _AT('upload_backup'); ?>" onclick="openWindow('<?php echo AT_BASE_HREF; ?>tools/prog.php');"  class="button"/> 
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  class="button"/>
		</div>
	<?php endif; ?>
	</fieldset>
</div>
</form>