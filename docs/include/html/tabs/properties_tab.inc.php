
		<?php if ($_POST['day']) { ?>
		<tr>
			<td class="row1"><br /><?php print_popup_help(AT_HELP_NOT_RELEASED); ?><b><?php echo _AT('release_date');  ?></b></td>
			<td class="row1"><br /><?php

				$today_day   = $_POST['day'];
				$today_mon   = $_POST['month'];
				$today_year  = $_POST['year'];

				$today_hour  = $_POST['hour'];
				$today_min   = $_POST['min'];
				require(AT_INCLUDE_PATH.'lib/release_date.inc.php');
		?>
	</td>
	</tr>
	<?php } else { ?>
	<tr>
	<td class="row1"><br /><?php print_popup_help(AT_HELP_NOT_RELEASED); ?><b><?php echo _AT('release_date');  ?>:</b></td>
	<td class="row1"><br /><?php

			require(AT_INCLUDE_PATH.'lib/release_date.inc.php');

			?>
	</td>
	</tr>
	
	<?php } ?>
			<tr><td height="1" class="row2" colspan="2"></td></tr>

<tr>
			<td class="row1"><?php print_popup_help(AT_HELP_RELATED); ?><b><?php echo _AT('related_to');  ?>:</b></td>
		<td class="row1"><?php

		if ($contentManager->getNumSections() > 1) {
			/* get existing related content */
			if (isset($_POST['current_tab'])) {
				$related_content = $_POST['related'];
			} else {
				$related_content = $contentManager->getRelatedContent($cid);
			}

			echo '<select class="formfield" name="related[]">';
			echo '<option value="0"></option>';

			$contentManager->print_select_menu(0, $related_content[0]);

			echo '</select></td></tr>';
			

			for ($i=1; $i<max( min(4, $contentManager->getNumSections()-1 ), count($related_content) ); $i++) {
				echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
				echo '<tr><td class="row1">&nbsp;</td>';
				echo '<td class="row1"><select class="formfield" name="related[]">
							<option value="0"></option>';
				
				$contentManager->print_select_menu(0, $related_content[$i]);

				echo '</select></td></tr>';
			}
		} else {
			echo _AT('none_available').'</td></tr>';
		}
?>
		<tr><td height="1" class="row2" colspan="2"></td></tr>			
		<tr>
			<td class="row1"><a name="jumpcodes"></a><?php print_popup_help(AT_HELP_INSERT); ?><b><label for="move"><?php echo _AT('move_to'); ?>:</label></b><br /><br /></td>
			
			<td class="row1"><select name="new_ordering" class="formfield" id="move">
				<option value="-1"></option><?php

				$top_level = $contentManager->getContent($row['content_parent_id']);

			if ($_POST['ordering'] != count($top_level)) {
				echo '<option value="'.count($top_level).'">'._AT('end_section').'</option>';
			}
			if ($_POST['ordering'] != 1) {
				echo '<option value="1">'._AT('start_section').'</option>';
			}

			foreach ($top_level as $x => $info) {
				if (($info['ordering'] != $_POST['ordering']-1) 
					&& ($info['ordering'] != $_POST['ordering']))
				{
					echo '<option value="';
					
					if ($info['ordering'] == count($top_level)) {
						/* special case, last item */
						$value = $info['ordering'];
					} else {
						$value = $info['ordering']+1;
					}
				
					echo $value.'"';

					if ($_POST['new_ordering'] == $value) {
						echo ' selected="selected"';
					}

					echo '>'._AT('after').': '.$info['ordering'].' "'.$info['title'].'"</option>';
				} else {
					echo '<option value="-1">'._AT('no_change').': '.$info['ordering'].' "'.$info['title'].'"</option>';
				}
			}
		?></select><?php
			$temp_menu = $contentManager->getContent();
			echo _AT('or').' <select name="move">';
			echo '<option value="-1"></option>';
			echo '<option value="0">'._AT('top').'</option>';
			$contentManager->print_move_select(0, $row['content_parent_id'], $_POST['move']);
			echo '</select>';

		?><br /><br /></td>
		</tr>