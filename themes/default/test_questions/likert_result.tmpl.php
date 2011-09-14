<p><?php echo AT_print($this->row['question'], 'tests_questions.question'); ?></p>

<ul style="padding: 0px; margin: 0px; list-style-type: none">
	<?php for ($i=0; $i < 10; $i++): ?>
		<?php if ($this->row['choice_'.$i] != ''): ?>
			<li style="padding: 4px; display: inline">
				<?php if ($this->answer == $i): ?>
					<img src="<?php echo $this->base_path; ?>images/checkbox_check.gif" alt="<?php echo _AT('checked'); ?>" title="<?php echo _AT('checked'); ?>" height="13" width="13" style="vertical-align: middle" />
				<?php else: ?>
					<img src="<?php echo $this->base_path; ?>images/checkbox_empty.gif" alt="<?php echo _AT('unchecked'); ?>" title="<?php echo _AT('unchecked'); ?>" height="13" width="13" style="vertical-align: middle" />
				<?php endif; ?>
				<?php echo AT_print($this->row['choice_'.$i], 'tests_answers.answer'); ?>
			</li>
		<?php endif; ?>
	<?php endfor; ?>
</ul>