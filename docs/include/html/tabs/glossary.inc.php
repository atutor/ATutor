	<tr>
		<td colspan="2" valign="top" align="left" class="row1"><?php
	
	if ($num_terms == 0) {
		echo _AT('no_terms_found');
	}

	for ($i=0; $i<$num_terms; $i++) {
		for ($j=0;$j<$i;$j++) {
			if ($word[$j] == $word[$i]) {
				debug('2 ignoring: '.$word[$i]);
				echo '<input type="hidden" name="ignore['.$i.']" value="1" />';
				continue 2;
			}
		}

		if ($word[$i] == '') {
			$word[$i] = ContentManager::cleanOutput($_POST['word'][$i]);
		}
		?><table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
		<tr>
			<td align="right" class="row1"><?php print_popup_help(AT_HELP_GLOSSARY_MINI); ?><b><?php 
		
				if ($glossary[$word[$i]] == '') {
					echo '<em>'._AT('new').'</em> ';
				}

				echo _AT('glossary_term'); ?>:</b></td>
			<td class="row1"><?php echo AT_print($word[$i], 'glossary.word'); ?></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td valign="top" align="right" class="row1"><b><label for="body<?php echo $i; ?>"><?php echo _AT('glossary_definition');  ?>:</label></b></td>
			<td class="row1">
				<textarea name="glossary_defs[<?php echo $word[$i]; ?>]" class="formfield" cols="55" rows="4" id="body<?php echo $i; ?>"><?php 
					echo ContentManager::cleanOutput($_POST['glossary_defs'][$word[$i]]); 
		
		?></textarea></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td valign="top" align="right" class="row1"><b><?php echo _AT('glossary_related');  ?>:</b></td>
			<td class="row1"><?php
				
					$sql = "SELECT * FROM ".TABLE_PREFIX."glossary WHERE course_id=$_SESSION[course_id] ORDER BY word";
					$result = mysql_query($sql, $db);
					if ($row_g = mysql_fetch_array($result)) {
						echo '<select name="related_term['.$i.']">';
						echo '<option value="0"></option>';
						do {
							echo '<option value="'.$row_g['word_id'].'">'.$row_g['word'].'</option>';
						} while ($row_g = mysql_fetch_assoc($result));
						echo '</select>';
					} else {
						echo _AT('none_available');
					}

				?></td>
		</tr></table><br />
	<?php } ?>
		</td>
	</tr>