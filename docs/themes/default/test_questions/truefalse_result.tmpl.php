<p><?php echo AT_print($this->row['question'], 'tests_questions.question'); ?></p>

<p>
	<?php if (($this->row['answer_0'] == 1) && ($this->answers == 1)): ?>
		<img src="<?php echo $this->base_path; ?>images/checkmark.gif" alt="<?php echo _AT('correct_answer'); ?>" title="<?php echo _AT('correct_answer'); ?>" height="16" width="16" style="vertical-align: middle" />
	<?php elseif ($this->row['answer_0'] == 1): ?>
		<img src="<?php echo $this->base_path; ?>images/x.gif" alt="<?php echo _AT('wrong_answer'); ?>" title="<?php echo _AT('wrong_answer'); ?>" height="16" width="16" style="vertical-align: middle" />
	<?php else: ?>
		<img src="<?php echo $this->base_path; ?>images/clr.gif" alt="" title="" height="16" width="16" style="vertical-align: middle" />
	<?php endif; ?>

	<?php if ($this->answers == 1): ?>
		<img src="<?php echo $this->base_path; ?>images/checkbox_check.gif" alt="<?php echo _AT('checked'); ?>" title="<?php echo _AT('checked'); ?>" height="13" width="13" style="vertical-align: middle" />
	<?php else: ?>
		<img src="<?php echo $this->base_path; ?>images/checkbox_empty.gif" alt="<?php echo _AT('unchecked'); ?>" title="<?php echo _AT('unchecked'); ?>" height="13" width="13" style="vertical-align: middle" />
	<?php endif; ?>

	<?php echo _AT('true'); ?>
</p>

<p>
	<?php if (($this->row['answer_0'] == 2) && ($this->answers == 2)): ?>
		<img src="<?php echo $this->base_path; ?>images/checkmark.gif" alt="<?php echo _AT('correct_answer'); ?>" title="<?php echo _AT('correct_answer'); ?>" height="16" width="16" style="vertical-align: middle" />
	<?php elseif ($this->row['answer_0'] == 2): ?>
		<img src="<?php echo $this->base_path; ?>images/x.gif" alt="<?php echo _AT('wrong_answer'); ?>" title="<?php echo _AT('wrong_answer'); ?>" height="16" width="16" style="vertical-align: middle" />
	<?php else: ?>
		<img src="<?php echo $this->base_path; ?>images/clr.gif" alt="" title="" height="16" width="16" style="vertical-align: middle" />
	<?php endif; ?>

	<?php if ($this->answers == 2): ?>
		<img src="<?php echo $this->base_path; ?>images/checkbox_check.gif" alt="<?php echo _AT('checked'); ?>" title="<?php echo _AT('checked'); ?>" height="13" width="13" style="vertical-align: middle" />
	<?php else: ?>
		<img src="<?php echo $this->base_path; ?>images/checkbox_empty.gif" alt="<?php echo _AT('unchecked'); ?>" title="<?php echo _AT('unchecked'); ?>" height="13" width="13" style="vertical-align: middle" />
	<?php endif; ?>
	<?php echo _AT('false'); ?>
</p>
