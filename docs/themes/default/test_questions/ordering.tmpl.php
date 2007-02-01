<p><?php echo AT_print($this->row['question'], 'tests_questions.question'); ?></p>

<ul style="padding: 0px; margin: 0px; list-style-type: none">
	<?php for ($i=0; $i < $this->num_choices; $i++): ?>
		<li style="padding: 4px">
		<select name="answers[<?php echo $this->row['question_id']; ?>][<?php echo $i; ?>]" id="choice_<?php echo $this->row['question_id'].'_'.$i; ?>" />
			<option value="-1">-</option>
			<?php for ($j=0; $j < $this->num_choices; $j++): ?>
				<option value="<?php echo $j; ?>"><?php echo ($j+1); ?></option>
			<?php endfor; ?>
		</select> <label for="choice_<?php echo $this->row['question_id'].'_'.$i; ?>"><?php echo AT_print($this->row['choice_'.$i], 'tests_questions.choice_'.$i); ?></label>
		</li>
	<?php endfor; ?>
</ul>