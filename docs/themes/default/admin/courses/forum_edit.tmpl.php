<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="edit_this->forum" value="true">
	<input type="hidden" name="forum" value="<?php echo $_REQUEST['forum']; ?>">

<div class="input-form">
	<div class="row">
		<label for="title"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php  echo _AT('title'); ?></label><br />
		<input type="text" name="title" size="40" id="title" value="<?php echo AT_print($this->forum['title'], 'input.text'); ?>" />
	</div>

	<div class="row">
		<label for="body"><?php echo _AT('description'); ?></label><br />
		<textarea name="description" cols="45" rows="5" id="body" wrap="wrap"><?php echo AT_print($this->forum['description'], 'input.text'); ?></textarea>
	</div>

	<div class="row">
		<label for="edit"><?php echo _AT('allow_editing'); ?></label><br />
		<input type="text" name="edit" size="3" id="edit" value="<?php echo intval($this->forum['mins_to_edit']); ?>" /> <?php echo _AT('in_minutes'); ?>
	</div>

	<div class="row">
		<label for="courses"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo _AT('courses'); ?></label><br />
		<select name="courses[]" id="courses" multiple="multiple" size="5"><?php
			/*
			echo '<option value="0"';
			if ($courses[0] == 0) {
				echo ' selected="selected"';
			}
			echo '> '._AT('all').' </option>';
			*/
			
			while ($row = mysql_fetch_assoc($this->result)) {
				if (in_array($row['course_id'], $this->courses) ) {
					echo '<option value="'.$row['course_id'].'" selected="selected">'.AT_print($row['title'], 'input.text').'</option>';		
				} else {
					echo '<option value="'.$row['course_id'].'">'.AT_print($row['title'], 'input.text').'</option>';
				}
			}
			?></select>
	</div>
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php  echo _AT('submit'); ?>" accesskey="s" /> <input type="submit" name="cancel" value="<?php  echo _AT('cancel'); ?>" />
	</div>
</div>
	</form>