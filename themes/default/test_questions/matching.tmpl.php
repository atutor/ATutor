<p><?php echo AT_print($this->row['question'], 'tests_questions.quotesNotConverted'); ?></p>

<table class="matching-question">
<tr>
	<td valign="top">
		<ul class="matching-question">
			<?php for ($i=0; $i < $this->num_choices; $i++): ?>
				<li class="matching-question"">
				<select name="answers[<?php echo $this->row['question_id']; ?>][<?php echo $i; ?>]">
					<option value="-1" <?php if ('' === $this->response[$i] || -1 == $this->response[$i]): ?>selected="selected"<?php endif; ?>>-</option>
					<?php for ($j=0; $j < $this->num_options; $j++): ?>
						<option value="<?php echo $j; ?>" <?php if (is_numeric($this->response[$i]) && $j == $this->response[$i]): ?>selected="selected"<?php endif; ?>><?php echo $this->letters[$j]; ?></option>
					<?php endfor; ?>
				</select>
				<?php echo AT_print($this->row['choice_'. $i], 'tests_questions.choice_'.$i); ?>
				</li>
			<?php endfor; ?>
		</ul>
	</td>
	<td valign="top">
		<ul class="matching-question">
			<?php for ($i=0; $i < $this->num_options; $i++): ?>
				<li class="matching-question"><?php echo $this->letters[$i]; ?>. <?php echo AT_print($this->row['option_'. $i], 'tests_questions.option_'.$i); ?></li>
			<?php endfor; ?>
		</ul>
	</td>
</tr>
</table>