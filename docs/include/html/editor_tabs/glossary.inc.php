<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: glossary.inc.php,v 1.3 2004/02/24 16:15:35 joel Exp $

if (!defined('AT_INCLUDE_PATH')) { exit; }

?>
	<tr>
		<td colspan="2" valign="top" align="left" class="row1"><?php
	
	if ($num_terms == 0) {
		echo _AT('no_terms_found');
	}

	$num_glossary = count($glossary_ids);

	for ($i=0; $i<$num_terms; $i++) {
		for ($j=0;$j<$i;$j++) {
			if ($word[$j] == $word[$i]) {
				/* skip multiple occurances of the same word: */
				continue 2;
			}
		}

		?><table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
		<tr>
			<td align="right" class="row1"><?php print_popup_help(AT_HELP_GLOSSARY_MINI); ?><b><?php 
		
				if (!isset($glossary[$word[$i]])) {
					echo '<em>'._AT('new').'</em> ';
				}

				echo _AT('glossary_term'); ?>:</b></td>
			<td class="row1"><?php echo AT_print(urldecode($word[$i]), 'glossary.word'); ?></td>
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
			<td valign="top" align="right" class="row1"><label for="r<?php echo $i; ?>"><b><?php echo _AT('glossary_related');  ?>:</b></label></td>
			<td class="row1"><?php

				if ($num_glossary > 1) {
					echo '<select name="related_term['.$word[$i].']" id="r'.$i.'">';
					echo '<option value="0"></option>';
					foreach ($glossary_ids as $id => $term) {
						if ($term == $word[$i]) {
							continue;
						}
						echo '<option value="'.$id.'"';
						if ($_POST['related_term'][$word[$i]] == $id) {
							echo ' selected="selected"';
						}
						echo '>'.urldecode($term).'</option>';
					}
					echo '</select>';
				} else {
					echo _AT('none_available');
				}

				?></td>
		</tr></table><br />
	<?php } ?>
		</td>
	</tr>