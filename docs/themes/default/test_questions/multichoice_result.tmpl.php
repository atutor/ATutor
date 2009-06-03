<p><?php echo AT_print($this->row['question'], 'tests_questions.question'); ?></p>

<ul style="padding: 0px; margin: 0px; list-style-type: none">
	<?php for ($i=0; $i < 10; $i++): ?>
		<?php if ($this->row['choice_'.$i] != ''): ?>
			<li style="padding: 4px">
				<?php if (($this->row['answer_'.$i] == 1) && in_array($i, $this->answers)): ?>
					<img src="<?php echo $this->base_path; ?>images/checkmark.gif" alt="<?php echo _AT('correct_answer'); ?>" title="<?php echo _AT('correct_answer'); ?>" height="16" width="16" style="vertical-align: middle" />
				<?php elseif (($this->row['answer_'.$i] == 0) || in_array($i, $this->answers)): ?>
					<img src="<?php echo $this->base_path; ?>images/x.gif" alt="<?php echo _AT('wrong_answer'); ?>" title="<?php echo _AT('wrong_answer'); ?>" height="16" width="16" style="vertical-align: middle" />
				<?php else: ?>
					<img src="<?php echo $this->base_path; ?>images/clr.gif" alt="" title="" height="16" width="16" style="vertical-align: middle" />
				<?php endif; ?>

				<?php if (in_array($i, $this->answers)): ?>
					<img src="<?php echo $this->base_path; ?>images/checkbox_check.gif" alt="<?php echo _AT('checked'); ?>" title="<?php echo _AT('checked'); ?>" height="13" width="13" style="vertical-align: middle" />
				<?php else: ?>
					<img src="<?php echo $this->base_path; ?>images/checkbox_empty.gif" alt="<?php echo _AT('unchecked'); ?>" title="<?php echo _AT('unchecked'); ?>" height="13" width="13" style="vertical-align: middle" />
				<?php endif; ?>
				<?php echo AT_print($this->row['choice_'.$i], 'tests_questions.choice_'.$i); ?>
		<?php endif; ?>
	<?php endfor; ?>
</ul>