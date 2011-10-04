<?php global $stripslashes;?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('add_question'); ?></legend>
	<div class="row">

		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="topic"><?php  echo _AT('topic'); ?></label><br />
		<select name="topic_id" id="topic">
			<?php while ($row = mysql_fetch_assoc($this->result)): ?>
				<option value="<?php echo $row['topic_id']; ?>"<?php if (isset($_POST['topic_id']) && ($row['topic_id'] == $_POST['topic_id'])) { echo ' selected="selected"'; } ?>><?php echo AT_print($row['name'], 'input.text'); ?></option>
			<?php endwhile; ?>
		</select>
	</div>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php  echo _AT('question'); ?></label><br />
		<input type="text" name="question" size="50" id="question" value="<?php if (isset($_POST['question'])) echo AT_print($stripslashes($_POST['question']), 'input.text');  ?>" />

	</div>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="answer"><?php  echo _AT('answer'); ?></label><br />
		<textarea name="answer" cols="45" rows="3" id="answer" style="width:90%;"><?php if (isset ($_POST['answer'])) echo AT_print($stripslashes($_POST['answer']), 'text.input');  ?></textarea>
	</div>


	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
	</fieldset>
</div>
</form>