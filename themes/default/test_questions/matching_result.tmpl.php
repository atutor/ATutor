<p><?php echo AT_print($this->row['question'], 'tests_questions.question'); ?></p>

<table style="width: 100%">
<tr>
	<td valign="top">
		<ul style="padding: 0px; margin: 0px; list-style-type: none">
			<?php for ($i=0; $i < 10; $i++): ?>
				<?php if ($this->row['choice_'. $i] != ''): ?>
					<li style="padding: 4px">
						<?php if ($this->row['answer_'.$i] == $this->answers[$i]): ?>
							<img src="<?php echo $this->base_path; ?>images/checkmark.gif" alt="<?php echo _AT('correct_answer'); ?>" title="<?php echo _AT('correct_answer'); ?>" height="16" width="16" style="vertical-align: middle" />
						<?php else: ?>
							<img src="<?php echo $this->base_path; ?>images/x.gif" alt="<?php echo _AT('wrong_answer'); ?>" title="<?php echo _AT('wrong_answer'); ?>" height="16" width="16" style="vertical-align: middle" />
							<?php if ($this->row['answer_'.$i] >= 0): ?>
								(<?php echo $this->letters[$this->row['answer_'.$i]]; ?>)
							<?php endif; ?>
						<?php endif; ?>
						<?php if ($this->answers[$i] != '' && $this->answers[$i] >= 0): ?>
							<?php echo $this->letters[$this->answers[$i]]; ?>.
						<?php else: ?>
							-.
						<?php endif; ?>
						<?php echo $this->row['choice_'. $i]; ?>
					</li>
				<?php endif; ?>
			<?php endfor; ?>
		</ul>
	</td>
	<td valign="top">
		<ul style="list-style-type: none; margin: 0px; padding: 0px">
			<?php for ($i=0; $i < $this->num_options; $i++): ?>
				<li style="padding: 4px"><?php echo $this->letters[$i]; ?>. <?php echo $this->row['option_'. $i]; ?></li>
			<?php endfor; ?>
		</ul>
	</td>
</tr>
</table>