<p><?php echo AT_print($this->row['question'], 'tests_questions.question'); ?></p>

<?php if ($this->row['properties'] == 1): /* one word */ ?>
	<input type="text" name="answers[<?php echo $this->row['question_id']; ?>]" class="formfield" size="15" value="<?php echo htmlspecialchars($this->response); ?>" />

<?php elseif ($this->row['properties'] == 2): /* sentence */ ?>
	<input type="text" name="answers[<?php echo $this->row['question_id']; ?>]" class="formfield" size="45" value="<?php echo htmlspecialchars($this->response); ?>" />

<?php elseif ($this->row['properties'] == 3): /* paragraph */ ?>
	<textarea cols="55" rows="5" name="answers[<?php echo $this->row['question_id']; ?>]" class="formfield"><?php echo htmlspecialchars($this->response); ?></textarea>

<?php elseif ($this->row['properties'] == 4): /* page */ ?>
	<textarea cols="55" rows="25" name="answers[<?php echo $this->row['question_id']; ?>]" class="formfield"><?php echo htmlspecialchars($this->response); ?></textarea>

<?php endif; ?>