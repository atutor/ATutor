<?php
// THIS FILE IS NOT USED IN THE MOBILE THEME
exit;
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="num_terms" value="<?php echo $this->num_terms; ?>" />
<?php
for ($i=0;$i<$this->num_terms;$i++) {
	if ($glossary[$word[$i]] != '') {
		echo '<input type="hidden" name="ignore['.$i.']" value="1" />';
		continue;
	}
	
	for ($j=0;$j<$i;$j++) {
		if ($word[$j] == $word[$i]) {
			echo '<input type="hidden" name="ignore['.$i.']" value="1" />';
			continue 2;
		}
	}

	if ($word[$i] == '') {
		$word[$i] = ContentManager::cleanOutput($_POST['word'][$i]);
	}
?>
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('add_glossary'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title<?php echo $i; ?>"><?php echo _AT('glossary_term');  ?></label><br />
		<input type="text" name="word[<?php echo $i; ?>]" size="30" value="<?php echo trim($word[$i]); ?>" id="title<?php echo $i; ?>" /><?php			
		if ($_GET['pcid'] != '') { 
			echo '<input type="checkbox" name="ignore['.$i.']" value="1" id="ig'.$i.'" /><label for="ig'.$i.'">Ignore this term</label>.';	
		}
		?>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="body<?php echo $i; ?>"><?php echo _AT('glossary_definition');  ?></label><br />
		<textarea name="definition[<?php echo $i; ?>]" class="formfield" cols="55" rows="7" id="body<?php echo $i; ?>" style="width:90%;"><?php echo ContentManager::cleanOutput($_POST['definition'][$i]); ?></textarea>
	</div>

	<div class="row">
	<?php echo _AT('glossary_related');  ?><br />
	<?php
		    if(count($this->rows_g) != 0 ){
			//if ($row_g = mysql_fetch_assoc($this->result_glossary)) {
				echo '<select name="related_term['.$i.']">';
				echo '<option value="0"></option>';
			    foreach($this->rows_g as $row_g){
				//do {
					//echo '<option value="'.$row_g['word_id'].'">'.$row_g['word'].'</option>';
				} //while ($row_g = mysql_fetch_assoc($this->result_glossary));
				echo '</select>';
			} else {
				echo _AT('none_available');
			}
		} // endfor
	?>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
	</fieldset>
</div>
</form>