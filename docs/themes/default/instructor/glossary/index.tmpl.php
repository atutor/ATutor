

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table class="data" summary="" rules="cols" style="width: 90%;">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('glossary_term'); ?></th>
	<th scope="col"><?php echo _AT('glossary_definition'); ?></th>
	<th scope="col"><?php echo _AT('glossary_related'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="4"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>

<?php if(!empty($this->gloss_results_row)):?>
	<?php foreach($this->gloss_results_row as $row): ?>
		<tr onmousedown="document.form['m<?php echo $row['word_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['word_id']; ?>">
			<td valign="top" width="10"><input type="radio" name="word_id" value="<?php echo $row['word_id']; ?>" id="m<?php echo $row['word_id']; ?>" /></td>
			<td valign="top"><label for="m<?php echo $row['word_id']; ?>"><?php echo AT_print($row['word'], 'glossary.word'); ?></label></td>
			<td style="whitespace:nowrap;"><?php echo AT_print($this->def_trunc, 'glossary.definition'); ?></td>		
			<td valign="top"><?php echo AT_print($this->related_word, 'glossary.word'); ?></td>
		</tr>
	
	<?php endforeach;?>
<?php endif; ?>

</tbody>
</table>
</form>

