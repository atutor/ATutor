<p><?php echo AT_print($this->row['question'], 'tests_questions.question'); ?></p>

<ul style="padding: 0px; margin: 0px; list-style-type: none">
	<?php for ($i=0; $i < $this->num_choices; $i++): ?>
		<li style="padding: 4px">
			<input type="radio" name="answers[<?php echo $this->row['question_id']; ?>]" value="<?php echo $i; ?>" id="choice_<?php echo $this->row['question_id'].'_'.$i; ?>" /><label for="choice_<?php echo $this->row['question_id'].'_'.$i; ?>"><?php echo AT_print($this->row['choice_'.$i], 'tests_questions.choice_'.$i); ?></label>
	<?php endfor; ?>
	<li style="padding: 4px">
		<input type="radio" name="answers[<?php echo $this->row['question_id']; ?>]" value="-1" id="choice_<?php echo $this->row['question_id']; ?>'_x" checked="checked" /><label for="choice_<?php echo $this->row['question_id']; ?>'_x"><em><?php echo _AT('leave_blank'); ?></em></label>
	</li>
</ul>