

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<table class="data" style="width: 90%;">
<thead>
<tr>
	<th>&nbsp;</th>
	<th style="width: 100%;"><?php echo _AT('name'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="2"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
				    <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<?php if (!empty($this->faq_topics)): ?>
		<?php foreach ($this->faq_topics as $topic_id => $row): ?>
					<tr onmousedown="document.form['t<?php echo $row['topic_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['topic_id']; ?>_0">
					<th style="border-top:1pt solid #e0e0e0;"><input type="radio" name="item" id="t<?php echo $row['topic_id']; ?>" value="<?php echo $row['topic_id']; ?>" /></th>
					<th style="border-top:1pt solid #e0e0e0;"><?php echo AT_print($row['name'], 'faqs.topic'); ?></th>
					</tr>
	
			<?php if (!empty($row['entry_rows'])): ?>
				<?php foreach($row['entry_rows'] as $question_row): ?>
				<tr onmousedown="document.form['q<?php echo $question_row['entry_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['topic_id']; ?>_<?php echo $question_row['entry_id']; ?>">
					<td><input type="radio" name="item" id="q<?php echo $question_row['entry_id']; ?>" value="<?php echo $question_row['entry_id']; ?>q" /></td>
					<td><?php echo AT_print($question_row['question'], 'faqs.question'); ?></td>
				</tr>
				<?php endforeach;?>
			
			<?php else:?>
			<tr>
					<td>&nbsp;</td>
					<td><?php echo _AT('no_questions'); ?></td>
				</tr>
			<?php endif;?>
		<?php endforeach; ?>
	<tbody>
	
		
	</tbody>
<?php else: ?>
	<tr>
		<td colspan="2"><strong><?php echo _AT('none_found'); ?></strong></td>
	</tr>
<?php endif; ?>
</table>
</form>