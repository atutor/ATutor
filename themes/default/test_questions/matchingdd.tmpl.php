<p><?php echo AT_print($this->row['question'], 'tests_questions.quotesNotConverted'); ?></p>

<?php $tmp_response = implode('|', $this->response); ?>
<?php $_SESSION['dd_question_ids'][$this->row['question_id']] = 1; ?>

<?php for ($i=0; $i < $this->num_choices; $i++): ?>
	<input type="hidden" name="answers[<?php echo $this->row['question_id']; ?>][<?php echo $i; ?>]" id="<?php echo $this->row['question_id']; ?>q<?php echo $i; ?>" value="<?php echo $this->response[$i]; ?>"/>
<?php endfor; ?>
<iframe id="qframe<?php echo $this->row['question_id']; ?>" src="<?php echo $this->base_href; ?>mods/_standard/tests/dd.php?qid=<?php echo $this->row['question_id'].SEP; ?>response=<?php echo $tmp_response; ?>" height="200" width="100%" frameborder="0"></iframe>