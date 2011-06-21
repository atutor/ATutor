<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="course" value="<?php echo $course; ?>" />

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('course_email'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
		<?php echo  _AT('to'); ?><br />
		<input type="checkbox" name="to_assistants" value="1" id="assistants" <?php if ($_POST['to_assistants']=='1') { echo 'checked="checked"'; } ?> /><label for="assistants"><?php echo  _AT('assistants'); ?></label>
		<input type="checkbox" name="to_enrolled" value="1" id="enrolled" <?php if ($_POST['to_enrolled']=='1') { echo 'checked="checked"'; } else { echo 'checked="checked"'; } ?> /><label for="enrolled"><?php echo  _AT('enrolled'); ?></label>
		<input type="checkbox" name="to_unenrolled" value="1" id="unenrolled" <?php if ($_POST['to_unenrolled']=='1') { echo 'checked="checked"'; } ?> /><label for="unenrolled"><?php echo  _AT('unenrolled'); ?></label>
		<input type="checkbox" name="to_alumni" value="1" id="alumni" <?php if ($_POST['to_alumni']=='1') { echo 'checked="checked"'; } ?> /><label for="alumni"><?php echo  _AT('alumni'); ?></label>

		<?php if (!empty($this->group_type_rows)): ?>
			<br /><br />
			<?php echo _AT('or_groups'); ?>:<br />
			<select name="groups[]" multiple="multiple" size="10" style="padding-right: 5px">
				<?php foreach ($this->group_type_rows as $type_id => $row): ?>
					<optgroup label="<?php echo $row['title']; ?>">
						<?php foreach ($row['group_type_row'] as $group_row): ?>
							<option value="<?php echo $group_row['group_id']; ?>"><?php echo $group_row['title']; ?></option>
						<?php endforeach; ?>
					</optgroup>
				<?php endforeach; ?>
			</select>
		<?php endif; ?>
		
		<?php debug($this->group_type_rows);?>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="subject"><?php echo _AT('subject'); ?></label><br />
		<input type="text" name="subject" size="60" id="subject" value="<?php echo $_POST['subject']; ?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="body"><?php echo _AT('body'); ?></label><br />
		<textarea cols="55" rows="18" name="body" id="body"><?php echo $_POST['body']; ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('send'); ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
	</fieldset>
</div>
</form>
