<p><?php echo AT_print($this->row['question'], 'tests_questions.question'); ?></p>

<table style="width: 100%">
<tr>
	<td valign="top">
		<ul style="padding: 0px; margin: 0px; list-style-type: none">
			<?php for ($i=0; $i < $this->num_choices; $i++): ?>
				<li style="padding: 4px">
				<select name="answers[<?php echo $this->row['question_id']; ?>][<?php echo $i; ?>]">
					<option>-</option>
					<?php for ($j=0; $j < $this->num_options; $j++): ?>
						<option value="<?php echo $j; ?>"><?php echo $this->letters[$j]; ?></option>
					<?php endfor; ?>
				</select>
				<?php echo $this->row['choice_'. $i]; ?>
				</li>
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