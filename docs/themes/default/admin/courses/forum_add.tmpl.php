<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="add_forum" value="true">

<div class="input-form">
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" size="40" id="title" value="<?php echo $_POST['title']; ?>" />
	</div>

	<div class="row">
		<label for="body"><?php echo _AT('description'); ?></label><br />
		<textarea name="description" cols="45" rows="2" id="body" wrap="wrap"><?php echo $_POST['description']; ?></textarea>
	</div>

	<div class="row">
		<label for="edit"><?php echo _AT('allow_editing'); ?></label><br />
		<input type="text" name="edit" size="3" id="edit" value="<?php echo intval($row['mins_to_edit']); ?>" /> <?php echo _AT('in_minutes'); ?>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="courses"><?php echo _AT('courses'); ?></label><br />
		<?php if ($this->system_courses): ?>
			<select name="courses[]" id="courses" multiple="multiple" size="5"><?php
				while ($row = mysql_fetch_assoc($this->result)) {
					echo '<option value="'.$row['course_id'].'">'.$row['title'].'</option>';		
				}
				?>
			</select>
		<?php else: ?>
			<span id="courses"><?php echo _AT('no_courses_found'); ?></span>
		<?php endif; ?>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>