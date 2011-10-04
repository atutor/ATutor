<?php global $stripslashes;
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="id" value="<?php echo $this->row['entry_id']; ?>" />

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('create_new_file'); ?></legend>
	<div class="row">
		
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="topic"><?php  echo _AT('topic'); ?></label><br />
		<select name="topic_id" id="topic">
		<?php if(!empty($this->faq_topics)):?>
			<?php foreach($this->faq_topics as $topic_row):?>
				<option value="<?php echo $topic_row['topic_id']; ?>"<?php if ($topic_row['topic_id'] == $row['topic_id']) { echo ' selected="selected"'; } ?>><?php echo AT_print($topic_row['name'], 'input.text'); ?></option>			
			<?php endforeach;?>		
		<?php endif;?>
		</select>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php echo _AT('question'); ?>:</label><br />
		<input type="text" name="question" size="50" id="question" value="<?php if (isset ($_POST['question'])) { echo AT_print($stripslashes($_POST['question']), 'input.text'); } else { echo AT_print($this->row['question'], 'input.text'); } ?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="answer"><?php  echo _AT('answer'); ?></label><br />
		<textarea name="answer" cols="45" rows="3" id="answer" style="width:90%;"><?php if (isset ($_POST['answer'])) { echo AT_print($stripslashes($_POST['answer']), 'input.text'); } else { echo AT_print($this->row['answer'], 'input.text'); } ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?> " />
	</div>
	</fieldset>
</div>
</form>
