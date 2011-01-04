<p><?php echo AT_print($this->row['question'], 'tests_questions.question'); ?></p>

<ol style="margin: 0px; padding: 0px">
	<?php for ($i=0; $i < $this->num_choices; $i++): ?>
		<li style="padding: 4px; list-style: none">
			<?php if ($this->right_answers[$i] == $this->answers[$i]): ?>
				<img src="<?php echo $this->base_path; ?>images/checkmark.gif" alt="<?php echo _AT('correct_answer'); ?>" title="<?php echo _AT('correct_answer'); ?>" height="16" width="16" style="vertical-align: middle" />
			<?php else: ?>
				<img src="<?php echo $this->base_path; ?>images/x.gif" alt="<?php echo _AT('wrong_answer'); ?>" title="<?php echo _AT('wrong_answer'); ?>" height="16" width="16" style="vertical-align: middle" />
			<?php endif; ?>
			<?php echo $this->answers[$i] == -1 ? '-' : $this->answers[$i]+1; ?>. <?php echo AT_print($this->row['choice_'.$i], 'tests_questions.choice_'.$i); ?>
		</li>
	<?php endfor; ?>
</ol>