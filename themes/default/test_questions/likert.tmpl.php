<p><?php echo AT_print($this->row['question'], 'tests_questions.quotesNotConverted'); ?></p>

<ul class="likert-question">
	<?php for ($i=0; $i < $this->num_choices; $i++): ?>
		<li class="likert-question">
			<input type="radio" name="answers[<?php echo $this->row['question_id']; ?>]" value="<?php echo $i; ?>" id="choice_<?php echo $this->row['question_id'].'_'.$i; ?>" <?php if ($this->response == $i): ?>checked="checked"<?php endif; ?>/><label for="choice_<?php echo $this->row['question_id'].'_'.$i; ?>"><?php echo AT_print($this->row['choice_'.$i], 'tests_answers.answer'); ?></label>
		</li>
	<?php endfor; ?>
	<li class="likert-question">
		<input type="radio" name="answers[<?php echo $this->row['question_id']; ?>]" value="-1" id="choice_<?php echo $this->row['question_id']; ?>_x" <?php if ($this->response == -1): ?>checked="checked"<?php endif; ?>/><label for="choice_<?php echo $this->row['question_id']; ?>_x"><strong><?php echo _AT('leave_blank'); ?></strong></label>
	</li>
</ul>