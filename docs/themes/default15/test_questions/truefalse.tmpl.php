<p><?php echo AT_print($this->row['question'], 'tests_questions.question'); ?></p>

<p><input type="radio" name="answers[<?php echo $this->row['question_id']; ?>]" value="1" id="choice_<?php echo $this->row['question_id']; ?>_0" <?php if ($this->response == 1):?>checked="checked"<?php endif; ?>/><label for="choice_<?php echo $this->row['question_id']; ?>_0"><?php echo _AT('true'); ?></label></p>

<p><input type="radio" name="answers[<?php echo $this->row['question_id']; ?>]" value="2" id="choice_<?php echo $this->row['question_id']; ?>_1" <?php if ($this->response == 2):?>checked="checked"<?php endif; ?>/><label for="choice_<?php echo $this->row['question_id']; ?>_1"><?php echo _AT('false'); ?></label></p>

<p><input type="radio" name="answers[<?php echo $this->row['question_id']; ?>]" value="-1" id="choice_<?php echo $this->row['question_id']; ?>_x" <?php if ($this->response < 1):?>checked="checked"<?php endif; ?>/><label for="choice_<?php echo $this->row['question_id']; ?>_x"><em><?php echo _AT('leave_blank'); ?></em></label></p>