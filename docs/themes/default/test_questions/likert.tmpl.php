<p><?php echo AT_print($this->row['question'], 'tests_questions.question'); ?></p>

<ul style="padding: 0px; margin: 0px; list-style-type: none">
	<?php for ($i=0; $i < $this->num_choices; $i++): ?>
		<li style="padding: 4px; display: inline">
			<input type="radio" name="answers[<?php echo $this->row['question_id']; ?>]" value="<?php echo $i; ?>" id="choice_<?php echo $this->row['question_id'].'_'.$i; ?>" <?php if ($this->response == $i): ?>checked="checked"<?php endif; ?>/><label for="choice_<?php echo $this->row['question_id'].'_'.$i; ?>"><?php echo AT_print($this->row['choice_'.$i], 'tests_answers.answer'); ?></label>
		</li>
	<?php endfor; ?>
	<li style="padding: 4px; display: inline">
		<input type="radio" name="answers[<?php echo $this->row['question_id']; ?>]" value="-1" id="choice_<?php echo $this->row['question_id']; ?>_x" <?php if ($this->response == -1): ?>checked="checked"<?php endif; ?>/><label for="choice_<?php echo $this->row['question_id']; ?>_x"><em><?php echo _AT('leave_blank'); ?></em></label>
	</li>
</ul>