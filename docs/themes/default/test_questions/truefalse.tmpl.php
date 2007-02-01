<p><?php echo AT_print($this->row['question'], 'tests_questions.question'); ?></p>

<p><input type="radio" name="answers[<?php echo $this->row['question_id']; ?>]" value="1" id="choice_<?php echo $this->row['question_id']; ?>_0" /><label for="choice_<?php echo $this->row['question_id']; ?>_0"><?php echo _AT('true'); ?></label></p>

<p><input type="radio" name="answers[<?php echo $this->row['question_id']; ?>]" value="2" id="choice_<?php echo $this->row['question_id']; ?>_1" /><label for="choice_<?php echo $this->row['question_id']; ?>_1"><?php echo _AT('false'); ?></label></p>

<p><input type="radio" name="answers[<?php echo $this->row['question_id']; ?>]" value="-1" id="choice_<?php echo $this->row['question_id']; ?>_x" checked="checked" /><label for="choice_<?php echo $this->row['question_id']; ?>_x"><em><?php echo _AT('leave_blank'); ?></em></label></p>