<p><?php echo AT_print($this->row['question'], 'tests_questions.question'); ?></p>

<ul style="padding: 0px; margin: 0px; list-style-type: none">
	<?php for ($i=0; $i < $this->num_choices; $i++): ?>
		<li style="padding: 4px">
			<input type="radio" name="answers[<?php echo $this->row['question_id']; ?>]" value="<?php echo $i; ?>" id="choice_<?php echo $this->row['question_id'].'_'.$i; ?>" <?php 
			//Multiple choice will always have just 1 choice, thus the response is always in the array of $this->response[0]
			if (is_numeric($this->response[0]) && $i == $this->response[0]): ?>checked="checked"<?php endif; ?>/><label for="choice_<?php echo $this->row['question_id'].'_'.$i; ?>"><?php echo AT_print($this->row['choice_'.$i], 'tests_questions.choice_'.$i); ?></label>
	<?php endfor; ?>
	<li style="padding: 4px">
		<input type="radio" name="answers[<?php echo $this->row['question_id']; ?>]" value="-1" id="choice_<?php echo $this->row['question_id']; ?>'_x" <?php if (is_numeric($this->response[0]) && -1 == $this->response[0]): ?>checked="checked"<?php endif; ?> /><label for="choice_<?php echo $this->row['question_id']; ?>'_x"><strong><?php echo _AT('leave_blank'); ?></strong></label>
	</li>
</ul>